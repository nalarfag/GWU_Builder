<?php

/*
  Plugin Name:  QuestionPeach 
  Plugin URI:
  Description: Create questionnaires with different types of questions that have some features such as branching and actions. The created questionnaire can be edited, deleted, duplicated, or published. The questionnaires can be executed using the published link by anyone if the questionnaires are anonymous or by registered user if the questionnaires are not anonymous. The plugin offers analysis capabilities like graphical view of results, results based on selected dates or responders or responders’ locations, and create custom made reports based on these filters.
  Version: 1.6
  Author: Builder and Analyzer team
  Author URI:
 */

include_once dirname(__FILE__) . '/GWUQuestionnaireTables.php';
include_once dirname(__FILE__) . '/GWUQuestionnaireAdmin.php';
include_once dirname(__FILE__) . '/response.php';
require_once 'Questionnaire_List_Table.php';
require_once 'PageTemplater.php';
require_once 'ExcludePublishedQuestionnaire.php';

require_once 'GWUQuestion.php';
require_once 'GWUQuestionnaire.php';
require_once 'GWUCondition.php';
require_once 'GWUAction.php';
require_once 'GWURole.php';
require_once 'GWUUsercap.php';
require_once 'GWUCustomform.php';
require_once 'models/GWCondition.php';
require_once 'models/ConditionParser.php';


require_once 'lib/GWBaseModel.php';
require_once 'lib/GWDb.php';
require_once 'lib/GWQuery.php';
require_once 'models/GWComment.php';
require_once 'models/GWPost.php';
require_once 'models/GWPage.php';
require_once 'models/GWUser.php';
require_once 'models/GWQuestionnaire.php';
require_once 'models/GWQuestion.php';
require_once 'models/GWAction.php';
require_once 'models/GWAnswerChoice.php';
require_once 'models/GWFlag.php';
require_once 'models/GWFlagCheck.php';
require_once 'models/GWFlagSet.php';
require_once 'models/GWResponse.php';
require_once 'models/GWSession.php';
require_once 'Analyzer.php';


global $wpdb_allow_null;
$wpdb_allow_null = new wpdbfixed(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
// Activation Callback
if (class_exists('GWUQuestionnaireTables')) {
    $QuestionnaireTables = new GWUQuestionnaireTables();
    register_activation_hook(__FILE__, array(&$QuestionnaireTables, 'Questionnaire_create_table'));
    $Analyzer = new Analyzer();
   // $Analyzer->analyzer_install();
    
/* Runs when plugin is activated */
register_activation_hook(__FILE__,array($Analyzer ,'analyzer_install'));

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, array($Analyzer ,'analyzer_remove') );

}

register_activation_hook(__FILE__, 'CreateQuestionnairsPublishedLists');

function CreateQuestionnairsPublishedLists() {

    $QuestionnairsPublishedList = array(
        'post_title' => 'Published Questionnairs',
        'post_status' => 'publish',
        'post_type' => 'page',
        'comment_status' => 'closed'
    );
    $page_exists = get_page_by_title($QuestionnairsPublishedList['post_title']);

    if ($page_exists == null) {
        // Page doesn't exist, so lets add it
        $insert = wp_insert_post($QuestionnairsPublishedList);
    }
}


// Create tmeplate for Questionnairs

add_action( 'plugins_loaded', array( 'PageTemplater', 'get_instance' ) );

if (class_exists('GWUQuestionnaireAdmin')) {
    $QuestionnaireAdmin = new GWUQuestionnaireAdmin();
    //$QuestionnaireAdmin->GWU_add_menu_links();
}

add_action('wp_enqueue_script', 'load_jquery');

function load_jquery() {
    wp_enqueue_script('jquery');
}

add_action('admin_enqueue_scripts', 'queue_my_admin_scripts');

function queue_my_admin_scripts() {
    wp_enqueue_script('jquery-ui-dialog'); // dependencies
    // A style available in WP
    wp_enqueue_style('wp-jquery-ui-dialog');
}

// Use [show_GWU_Questionnaire_tables] to show data dictionary 
// of the Questionnaire Tables
//Actions to create a session
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

//Start session
function myStartSession() {
    if (!session_id()) {
        session_start();
    }
}

//End session
function myEndSession() {
    session_destroy();
}


/////////////////////// register_activation_hooks ////////////////////////////////
register_activation_hook(__FILE__, 'analyzer_create_tbl');
register_deactivation_hook(__FILE__, 'analyzer_drop_tbl');
register_activation_hook(__FILE__, 'analyzer_migration');
register_activation_hook(__FILE__, 'analyzer_cron_job_activation');
register_deactivation_hook(__FILE__, 'analyzer_cron_job_deactivation');

?>
