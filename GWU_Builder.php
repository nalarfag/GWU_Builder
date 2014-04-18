<?php

/*
  Plugin Name:  GWU Builder Plugin
  Plugin URI:
  Description: This plugin create the necessory tables for the builder part of the Questionnaire plugin, create admin page for adding questionnaire
  Version: 1.1
  Author: Builder team
  Author URI:
 */

include_once dirname( __FILE__ ) . '/GWUQuestionnaireTables.php';
include_once dirname( __FILE__ ) . '/GWUQuestionnaireAdmin.php';
include_once dirname(__FILE__) .'/response.php';
require_once 'GWUQuestion.php';
//require_once 'models/GWCondition.php';


require_once 'lib/GWBaseModel.php';
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
//require_once 'views/mutlipleS.php';
//require_once 'views/Template.php';
// Activation Callback
if( class_exists( 'GWUQuestionnaireTables' ) ) {
  $QuestionnaireTables= new GWUQuestionnaireTables();
  register_activation_hook(__FILE__, array(&$QuestionnaireTables, 'Questionnaire_create_table'));
 
}

 //$QuestionnaireAdmin= new GWUQuestionnaireAdmin();

if( class_exists( 'GWUQuestionnaireAdmin' ) ) {
  $QuestionnaireAdmin= new GWUQuestionnaireAdmin();
 //$QuestionnaireAdmin->GWU_add_menu_links();
}

add_action( 'wp_enqueue_script', 'load_jquery' );
function load_jquery() {
    wp_enqueue_script( 'jquery' );
}

add_action( 'admin_enqueue_scripts', 'queue_my_admin_scripts');

function queue_my_admin_scripts() {
    wp_enqueue_script (  'jquery-ui-dialog'); // dependencies
    // A style available in WP
    wp_enqueue_style (  'wp-jquery-ui-dialog');
}
// Use [show_GWU_Questionnaire_tables] to show data dictionary 
// of the Questionnaire Tables

//Actions to create a session
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

//Start session
function myStartSession() {
	if(!session_id()) {
        session_start();
    }
}

//End session
function myEndSession() {
	session_destroy();
}

?>
