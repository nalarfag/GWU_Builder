<?php

/**
 * Description of ExcludePublishedQuestionnaire
 * hide all the published questionnaire from the main page
 *
 * @author Nada Alarfag
 */

add_filter('wp_list_pages_excludes',  'questionnaire_pages_excludes');
function questionnaire_pages_excludes($exclude_array) {
	global $wpdb;

	$sql = "SELECT PostId FROM gwu_questionnaire
	    WHERE PublishFlag = true";
	$id_array = $wpdb->get_col($sql);
	 $page = get_page_by_title('Published Questionnairs');
	array_push($id_array, $page->ID );
	$exclude_array=array_merge($id_array, $exclude_array);
	return $exclude_array;
}

?>