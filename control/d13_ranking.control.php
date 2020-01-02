<?php

// ========================================================================================
//
// EMPTY.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_rankingController extends d13_controller
{
	
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct()
	{
		
		$tvars = array();
		$tvars = $this->doControl();
		$this->getTemplate($tvars);
		
	}

	// ----------------------------------------------------------------------------------------
	// doControl
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doControl()
	{
		
		global $d13;
		
		$tvars = array();

		$message = NULL;

		$tvars['tvar_userRankings'] = '';

		if (isset($_SESSION[CONST_PREFIX . 'User']['id'])) {


			$limit = 8;
			if (isset($_GET['page'])) {
				$offset = $limit * $_GET['page'];
			} else {
				$offset = 0;
			}
	
			$users = array();
	
			$result = $d13->dbQuery('select count(*) as count from users');
			$row = $d13->dbFetch($result);
			$count = $row['count'];
	
	
			$result = $d13->dbQuery('select * from users order by trophies desc, level desc limit ' . $limit . ' offset ' . $offset);
			for ($i = 0; $row = $d13->dbFetch($result); $i++) {
					$row['league'] = d13_misc::getLeague($row['level'], $row['trophies']);
					$users[] = new d13_user($row['id']);
			}
	
			$pageCount = ceil($count / $limit);
		
			foreach ($users as $user) {
				
				$vars = array();
				$uvars = array();
				$uvars = $user->getTemplateVariables();
				
				$vars['tvar_listLink']		= '?p=status&userId='.$uvars['tvar_userID'];
				$vars['tvar_listAmount'] 	= $uvars['tvar_userTrophies'];
				$vars['tvar_listLabel'] 	= $uvars['tvar_userName'];
				
				$vars['tvar_listAvatar'] 	= $uvars['tvar_userImage'];
				$vars['tvar_listLeague']	= $uvars['tvar_userImageLeague'];
				$vars['tvar_listName'] 		= $uvars['tvar_userLeague'];
				
				
				
				
				$tvars['tvar_userRankings'] .= $d13->templateSubpage("sub.module.leaguecontent", $vars);
			}
	
			// - - - Build Pagination
			$tvars['tvar_controls'] = '';
	
			if ($pageCount > 1) {
				$previous = '';
				$next = '';
				if (isset($_GET['page'])) {
					if ($_GET['page']) {
						$previous = '<a class="external" href="?p=ranking&action=list&page=' . ($_GET['page'] - 1) . '">' . $d13->getLangUI("previous") . '</a>';
					}
				} else if (!isset($_GET['page'])) {
					if ($pageCount) {
						$next = '<a class="external" href="?p=ranking&action=list&page=1">' . $d13->getLangUI("next") . '</a>';
					}
				}

				if (isset($_GET['page']) && $pageCount - $_GET['page'] - 1) {
					$next = '<a class="external" href="?p=ranking&action=list&page=' . ($_GET['page'] + 1) . '">' . $d13->getLangUI("next") . '</a>';
				}

				$tvars['tvar_controls'].= $d13->getLangUI("page") . $previous . ' <select class="dropdown" id="page" onChange="window.location.href=\'index.php?p=ranking&action=list&page=\'+this.value">';
				for ($i = 0; $i < $pageCount; $i++) {
					$tvars['tvar_controls'].= '<option value="' . $i . '">' . $i . '</option>';
				}

				$tvars['tvar_controls'].= '</select> ' . $next;
				if (isset($_GET['page'])) {
					$tvars['tvar_controls'].= '<script type="text/javascript">document.getElementById("page").selectedIndex=' . $_GET['page'] . '</script>';
				}
			}


		} else {
			$message = $d13->getLangUI("accessDenied");
		}

	
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate($tvars)
	{
	
		global $d13;
		

		$d13->templateRender("ranking", $tvars);
		
	}

}

// =====================================================================================EOF

