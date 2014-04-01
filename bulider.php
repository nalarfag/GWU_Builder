<?php

/*
  Plugin Name:  Builder Plugin
  Plugin URI:
  Description: This plugin create the necessory tables for the builder part
  Version: 1.0
  Author: Builder team
  Author URI:
 */

// Register function to be called when plugin is activated
register_activation_hook(__FILE__, 'builder_activation');

// Activation Callback
function builder_activation() {


    // Connect to MySQL
    $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD, TRUE);
    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }

    // Make builder the current database
    $db_selected = mysql_select_db('builder', $link);

    if (!$db_selected) {
        // If we couldn't, then it either doesn't exist, or we can't see it.
        $sql = 'CREATE DATABASE builder';

        $retval = mysql_query($sql, $link);
        if (!$retval) {
            die('Could not create database: ' . mysql_error());
        }
    }

    mysql_close($link);

    // Create table on main blog in network mode or single blog
    builder_create_table();
}

// Function to create new database table
function builder_create_table() {

    $builder_db = new wpdb(DB_USER, DB_PASSWORD, 'builder', DB_HOST);

    // Prepare SQL query to create database table
    // using received table prefix
    $Questionnaire_creation_query =
            'CREATE TABLE IF NOT EXISTS  Questionnaire (
                `ID` INT(20) NOT NULL AUTO_INCREMENT ,
                `Title` VARCHAR( 100 ) NOT NULL ,
                `Date_created` DATE NOT NULL ,
                `Topic` VARCHAR( 100 ) NULL ,
                `Anonymous` BOOL NOT NULL ,
                `Multiple` BOOL NULL ,
                `creator_name` VARCHAR( 100 ) NOT NULL,
                PRIMARY KEY (`ID`)
            )ENGINE = INNODB;';
    $Question_creation_query =
            'CREATE TABLE IF NOT EXISTS  Question (
                `Question_Number` INT NOT NULL ,
                `QuestionnaireID` INT NOT NULL ,
                `Type` VARCHAR( 100 ) NOT NULL ,
                `Text` VARCHAR( 255 ) NOT NULL ,
                `Mandatory` BOOL NOT NULL ,
                PRIMARY KEY (  `Question_Number` , `QuestionnaireID` ),
                FOREIGN KEY (`QuestionnaireID`) REFERENCES Questionnaire(`ID`)
            )ENGINE = INNODB;';

    $Action_creation_query =
            'CREATE TABLE IF NOT EXISTS  Action (
                `ID` INT( 11 ) NOT NULL ,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                `Sequence` INT NOT NULL ,
                `Type` VARCHAR( 100 ) NOT NULL ,
                `Link` VARCHAR( 200 ) NULL ,
                `Content` VARCHAR( 255 ) NULL ,
                `Duration` INT( 10 ) NOT NULL ,
                PRIMARY KEY (  `ID` ,  `QuestionnaireID` ,  `Question_Number` ),
                FOREIGN KEY (`QuestionnaireID`) REFERENCES Question(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES Question(`Question_Number`)
            ) ENGINE = INNODB;';
    $AnswerChoice_creation_query =
            'CREATE TABLE IF NOT EXISTS  AnswerChoice (
                `OptionNumber` INT NOT NULL ,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                `Value` VARCHAR( 255 ) NULL ,
                PRIMARY KEY (  `OptionNumber` ,  `QuestionnaireID` ,  `Question_Number` ),
                FOREIGN KEY (`QuestionnaireID`) REFERENCES Question(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES Question(`Question_Number`)
            ) ENGINE = INNODB;';
    $Session_creation_query =
            'CREATE TABLE IF NOT EXISTS  Session (
                `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `User_name` VARCHAR( 100 ) NULL ,
                `Completed` BOOL NOT NULL ,
                `Duration` TIME NOT NULL ,
                `Date` DATE NOT NULL ,
                `IP` VARCHAR( 20 ) NOT NULL ,
                `Location` VARCHAR( 100 ) NULL
            ) ENGINE = INNODB;';
    $Response_creation_query =
            'CREATE TABLE IF NOT EXISTS  Response (
                `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `SessionID` INT NOT NULL ,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                `AnswerNumber` INT NULL ,
                `TextTyped` TEXT NULL ,
                `Link` VARCHAR( 255 ) NULL ,
                `Action` TEXT NULL,
                FOREIGN KEY (`QuestionnaireID`) REFERENCES Question(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES Question(`Question_Number`),
                FOREIGN KEY (`AnswerNumber`) REFERENCES AnswerChoice(`OptionNumber`)
            ) ENGINE = INNODB;';

    $builder_db->query($Questionnaire_creation_query);
    $builder_db->query($Question_creation_query);
    $builder_db->query($Action_creation_query);
    $builder_db->query($AnswerChoice_creation_query);
    $builder_db->query($Session_creation_query);
    $builder_db->query($Response_creation_query);
}

?>
