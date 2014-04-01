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

// Function to create new database tables
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

//Plugin shortcode 
// Use label [show_builder_tables] to show data dictionary

add_shortcode('show_builder_tables', function() {
            return show_builder_tables();
        });

//builder show tbl function
function show_builder_tables() {
    //string to hold the HTML code for output
    $output = '<body style="padding-top: 58px;">
		<div id = wrapper>
			<h1>The Builder Database</h1>';


    $builder_db = new wpdb(DB_USER, DB_PASSWORD, 'builder', DB_HOST);
    // $tables=$builder_db->get_results("SHOW  TABLES FROM  builder");
    $tables = $builder_db->get_results("select TABLE_NAME from INFORMATION_SCHEMA.TABLES where
          TABLE_SCHEMA = 'builder' order by create_time");


    foreach ($tables as $table) {

        //$tableName=$table->Tables_in_builder;
        $tableName = $table->TABLE_NAME;

        $columns = $builder_db->get_results("SHOW COLUMNS FROM " . $tableName . " ");


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

//Plugin shortcode 
// Use label [builder_show_tables] to show data dictionary

add_shortcode('builder_show_tables', function() {
            return builder_show_tbl();
        });

//builder show tbl function
function builder_show_tbl() {
    $table_html =
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html lang="en" dir="ltr" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<body style="padding-top: 58px;">
		<div id = wrapper>
			<h1>The Builder Database</h1>
			<div class=table>
				<h2>Action</h2>
				Table comments: action
				<br>
				<br>
				<table class="print" width="100%" border="1">
					<tbody>
						<tr>
							<th width="50">Column</th>
							<th width="80">Type</th>
							<th width="40">Null</th>
							<th width="70">Default</th>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> ID </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> QuestionnaireID </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> Question_Number </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> Sequence </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="odd marked">
							<td nowrap="nowrap"> Type </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(100)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> Link </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(200)</td>
							<td>Yes</td>
							<td nowrap="nowrap"><i>NULL</i></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> Content </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(255)</td>
							<td>Yes</td>
							<td nowrap="nowrap"><i>NULL</i></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> Duration </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(10)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class=table>
				<h2>Answerchoice</h2>
				Table comments: answerchoice
				<br>
				<br>
				<table class="print" width="100%" border="1">
					<tbody>
						<tr>
							<th width="50">Column</th>
							<th width="80">Type</th>
							<th width="40">Null</th>
							<th width="70">Default</th>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> OptionNumber </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> QuestionnaireID </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> Question_Number </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> Value </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(255)</td>
							<td>Yes</td>
							<td nowrap="nowrap"><i>NULL</i></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class=table>
				<h2>Question</h2>
				Table comments: question
				<br>
				<br>
				<table class="print" width="100%" border="1">
					<tbody>
						<tr>
							<th width="50">Column</th>
							<th width="80">Type</th>
							<th width="40">Null</th>
							<th width="70">Default</th>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> Question_Number </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap">QuestionnaireID</td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> Type </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(100)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> Text </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(255)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> Mandatory </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">tinyint(1)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class=table>
				<h2>Questionnaire</h2>
				Table comments: questionnaire
				<br>
				<br>
				<table class="print" width="100%" border="1">
					<tbody>
						<tr>
							<th width="50">Column</th>
							<th width="80">Type</th>
							<th width="40">Null</th>
							<th width="70">Default</th>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> ID </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(20)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> Title </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(100)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> Date_created </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">date</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> Topic </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(100)</td>
							<td>Yes</td>
							<td nowrap="nowrap"><i>NULL</i></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> Anonymous </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">tinyint(1)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> Multiple </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">tinyint(1)</td>
							<td>Yes</td>
							<td nowrap="nowrap"><i>NULL</i></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> creator_name </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(100)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class=table>
			   <h2>Response</h2>
				Table comments: response
				<br>
				<br>
				<table class="print" width="100%" border="1">
					<tbody>
						<tr>
							<th width="50">Column</th>
							<th width="80">Type</th>
							<th width="40">Null</th>
							<th width="70">Default</th>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> ID </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> SessionID </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> QuestionnaireID </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> Question_Number </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>No</td>
							<td nowrap="nowrap"></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> AnswerNumber </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
							<td>Yes</td>
							<td nowrap="nowrap"><i>NULL</i></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> TextTyped </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">text</td>
							<td>Yes</td>
							<td nowrap="nowrap"><i>NULL</i></td>
						</tr>
						<tr class="odd">
							<td nowrap="nowrap"> Link </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(255)</td>
							<td>Yes</td>
							<td nowrap="nowrap"><i>NULL</i></td>
						</tr>
						<tr class="even">
							<td nowrap="nowrap"> Action </td>
							<td xml:lang="en" dir="ltr" nowrap="nowrap">text</td>
							<td>Yes</td>
							<td nowrap="nowrap"><i>NULL</i></td>
						</tr>
					</tbody>
				</table>
				<div class=table>
					<h2>Session</h2>
					Table comments: session
					<br>
					<br>
					<table class="print" width="100%" border="1">
						<tbody>
							<tr>
								<th width="50">Column</th>
								<th width="80">Type</th>
								<th width="40">Null</th>
								<th width="70">Default</th>
							</tr>
							<tr class="odd">
								<td nowrap="nowrap"> ID </td>
								<td xml:lang="en" dir="ltr" nowrap="nowrap">int(11)</td>
								<td>No</td>
								<td nowrap="nowrap"></td>
							</tr>
							<tr class="even">
								<td nowrap="nowrap"> User_name </td>
								<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(100)</td>
								<td>Yes</td>
								<td nowrap="nowrap"><i>NULL</i></td>
							</tr>
							<tr class="odd">
								<td nowrap="nowrap"> Completed </td>
								<td xml:lang="en" dir="ltr" nowrap="nowrap">tinyint(1)</td>
								<td>No</td>
								<td nowrap="nowrap"></td>
							</tr>
							<tr class="even">
								<td nowrap="nowrap"> Duration </td>
								<td xml:lang="en" dir="ltr" nowrap="nowrap">time</td>
								<td>No</td>
								<td nowrap="nowrap"></td>
							</tr>
							<tr class="odd">
								<td nowrap="nowrap"> Date </td>
								<td xml:lang="en" dir="ltr" nowrap="nowrap">date</td>
								<td>No</td>
								<td nowrap="nowrap"></td>
							</tr>
							<tr class="even">
								<td nowrap="nowrap"> IP </td>
								<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(20)</td>
								<td>No</td>
								<td nowrap="nowrap"></td>
							</tr>
							<tr class="odd">
								<td nowrap="nowrap"> Location </td>
								<td xml:lang="en" dir="ltr" nowrap="nowrap">varchar(100)</td>
								<td>Yes</td>
								<td nowrap="nowrap"><i>NULL</i></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
	</body>
</html> ';
    echo $table_html;
}

?>
