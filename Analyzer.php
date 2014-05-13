<?php
/**************Global vars******************/
global $wp_session;
global $saved_user_analyzer;
global $saved_questionnaire;
global $saved_question;
global $saved_location;
global $saved_responder;
global $saved_start_date_id;
global $saved_end_date_id;


/**************ShortCode******************/
add_shortcode( 'QP_JS', 'getJavaScript' );
add_shortcode( "QP_CSS", "getCss" );
add_shortcode( "QP_AdminForm", "getAdminForm" );
add_shortcode( "QP_QuestionnaireList", "getQuestionnaireList" );
add_shortcode( "QP_GeoChart", "getGeoChart" );


/**************PHP Code******************/
class Analyzer{
	
	public $page_title;
    public $page_name;
    public $page_id;
	
	public function __construct()
    {
      $this->page_title = 'QuestionPeach Analyzer';
      $this->page_name  = 'uQuestionPeach Analyzer';
      $this->page_id    = '0';

      register_activation_hook(__FILE__, array($this, 'activate'));
      register_deactivation_hook(__FILE__, array($this, 'deactivate'));
      register_uninstall_hook(__FILE__, array($this, 'uninstall'));
	  add_action('admin_menu', array($this, 'GWU_add_Questionnaire_menu_links'));
    }
	/*install function, which create tables and
	create analyzer user interface (GUI)*/
	
	public function GWU_add_Questionnaire_menu_links() {
		
              add_menu_page('Analyze Survey', 'Analyze Survey', 'edit_pages', 'questionpeach-analyzer', array($this,'goToAnalyzer'), plugins_url('images/GWUQuestionnaire.png', __FILE__));
	}
	
	public function goToAnalyzer(){
	$resultMsg = '';
		$resultMsg = $resultMsg. getCss();
		$resultMsg = $resultMsg.getJavaScript();
		$resultMsg = $resultMsg.'<div></div><form id="theForm">
		<table>
		<tbody>
			<tr><td colspan="4">'.getQuestionnaireList().'</td>
			</tr>	
		<tbody>
		</table>
		<br>
		<br>	
	</form>';
		
		echo $resultMsg;
	//echo 'Hello Alemberhan';
	}
	
	function activate(){
			
		//alem's create page code
			
	  global $wpdb;      

     
	}
	
	/*remove function, which drop tables and
	remove analyzer user interface (GUI)*/
	
	 public function deactivate()
    {
      $this->deletePage();
      $this->deleteOptions();
    }

    public function uninstall()
    {
      $this->deletePage(true);
      $this->deleteOptions();
    }
	
	public function query_parser($q)
    {
      if(isset($q->query_vars['page_id']) AND (intval($q->query_vars['page_id']) == $this->page_id ))
      {
        $q->set($this->_name.'_page_is_called', true);
      }
      elseif(isset($q->query_vars['pagename']) AND (($q->query_vars['pagename'] == $this->page_name) OR ($_pos_found = strpos($q->query_vars['pagename'],$this->page_name.'/') === 0)))
      {
        $q->set($this->_name.'_page_is_called', true);
      }
      else
      {
        $q->set($this->_name.'_page_is_called', false);
      }
    }

    
	
	private function deletePage($hard = false)
    {
      global $wpdb;

      $id = get_option($this->_name.'_page_id');
      if($id && $hard == true)
        wp_delete_post($id, true);
      elseif($id && $hard == false)
        wp_delete_post($id);
    }

    private function deleteOptions()
    {
      delete_option($this->_name.'_page_title');
      delete_option($this->_name.'_page_name');
      delete_option($this->_name.'_page_id');
    }
	
	//createUI to create a admin panel for analyzer
	function creatUI(){
		$resultMsg = '';
		$resultMsg = $resultMsg. do_shortcode('[QP_CSS]');
		$resultMsg = $resultMsg.'[QP_JS]';
		$resultMsg = $resultMsg.'[QP_GeoChart]';
		$resultMsg = $resultMsg.'[QP_AdminForm]';
		
		return $resultMsg;
	}
	
	
	
	function getTopPanel(){
		// get top panel
		
		$msg = '<table class="table">
		<tbody>';
		
		return $msg;
	}
	
	function export_data(){
		// to export data form the the builder table and copy to star schema
		
		if(true){
			$wp_session['message'] = array("You have sucessfully Export the database.");
			
		} else {
			$wp_session['message'] = array("ERROR".mysql_error());
			
		}
	}
	
}

//$admin = new Analyzer();
global $msg;
global $analyzer_tbls;

////////////////////////// Plugin shortcode ////////////////////////////////
add_shortcode('analyzer_create_shtz', function(){
		return analyzer_create_tbl();
});

add_shortcode('analyzer_drop_shtz', function(){
		return analyzer_drop_tblXXX();
});

add_shortcode('analyzer_migrate_builder_data_shtz', function(){
		return analyzer_migrate_builder_data();
});

add_shortcode('analyzer_cron_job_actv_shtz', function(){
		analyzer_cron_job_activation();
		return true;
});

add_shortcode('analyzer_cron_job_deactv_shtz', function(){
		analyzer_cron_job_deactivation();
		return true;
});



add_shortcode('rpt', function(){
		
		return analyzer_show_tbls();
});

/////////////////////////////analyzer_create_tbl///////////////////
function analyzer_create_tbl()
{
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	$analyzer_tbls= array('wp_respondee_dim', 'wp_time_dim', 'wp_question_dim', 'wp_location_dim', 'wp_questionnaire_dim','wp_question_response');
	
	$msg.="<h4> 1. Verifying if table the following tables exist:</h4><br> ".loop_arr($analyzer_tbls);
	
	if( $wpdb->get_var( "SHOW TABLES LIKE '".$analyzer_tbls[0]."'" ) != $analyzer_tbls[0] )
	{
		$msg.="<h4>2. Now creating tables </h4><i>".loop_arr($analyzer_tbls)."</i>";
		
		$sql = "
		
		CREATE TABLE wp_filters (
		save_datetime DATETIME NOT NULL,
		user_id INTEGER UNSIGNED NULL,
		questionnaire_id INTEGER(20) UNSIGNED NULL,
		question_id INTEGER UNSIGNED NULL,
		location_id INTEGER UNSIGNED NULL,
		respondee_id INTEGER UNSIGNED NULL,
		start_date DATE NULL,
		end_date DATE NULL,
		name VARCHAR(50) NULL,
		PRIMARY KEY(save_datetime)
		);
		
		CREATE TABLE wp_respondee_dim (
		respondee_id INTEGER UNSIGNED NOT NULL,
		survey_completed BOOL NULL,
		survey_taken_date DATE NULL,
		username VARCHAR(100) NULL,
		ip VARCHAR(20) NULL,
		duration TIME NULL,
		PRIMARY KEY(respondee_id)
		);
		
		CREATE TABLE wp_time_dim (
		time_id BIGINT NOT NULL,
		date DATE NOT NULL,
		day_2 CHAR(10) NULL,
		day_of_week INT NULL,
		day_of_month INT NULL,
		day_of_year INT NULL,
		weekend CHAR(10) NOT NULL DEFAULT 'Weekday',
		week_of_year CHAR(2) NULL,
		month_3 CHAR(10) NULL,
		month_of_year CHAR(2) NULL,
		quarter_of_year INT NULL,
		year_3 INT NULL,
		PRIMARY KEY(time_id),
		UNIQUE INDEX time_dim_uniq(date)
		);
		
		DELETE FROM wp_time_dim;
		
		DROP TABLE IF EXISTS numbers_small;
		CREATE TABLE numbers_small (number INT);
		
		INSERT INTO numbers_small VALUES (0),(1),(2),(3),(4),(5),(6),(7),(8),(9);
		
		
		DROP TABLE IF EXISTS numbers;
		
		CREATE TABLE numbers (number BIGINT);
		INSERT INTO numbers
		SELECT thousands.number * 1000 + hundreds.number * 100 + tens.number * 10 + ones.number
		FROM numbers_small thousands, numbers_small hundreds, numbers_small tens, numbers_small ones
		LIMIT 1000000;
		
		
		
		INSERT INTO wp_time_dim (time_id, date)
		SELECT number, DATE_ADD( '2010-01-01', INTERVAL number DAY )
		FROM numbers
		WHERE DATE_ADD( '2010-01-01', INTERVAL number DAY ) BETWEEN '2010-01-01' AND '2015-12-31'
		ORDER BY number;
		
		
		UPDATE wp_time_dim SET
		day_2             = DATE_FORMAT( date, \"%W\" ),
		day_of_week     = DAYOFWEEK(date),
		day_of_month    = DATE_FORMAT( date, \"%d\" ),
		day_of_year     = DATE_FORMAT( date, \"%j\" ),
		weekend         = IF( DATE_FORMAT( date, \"%W\" ) IN ('Saturday','Sunday'), 'Weekend', 'Weekday'),
		week_of_year    = DATE_FORMAT( date, \"%V\" ),
		month_3           = DATE_FORMAT( date, \"%M\"),
		month_of_year   = DATE_FORMAT( date, \"%m\"),
		quarter_of_year = QUARTER(date),
		year_3            = DATE_FORMAT( date, \"%Y\" );
		
		
		
		CREATE TABLE wp_question_dim (
		question_id INTEGER UNSIGNED NOT NULL,
		questionnaire_id INTEGER(20) UNSIGNED NOT NULL,
		question_text TEXT NULL,
		ans_type VARCHAR(100) NULL,
		PRIMARY KEY(question_id, questionnaire_id)
		);
		
		CREATE TABLE wp_location_dim (
		location_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
		city VARCHAR(50) NULL,
		country VARCHAR(50) NULL,
		PRIMARY KEY(location_id)
		);

		CREATE TABLE wp_cron_job (
  		cronjob_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  		cronjob_name VARCHAR(50) NULL,
  		cronjob_interval INTEGER NULL,
  		PRIMARY KEY(cronjob_id)
		);
		
		CREATE TABLE wp_questionnaire_dim (
		questionnaire_id INTEGER(20) UNSIGNED NOT NULL,
		topic VARCHAR(100) NULL,
		date_created DATE NULL,
		allow_anonymous BOOL NULL,
		allow_multiple BOOL NULL,
		title VARCHAR(100) NULL,
		creator_name VARCHAR(100) NULL,
		OwnerId INT(11) NULL DEFAULT NULL,
		EditorId INT(11) NULL DEFAULT NULL,
		PRIMARY KEY(questionnaire_id)
		);
		
		CREATE TABLE wp_question_response (
		response_id int(10) unsigned NOT NULL,
		question_dim_questionnaire_id int(20) unsigned NOT NULL,
		question_dim_question_id int(10) unsigned NOT NULL,
		time_dim_time_id bigint(20) DEFAULT NULL,
		respondee_dim_respondee_id int(10) unsigned NOT NULL,
		questionnaire_dim_questionnaire_id int(20) unsigned NOT NULL,
		location_dim_location_id int(10) unsigned DEFAULT NULL,
		response_content text,
		response_type varchar(100) DEFAULT NULL,
		PRIMARY KEY (response_id)
		);
		$charset_collate;";
		
		dbDelta( $sql );
		
	}
}


/////////////////////////////analyzer_drop_tbl///////////////////
function analyzer_drop_tbl()
{
	$analyzer_tbls= array('wp_respondee_dim', 'wp_time_dim', 'wp_question_dim', 'wp_location_dim', 'wp_questionnaire_dim','wp_question_response');
	
	$msg=loop_arr($analyzer_tbls);
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	
	
	if($wpdb->get_var( "SHOW TABLES LIKE '".$analyzer_tbls[0]."'" ) == $analyzer_tbls[0])
	{
		$wpdb->query( 'DROP TABLE IF EXISTS wp_respondee_dim,
			wp_time_dim,
			wp_question_dim,
			wp_location_dim,
			wp_cron_job,
			wp_questionnaire_dim,
			numbers,
			numbers_small,
			wp_filters,
			wp_question_response');
		
	}
}

/////////////////////////////analyzer_migration /////////////////////////////////////////////////
function analyzer_migration()
{
	global $wpdb;
	
	$sql_0="INSERT INTO wp_location_dim (city, country)
	SELECT distinct City, Country
	FROM gwu_session
	//WHERE city    NOT IN(select distinct city    from wp_location_dim)
	//AND   country NOT IN(select distinct country from wp_location_dim)";
	
	$sql_1="INSERT INTO wp_question_dim (question_id, questionnaire_id, question_text, ans_type)
	SELECT questsequence, QuestionnaireID, gwu_question.text, AnsType
	FROM gwu_question";
	
	
	$sql_2="INSERT INTO wp_respondee_dim(respondee_id, survey_completed, survey_taken_date, username, ip, duration)
	SELECT SessionID, SurveyCompleted, SurveyTakenDate, Username, IP, Duration
	FROM gwu_session";
	
	$sql_3="INSERT INTO wp_questionnaire_dim (questionnaire_id, topic, date_created, allow_anonymous, allow_multiple, title, creator_name, OwnerId, EditorId)
	SELECT QuestionnaireID, Topic, DateCreated, AllowAnnonymous, AllowMultiple, Title, CreatorName, OwnerId, EditorId
	FROM gwu_questionnaire";
	
	
	$sql_4="INSERT INTO wp_question_response (response_id, response_content, response_type, questionnaire_dim_questionnaire_id, question_dim_questionnaire_id, question_dim_question_id, respondee_dim_respondee_id)
	SELECT ResponseID, ResponseContent, ResponseType, QuestionnaireID, QuestionnaireID, QuestSequence, SessionID
	FROM gwu_response";
	
	
	$sql_5="UPDATE wp_question_response SET
	time_dim_time_id =
	(SELECT time_id
	FROM wp_time_dim, gwu_session
	WHERE wp_question_response.respondee_dim_respondee_id = gwu_session.SessionID
	AND wp_time_dim.date = gwu_session.SurveyTakenDate)";
	
	
	$sql_6="UPDATE wp_question_response SET
	location_dim_location_id =
	(SELECT location_id
	FROM wp_location_dim, gwu_session
	WHERE wp_question_response.respondee_dim_respondee_id = gwu_session.SessionID
	AND wp_location_dim.country = gwu_session.Country
	AND wp_location_dim.city = gwu_session.city)";
	
	
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	
	$wpdb->query($wpdb->prepare($sql_0));
	$wpdb->query($wpdb->prepare($sql_1));
	$wpdb->query($wpdb->prepare($sql_2));
	$wpdb->query($wpdb->prepare($sql_3));
	$wpdb->query($wpdb->prepare($sql_4));
	$wpdb->query($wpdb->prepare($sql_5));
	$wpdb->query($wpdb->prepare($sql_6));
	
}
/////////////////////////////analyzer_migration_cron /////////////////////////////////////////////////
function analyzer_migration_cron()
{
global $wpdb;

$sql_0="INSERT INTO wp_respondee_dim(respondee_id, survey_completed, survey_taken_date, username, ip, duration)
	SELECT SessionID, SurveyCompleted, SurveyTakenDate, Username, IP, Duration
	FROM gwu_session
	where SessionID in (
	select SessionID
	from   gwu_session
	where SessionID not in(select respondee_id from wp_respondee_dim)); ";

$sql_1="INSERT INTO wp_question_dim (question_id, questionnaire_id, question_text, ans_type)
	SELECT GQ.questsequence, GQ.QuestionnaireID, GQ.text, GQ.AnsType
	FROM gwu_question GQ
	left join wp_question_dim WQ
	on GQ.QuestionnaireID = WQ.questionnaire_id and GQ.QuestSequence = WQ.question_id
	WHERE WQ.question_id is null
	AND WQ.questionnaire_id is null; ";


$sql_2="INSERT INTO wp_questionnaire_dim (questionnaire_id, topic, date_created, allow_anonymous, allow_multiple, title, creator_name, OwnerId, EditorId)
	SELECT QuestionnaireID, Topic, DateCreated, AllowAnnonymous, AllowMultiple, Title, CreatorName, OwnerId, EditorId
	FROM gwu_questionnaire
	WHERE PublishFlag = '1'
    AND QuestionnaireID in (
	SELECT QuestionnaireID
	FROM gwu_questionnaire
	WHERE QuestionnaireID not in (SELECT questionnaire_id from wp_questionnaire_dim)); ";


$sql_3="INSERT INTO wp_question_response (response_id, response_content, response_type, questionnaire_dim_questionnaire_id, question_dim_questionnaire_id, question_dim_question_id, respondee_dim_respondee_id)
	SELECT ResponseID, ResponseContent, ResponseType, QuestionnaireID, QuestionnaireID, QuestSequence, gwu_response.SessionID
	FROM gwu_response, gwu_session
	WHERE gwu_response.SessionID = gwu_session.SessionID
	AND ResponseID in (
	SELECT ResponseID
	FROM gwu_response
	WHERE ResponseID not in (SELECT response_id from wp_question_response));";


$sql_4="UPDATE wp_question_response SET
	time_dim_time_id =
	(SELECT time_id
	FROM wp_time_dim, gwu_session
	WHERE wp_question_response.respondee_dim_respondee_id = gwu_session.SessionID
	AND wp_time_dim.date = gwu_session.SurveyTakenDate);";


$sql_5="UPDATE wp_question_response SET location_dim_location_id =
	(SELECT location_id
	FROM wp_location_dim, gwu_session
	WHERE wp_question_response.respondee_dim_respondee_id = gwu_session.SessionID
	AND wp_location_dim.country = gwu_session.Country
	AND wp_location_dim.city = gwu_session.city); ";

$sql_6="INSERT INTO wp_location_dim (city, country)
	SELECT distinct City, Country
	FROM gwu_session
	WHERE city    NOT IN(select distinct city    from wp_location_dim)
	AND   country NOT IN(select distinct country from wp_location_dim);";
	
require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

$wpdb->query($sql_0);
$wpdb->query($sql_1);
$wpdb->query($sql_2);
$wpdb->query($sql_3);
$wpdb->query($sql_4);
$wpdb->query($sql_5);
$wpdb->query($sql_6);

}

/////////////////////////////analyzer_deletion_cron /////////////////////////////////////////////////
function analyzer_deletion_cron()
{
global $wpdb;
/*$sql_0="DELETE FROM wp_location_dim;";
$sql_1="DELETE FROM gwu_questionnaire;";
$sql_2="DELETE FROM gwu_question;";
$sql_3="DELETE FROM gwu_answerChoice;";
$sql_4="DELETE FROM gwu_condition;";
$sql_5="DELETE FROM gwu_response;";
$sql_6="DELETE FROM gwu_session;";
$sql_7="DELETE FROM gwu_action;";
$sql_8="DELETE FROM gwu_flag;";*/
$sql_9="DELETE FROM wp_respondee_dim;";
$sql_10="DELETE FROM wp_question_dim;";
$sql_11="DELETE FROM wp_questionnaire_dim;";
$sql_12="DELETE FROM wp_location_dim;";
$sql_13="DELETE FROM wp_question_response;";
$sql_14="DELETE FROM wp_filters;";

require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

/*$wpdb->query($sql_0);
$wpdb->query($sql_1);
$wpdb->query($sql_2);
$wpdb->query($sql_3);
$wpdb->query($sql_4);
$wpdb->query($sql_5);
$wpdb->query($sql_6);
$wpdb->query($sql_7);
$wpdb->query($sql_8);*/
$wpdb->query($sql_9);
$wpdb->query($sql_10);
$wpdb->query($sql_11);
$wpdb->query($sql_12);
$wpdb->query($sql_13);
$wpdb->query($sql_14);
}

//////////////////////////////////////// analyzer_deletion_cron_builder////////////////////////////////////////////
function analyzer_deletion_cron_builder()
{
 $sql2 = "SELECT PostId FROM gwu_questionnaire WHERE PublishFlag = true";
 $id_array = $wpdb->get_col($sql2);  
 foreach($id_array as $id)
 {
  wp_delete_post($id);
 }   

 $wpdb->query("Truncate Table gwu_response"); 
 $wpdb->query("Truncate Table gwu_session"); 
 $wpdb->query("Truncate Table gwu_flag"); 
 $wpdb->query("Truncate Table gwu_answerChoice"); 
 $wpdb->query("Truncate Table gwu_action"); 
 $wpdb->query("Truncate Table gwu_condition"); 
 $wpdb->query("Truncate Table gwu_question"); 
 $wpdb->query("Truncate Table gwu_questionnaire"); 
 $wpdb->query("Truncate Table gwu_answerChoice"); 
 $wpdb->query("Truncate Table gwu_question"); 
 $wpdb->query("Truncate Table gwu_questionnaire");
}



///////////////////////////// analyzer_get_rec_count($qry) ////////////////////////////////////
function analyzer_get_rec_count($qry)
{
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	
	$cnt= $wpdb->get_var($qry);
	
	return $cnt;
}


///////////////////////////// analyzer_show_tbls ////////////////////////////////////
function analyzer_show_tbls()
{
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	$wpdb->show_errors();
	$qry="select TABLE_NAME from INFORMATION_SCHEMA.TABLES
	
	WHERE  table_name LIKE 'wp_%dim'
	or  table_name='wp_question_response' ";
	$arr_tbl= $wpdb->get_results($qry);
	
	$res_tbl.="<div class='wrap'>";
	$res_tbl.="<h3>".$title."</h3>";
	$res_tbl.=" <table>";
	$res_tbl.="  <tr>";
	$res_tbl.="   <th>Analyzer tables</th>";
	$res_tbl.="  </tr>";
	foreach($arr_tbl as $i)
	{
		$res_tbl.="  <tr>";
		$res_tbl.="   <td>".$i->TABLE_NAME."</td>";
		$res_tbl.=" </tr>";
	}
	$res_tbl.="  <tr>";
	$res_tbl.="   <td colspan=2>Mgration Errors:</td><td>".$wpdb->print_error()."</td>";
	$res_tbl.=" </tr>";
	$res_tbl.=" </table>";
	$res_tbl.="<div>";
	return $res_tbl;
}
/////////////////////////////analyzer_exec_sql///////////////////
function analyzer_exec_sql($qry, $qry_type)
{
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	
	$msg.="<br>".$db_table_name;
	$sql_res=null;
	$res_view="";
	if($qry_type=='exec')
	{
		$sql_res = $wpdb->get_results($qry);
		
		$res_view.='<ul>';
		foreach($sql_res as $i)
		{
			$res_view.='<li>'.$i->emp_id.'</li>';
		}
		$res_view.='</ul>';
		$msg.="<br>".$res_view;
	}
	else if($qry_type=='update')
	{
		$sql_res = $wpdb->query($qry);
		$msg.='<br>'.'table updated';
	}
	
	return $msg;
}

//////////////////////////////////////// analyzer_cron_job_migration_activation functions ////////////////////////////
function analyzer_cron_job_migration_activation() 
{
 if(!wp_next_scheduled('analyzer_data_migration')) 
 {
  wp_schedule_event(current_time('timestamp'), 'everyminute', 'analyzer_data_migration');
 }
}
//////////////////////////////////////// analyzer_cron_job__deletion_activation functions ////////////////////////////
function analyzer_cron_job_deletion_activation() 
{
 if(!wp_next_scheduled('analyzer_data_deletion')) 
 {
  wp_schedule_event(current_time('timestamp'), 'OnceEvery30mins', 'analyzer_data_deletion');
 }
}

function analyzer_task_migrate()  
{
 return analyzer_migration_cron();
}

function analyzer_task_delete()  
{
 analyzer_deletion_cron();
 analyzer_deletion_cron_builder();

 return true;
}


/*function analyzer_task_delete()  
{
 return analyzer_deletion_cron();
}*/

/////////////////////////////////// analyzer_cron_job_migrate_intervals //////////////////////////////////////////////
function analyzer_cron_job_migrate_intervals($schedules) 
{
 $schedules['everyminute'] = array(
									'interval' => 60,
									'display' => __( 'Once Every Minute' )
								  );
 return $schedules;
}

/////////////////////////////////// analyzer_cron_job_delete_intervals //////////////////////////////////////////////
function analyzer_cron_job_delete_intervals($schedules2) 
{
 $schedules2['OnceEvery30mins'] = array(
									'interval' => 1800,
									'display' => __( 'Once Every 1/2 Hour' )
								  );
 return $schedules2;
}


////////////////////////////////////// analyzer_cron_job_migration_deactivation ///////////////////////////////////////////
function analyzer_cron_job_migration_deactivation() 
{
 wp_clear_scheduled_hook('analyzer_data_migration');
}
////////////////////////////////////// analyzer_cron_job_deletetion_deactivation ///////////////////////////////////////////
function analyzer_cron_job_deletion_deactivation() 
{
 wp_clear_scheduled_hook('analyzer_data_deletion');
}

///////////////////////////////////// analyzer_cron_jobs action hooks //////////////////////////////////////////
add_action('wp', analyzer_cron_job_migration_activation);
add_action('wp', analyzer_cron_job_deletion_activation);
add_filter('cron_schedules', 'analyzer_cron_job_migrate_intervals');
add_filter('cron_schedules', 'analyzer_cron_job_delete_intervals');
add_action('analyzer_data_migration', 'analyzer_task_migrate'); 
add_action('analyzer_data_deletion', 'analyzer_task_delete');


/////////////////////// register_activation_hooks ////////////////////////////////
register_activation_hook(__FILE__, 'analyzer_cron_job_migration_activation');
register_activation_hook(__FILE__, 'analyzer_cron_job_deletion_activation');
register_deactivation_hook(__FILE__, 'analyzer_cron_job_migration_deactivation');
register_deactivation_hook(__FILE__, 'analyzer_cron_job_deletion_deactivation');


/////////////////////////////////// analyzer_utils loop_arr /////////////////////////////////
function loop_arr($arr)
{
	$res='<ul>';
	foreach ($arr as $i)
	{
		$res.='<li>'.$i .'<li>';
		
	}
	$res.='</ul>';
	
	return $res;
}

/////////////////////////////////// analyzer_utils loop_arr_tbl /////////////////////////////////
function loop_arr_tbl($arr, $arr_cols, $title)
{
	$res_tbl.="<div class='wrap'>";
	$res_tbl.="<h3>".$title."</h3>";
	$res_tbl.=" <table class='wp-list-table widefat fixed'>";
	$res_tbl.="  <tr>";
	foreach($arr_cols as $i)
	{
		$res_tbl.="   <th>".$i."</th>";
	}
	$res_tbl.="  </tr>";
	foreach($arr as $j)
	{
		$res_tbl.="  <tr>";
		$res_tbl.="   <td>".$j."</td>";
		$res_tbl.=" </tr>";
	}
	$res_tbl.=" </table>";
	$res_tbl.="<div>";
	
	return $res_tbl;
}

/**************Template Code******************/
add_action('init', 'simpleSessionStart', 1);
add_action('wp_logout', 'simpleSessionDestroy');
add_action('wp_login', 'simpleSessionDestroy');
add_action( 'wp_enqueue_scripts', 'getCss' );

/**
* Enqueue plugin style-file
*/

function getCss(){
	
	// Respects SSL, Style.css is relative to the current file
	wp_register_style( 'prefix-style', plugins_url('/images/Menustyle.css', __FILE__) );
	wp_enqueue_style( 'prefix-style' );
}


function getJavaScript(){
	
	$msg = '<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
	<script  type="text/javascript">
	$(function() {
	$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
	});
	</script><script>
	
	
	function reload(form)
	{
	var question = form.question.options[form.question.options.selectedIndex].value;
	var val=form.questionnaire.options[form.questionnaire.options.selectedIndex].value;
	if(question){
	self.location=\'/wp-admin/admin.php?page=questionpeach-analyzer&questionnaire=\' + val +\'&question=\'+ question;
	} else {
	self.location=\'/wp-admin/admin.php?page=questionpeach-analyzer&questionnaire=\' + val;
	}
	}
	
	
	function getQueryString() {
	var result = {};
	if(!window.location.search.length) return result;
	var qs = window.location.search.slice(1);
	var parts = qs.split("&");
	for(var i=0, len=parts.length; i<len; i++) {
		var tokens = parts[i].split("=");
		result[tokens[0]] = decodeURIComponent(tokens[1]);
	}
	return result;
}
	$(document).ready(function() {
	$("#theForm").submit(function(e) {
		//var that = this;
		var qs = getQueryString();
		for(var key in qs) {
			var field = $(document.createElement("input"));
			field.attr("name", key).attr("type","hidden");
			field.val(qs[key]);
			$(this).append(field);
		}
	});
});

$(function () {
    var doc = new jsPDF();
    var specialElementHandlers = {
        "#editor": function (element, renderer) {
            return true;
        }
    };

    $("#generate").click(function () {
        doc.fromHTML($("#qpresult").html(), 15, 15, {
            "width": 170,
                "elementHandlers": specialElementHandlers
        });
        doc.save("Report.pdf");
    });
});
	</script>';
	return $msg;
}

function simpleSessionStart() {
	if(!session_id())session_start();
}
function getQuestionnaireList(){
	global $wpdb;
	$msg = '<h3><center>QuestionPeach Analyzer</center></h3>';
	$questionnaire=$_GET['questionnaire'];
	$question=$_GET['question'];
	$location=$_GET['location'];
	$responder=$_GET['responder'];
	$start=$_GET['start'];
	$end=$_GET['end'];
	$filterName =$_GET['filterName'];
	$execute=$_GET['Execute'];
	$refresh=$_GET['Refresh'];
	$session=$_GET['action'];
	$viewAll=$_GET['ViewAll'];
	$delete=$_GET['Delete'];
	$generate=$_GET['Generate'];
	$saveFilters=$_GET['saveFilters'];
	$manageFilters =$_GET['manageFilters'];
	$export = $_GET['Export'];
	$filter = $_GET['filter'];
	$reset = $_GET['Reset'];
	
	require_once( ABSPATH.'wp-includes/user.php' );
	$saved_user_id = intval(get_current_user_id());
	$saved_user_name = wp_get_current_user();  
	$saved_questionnaire=$questionnaire;
	$saved_question=$question;
	$saved_location=$location;
	$saved_responder=$responder;
	$saved_start_date_id=$start; 
	$saved_end_date_id=$end;
	
		
	$sql = "SELECT questionnaire_id as id , title
	FROM wp_questionnaire_dim where OwnerId = $saved_user_id  order by questionnaire_id DESC";
	
	/*$sql = 'SELECT questionnaire_id as id , title
	FROM wp_questionnaire_dim  order by questionnaire_id DESC';*/
	$res = $wpdb->get_results($sql);
	
	if($session == 'refresh'){
		$message = 'The database is refreshed sucessfully';
		$msg = getMessage($message);
	} else if($session == 'save filter'){
		$message = 'The Filter is saved sucessfully';
		$msg = getMessage($message);
	} else if($session == 'delete filter'){
		$message = 'The Filter is deleted sucessfully';
		$msg = getMessage($message);
	}
	
	if(isset($delete) and $delete == 'Delete'){
		$msg = deleteFilter($saved_user_id, $saved_user_name, $filter);
	} else if(isset($generate) and $generate == 'Generate Report'){
		$msg = analyzer_generateReport($saved_user_id, $saved_user_name, $filter);
	} else if(isset($manageFilters) and $manageFilters == 'Manage Filters'){
		$msg = $msg.analyzer_manage_my_filters($saved_user_id, $saved_user_name);
	} else if(isset($saveFilters) and $saveFilters == 'Save Filters'){
		analyzer_save_user_report_params($saved_user_id, $saved_questionnaire, $saved_question, $saved_location,
		$saved_responder, $saved_start_date_id, $saved_end_date_id, $filterName);
	} else if(isset($refresh) and $refresh == 'Refresh'){
		analyzer_refresh();
		
	} else if(isset($reset) and $reset == 'Reset'){
		reset_ui();
	}else if(isset($export) and $export == 'Export Data'){
		
		
		exportpdf($questionnaire);
		
	} else if(isset($viewAll) and $viewAll == 'ViewResult'){
		
		$msg = viewAll($questionnaire);
	} else if(!empty($res)){
		$msg =$msg.'<table style"border: 1px solid black;"><tr class="tr1">
		<td class="td"><strong>Survey</strong></td>
		<td class="td" colspan="3">
		
		<select name="questionnaire" onChange="reload(this.form)"> <option value="-1"> Select Survey </option>';
		foreach ($res as $rs) {
			if($rs->id == $questionnaire){
				$msg = $msg.'<option selected value="'.$rs->id.'">'.$rs->title.'</option>';
			} else{
				$msg = $msg.'<option value="'.$rs->id.'">'.$rs->title.'</option>';
			}
		}
		
		$msg = $msg.'</select></td>
		</tr>';
		if(isset($questionnaire) and strlen($questionnaire) > 0){
			
			
			//for question
			$msg = $msg.'<tr class="tr1">
			<td class="td"><strong>Filter By:</strong></td>
			<td class="td">';
			
			$result = mysql_query("select question_id, question_text from wp_question_dim
				where questionnaire_id = $questionnaire");
			$msg = $msg.'	<select name="question" onChange="reload(this.form)"><option value=""> Select Question </option>';
			while($rows = mysql_fetch_assoc($result)) {
				if($rows['question_id'] == $question){
					$msg = $msg.'<option selected value="'.$rows['question_id'].'">'.$rows['question_text'].'</option>';
				} else {
					$msg = $msg.'<option value="'.$rows['question_id'].'">'.$rows['question_text'].'</option>';
				}
			}
			
			$msg = $msg.'</select><br>
			<br>';
			
			//location
			$country = array();
			$result = mysql_query("SELECT DISTINCT location_id, city, country
				FROM wp_question_response QR, wp_location_dim L
				WHERE L.location_id = QR.location_dim_location_id
				AND QR.question_dim_questionnaire_id = $questionnaire");
			$msg = $msg.'	<select name="location"><option value=""> Select Location </option>';
			while($rows = mysql_fetch_assoc($result)) {
				$country = $rows['country'];
				if($rows['location_id'] == $location){
					$msg = $msg.'<option selected value="'.$rows['location_id'].'">'.$rows['city'].' - '.$rows['country'].'</option>';
				} else {
					$msg = $msg.'<option value="'.$rows['location_id'].'">'.$rows['city'].' - '.$rows['country'].'</option>';
					
				}
			}
			$msg = $msg.'</select><br>
			<br>';
			
			//responder
			$result = mysql_query("SELECT DISTINCT R.respondee_id, username
				FROM wp_respondee_dim R, wp_question_response QR, wp_questionnaire_dim Q
				WHERE R.respondee_id = QR.respondee_dim_respondee_id
				AND QR.question_dim_questionnaire_id = Q.questionnaire_id
				AND username <>  'NULL'
				AND Q.questionnaire_id = $questionnaire GROUP BY username");
			$msg = $msg.'	<select name="responder"><option value=""> Select Responder </option>';
			while($rows = mysql_fetch_assoc($result)) {
				if($rows['respondee_id'] == $responder){
					$msg = $msg.'<option selected value="'.$rows['respondee_id'].'">'.$rows['username'].'</option>';
					
				} else {
					$msg = $msg.'<option value="'.$rows['respondee_id'].'">'.$rows['username'].'</option>';
					
				}
			}
			
			$msg = $msg.'</select><br>
			<br></td><td class="td"><strong>Start Date:</strong>
			<br><input type="text" name="start" class="datepicker" value="'.$start.'" />
			<br><strong>End Date:</strong>
			<br><input type="text" name="end" class="datepicker" value="'.$end.'" /></td>
			<td class="td1">
			<input class="button-primary" type="submit" name="Execute" value="Execute" /> 
			</td>
			</tr>
			<tr class="tr1">
			<td colspan="4"><center> 
			<strong>Filter Name: </strong>
			<input type="text" name="filterName" value="" />&nbsp;&nbsp;&nbsp;&nbsp;<input class="button-primary" type="submit" name="saveFilters" value="Save Filters" />&nbsp;&nbsp;&nbsp;&nbsp;
			<input class="button-primary" type="submit" name="manageFilters" value="Manage Filters" />&nbsp;&nbsp;&nbsp;&nbsp;
			<input class="button-primary" type="submit" name="Refresh" value="Refresh" />&nbsp;&nbsp;&nbsp;&nbsp;
			<input class="button-primary" type="submit" name="Reset" value="Reset" /></center></td>
			</tr>';
			
			// For The Questionnaire information
			$result = mysql_query("SELECT Q.questionnaire_id, Q.title, Q.creator_name, Q.date_created, COUNT( DISTINCT QR.respondee_dim_respondee_id ) total_Response, AVG( R.duration ) AS ave_time
				FROM wp_questionnaire_dim Q, wp_question_response QR, wp_respondee_dim R
				WHERE Q.questionnaire_id = $questionnaire
				AND QR.question_dim_questionnaire_id = Q.questionnaire_id
				AND QR.respondee_dim_respondee_id = R.respondee_id");
			
			
			if(!empty($result)){
				
				$msg =$msg.'<tr class="tr1">
				<td colspan="4">
				
				<tr class="tr1">
				<td colspan="4" class="td"><br>
				<table align="center" border="1" cellpadding="10">
				<tr>
				<th>Title</th>
				<th>Date Created</th>
				<th>Created By</th>
				<th>Number Response</th>
				<th>&nbsp;</th>
				</tr>';
				
				while($rows = mysql_fetch_assoc($result)) {
					$completed = $rows['total_Response'];
					$ave_time = round($rows['ave_time'],2);
					$ave_time = gmdate("H:i:s", (int)$rows['ave_time']);
					$msg =$msg.'<tr>';
					$msg = $msg.'<td>'.$rows['title'].'</td><td>'.$rows['date_created'].'</td>
					<td>'.$rows['creator_name'].'</td><td>'.$rows['total_Response'].'</td>
					<td><a href="/wp-admin/admin.php?page=questionpeach-analyzer&questionnaire='.$questionnaire.'&ViewAll=ViewResult">View Result</a>
					</td>';
					$msg =$msg.'</tr>';
				}
				
				$msg = $msg.'</table><br></td>
				</tr><tr>
				<td class="td" width="20%">&nbsp; </td>
				<td class="td" width="30%"><p style="text-align:center; border:2px solid black;"><b>Completed</b> <br>'.  $completed.' </p></td>
				<td class="td" width="30%"><p style="text-align:center; border:2px solid black;"><b>Average Time</b> <br>'.  $ave_time.' </p></td>
				<td class="td" width="20%">&nbsp; </td>
				</tr>
				
				
				</td></tr>';
			}
			//Geo Map
			$result = mysql_query("SELECT count(distinct QR.respondee_dim_respondee_id) total, country FROM wp_question_response QR, wp_location_dim L
				WHERE L.location_id = QR.location_dim_location_id
				AND QR.question_dim_questionnaire_id = $questionnaire group by country");
			if(!empty($result)){
				
				
				$country = '[	["Country", "Popularity"],';
				while($rows = mysql_fetch_assoc($result)) {
					$country = $country.'["'.$rows['country'].'",'.$rows['total'].'],';
				}
			}
			
			$country = $country.']';
			
			$msg = $msg.'<tr class="tr1">
			<td colspan="4">
			<center>Response Distribution</center><br>
			<div id="map_div" style="width: 600px; height: 322px; margin:0 auto;"></div><br>
			</td>
			</tr>
			<script type="text/javascript" src="https://www.google.com/jsapi"></script>
			<script type="text/javascript">
			google.load("visualization", "1", {"packages": ["geochart"]});
			google.setOnLoadCallback(drawRegionsMap);
			
			function drawRegionsMap() {
			var data = google.visualization.arrayToDataTable('.$country.');
			var options = {};
			var chart = new google.visualization.GeoChart(document.getElementById("map_div"));
			chart.draw(data, options);
			};
			
			</script>';
			
			//question result
			$msg=$msg.'<tr class="tr1">
			<td colspan="4">';
			if(isset($execute) and $execute == 'Execute'){
				$msg=$msg.execute($questionnaire,$question,$location,$responder, $start, $end);
			} else if(isset($question) and strlen($question) > 0){
				$result = mysql_query("SELECT question_id, questionnaire_id, question_text, ans_type, response_content, COUNT( * ) AS total
					FROM wp_question_dim Q, wp_question_response QR
					WHERE questionnaire_id = $questionnaire
					AND question_id = $question
					AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
					AND QR.question_dim_question_id = Q.question_id
					GROUP BY response_content");
				if(mysql_num_rows($result) > 0){
					$row = mysql_fetch_row($result);
					$question_id = $row[0];
					$question_txt = $row[2];
					
					if (mysql_data_seek($result, 0))
						{}
					
					$text = '<ol>';
					
					// if the answer type is text
					if(trim($row[3]) == "Text Box"){
						while($rows = mysql_fetch_assoc($result)) {
							//$question_id = $rows['question_id'];
							//$question_txt = $rows['question_text'];
							$text = $text.'<li>'.$rows['response_content'].'</li><br>';
						}
						$text = $text.'</ol>';
						$msg =$msg.'<b>'.$question_id.'. '.$question_txt.'</b><br>
						<br>
						'.$text.'
						<br>';
					} else if(trim($row[3]) == "NPS"){
						
						
						$nps = mysql_query("SELECT COUNT( * ) detractors
							FROM ( SELECT CONVERT( response_content, UNSIGNED INTEGER ) AS response_content
							FROM wp_question_response
							WHERE questionnaire_dim_questionnaire_id =$questionnaire
							AND question_dim_question_id =$question
							AND response_type = 'NPS'
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 0
							AND 6
							) AS detractors");
						$detractors = 0;
						if(!empty($nps)){
							while($row = mysql_fetch_assoc($nps)) {
								$detractors = $row['detractors'];
							}
						}
						$nps1 = mysql_query("SELECT COUNT( * ) passives
							FROM (
							
							SELECT CONVERT( response_content, UNSIGNED INTEGER ) AS response_content
							FROM wp_question_response
							WHERE questionnaire_dim_questionnaire_id =$questionnaire
							AND question_dim_question_id =$question
							AND response_type =  'NPS'
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 7
							AND 8
							) AS passives");
						$passives =0;
						if(!empty($nps1)){
							while($row = mysql_fetch_assoc($nps1)) {

								
								$passives = $row['passives'];
							}
						}
						$nps2 = mysql_query("SELECT COUNT( * ) promoters
							FROM (SELECT CONVERT( response_content, UNSIGNED INTEGER ) AS response_content
							FROM wp_question_response
							WHERE questionnaire_dim_questionnaire_id =$questionnaire
							AND question_dim_question_id =$question
							AND response_type =  'NPS'
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 9
							AND 10
							) AS promoters");
						$promoters = 0;
						if(!empty($nps2)){
							while($row = mysql_fetch_assoc($nps2)) {
								
								$promoters = $row['promoters'];
							}
						}
						
						$total = $detractors + $passives + $promoters;
						if($total > 0){
							$promoters_per = ($promoters / $total)* 100;
							$detractors_per = ($detractors / $total)* 100;
						} else {
							$promoters_per = 0;
							$detractors_per = 0;
						}
						$npsFinal = $promoters_per - $detractors_per;
						$dataArray = '[	["Promoter", "value"]';
						$dataArray = $dataArray.',["Promoters",'.$promoters.']';
						$dataArray = $dataArray.',["Detractors",'.$detractors.']';
						$dataArray = $dataArray.',["Passives",'.$passives.']';
						$dataArray = $dataArray.']';
						
						$msg =$msg.'<script type="text/javascript" src="https://www.google.com/jsapi"></script>
						<script type="text/javascript">
						google.load("visualization", "1", {packages:["corechart"]});
						google.setOnLoadCallback(drawChart);
						function drawChart() {
						var data = google.visualization.arrayToDataTable('.$dataArray.');
						var options = {
						title: "'.$question_id.'. '.$question_txt.'"
						};
						
						var chart = new google.visualization.PieChart(document.getElementById("chart_div'.$question_id.'"));
						chart.draw(data, options);
						}
						</script><br>
						<div id="chart_div'.$question_id.'" style="width: 800px; height: 400px; margin:0 auto;"></div>
						<br>
						<center><b> NET PROMOTER SCORE (NPS): </b> '.$npsFinal.'<br></center><br>
						<br>';
						
						
						
						
					} else{
						
						$dataArray = '[	["Answer", "Responses"]';
						while($rows = mysql_fetch_assoc($result)) {
							//$question_id = $rows['question_id'];
							//$question_txt = $rows['question_text'];
							$key = $rows['response_content'];
							$value = $rows['total'];
							$dataArray = $dataArray.',["'.$key.'",'.$value.']';
							
						}
						
						$dataArray = $dataArray.']';
						
						
						$msg=$msg.'
						
						<script type="text/javascript" src="https://www.google.com/jsapi"></script>
						<script type="text/javascript">
						google.load("visualization", "1", {packages:["corechart"]});
						google.setOnLoadCallback(drawChart);
						function drawChart() {
						var data = google.visualization.arrayToDataTable('.$dataArray.');
						var options = {
						title: "'.$question_id.'. '.$question_txt.'",
						vAxis: {title: "Answer",  titleTextStyle: {color: "red"}}
						};



						
						var chart = new google.visualization.BarChart(document.getElementById("chart_div'.$question_id.'"));
						chart.draw(data, options);
						}
						</script><br>
						<div id="chart_div'.$question_id.'" style="width: 800px; height: 400px; margin:0 auto;"></div>
						<br>';
						
						
					}
				} else {
					
					$msg = $msg.'<table class="table">
					<tbody>
					<tr class="tr1">
					<td colspan="4" class="td">There is no result available for this question</td>
					</tr></tr>
					</tbody>
					</table>';
				}
			}
			$msg=$msg.'</td>
			</tr></table>';
			
		} else {
			$msg = $msg.'<tr class="tr1">
			<td class="td"><strong>Filter By:</strong></td>
			<td class="td">
			<select name="question">
			<option value="">Select Question</option>
			</select>
			<br>
			<br>
			<select name="location">
			<option value="">Select Location</option>
			</select>
			<br>
			<br>
			
			<select name="responder">
			<option value="">Select Responder</option>
			</select>
			
			</td><td class="td"><strong>Start Date:</strong>
			<br><input type="text" name="start" class="datepicker" value="" />
			<br><strong>End Date:</strong>
			<br><input type="text" name="end" class="datepicker" value="" /></td>
			
			<td class="td1">
			<input class="button-primary" type="submit" name="submit" value="Execute" />
			</td>
			
			
			</tr>
			<tr class="tr1">
			<td colspan="4"><center> 
			<strong>Filter Name: </strong>
			<input type="text" name="filterName" value="" />&nbsp;&nbsp;&nbsp;&nbsp;<input class="button-primary" type="submit" name="saveFilters" value="Save Filters" />&nbsp;&nbsp;&nbsp;&nbsp;
			<input class="button-primary" type="submit" name="manageFilters" value="Manage Filters" />&nbsp;&nbsp;&nbsp;&nbsp;
			<input class="button-primary" type="submit" name="Refresh" value="Refresh" />&nbsp;&nbsp;&nbsp;&nbsp;
			<input class="button-primary" type="submit" name="Reset" value="Reset" /></center></td></center></td>
			</tr></table>';
		}
		
	} else {
		if(!empty($saved_user_name) && $saved_user_id > 0){
		        $msg = $msg.'<p class="one">&nbsp;<br><b>You do not have any survey in the system.</b><br>&nbsp;
				<center><input class="button-primary" type="submit" name="Refresh" value="Refresh" /></center><br></p>';
		} else {
			$msg = $msg.'<p class="one"><br><b>Please login to access your survey.</b><br>&nbsp;</p>';
		}
	}
	return $msg;
}


function getMessage($message){
	
	
	$msg = '<div style="background:#ffff99;">';
	$msg = $msg.'<p align="center"><br> <strong>'.$message.'</strong><br></p>';
	$msg = $msg.'</div>';
	
	return $msg;
	
}

function getAdminForm(){
	
	$msg = '<div class="loader"></div><form>
	<table class="table" bgcolor="#FFF">
	<tbody><tr><td colspan="4">'.do_shortcode('[QP_QuestionnaireList]').'</td>
	</tr>
	
	<tbody>
	</table>
	<br>
	<br>
	
	
	<!-- the code below should be seen when the query is executed  -->
	
	
	
	</form>';
	
	return $msg;
}

function exportCSV($id){
	
	$sql = mysql_query("SELECT question_id, questionnaire_id, question_text, ans_type, response_content, COUNT( * ) AS total
		FROM wp_question_dim Q, wp_question_response QR
		WHERE questionnaire_id = $id
		AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
		AND QR.question_dim_question_id = Q.question_id
		GROUP BY response_content
		ORDER BY questionnaire_id, question_id");
	if(mysql_num_rows($result) > 0){
		$filename = 'Export.csv';
		$headers = array('Question Id', 'Question Text', 'Response', 'Total');
		
		$handle = fopen($filename, 'w');
		fputcsv($handle, $headers, ',', '"');
		
		
		
		while($results = mysql_fetch_array($sql)) {
			$row = array(
				$results[0],
				$results[2],
				$results[4],
				$results[5]
				);
			$fputcsv($handle, $row, ',', '"');
		}
		
		fclose($handle);
	}
}

function getLocation(){
	$rows = array();
	$table = array();
	$table['cols'] = array(
		array('label' => 'country', 'type' => 'string')
		//,array('label' => 'Popularity', 'type' => 'number')
		);
	
	global $wpdb;
	$sql = 'SELECT * FROM  wp_location_dim';
	$res = $wpdb->get_results($sql);
	if(!empty($res)){
		
		foreach ($res as $rs) {
			$country = array();
			
			$country[] = array('v' => (string) $rs->country);
			$rows[] = array('c' => $country);
		}
		
	}
	$table['rows'] = $rows;
	
	// convert data into JSON format
	$jsonTable = json_encode($table);
	
	//return $jsonTable ;
	return $table;
}

function getFilterPanel(){
	
	
	global $wpdb;
	$msg = '';
	$sql = 'SELECT id, title
	FROM questionnior_fact group by id';
	$res = $wpdb->get_results($sql);
	
	if(!empty($res)){
		$msg ='<tr class="tr">
		<td class ="td" colspan="3">
		<select name="survey"> <option value=""> Select Survey </option>';
		foreach ($res as $rs) {
			$msg = $msg.'<option value="'.$rs->id.'">'.$rs->title.'</option>';
			
		}
		
		$msg = $msg.'</select>';
		
		$msg = $msg.'</td></tr>';
	}
	return $msg;
}

function getGeoChart(){
	
	global $wpdb;
	$sql = 'SELECT count(*) total, country FROM  wp_location_dim group by country';
	$res = $wpdb->get_results($sql);
	
	//$country = array(array('Country'), array('USA'), array('India'), array('China'));
	$country = '[	["Country", "Popularity"],';
	if(!empty($res)){
		foreach ($res as $rs)  {
			$country = $country.'["'.$rs->country.'",'.$rs->total.'],';
		}
	}
	
	$country = $country.']';
	
	$geoChart = '<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">
	google.load("visualization", "1", {"packages": ["geochart"]});
	google.setOnLoadCallback(drawRegionsMap);
	
	function drawRegionsMap() {
	var data = google.visualization.arrayToDataTable('.$country.');
	var options = {};
	var chart = new google.visualization.GeoChart(document.getElementById("map_div"));
	chart.draw(data, options);
	};
	
	</script>';
	return $geoChart;
}
function exportpdf($questionnaire){
          
       require_once('tcpdf/tcpdf.php');
       $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
       $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
       $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

       if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
       	       require_once(dirname(__FILE__).'/lang/eng.php');
       	       $pdf->setLanguageArray($l);
       }
       $pdf->SetFont('helvetica', '', 9);
       $pdf->AddPage();
       $msg = viewAll($questionnaire);
        echo $msg;
         
         $pdf->writeHTML($msg, true, 0, true, 0);
         $pdf->writeHTMLCell(0, 0, '', '', $msg, 0, 1, 0, true, '', true);
         $pdf->lastPage();

         $pdf->Output('report.pdf', 'D');
        
}
function viewAll($questionnaire){
	$msg = '<h3><center>QuestionPeach Analyzer</center></h3>';
	
	$questions = mysql_query("SELECT question_id, question_text, topic, title
		FROM wp_question_dim A, wp_questionnaire_dim B
		WHERE A.questionnaire_id = B.questionnaire_id
		AND A.questionnaire_id = $questionnaire order by question_id");
	$survey = mysql_fetch_row($questions);
	$msg = $msg.'<button id="generate">generate PDF</button><br><div id="qpresult"><table class="table"><tr class="tr1"><td class="td"><h4>Topic: '.$survey[2].'</h4><h5>Title: '.$survey[3].'</h5></td></tr>';
	
	if (mysql_data_seek($questions, 0))
		{}
	
	if(mysql_num_rows($questions) > 0){
		while($question = mysql_fetch_assoc($questions)) {
			
			$qid = $question['question_id'];
			$qtext = $question['question_text'];
			
			// For The Questionnaire information
			$result = mysql_query("SELECT question_id, questionnaire_id, question_text, ans_type, response_content, COUNT( * ) AS total
				FROM wp_question_dim Q, wp_question_response QR
				WHERE questionnaire_id = $questionnaire
				AND question_id = $qid
				AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
				AND QR.question_dim_question_id = Q.question_id
				GROUP BY response_content");
			if(mysql_num_rows($result) > 0){
				
				$row = mysql_fetch_row($result);
				if (mysql_data_seek($result, 0))
					{}
				$text = '<ol>';
				// if the answer type is text
				if(trim($row[3])=='Text Box'){
					while($rows = mysql_fetch_assoc($result)) {
						$question_id = $rows['question_id'];
						$question_txt = $rows['question_text'];
						$text = $text.'<li>'.$rows['response_content'].'</li><br>';
					}
					$text = $text.'</ol>';
					$msg =$msg.'
					<tr class="tr1">
					<td class="td"><b>'.$question_id.'. '.$question_txt.'</b><br>
					<br>
					'.$text.'
					</td>
					</tr>';
				} else if(trim($row[3]) == "NPS"){
					
					$res = mysql_query("SELECT question_id,  question_text FROM wp_question_dim
						where questionnaire_id = $questionnaire
						and question_id = $qid");
					while($rows = mysql_fetch_assoc($res)) {
						$question_id = $rows['question_id'];
						$question_txt = $rows['question_text'];
					}
					$nps = mysql_query("SELECT COUNT( * ) detractors
						FROM ( SELECT CONVERT( response_content, UNSIGNED INTEGER ) AS response_content
						FROM wp_question_response
						WHERE questionnaire_dim_questionnaire_id =$questionnaire
						AND question_dim_question_id =$qid
						AND response_type = 'NPS'
						AND CONVERT( response_content, UNSIGNED INTEGER )
						BETWEEN 0
						AND 6
						) AS detractors");
					$detractors = 0;
					if(!empty($nps)){
						while($row = mysql_fetch_assoc($nps)) {
							$detractors = $row['detractors'];
						}
					}
					$nps1 = mysql_query("SELECT COUNT( * ) passives
						FROM (
						
						SELECT CONVERT( response_content, UNSIGNED INTEGER ) AS response_content
						FROM wp_question_response
						WHERE questionnaire_dim_questionnaire_id =$questionnaire
						AND question_dim_question_id =$qid
						AND response_type =  'NPS'
						AND CONVERT( response_content, UNSIGNED INTEGER )
						BETWEEN 7
						AND 8
						) AS passives");
					$passives =0;
					if(!empty($nps1)){
						while($row = mysql_fetch_assoc($nps1)) {
							
							$passives = $row['passives'];
						}
					}
					$nps2 = mysql_query("SELECT COUNT( * ) promoters
						FROM (SELECT CONVERT( response_content, UNSIGNED INTEGER ) AS response_content
						FROM wp_question_response
						WHERE questionnaire_dim_questionnaire_id =$questionnaire
						AND question_dim_question_id =$qid
						AND response_type =  'NPS'
						AND CONVERT( response_content, UNSIGNED INTEGER )
						BETWEEN 9
						AND 10
						) AS promoters");
					$promoters = 0;
					if(!empty($nps2)){
						while($row = mysql_fetch_assoc($nps2)) {
							
							$promoters = $row['promoters'];
						}
					}
					
					$total = $detractors + $passives + $promoters;
					if($total > 0){
						$promoters_per = ($promoters / $total)* 100;
						$detractors_per = ($detractors / $total)* 100;
					} else {
						$promoters_per = 0;
						$detractors_per = 0;
					}
					
					$npsFinal = $promoters_per - $detractors_per;
					$dataArray = '[	["Promoter", "value"]';
					$dataArray = $dataArray.',["Promoters",'.$promoters.']';
					$dataArray = $dataArray.',["Detractors",'.$detractors.']';
					$dataArray = $dataArray.',["Passives",'.$passives.']';
					$dataArray = $dataArray.']';
					
					$msg =$msg.'
					<script type="text/javascript" src="https://www.google.com/jsapi"></script>
					<script type="text/javascript">
					google.load("visualization", "1", {packages:["corechart"]});
					google.setOnLoadCallback(drawChart);
					function drawChart() {
					var data = google.visualization.arrayToDataTable('.$dataArray.');
					var options = {
					title: "'.$question_id.'. '.$question_txt.'"
					};
					
					var chart = new google.visualization.PieChart(document.getElementById("chart_div'.$qid.'"));
					chart.draw(data, options);
					}
					</script><tr class="tr1">
					<td class="td">
					<div id="chart_div'.$qid.'" style="width: 800px; height: 400px; margin:0 auto;"></div>
					<br>
					<center><b> NET PROMOTER SCORE (NPS): </b> '.$npsFinal.'<br></center><br>
					</td>
					</tr>';
										
					
				}else{
					
					$dataArray = '[	["Answer", "Responses"]';
					while($rows = mysql_fetch_assoc($result)) {
						$question_id = $rows['question_id'];
						$question_txt = $rows['question_text'];
						$key = $rows['response_content'];
						$value = $rows['total'];
						$dataArray = $dataArray.',["'.$key.'",'.$value.']';
						
					}
					
					$dataArray = $dataArray.']';
					
					
					$msg=$msg.'<script type="text/javascript" src="https://www.google.com/jsapi"></script>
					<script type="text/javascript">
					google.load("visualization", "1", {packages:["corechart"]});
					google.setOnLoadCallback(drawChart);
					function drawChart() {
					var data = google.visualization.arrayToDataTable('.$dataArray.');
					
					var options = {
					title: "'.$question_id.'. '.$question_txt.'",
					vAxis: {title: "Answer",  titleTextStyle: {color: "red"}}
					};
					
					var chart = new google.visualization.BarChart(document.getElementById("chart_div'.$qid.'"));
					chart.draw(data, options);
					}
					</script><tr class="tr1">
					<td class="td">
					<div id="chart_div'.$qid.'" style="width: 800px; height: 400px; margin:0 auto;"></div>
					</td>
					</tr>';
									
				}
				
							
			} else {
				
				$msg = $msg.'<tr class="tr1">
				<td colspan="4" class="td"><b>'.$qid.'.'.$qtext.'</b><br><br>
				There is no result available for this question</td>
				</tr>';
			}
		}
	}
	
	$msg = $msg.'</table></div>';
	
	return $msg;
}

function execute($questionnaire,$question,$location,$responder, $start, $end){
	$msg='';
	$query= '';
	if(isset($question) and strlen($question) > 0){
		$sql = "SELECT question_id, questionnaire_id, question_text, ans_type, response_content, COUNT( * ) AS total
		FROM wp_question_dim Q, wp_question_response QR
		WHERE questionnaire_id = $questionnaire
		AND question_id = $question
		AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
		AND QR.question_dim_question_id = Q.question_id
		GROUP BY response_content";
		
		//location
		if(isset($location) and strlen($location) > 0){
			$sql = "SELECT question_id, questionnaire_id, question_text, ans_type, response_content, country, city, COUNT( * ) AS total
			FROM wp_question_dim Q, wp_question_response QR, wp_location_dim L
			WHERE questionnaire_id =$questionnaire
			AND question_id =$question
			AND location_id =$location
			AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
			AND QR.question_dim_question_id = Q.question_id
			AND QR.location_dim_location_id = L.location_id
			GROUP BY response_content";
			
			
			//responde
			if(isset($responder) and strlen($responder) > 0){
				$sql = "SELECT question_id, questionnaire_id, question_text, ans_type, response_content, country, city, username, COUNT( * ) AS total
				FROM wp_question_dim Q, wp_question_response QR, wp_location_dim L, wp_respondee_dim R
				WHERE questionnaire_id = $questionnaire
				AND question_id =$question
				AND location_id =$location
				AND respondee_id =$responder
				AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
				AND QR.question_dim_question_id = Q.question_id
				AND QR.location_dim_location_id = L.location_id
				AND QR.respondee_dim_respondee_id = R.respondee_id
				GROUP BY response_content";
				
				//date
				if(strlen($end) > 0 and strlen($start) > 0){
					$sql = "SELECT question_id, questionnaire_id, question_text, ans_type, response_content, country, city, username, date, COUNT( * ) AS total
					FROM wp_question_dim Q, wp_question_response QR, wp_location_dim L, wp_respondee_dim R, wp_time_dim T
					WHERE questionnaire_id =$questionnaire
					AND question_id =$question
					AND location_id =$location
					AND respondee_id =$responder
					AND T.date >= '$start' and T.date <= '$end'
					AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
					AND QR.question_dim_question_id = Q.question_id
					AND QR.location_dim_location_id = L.location_id
					AND QR.respondee_dim_respondee_id = R.respondee_id
					AND QR.time_dim_time_id = T.time_id
					GROUP BY response_content";
				}
				
			} else {
				//date
				if(strlen($end) > 0 and strlen($start) > 0){
					$sql = "SELECT question_id, questionnaire_id, question_text, ans_type, response_content, country, city, date, COUNT( * ) AS total
					FROM wp_question_dim Q, wp_question_response QR, wp_location_dim L, wp_time_dim T
					WHERE questionnaire_id =$questionnaire
					AND question_id =$question
					AND location_id =$location
					AND T.date >= '$start' and T.date <= '$end'
					AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
					AND QR.question_dim_question_id = Q.question_id
					AND QR.location_dim_location_id = L.location_id
					AND QR.time_dim_time_id = T.time_id
					GROUP BY response_content";
				}
			}
		} else 			if(isset($responder) and strlen($responder) > 0){
			$sql = "SELECT question_id, questionnaire_id, question_text, ans_type, response_content, username, COUNT( * ) AS total
			FROM wp_question_dim Q, wp_question_response QR, wp_respondee_dim R
			WHERE questionnaire_id =$questionnaire
			AND question_id =$question
			AND respondee_id = $responder
			AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
			AND QR.question_dim_question_id = Q.question_id
			AND QR.respondee_dim_respondee_id = R.respondee_id
			GROUP BY response_content";
			
			//date
			if(strlen($end) > 0 and strlen($start) > 0){
				$sql = "SELECT question_id, questionnaire_id, question_text, ans_type, response_content, username, date, COUNT( * ) AS total
				FROM wp_question_dim Q, wp_question_response QR, wp_respondee_dim R, wp_time_dim T
				WHERE questionnaire_id =$questionnaire
				AND question_id =$question
				AND respondee_id =$responder
				AND T.date >= '$start' and T.date <= '$end'
				AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
				AND QR.question_dim_question_id = Q.question_id
				AND QR.respondee_dim_respondee_id = R.respondee_id
				AND QR.time_dim_time_id = T.time_id
				GROUP BY response_content";
			}
		} else {
			//date
			if(strlen($end) > 0 and strlen($start) > 0){
				$sql = "SELECT question_id, questionnaire_id, question_text, ans_type, response_content, date, COUNT( * ) AS total
				FROM wp_question_dim Q, wp_question_response QR, wp_time_dim T
				WHERE questionnaire_id =$questionnaire
				AND question_id =$question
				AND T.date >= '$start' and T.date <= '$end'
				AND QR.questionnaire_dim_questionnaire_id = Q.questionnaire_id
				AND QR.question_dim_question_id = Q.question_id
				AND QR.time_dim_time_id = T.time_id
				GROUP BY response_content";
			}
		}
		
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0){
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$question_id = $row['question_id'];
			$question_txt = $row['question_text'];
			if(strlen($location) > 0){
				$query= $query.'Location: '.$row[country].' - '. $row['city'].'<br>';
			}
			
			if(strlen($responder) > 0){
				$query= $query.'Responder: '.$row['username'].' <br> ';
			}
			
			if(strlen($start) > 0){
				$query= $query.'Start Date: '.$start.' <br> ';
			}
			
			if(strlen($end) > 0){
				$query= $query.'End Date: '.$end.' <br> ';
			}
			
			if (mysql_data_seek($result, 0))
				{}
			
			$text = '<ol>';
			
			// if the answer type is text
			if(trim($row['ans_type']) == "Text Box"){
				while($rows = mysql_fetch_assoc($result)) {
					//$question_id = $rows['question_id'];
					//$question_txt = $rows['question_text'];
					$text = $text.'<li>'.$rows['response_content'].'</li><br>';
				}
				$text = $text.'</ol>';
				$msg =$msg.'<br><b>'.$question_id.'. '.$question_txt.'</b><br>
				<br>
				'.$text.'
				<br>
				<br>
				<p class="two">'.$query.'</p>
				<br>';
			} else if(trim($row['ans_type']) == "NPS"){
				$nps_sql = "SELECT COUNT( * ) detractors
						FROM ( SELECT CONVERT( response_content, UNSIGNED INTEGER ) AS response_content
						FROM wp_question_response
						WHERE questionnaire_dim_questionnaire_id =$questionnaire
						AND question_dim_question_id =$question
						AND response_type = 'NPS'
						AND CONVERT( response_content, UNSIGNED INTEGER )
						BETWEEN 0
						AND 6
						) AS detractors";
				$nps1_sql = "SELECT COUNT( * ) passives
						FROM (
						
						SELECT CONVERT( response_content, UNSIGNED INTEGER ) AS response_content
						FROM wp_question_response
						WHERE questionnaire_dim_questionnaire_id =$questionnaire
						AND question_dim_question_id =$question 
						AND response_type =  'NPS'
						AND CONVERT( response_content, UNSIGNED INTEGER )
						BETWEEN 7
						AND 8
						) AS passives";
						
					$nps2_sql = "SELECT COUNT( * ) promoters
						FROM (SELECT CONVERT( response_content, UNSIGNED INTEGER ) AS response_content
						FROM wp_question_response
						WHERE questionnaire_dim_questionnaire_id =$questionnaire
						AND question_dim_question_id =$question 
						AND response_type =  'NPS'
						AND CONVERT( response_content, UNSIGNED INTEGER )
						BETWEEN 9
						AND 10
						) AS promoters";
				
				if(isset($location) and strlen($location) > 0){
					$nps_sql = "SELECT L.city, L.country, COUNT(*) detractors 
							FROM wp_question_response QR, wp_location_dim L
							WHERE questionnaire_dim_questionnaire_id = $questionnaire
							AND question_dim_question_id = $question
                            AND location_dim_location_id = $location
							AND response_type = 'NPS'
                            AND L.location_id = QR.location_dim_location_id
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 0
							AND 6";
					 $nps1_sql = "SELECT L.city, L.country, COUNT(*) passives 
							FROM wp_question_response QR, wp_location_dim L
							WHERE questionnaire_dim_questionnaire_id = $questionnaire
							AND question_dim_question_id = $question
                            AND location_dim_location_id = $location
							AND response_type = 'NPS'
                            AND L.location_id = QR.location_dim_location_id
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 7
							AND 8"; 
						$nps2_sql = "SELECT L.city, L.country, COUNT(*) promoters
							FROM wp_question_response QR, wp_location_dim L
							WHERE questionnaire_dim_questionnaire_id = $questionnaire
							AND question_dim_question_id = $question
                            AND location_dim_location_id = $location
							AND response_type = 'NPS'
                            AND L.location_id = QR.location_dim_location_id
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 9
							AND 10";
						
					if(isset($responder) and strlen($responder) > 0){
						$nps_sql = "SELECT R.username, L.city, L.country, COUNT(*) as detractors
							FROM wp_question_response QR, wp_respondee_dim R, wp_location_dim L
							WHERE questionnaire_dim_questionnaire_id = $questionnaire
							AND question_dim_question_id = $question
                            AND respondee_dim_respondee_id = $responder
                            AND location_dim_location_id = $location
							AND response_type = 'NPS'
                            AND R.respondee_id = QR.respondee_dim_respondee_id
                            AND L.location_id = QR.location_dim_location_id
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 0
							AND 6";
							
						$nps1_sql = "SELECT R.username, L.city, L.country, COUNT(*) as passives
							FROM wp_question_response QR, wp_respondee_dim R, wp_location_dim L
							WHERE questionnaire_dim_questionnaire_id = $questionnaire
							AND question_dim_question_id = $question
                            AND respondee_dim_respondee_id = $responder
                            AND location_dim_location_id = $location
							AND response_type = 'NPS'
                            AND R.respondee_id = QR.respondee_dim_respondee_id
                            AND L.location_id = QR.location_dim_location_id
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 7
							AND 8";
							
						$nps2_sql = "SELECT R.username, L.city, L.country, COUNT(*) as promoters
							FROM wp_question_response QR, wp_respondee_dim R, wp_location_dim L
							WHERE questionnaire_dim_questionnaire_id = $questionnaire
							AND question_dim_question_id = $question
                            AND respondee_dim_respondee_id = $responder
                            AND location_dim_location_id = $location
							AND response_type = 'NPS'
                            AND R.respondee_id = QR.respondee_dim_respondee_id
                            AND L.location_id = QR.location_dim_location_id
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 9
							AND 10";
							if(strlen($start)> 0 and strlen($end) > 0){
							 	$nps_sql = "SELECT R.username,
											L.city,
											L.country,
											COUNT(*) as detractors,
											(select T.date
											from  wp_time_dim T
											where T.time_id in(select wqr.time_dim_time_id
											from wp_question_response wqr
											where wqr.respondee_dim_respondee_id = $responder
											AND question_dim_question_id = $question
											AND location_dim_location_id = $location
											AND response_type = 'NPS'
											)
											and T.date between '$start' and '$end' ) as dt
											FROM   wp_question_response QR, wp_respondee_dim R, wp_location_dim L
											WHERE  R.respondee_id = QR.respondee_dim_respondee_id
											AND    L.location_id = QR.location_dim_location_id
											AND questionnaire_dim_questionnaire_id = $questionnaire
											AND question_dim_question_id = $question
											AND location_dim_location_id = $location
											AND respondee_dim_respondee_id = $responder
											AND response_type = 'NPS'
											AND CONVERT( response_content, UNSIGNED INTEGER ) BETWEEN 0 AND 6";
							
							    $nps1_sql = "SELECT R.username,
											L.city,
											L.country,
											COUNT(*) as passives,
											(select T.date
											from  wp_time_dim T
											where T.time_id in(select wqr.time_dim_time_id
											from wp_question_response wqr
											where wqr.respondee_dim_respondee_id = $
											AND question_dim_question_id=3
											AND location_dim_location_id = 1931
											AND response_type = 'NPS'
											)
											and T.date between '2014-05-01' and '2014-05-10' ) as dt
											
											FROM   wp_question_response QR, wp_respondee_dim R, wp_location_dim L
											WHERE  R.respondee_id = QR.respondee_dim_respondee_id
											AND    L.location_id = QR.location_dim_location_id
											AND questionnaire_dim_questionnaire_id = $questionnaire
											AND question_dim_question_id=3
											AND location_dim_location_id = 1931
											AND respondee_dim_respondee_id=1
											AND response_type = 'NPS'
											AND CONVERT( response_content, UNSIGNED INTEGER ) BETWEEN 7 AND 8";
											
								$nps2_sql = "SELECT R.username,
											L.city,
											L.country,
											COUNT(*) as promoters,
											(select T.date
											from  wp_time_dim T
											where T.time_id in(select wqr.time_dim_time_id
											from wp_question_response wqr
											where wqr.respondee_dim_respondee_id=1
											AND question_dim_question_id=3
											AND location_dim_location_id = 1931
											AND response_type = 'NPS'
											)
											and T.date between '2014-05-01' and '2014-05-10' ) as dt
											FROM   wp_question_response QR, wp_respondee_dim R, wp_location_dim L
											WHERE  R.respondee_id = QR.respondee_dim_respondee_id
											AND    L.location_id = QR.location_dim_location_id
											AND questionnaire_dim_questionnaire_id = 1
											AND question_dim_question_id=3
											AND location_dim_location_id = 1931
											AND respondee_dim_respondee_id=1
											AND response_type = 'NPS'
											AND CONVERT( response_content, UNSIGNED INTEGER ) BETWEEN 9 AND 10;
											";
							}
					}
				} else if(isset($responder) and strlen($responder) > 0){
					$nps_sql = "SELECT R.username, COUNT(*) as detractors
							FROM wp_question_response QR, wp_respondee_dim R
							WHERE questionnaire_dim_questionnaire_id = $questionnaire
							AND question_dim_question_id = $question
                            AND respondee_dim_respondee_id = $responder
							AND response_type = 'NPS'
                            AND R.respondee_id = QR.respondee_dim_respondee_id
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 0
							AND 6";
						$nps1_sql = "SELECT R.username, COUNT(*) as passives
							FROM wp_question_response QR, wp_respondee_dim R
							WHERE questionnaire_dim_questionnaire_id = $questionnaire
							AND question_dim_question_id = $question
                            AND respondee_dim_respondee_id = $responder
							AND response_type = 'NPS'
                            AND R.respondee_id = QR.respondee_dim_respondee_id
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 7
							AND 8";
							
						$nps2_sql = "SELECT R.username, COUNT(*) as promoters
							FROM wp_question_response QR, wp_respondee_dim R
							WHERE questionnaire_dim_questionnaire_id = $questionnaire
							AND question_dim_question_id = $question
                            AND respondee_dim_respondee_id = $responder
							AND response_type = 'NPS'
                            AND R.respondee_id = QR.respondee_dim_respondee_id
							AND CONVERT( response_content, UNSIGNED INTEGER )
							BETWEEN 9
							AND 10";
				} else if(strlen($start) and strlen($end) > 0){
					
				}
				
				
				$nps = mysql_query($nps_sql);
				
				
				$detractors = 0;
				if(!empty($nps)){
					while($row = mysql_fetch_assoc($nps)) {
						$detractors = $row['detractors'];
					}
				}
				
				
				$nps1 = mysql_query($nps1_sql);
											
				
				$passives =0;
				if(!empty($nps1)){
					while($row = mysql_fetch_assoc($nps1)) {
						
						$passives = $row['passives'];
					}
				}
				
				$nps2 = mysql_query($nps2_sql);
									
				$promoters = 0;
				if(!empty($nps2)){
					while($row = mysql_fetch_assoc($nps2)) {
						
						$promoters = $row['promoters'];
					}
				}
				
				$total = $detractors + $passives + $promoters;
				if($total > 0){
					$promoters_per = ($promoters / $total)* 100;
					$detractors_per = ($detractors / $total)* 100;
				} else {
					$promoters_per = 0;
					$detractors_per = 0;
				}
				$npsFinal = $promoters_per - $detractors_per;
				$dataArray = '[	["Promoter", "value"]';
				$dataArray = $dataArray.',["Promoters",'.$promoters.']';
				$dataArray = $dataArray.',["Detractors",'.$detractors.']';
				$dataArray = $dataArray.',["Passives",'.$passives.']';
				$dataArray = $dataArray.']';
				
				$msg =$msg.'
				<script type="text/javascript" src="https://www.google.com/jsapi"></script>
				<script type="text/javascript">
				google.load("visualization", "1", {packages:["corechart"]});
				google.setOnLoadCallback(drawChart);
				function drawChart() {
				var data = google.visualization.arrayToDataTable('.$dataArray.');
				var options = {
				title: "'.$question_id.'. '.$question_txt.'"
				};
				
				var chart = new google.visualization.PieChart(document.getElementById("chart_div'.$question_id.'"));
				chart.draw(data, options);
				}
				</script><br>
				<div id="chart_div'.$question_id.'" style="width: 800px; height: 400px; margin:0 auto;"></div>
				<br>
				<center><b> NET PROMOTER SCORE (NPS): </b> '.$npsFinal.'<br></center><br>
				<p class="two">'.$query.'</p>
				<br>';
								
			} else{
				
				$dataArray = '[	["Answer", "Responses"]';
				while($rows = mysql_fetch_assoc($result)) {
					//$question_id = $rows['question_id'];
					//$question_txt = $rows['question_text'];
					$key = $rows['response_content'];
					$value = $rows['total'];
					$dataArray = $dataArray.',["'.$key.'",'.$value.']';
					
				}
				
				$dataArray = $dataArray.']';
				
				
				$msg=$msg.'				
				<script type="text/javascript" src="https://www.google.com/jsapi"></script>
				<script type="text/javascript">
				google.load("visualization", "1", {packages:["corechart"]});
				google.setOnLoadCallback(drawChart);
				function drawChart() {
				var data = google.visualization.arrayToDataTable('.$dataArray.');
				var options = {
				title: "'.$question_id.'. '.$question_txt.'",
				vAxis: {title: "Answer",  titleTextStyle: {color: "red"}}
				};
				
				var chart = new google.visualization.BarChart(document.getElementById("chart_div'.$question_id.'"));
				chart.draw(data, options);
				}
				</script><br>
				<div id="chart_div'.$question_id.'" style="width: 800px; height: 400px; margin:0 auto;"></div>
				<br>
				<p class="two">'.$query.'</p>
				<br>
				<br>';
				
				
			}
		} else {
			
			$msg = $msg.'<br>There is no result available for this question.
			<br>';
		}
	} else {
		$msg = 'Please select at least one question.';
	}
	return $msg;
}

/////////////////////////////analyzer_refresh /////////////////////////////////////////////////
function analyzer_refresh()
{
	global $wpdb;
	
	$sql_0="INSERT INTO wp_respondee_dim(respondee_id, survey_completed, survey_taken_date, username, ip, duration)
	SELECT SessionID, SurveyCompleted, SurveyTakenDate, Username, IP, Duration
	FROM gwu_session
	where SessionID in (
	select SessionID
	from   gwu_session
	where SessionID not in(select respondee_id from wp_respondee_dim));";
	
	
	$sql_1="INSERT INTO wp_question_dim (question_id, questionnaire_id, question_text, ans_type)
		SELECT GQ.questsequence, GQ.QuestionnaireID, GQ.text, GQ.AnsType
		FROM gwu_question GQ
		left join wp_question_dim WQ
		on GQ.QuestionnaireID = WQ.questionnaire_id and GQ.QuestSequence = WQ.question_id
		WHERE WQ.question_id is null
		AND WQ.questionnaire_id is null;";
	
	
	$sql_2="INSERT INTO wp_questionnaire_dim (questionnaire_id, topic, date_created, allow_anonymous, allow_multiple, title, creator_name, OwnerId, EditorId)
	SELECT QuestionnaireID, Topic, DateCreated, AllowAnnonymous, AllowMultiple, Title, CreatorName, OwnerId, EditorId
	FROM gwu_questionnaire
	WHERE PublishFlag = '1'
	AND QuestionnaireID in (
	SELECT QuestionnaireID
	FROM gwu_questionnaire
	WHERE QuestionnaireID not in (SELECT questionnaire_id from wp_questionnaire_dim));";
	
	$sql_3="INSERT INTO wp_question_response (response_id, response_content, response_type, questionnaire_dim_questionnaire_id, question_dim_questionnaire_id, question_dim_question_id, respondee_dim_respondee_id)
	SELECT ResponseID, ResponseContent, ResponseType, QuestionnaireID, QuestionnaireID, QuestSequence, gwu_response.SessionID
	FROM gwu_response, gwu_session
	WHERE gwu_response.SessionID = gwu_session.SessionID
	AND ResponseID in (
	SELECT ResponseID
	FROM gwu_response
	WHERE ResponseID not in (SELECT response_id from wp_question_response));";
	
	$sql_4="UPDATE wp_question_response SET
	time_dim_time_id =
	(SELECT time_id
	FROM wp_time_dim, gwu_session
	WHERE wp_question_response.respondee_dim_respondee_id = gwu_session.SessionID
	AND wp_time_dim.date = gwu_session.SurveyTakenDate)";
	
	$sql_5="UPDATE wp_question_response SET location_dim_location_id =
	(SELECT location_id
	FROM wp_location_dim, gwu_session
	WHERE wp_question_response.respondee_dim_respondee_id = gwu_session.SessionID
	AND wp_location_dim.country = gwu_session.Country
	AND wp_location_dim.city = gwu_session.city);";
	
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	
	$wpdb->query($sql_0);
	$wpdb->query($sql_1);
	$wpdb->query($sql_2);
	$wpdb->query($sql_3);
	$wpdb->query($sql_4);
	$wpdb->query($sql_5);
	
	echo '<script type="text/javascript">self.location="/wp-admin/admin.php?page=questionpeach-analyzer&action=refresh";</script>';
}

function reset_ui(){
	echo '<script type="text/javascript">self.location="/wp-admin/admin.php?page=questionpeach-analyzer";</script>';
}
//////////////////////////////// analyzer_manage_my_filters //////////////////////////////////////
function analyzer_manage_my_filters($saved_user_id, $saved_user_name)
{
	global $wpdb;
	
	$curr_user_analyzer=$saved_user_id;
	
	
	$sql_0="SELECT distinct save_datetime, name
	FROM wp_filters 
	WHERE user_id =$curr_user_analyzer";
	
	$result=mysql_query( $sql_0);
	$filter_form = '<form><table class="table"><tr class="tr1"><td><center><b>'.strtoupper($saved_user_name->user_login).'\'s Filter </b></center></td></tr><tr class="tr1"><td>';
	
	if(mysql_num_rows($result) > 0){
		while($rows = mysql_fetch_assoc($result)) {
			$filter_form= $filter_form.'<input type="checkbox" name="filter[]" value="'.$rows['name'].'">'.$rows['name'].' - ' .$rows['save_datetime'].'<br>';
		}
	} else {
		$filter_form= $filter_form.'There is no saved filter for '.$saved_user_name->user_login.'.';
	}
	$filter_form= $filter_form.'</td></tr>
	<tr class="tr1"><td><center><input class="button-primary" type="submit" name="Delete" value="Delete" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input class="button-primary" type="submit" name="Generate" value="Generate Report" /></center>
	
	</td></tr></table></form>';
	
	return $filter_form; 
}

/////////////////////////////analyzer_generateReport.sql///////////////////
function analyzer_generateReport($user_id, $user_name, $filter)
{
	$msg = '<h3><center>QuestionPeach Analyzer</center></h3><table class="table">';
	foreach ($filter as &$name) {
		
		$result = mysql_query("select * from wp_filters where name = '$name'");
		while($rows = mysql_fetch_assoc($result)) {
			$questionnaire = $rows['questionnaire_id'];
			$question = $rows['question_id'];
			$location = $rows['location_id'];
			$responder = $rows['respondee_id'];
			$start = $rows['start_date'];
			$end = $rows['end_date'];
			
			$msg = $msg.'<tr class="tr1"><td class="td">'.execute($questionnaire,$question,$location,$responder, $start, $end).'</td></tr>';
			
		}
		
	}
	$msg = $msg.'</table>';
	return $msg;
}

function deleteFilter($user_id, $user_name, $filter){
	array_map ('mysql_real_escape_string', $filter);
	//implode will concatenate array values into a string divided by commas
	$ids = "'" . implode("','", $filter) . "'";
	//building query
	$sql = "DELETE FROM wp_filters WHERE name IN ($ids) and user_id = $user_id";
	//running query
	mysql_query($sql);
	
	echo'<script type="text/javascript">self.location="/wp-admin/admin.php?page=questionpeach-analyzer&manageFilters=Manage+Filters";</script>';
	
}
//////////////////////////////// analyzer_save_user_report_params //////////////////////////////////////
function analyzer_save_user_report_params($saved_user_analyzer, $saved_questionnaire, $saved_question, $saved_location,
	$saved_responder, $saved_start_date_id, $saved_end_date_id, $filterName)
{
	$query="select * from wp_filters where user_id = $saved_user_analyzer and name = '$filterName'";
	$result = mysql_query($query);
	
	$rows = mysql_num_rows($result);
	if (strlen($filterName)==0){
		echo '<script type="text/javascript">alert("Please enter the filter name.");</script>
		<script type="text/javascript">self.location="/wp-admin/admin.php?page=questionpeach-analyzer&questionnaire='.$saved_questionnaire.'&question='.$saved_question
		.'&location='.$saved_location.'&responder='.$saved_responder.'&start='.$saved_start_date_id.'&end='.$saved_end_date_id.'";</script>';
	}else if($rows == 0){
		global $wpdb;
		
		$curr_user_analyzer=$saved_user_analyzer;
		$curr_questionnaire=$saved_questionnaire;
		$curr_question=$saved_question;
		$curr_location=$saved_location;
		$curr_responder=$saved_responder;
		$curr_start_date_id=$saved_start_date_id;
		$curr_end_date_id=$saved_end_date_id;
		
		
		if($curr_location==0 || $curr_location=="")
		{
			$curr_location=null;
		}
		
		if($curr_responder==0 || $curr_responder=="")
		{
			$curr_responder=null;
		}
		
		if($curr_start_date_id=='0000-00-00' || $curr_start_date_id=="")
		{
			$curr_start_date_id=null;
		}
		
		if($curr_end_date_id=='0000-00-00' || $curr_end_date_id=="")
		{
			$curr_end_date_id=null;
		}
		
		$date = date('Y-m-d H:i:s');
		$sql_0="INSERT INTO wp_filters (save_datetime, user_id, questionnaire_id, question_id, location_id, respondee_id, start_date, end_date, name)
		VALUES('$date', '$curr_user_analyzer', '$curr_questionnaire', '$curr_question', '$curr_location', '$curr_responder', 
		'$curr_start_date_id', '$curr_end_date_id', '$filterName')";
		
		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		
		$wpdb->query($sql_0);  
		analyzer_remove_nulls();
		echo '<script type="text/javascript">self.location="/wp-admin/admin.php?page=questionpeach-analyzer&questionnaire='.$saved_questionnaire.'&question='.$saved_question
		.'&location='.$saved_location.'&responder='.$saved_responder.'&start='.$saved_start_date_id.'&end='.$saved_end_date_id.'&action=save+filter";</script>';
	} else {
		echo '<script type="text/javascript">alert("The filter name entered already in use. please choose a different name. ");</script>
		<script type="text/javascript">self.location="/wp-admin/admin.php?page=questionpeach-analyzer&questionnaire='.$saved_questionnaire.'&question='.$saved_question
		.'&location='.$saved_location.'&responder='.$saved_responder.'&start='.$saved_start_date_id.'&end='.$saved_end_date_id.'";</script>';
	}
}

/////////////////////////////analyzer_remove_nulls /////////////////////////////////////////////////
function analyzer_remove_nulls()
{
	global $wpdb;
	
	$sql_0="update  wp_filters
	set end_date =null
	where end_date ='0000-00-00'";
	
	$sql_1="update  wp_filters
	set start_date =null
	where start_date ='0000-00-00'";
	
	$sql_2="update  wp_filters
	set respondee_id =null
	where respondee_id =0";
	
	$sql_3="update  wp_filters
	set location_id =null
	where location_id =0";
	
	
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	
	$wpdb->query($sql_0);
	$wpdb->query($sql_1);
	$wpdb->query($sql_2);
	$wpdb->query($sql_3);
}
?>