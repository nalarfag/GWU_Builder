<?php

// Check that file was called from WordPress admin
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();

bulider_drop_table();

function bulider_drop_table() {

    $builder_db = new wpdb(DB_USER, DB_PASSWORD, 'GWU_Builder', DB_HOST);
    $builder_db->query("Drop Table Response");
    $builder_db->query("Drop Table Session");
    $builder_db->query("Drop Table AnswerChoice");
    $builder_db->query("Drop Table Action");
    $builder_db->query("Drop Table Question");
    $builder_db->query("Drop Table Questionnaire");


    // Connect to MySQL
    $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD, TRUE);
    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }


    // Query to drop database 
    $sql = 'DROP DATABASE GWU_Builder';
    $retval = mysql_query($sql, $link);
    if (!$retval) {
        die('Could not create database: ' . mysql_error());
    }


    mysql_close($link);
}

?>
