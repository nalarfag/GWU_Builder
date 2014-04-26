<?php

/*
  Plugin Name:  GWU Builder Plugin
  Plugin URI:
  Description: This plugin create the necessary tables for the builder part of the Questionnaire plugin, create admin page for adding questionnaire
  Version: 1.4
  Author: Builder team
  Author URI:
 */

include_once dirname(__FILE__) . '/GWUQuestionnaireTables.php';
include_once dirname(__FILE__) . '/GWUQuestionnaireAdmin.php';
include_once dirname(__FILE__) . '/response.php';
require_once 'Questionnaire_List_Table.php';



require_once 'GWUQuestion.php';
require_once 'GWUQuestionnaire.php';
require_once 'GWURole.php';
//require_once 'GWUUser.php';
require_once 'models/GWCondition.php';


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

global $wpdb_allow_null;
$wpdb_allow_null = new wpdbfixed(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
// Activation Callback
if (class_exists('GWUQuestionnaireTables')) {
    $QuestionnaireTables = new GWUQuestionnaireTables();
    register_activation_hook(__FILE__, array(&$QuestionnaireTables, 'Questionnaire_create_table'));
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

?>