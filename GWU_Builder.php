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

// Register function to be called when plugin is activated
register_activation_hook(__FILE__, 'builder_activation');

// Activation Callback
function builder_activation() {

/*
    // Connect to MySQL
    $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD, TRUE);
    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }

    // Make builder the current database
    $db_selected = mysql_select_db('GWU_Builder', $link);

    if (!$db_selected) {
        // If we couldn't, then it either doesn't exist, or we can't see it.
        $sql = 'CREATE DATABASE GWU_Builder';

        $retval = mysql_query($sql, $link);
        if (!$retval) {
            die('Could not create database: ' . mysql_error());
        }
    }

    mysql_close($link);
*/
    // Create table on main blog in network mode or single blog
    builder_create_table();
}

// Function to create new database tables
function builder_create_table() {

 //   global $builder_db = new wpdb(DB_USER, DB_PASSWORD, 'GWU_Builder', DB_HOST);

    global $wpdb;
    // Prepare SQL query to create database table
    // using received table prefix
    $Questionnaire_creation_query =
            'CREATE TABLE IF NOT EXISTS  GWU_Questionnaire (
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
            'CREATE TABLE IF NOT EXISTS  GWU_Question (
                `Question_Number` INT NOT NULL ,
                `QuestionnaireID` INT NOT NULL ,
                `Type` VARCHAR( 100 ) NOT NULL ,
                `Text` VARCHAR( 255 ) NOT NULL ,
                `Mandatory` BOOL NOT NULL ,
                PRIMARY KEY (  `Question_Number` , `QuestionnaireID` ),
                FOREIGN KEY (`QuestionnaireID`) REFERENCES GWU_Questionnaire(`ID`)
            )ENGINE = INNODB;';

    $Action_creation_query =
            'CREATE TABLE IF NOT EXISTS  GWU_Action (
                `ID` INT( 11 ) NOT NULL ,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                `Sequence` INT NOT NULL ,
                `Type` VARCHAR( 100 ) NOT NULL ,
                `Link` VARCHAR( 200 ) NULL ,
                `Content` VARCHAR( 255 ) NULL ,
                `Duration` INT( 10 ) NOT NULL ,
                PRIMARY KEY (  `ID` ,  `QuestionnaireID` ,  `Question_Number` ),
                FOREIGN KEY (`QuestionnaireID`) REFERENCES GWU_Question(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES GWU_Question(`Question_Number`)
            ) ENGINE = INNODB;';
    $AnswerChoice_creation_query =
            'CREATE TABLE IF NOT EXISTS  GWU_AnswerChoice (
                `OptionNumber` INT NOT NULL ,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                `Value` VARCHAR( 255 ) NULL ,
                PRIMARY KEY (  `OptionNumber` ,  `QuestionnaireID` ,  `Question_Number` ),
                FOREIGN KEY (`QuestionnaireID`) REFERENCES GWU_Question(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES GWU_Question(`Question_Number`)
            ) ENGINE = INNODB;';
    $Session_creation_query =
            'CREATE TABLE IF NOT EXISTS  GWU_Session (
                `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `User_name` VARCHAR( 100 ) NULL ,
                `Completed` BOOL NOT NULL ,
                `Duration` TIME NOT NULL ,
                `Date` DATE NOT NULL ,
                `IP` VARCHAR( 20 ) NOT NULL ,
                `Location` VARCHAR( 100 ) NULL
            ) ENGINE = INNODB;';
    $Response_creation_query =
            'CREATE TABLE IF NOT EXISTS  GWU_Response (
                `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `SessionID` INT NOT NULL ,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                `AnswerNumber` INT NULL ,
                `TextTyped` TEXT NULL ,
                `Link` VARCHAR( 255 ) NULL ,
                `Action` TEXT NULL,
                FOREIGN KEY (`QuestionnaireID`) REFERENCES GWU_Question(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES GWU_Question(`Question_Number`),
                FOREIGN KEY (`AnswerNumber`) REFERENCES GWU_AnswerChoice(`OptionNumber`)
            ) ENGINE = INNODB;';

   // $builder_db->query($Questionnaire_creation_query);
    //$builder_db->query($Question_creation_query);
   // $builder_db->query($Action_creation_query);
    //$builder_db->query($AnswerChoice_creation_query);
    //$builder_db->query($Session_creation_query);
   // $builder_db->query($Response_creation_query);
    
    $wpdb->query($Questionnaire_creation_query);
    $wpdb->query($Question_creation_query);
    $wpdb->query($Action_creation_query);
    $wpdb->query($AnswerChoice_creation_query);
    $wpdb->query($Session_creation_query);
    $wpdb->query($Response_creation_query);
}

//Plugin shortcode 
// Use label [show_GWU_builder_tables] to show data dictionary

add_shortcode('show_GWU_builder_tables', function() {
            return show_builder_tables();
        });

//builder show tbl function
function show_builder_tables() {
    //string to hold the HTML code for output
    $output = '<body style="padding-top: 58px;">
		<div id = wrapper>
			<h1>The Builder Database</h1>';


   // $builder_db = new wpdb(DB_USER, DB_PASSWORD, 'GWU_Builder', DB_HOST);
    global $wpdb;
    // $tables=$builder_db->get_results("SHOW  TABLES FROM  builder");
   /* $tables = $builder_db->get_results("select TABLE_NAME from INFORMATION_SCHEMA.TABLES where
          TABLE_SCHEMA = 'GWU_Builder' order by create_time");
    */
    
    $tables = $wpdb->get_results("select TABLE_NAME from INFORMATION_SCHEMA.TABLES where
                                       table_name LIKE 'GWU_%'  order by create_time");
    

    foreach ($tables as $table) {

        //$tableName=$table->Tables_in_builder;
        $tableName = $table->TABLE_NAME;

       // $columns = $builder_db->get_results("SHOW COLUMNS FROM " . $tableName . " ");
                                 
                                 $columns = $wpdb->get_results("SHOW COLUMNS FROM " . $tableName . " ");


        $output .=' <br><div class=table>
            <h2>' . $tableName . '</h2>
            <br>
				<table class="print" width="100%" border="1">
					<tbody>
                                        <tr>
							<th width="50">Column</th>
							<th width="80">Type</th>
							<th width="40">Null</th>
							<th width="70">Primary</th>
						</tr>';
        foreach ($columns as $column) {
            $columnName = $column->Field;
            $columnType = $column->Type;
            $columnNull = $column->Null;
            if (strcmp($column->Key, 'PRI') == 0) {
                $columnPrimary = 'yes';
            } else {
                $columnPrimary = '';
            }

            $output .= '  <tr>
				<td nowrap="nowrap">' . $columnName . '</td>
				<td xml:lang="en" dir="ltr" nowrap="nowrap">' . $columnType . '</td>
				<td>' . $columnNull . '</td>
				<td nowrap="nowrap">' . $columnPrimary . '</td>
		    </tr>';
        }
        $output.='		</tbody>
				</table>
			</div>';
    }

    return $output;
}


?>
