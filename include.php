<?php

/**
 *	@module			Anytopics
 *	@version		see info.php of this module
 *	@authors		Chio Maisriml, Dietrich Roland Pehlke, erpe
 *	@copyright		2008-2012 Chio Maisriml
 *	@copyright		2012-2017 Dietrich Roland Pehlke, erpe
 *	@license		GNU General Public License
 *	@license terms	see info.php of this module
 *
 */

// include class.secure.php to protect this file and the whole CMS!
if (defined('LEPTON_PATH')) {	
	include(LEPTON_PATH.'/framework/class.secure.php'); 
} else {
	$root = "../";
	$level = 1;
	while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
		$root .= "../";
		$level += 1;
	}
	if (file_exists($root.'/framework/class.secure.php')) { 
		include($root.'/framework/class.secure.php'); 
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}
// end include class.secure.php

// function to display topics and comments on every/any page. (invoke function from template or code page)

if (!function_exists('display_topics')) {
	
	function display_topics($showwhat=3, $max_items = 6, $max_chars=600, $max_chars_per=120, $active_only=1,$readmore_text='More..', $latesttopicstitle='<h3>Latest Topics:</h3>', $latestcommentstitle='<h3>Latest Comments:</h3>', $sectiononly=0, $style=0) {
	
		// register outside object
		global $database;
		global $wb;
		global $topic_id;
		
		$this_id = 0;
		if (isset($topic_id)) {$this_id = $topic_id;}
		

		include (WB_PATH."/modules/topics/defaults/module_settings.default.php");
		include (WB_PATH."/modules/topics/module_settings.php");
		if (!$topics_virtual_directory)  { exit("This snippet needs the Topics-Module"); }
		
		if (!defined('TOPICS_VIRTUAL_DIRECTORY')) {define("TOPICS_VIRTUAL_DIRECTORY", $topics_virtual_directory);}
		
		// convert all numeric inputs to integer variables
		$showwhat = (int) $showwhat;		
		$max_items = (int) $max_items;
		$max_chars = (int) $max_chars;
		$max_chars_per = (int) $max_chars_per;
		$active_only = (int) $active_only;
		
		$makeeditlink = false;
		if ($wb->is_authenticated()) { 
			if ($wb->get_group_id() == 1) $makeeditlink = true;
		}
		
		$t = time();
		
		//--------------------------------------------------------------------------------------------------------------------
		$query_section = "";
		//Checking Comments
		
		if ($showwhat > 0) {
			$t = mktime ( (int) gmdate("H"), (int) gmdate("i"), (int) gmdate("s"), (int) gmdate("n"), (int) gmdate("j"), (int) gmdate("Y")) + DEFAULT_TIMEZONE;

			$minimum_commentedtime = $t - 120; //Verzögerung in Sekunden
			$dmax_items = 2 * $max_items;
			$query = "SELECT * FROM ".TABLE_PREFIX."mod_topics_comments WHERE commented_when < '".$minimum_commentedtime."' AND topic_id <> '$this_id' ORDER BY commented_when DESC LIMIT 0, $dmax_items";
			// make database query and obtain number of comments found
			$result = $database->query($query);
			$number_comments = $result->numRows();      

		
			$commentoutput = '';
			$now_chars = 0;
			$counter = 0;
			$commentmailArr = array();
			if ($number_comments > 0) {
				while($data = $result->fetchRow()) {
			 		if ($active_only==1) { if ($data['active'] < 1) {continue;}	}
					 //if ($data['topic_id'] == $this_id ) {continue;} // Not show comments to THIS topic
					
					$the_comment = strip_tags($data['comment']);
					if ($the_comment=='') {continue;}								
					$name= strip_tags($data['name']);
					$the_id= $data['comment_id'];
					//$email = $data['email'];
					if ($counter > 1) {
						if (in_array($name, $commentmailArr)) { continue; } else {$commentmailArr[] = $name;}
					}
			 
			 		$query = "SELECT link,active,page_id,section_id,topic_id FROM " .TABLE_PREFIX ."mod_topics WHERE topic_id=". $data['topic_id'].";";
			 		$postresult = $database->query($query);
			 		$topicdata = $postresult->fetchRow();				
			 		//check if topic is active:
					if ($active_only==1) { if ($topicdata['active'] < 4) {continue;} }
							 
		
				 
					 //check the number of chars; this is NOT very exact:
					$is_chars = strlen($the_comment);
					if ($is_chars > $max_chars_per) {$is_chars = $max_chars_per + 1;}
					if (($now_chars + $is_chars) > $max_chars) {break;} //too much chars				
				
					$now_chars = $now_chars + $is_chars;				
					if ($is_chars >  $max_chars_per) {
						$pos = strpos($the_comment, " ", $max_chars_per);					
						if ($pos > 0) {$the_comment = substr($the_comment, 0,  $pos) . '...'; }				
					}
					//make output line
					$topic_link = WB_URL.TOPICS_VIRTUAL_DIRECTORY.$topicdata['link'].PAGE_EXTENSION;
					$the_line = "<div><h4>".$name."</h4>\n<p>".$the_comment."</p>\n";
					$the_line .= '<a href="'.$topic_link.'">'.$readmore_text.'</a>';				
				
					//Edit link if user is authenticated:
					$edit_link = '';
					if ($makeeditlink) { 
						$the_line .= '<span> <a target=_blank" class="fredit_mini" href="'.WB_URL.'/modules/topics/modify_comment.php?page_id='.$topicdata['page_id'].'&section_id='.$topicdata['section_id'].'&comment_id='.$data['comment_id'].'&fredit=1">Edit</a></span>';
					}
								
		 			$commentoutput .= $the_line."</div>\n";
					$counter ++;
					if ($counter > $max_items) break;
				
				} //end while
				if ($commentoutput != '') { $commentoutput = '<div class="mod_anytopics_comments">'."\n".$latestcommentstitle."\n".$commentoutput ."</div>\n";}
			} //end if ($number_comments) 
		} //end if ($showwhat > 0)
		//--------------------------------------------------------------------------------------------------------------------
		
		//--------------------------------------------------------------------------------------------------------------------
		//Checking Topics
		
		if ($showwhat == 0 OR $showwhat > 1) {
			
			$activequery = "active > '3'";
			if ($wb->is_authenticated()) { $activequery = "(active > '3' OR active = '1')"; }
			
			$query_extra = " AND (published_when = '0' OR published_when <= $t) AND (published_until = 0 OR published_until >= $t)";
			if ($sectiononly > 0) {$query_section = " AND section_id='$sectiononly' "; }		
			//$sort_topics_by =  ' active DESC, published_when DESC'; Bis Version 4.0
			$sort_topics_by =  ' published_when DESC';
			
			$limit_sql = " LIMIT 0, $max_items";
			$query = "SELECT * FROM ".TABLE_PREFIX."mod_topics WHERE ".$activequery." AND topic_id <> '".$this_id."'". $query_section . $query_extra." ORDER BY ".$sort_topics_by.$limit_sql;					
			$result = $database->query($query);
			$number_topics = $result->numRows();
		
			$topicsoutput = '';
			if ($showwhat < 3)  $now_chars = 0;
			if ($number_topics > 0) {
				while($topicdata = $result->fetchRow()) {				
					$title = strip_tags($topicdata['title']);									
			 		$the_short= strip_tags($topicdata['content_short']);
					if ($the_short=='') {continue;}								
					$active = strip_tags($topicdata['active']);
				 
					 //check the number of chars; this is NOT very exact:
					$is_chars = strlen($the_short);
					if ($is_chars > $max_chars_per) {$is_chars = $max_chars_per + 1;}
					if (($now_chars + $is_chars) > $max_chars) {break;} //too much chars				
				
					$now_chars = $now_chars + $is_chars;				
					if ($is_chars >  $max_chars_per) {
						$pos = strpos($the_short, " ", $max_chars_per);					
						if ($pos > 0) {$the_short = substr($the_short, 0,  $pos) . '...'; }				
					}
					//make output line
					$topic_link = WB_URL.TOPICS_VIRTUAL_DIRECTORY.$topicdata['link'].PAGE_EXTENSION;
					
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					$topic_thumb = '';					
					if ($style > 0) {$topic_thumb = '<a href="'.$topic_link.'" class="piclink"><img src="'.$picture_dir.'/thumbs/'.$topicdata['picture'].'" alt="'.$title.'"/></a>';}
					
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//Different styles here:
					// style 0 and 1:					
					$the_line = '<div class="mod_anytopics_active'.$active.'">'.$topic_thumb.'<h4>'.$title."</h4>\n<p>".$the_short."</p>\n";
					$the_line .= '<a href="'.$topic_link.'">'.$readmore_text.'</a>';
					
									
				
					//Edit link if user is authenticated:
					$edit_link = '';
					if ($makeeditlink) { 
						$the_line .= '<span> <a href="'.WB_URL.'/modules/topics/modify_topic.php?page_id='.$topicdata['page_id'].'&section_id='.$topicdata['section_id'].'&topic_id='.$topicdata['topic_id'].'">Edit</a></span>';
					}								
		 			$topicsoutput .= $the_line."</div>\n";
			 
		
		
		
		
				} //end while
				if ($topicsoutput != '') {$topicsoutput = '<div class="mod_anytopics_topic">'."\n".$latesttopicstitle."\n".$topicsoutput ."</div>\n";}
			} //end if ($number_comments) 
		} //end Checking Topics
		
		//Finally: Output:
		if ($showwhat == 0) {echo $topicsoutput;}
		if ($showwhat == 1) {echo $commentoutput;}
		if ($showwhat == 2) {echo $topicsoutput.$commentoutput; }
		if ($showwhat == 3) {echo $commentoutput.$topicsoutput;}
		//echo $query_section;
		
	}
}

?>