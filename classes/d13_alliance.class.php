<?php

// ========================================================================================
//
// ALLIANCE.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// Handles Alliances (Guilds/Clans etc.) - allows to create, edit, delete Alliances. Also
// allows to invite new members to the alliance, remove existing members, declare a war
// or propose Peace to other alliances.
//
// ========================================================================================

class d13_alliance
{
    
    public $data, $members, $invitations, $wars;
	
	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function get($idType, $id)
    {
        global $d13;
        $result = $d13->dbQuery('select * from alliances where ' . $idType . '="' . $id . '"');
        $this->data = $d13->dbFetch($result);
        if (isset($this->data['id'])) $status = 'done';
        else $status = 'noAlliance';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function set($nodeId)
    {
        global $d13;
        $alliance = new d13_alliance();
        if ($alliance->get('id', $this->data['id']) == 'done')
            if ($alliance->get('name', $this->data['name']) == 'noAlliance') {
                $node = new d13_node();
                if ($node->get('id', $nodeId) == 'done') {
                    $node->getResources();
                    $setCost = $d13->getFaction($node->data['faction'], 'costs', 'alliance');
                    $setCostData = $node->checkCost($setCost, 'alliance');
                    if ($setCostData['ok']) {
                        $ok = 1;
                        foreach ($setCost as $cost) {
                            $node->resources[$cost['resource']]['value'] -= $cost['value'] * $d13->getGeneral('users', 'efficiency', 'alliance');
                            $d13->dbQuery('update resources set value="' . $node->resources[$cost['resource']]['value'] . '" where node="' . $node->data['id'] . '" and id="' . $cost['resource'] . '"');
                            if ($d13->dbAffectedRows() == -1) $ok = 0;
                        }

                        $d13->dbQuery('update alliances set name="' . $this->data['name'] . '", tag="' . $this->data['tag'] . '", avatar="' . $this->data['avatar'] . '" where id="' . $this->data['id'] . '"');
                        if ($d13->dbAffectedRows() == -1) $ok = 0;
                        if ($ok) $status = 'done';
                        else $status = 'error';
                    } else $status = 'notEnoughResources';
                } else $status = 'noNode';
            } else $status = 'nameTaken';
        else $status = 'noAlliance';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function add($nodeId)
    {
        global $d13;
        $alliance = new d13_alliance();
        if ($alliance->get('name', $this->data['name']) == 'noAlliance') {
            $node = new d13_node();
            if ($node->get('id', $nodeId) == 'done') {
                $node->checkResources(time());
                $addCost = $d13->getFaction($node->data['faction'], 'costs', 'alliance');
                $addCostData = $node->checkCost($addCost, 'alliance');
                if ($addCostData['ok']) {
                    $ok = 1;
                    foreach ($addCost as $cost) {
                        $node->resources[$cost['resource']]['value'] -= $cost['value'] * $d13->getGeneral('users', 'efficiency', 'alliance');
                        $d13->dbQuery('update resources set value="' . $node->resources[$cost['resource']]['value'] . '" where node="' . $node->data['id'] . '" and id="' . $cost['resource'] . '"');
                        if ($d13->dbAffectedRows() == -1) $ok = 0;
                    }

                    $this->data['id'] = d13_misc::newId('alliances');
                    $d13->dbQuery('insert into alliances (id, user, name) values ("' . $this->data['id'] . '", "' . $node->data['user'] . '", "' . $this->data['name'] . '")');
                    if ($d13->dbAffectedRows() == -1) $ok = 0;
                    $d13->dbQuery('update users set alliance="' . $this->data['id'] . '" where id="' . $this->data['user'] . '"');
                    if ($d13->dbAffectedRows() == -1) $ok = 0;
                    if ($ok) $status = 'done';
                    else $status = 'error';
                } else $status = 'notEnoughResources';
            } else $status = 'noNode';
        } else $status = 'nameTaken';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public static function remove($id)
    {
        global $d13;
        $alliance = new d13_alliance();
        if ($alliance->get('id', $id) == 'done') {
            $ok = 1;
            $d13->dbQuery('delete from invitations where alliance="' . $id . '"');
            if ($d13->dbAffectedRows() == -1) $ok = 0;
            $d13->dbQuery('delete from wars where sender="' . $id . '" or recipient="' . $id . '"');
            if ($d13->dbAffectedRows() == -1) $ok = 0;
            $d13->dbQuery('insert into free_ids (id, type) values ("' . $id . '", "alliances")');
            if ($d13->dbAffectedRows() == -1) $ok = 0;
            $d13->dbQuery('delete from alliances where id="' . $id . '"');
            if ($d13->dbAffectedRows() == -1) $ok = 0;
            if ($ok) $status = 'done';
            else $status = 'error';
        } else $status = 'noAlliance';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function getMembers()
    {
        global $d13;
        $result = $d13->dbQuery('select * from users where alliance="' . $this->data['id'] . '"');
        $this->members = array();
        for ($i = 0; $row = $d13->dbFetch($result); $i++) $this->members[$i] = $row;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public static function getInvitations($column, $id)
    {
        global $d13;
        $result = $d13->dbQuery('select * from invitations where ' . $column . '="' . $id . '"');
        $invitations = array();
        for ($i = 0; $row = $d13->dbFetch($result); $i++) $invitations[$i] = $row;
        return $invitations;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function addInvitation($userId)
    {
        global $d13;
        $user = new d13_user();
        if ($user->get('id', $userId) == 'done') {
            $result = $d13->dbQuery('select count(*) as count from invitations where alliance="' . $this->data['id'] . '" and user="' . $userId . '"');
            $row = $d13->dbFetch($result);
            if (!$row['count']) {
                $ok = 1;
                $d13->dbQuery('insert into invitations (alliance, user) values ("' . $this->data['id'] . '", "' . $user->data['id'] . '")');
                if ($d13->dbAffectedRows() == -1) $ok = 0;
                if ($ok) $status = 'done';
                else $status = 'error';
            } else $status = 'invitationSet';
        } else $status = 'noUser';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public static function removeInvitation($allianceId, $userId)
    {
        global $d13;
        $alliance = new d13_alliance();
        if ($alliance->get('id', $allianceId) == 'done') {
            $user = new d13_user();
            if ($user->get('id', $userId) == 'done') {
                $result = $d13->dbQuery('select count(*) as count from invitations where alliance="' . $allianceId . '" and user="' . $userId . '"');
                $row = $d13->dbFetch($result);
                if ($row['count']) {
                    $d13->dbQuery('delete from invitations where alliance="' . $allianceId . '" and user="' . $userId . '"');
                    if ($d13->dbAffectedRows() > -1) $status = 'done';
                    else $status = 'error';
                } else $status = 'noEntry';
            } else $status = 'noUser';
        } else $status = 'noAlliance';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public static function acceptInvitation($allianceId, $userId)
    {
        global $d13;
        $alliance = new d13_alliance();
        if ($alliance->get('id', $allianceId) == 'done') {
            $user = new d13_user();
            if ($user->get('id', $userId) == 'done')
                if (!$user->data['alliance']) {
                    $result = $d13->dbQuery('select count(*) as count from invitations where alliance="' . $allianceId . '" and user="' . $userId . '"');
                    $row = $d13->dbFetch($result);
                    if ($row['count']) {
                        $ok = 1;
                        $d13->dbQuery('update users set alliance="' . $allianceId . '" where id="' . $userId . '"');
                        if ($d13->dbAffectedRows() == -1) $ok = 0;
                        $d13->dbQuery('delete from invitations where alliance="' . $allianceId . '" and user="' . $userId . '"');
                        if ($d13->dbAffectedRows() == -1) $ok = 0;
                        if ($ok) $status = 'done';
                        else $status = 'error';
                    } else $status = 'noEntry';
                } else $status = 'allianceSet';
            else $status = 'noUser';
        } else $status = 'noAlliance';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function removeMember($userId)
    {
        global $d13;
        $user = new d13_user();
        if ($user->get('id', $userId) == 'done') {
            $d13->dbQuery('update users set alliance=0 where id="' . $userId . '"');
            if ($d13->dbAffectedRows() > -1) $status = 'done';
            else $status = 'error';
        } else $status = 'noUser';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function getWars()
    {
        global $d13;
        $result = $d13->dbQuery('select * from wars where sender="' . $this->data['id'] . '" or recipient="' . $this->data['id'] . '"');
        $this->wars = array();
        for ($i = 0; $row = $d13->dbFetch($result); $i++) $this->wars[$i] = $row;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function getWar($allianceId)
    {
        global $d13;
        $result = $d13->dbQuery('select * from wars where type=1 and (sender="' . $this->data['id'] . '" and recipient="' . $allianceId . '") or (sender="' . $allianceId . '" and recipient="' . $this->data['id'] . '")');
        $row = $d13->dbFetch($result);
        return $row;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function addWar($allianceId)
    {
        global $d13;
        $alliance = new d13_alliance();
        if ($alliance->get('id', $allianceId) == 'done') {
            $result = $d13->dbQuery('select count(*) as count from wars where type=1 and (sender="' . $this->data['id'] . '" or recipient="' . $this->data['id'] . '")');
            $row = $d13->dbFetch($result);
            if (!$row['count']) {
                $d13->dbQuery('insert into wars (type, sender, recipient) values ("1", "' . $this->data['id'] . '", "' . $alliance->data['id'] . '")');
                if ($d13->dbAffectedRows() > -1) $status = 'done';
                else $status = 'error';
            } else $status = 'warSet';
        } else $status = 'noAlliance';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function proposePeace($allianceId)
    {
        global $d13;
        $alliance = new d13_alliance();
        if ($alliance->get('id', $allianceId) == 'done') {
            $result = $d13->dbQuery('select count(*) as count from wars where type=1 and ((sender="' . $this->data['id'] . '" and recipient="' . $alliance->data['id'] . '") or (sender="' . $alliance->data['id'] . '" and recipient="' . $this->data['id'] . '"))');
            $row = $d13->dbFetch($result);
            if ($row['count']) {
                $result = $d13->dbQuery('select count(*) as count from wars where type=0 and ((sender="' . $this->data['id'] . '" and recipient="' . $alliance->data['id'] . '") or (sender="' . $alliance->data['id'] . '" and recipient="' . $this->data['id'] . '"))');
                $row = $d13->dbFetch($result);
                if (!$row['count']) {
                    $d13->dbQuery('insert into wars (type, sender, recipient) values ("0", "' . $this->data['id'] . '", "' . $alliance->data['id'] . '")');
                    if ($d13->dbAffectedRows() > -1) $status = 'done';
                    else $status = 'error';
                } else $status = 'peaceSet';
            } else $status = 'noWar';
        } else $status = 'noAlliance';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function removePeace($allianceId)
    {
        global $d13;
        $alliance = new d13_alliance();
        if ($alliance->get('id', $allianceId) == 'done') {
            $result = $d13->dbQuery('select count(*) as count from wars where type=0 and ((sender="' . $this->data['id'] . '" and recipient="' . $alliance->data['id'] . '") or (sender="' . $alliance->data['id'] . '" and recipient="' . $this->data['id'] . '"))');
            $row = $d13->dbFetch($result);
            if ($row['count']) {
                $d13->dbQuery('delete from wars where type=0 and ((sender="' . $this->data['id'] . '" and recipient="' . $alliance->data['id'] . '") or (sender="' . $alliance->data['id'] . '" and recipient="' . $this->data['id'] . '"))');
                if ($d13->dbAffectedRows() > -1) $status = 'done';
                else $status = 'error';
            } else $status = 'noPeace';
        } else $status = 'noAlliance';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function acceptPeace($allianceId)
    {
        global $d13;
        $alliance = new d13_alliance();
        if ($alliance->get('id', $allianceId) == 'done') {
            $result = $d13->dbQuery('select count(*) as count from wars where type=0 and sender="' . $alliance->data['id'] . '" and recipient="' . $this->data['id'] . '"');
            $row = $d13->dbFetch($result);
            if ($row['count']) {
                $ok = 1;
                $d13->dbQuery('delete from wars where type=1 and ((sender="' . $this->data['id'] . '" and recipient="' . $alliance->data['id'] . '") or (sender="' . $alliance->data['id'] . '" and recipient="' . $this->data['id'] . '"))');
                if ($d13->dbAffectedRows() == -1) $ok = 0;
                $d13->dbQuery('delete from wars where type=0 and sender="' . $alliance->data['id'] . '" and recipient="' . $this->data['id'] . '"');
                if ($d13->dbAffectedRows() == -1) $ok = 0;
                if ($ok) $status = 'done';
                else $status = 'error';
            } else $status = 'noPeace';
        } else $status = 'noAlliance';
        return $status;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @ 
	// ----------------------------------------------------------------------------------------
    public function getAll()
    {
        $this->getMembers();
        $this->invitations = d13_alliance::getInvitations('alliance', $this->data['id']);
        $this->getWars();
    }
}

// =====================================================================================EOF