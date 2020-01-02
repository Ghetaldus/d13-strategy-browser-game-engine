<?php

// ========================================================================================
//
// MISC.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// Includes some static functions for common stuff that is required everywhere.
//
// ========================================================================================

class d13_misc

{

	//----------------------------------------------------------------------------------------
	// getMobileClient
	// 
	//----------------------------------------------------------------------------------------
	public
	
	function getMobileClient()
	{
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//----------------------------------------------------------------------------------------
	// toolTip
	// 
	//----------------------------------------------------------------------------------------
	public static
	
	function toolTip($data, $cache=0) {
		
		global $d13;
		
		$id=md5($data);
				
		$tvars = array();
		$tvars['tvar_tipContent'] = $data;
		$tvars['tvar_tipID'] = $id;

		$d13->templateInject($d13->templateSubpage("sub.tooltip", $tvars), $cache);
		
		return 'showTip tip_'.$id;

	}

	//----------------------------------------------------------------------------------------
	// getLeague
	// level ca. 1-999 / trophies ca. 1-9999
	//----------------------------------------------------------------------------------------
	public static
	
	function getLeague($level, $trophies) {
		
		global $d13;
		
		$my_league = 0;
		$my_value = ceil(($level*100)+$trophies)/2;
		
		foreach ($d13->getLeague() as $league) {
		
			if ($league['min'] <= $my_value) {
				$my_league = $league['id'];
			} else {
				break;
			}
		
		}

		return $my_league;
		
	}
	
	//----------------------------------------------------------------------------------------
	// gq_percent_difference
	//----------------------------------------------------------------------------------------
	public static
	
	function percent_difference($value1, $value2) {
		
		$percentage = 0;
		
		$value1++;
		$value2++;
		
		$percentage = floor( (max($value1, $value2) / min($value1, $value2)) * 100);
				
		return $percentage;
		
	}
	
	//----------------------------------------------------------------------------------------
	// gq_nextlevelexp
	// @ Returns the experience points required to reach the next level, depending on the current
	//   level as well as the current grade. This formula can scale into infinite.
	// 3.0
	//----------------------------------------------------------------------------------------
	public static
	
	function nextlevelexp($level, $grade=1) {
	
		global $d13;
		$factor = $d13->getGeneral('factors', 'experience');
		$modifier = ceil($grade/4);													// !important - modifier is quarter of the grade
		$level++;																	// because we want next level
		$required_exp = ($level * ($grade * $factor)) * ($level * $modifier) * $factor;
		return ceil($required_exp);
		
	}

	// ----------------------------------------------------------------------------------------
	// record_sort
	//
	// ----------------------------------------------------------------------------------------

	public static
	
	function record_sort($records, $field, $reverse = false)
	{
		$hash = array();
		foreach($records as $key => $record) {
			$hash[$record[$field] . $key] = $record;
		}

		($reverse) ? krsort($hash) : ksort($hash);
		$records = array();
		foreach($hash as $record) {
			$records[] = $record;
		}

		return $records;
	}

	// ----------------------------------------------------------------------------------------
	// percentage
	// ----------------------------------------------------------------------------------------
	public static

	function percentage($fraction, $total)
	{
		$percentage = 0;
		if ($fraction > 0 && $total > 0) {
			$percentage = ($total/100)*$fraction;
		}

		return $percentage;
	}

	// ----------------------------------------------------------------------------------------
	// upgraded_value
	// ----------------------------------------------------------------------------------------
	public static
	
	function upgraded_value($fraction, $total)
	{
		
		$value = 1;
		
		if ($fraction > 0 && $total > 0) {
			$value = floor(d13_misc::percentage($fraction, $total));
			if ($value > $total) {
				$value = $total;
			} else if ($value < 1) {
				$value = 1;
			}
		}
		
		return $value;
		
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public static

	function clean($data, $type = 0)
	{
		global $d13;
		if (is_array($data))
		foreach($data as $key => $value) {
			if (($type) && ($type == 'numeric'))
			if (!is_numeric($value)) $value = 0;
			else $value = floor(abs($value));
			$value = $d13->dbRealEscapeString($value);
			$data[$key] = htmlspecialchars($value);
		}
		else {
			if (($type) && ($type == 'numeric'))
			if (!is_numeric($data)) $data = 0;
			else $data = floor(abs($data));
			$data = $d13->dbRealEscapeString($data);
			$data = htmlspecialchars($data);
		}

		return $data;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public static

	function newId($type)
	{
		global $d13;
		$result = $d13->dbQuery('select min(id) as id from free_ids where type="' . $type . '"');
		$id = $d13->dbFetch($result);
		if (isset($id['id'])) {
			$d13->dbQuery('delete from free_ids where id="' . $id['id'] . '" and type="' . $type . '"');
			return $id['id'];
		}
		else {
			$result = $d13->dbQuery('select max(id) as id from ' . $type);
			$id = $d13->dbFetch($result);
			if (isset($id['id'])) return $id['id'] + 1;
			else return 1;
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public static

	function sToHMS($seconds, $asString=false)
	{
		$h = sprintf('%02d', floor($seconds / 3600));
		$m = sprintf('%02d', floor($seconds % 3600 / 60));
		$s = sprintf('%02d', floor($seconds % 3600 % 60));
		$t = array($h,$m,$s);
		
		if ($asString) {
			return implode(":", $t);
		} else {
			return $t;
		}
		
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public static

	function microTime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
}

// =====================================================================================EOF