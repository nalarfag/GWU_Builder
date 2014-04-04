<?php

// Check that file was called from WordPress admin
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();

bulider_drop_table();

function bulider_drop_table() {

 

global $wpdb;
    $wpdb->query("Drop Table GWU_Response");
    $wpdb->query("Drop Table GWU_Session");
    $wpdb->query("Drop Table GWU_FlagCheck");
    $wpdb->query("Drop Table GWU_FlagSet");
    $wpdb->query("Drop Table GWU_Flag");
    $wpdb->query("Drop Table GWU_AnswerChoice");
    $wpdb->query("Drop Table GWU_Action");
    $wpdb->query("Drop Table GWU_Question");
    $wpdb->query("Drop Table GWU_Questionnaire");
}

?>
