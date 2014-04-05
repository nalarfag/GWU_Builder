<?php

/*
  Plugin Name:  GWU Builder Plugin
  Plugin URI:
  Description: This plugin create the necessory tables for the builder part
 *  of the Questionnaire plugin
  Version: 1.0
  Author: Builder team
  Author URI:
 */
include_once dirname( __FILE__ ) . '/GWUQuestionnaireTables.php';
// Activation Callback
if( class_exists( 'GWUQuestionnaireTables' ) ) {
  $QuestionnaireTables= new GWUQuestionnaireTables();
  register_activation_hook(__FILE__, array(&$QuestionnaireTables, 'Questionnaire_create_table'));
 
}


// Use [show_GWU_Questionnaire_tables] to show data dictionary 
// of the Questionnaire Tables



?>
