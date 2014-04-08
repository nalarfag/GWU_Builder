<?php

include_once dirname( __FILE__ ) . '/models/GWQuestion.php';
include_once dirname( __FILE__ ) . '/models/GWQuestionnaire.php';
include_once dirname( __FILE__ ) . '/models/GWWrapper.php';

if(!defined('GWU_BUILDER_DIR'))
	define('GWU_BUILDER_DIR',WP_PLUGIN_DIR.'\\'.GWU_Builder);

use WordPress\ORM\Model\GWQuestionnaire;
use WordPress\ORM\Model\GWQuestion;
use WordPress\ORM\Model\GWWrapper;
/**
 * Description of GWUQuestionnaireTables
 * 
 * Add the tables to the database and view them through short code
 *
 * @author Nada Alarfag
 */
if (!class_exists('GWUQuestionnaireTables')) {

    class GWUQuestionnaireTables {

        public function __construct() {
            // Register for activation
           // register_activation_hook(__FILE__, array($this, 'Questionnaire_create_table'));

            //Plugin shortcode 
            // Use label [show_GWU_Questionnaire_tables] to show data dictionary
            add_shortcode('show_GWU_Questionnaire_tables', array($this, 'showQuestionnaireTables'));
			add_shortcode('list_GWU_Questionnaires', array($this, 'displayQuestionnaires'));
			add_shortcode('list_GWU_Questions', array($this, 'displayQuestions'));
        }

        // Function to create new database tables
        public function Questionnaire_create_table() {


            global $wpdb;
            // Prepare SQL query to create database table
            // using received table prefix
            $Questionnaire_creation_query =
                    'CREATE TABLE IF NOT EXISTS  GWU_Questionnaire (
                `QuestionnaireID` INT(20) NOT NULL AUTO_INCREMENT ,
                `Title` VARCHAR( 100 ) NOT NULL ,
                `DateCreated` DATE NOT NULL ,
                `Topic` VARCHAR( 100 ) NULL ,
                `AllowAnonymous` BOOL NOT NULL ,
                `AllowMultiple` BOOL NULL ,
                `CreatorName` VARCHAR( 100 ) NOT NULL,
                PRIMARY KEY (`QuestionnaireID`)
            )ENGINE = INNODB;';
            $Question_creation_query =
                    'CREATE TABLE IF NOT EXISTS  GWU_Question (
                `Question_Number` INT NOT NULL,
                `QuestionnaireID` INT NOT NULL ,
                `AnsType` VARCHAR( 100 ) NOT NULL ,
                `Text` VARCHAR( 255 ) NOT NULL ,
                `Mandatory` BOOL NOT NULL ,
                PRIMARY KEY (  `Question_Number` , `QuestionnaireID` ),
                FOREIGN KEY (`QuestionnaireID`) REFERENCES GWU_Questionnaire(`QuestionnaireID`)
            )ENGINE = INNODB;';

            $Action_creation_query =
                    'CREATE TABLE IF NOT EXISTS  GWU_Action (
                `ActionID` INT( 11 ) NOT NULL AUTO_INCREMENT ,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                `Sequence` INT NOT NULL ,
                `ActionType` VARCHAR( 100 ) NOT NULL ,
                `LinkToAction` VARCHAR( 200 ) NULL ,
                `Content` VARCHAR( 255 ) NULL ,
                `Duration` INT( 10 ) NOT NULL ,
                PRIMARY KEY ( `ActionID`),
                FOREIGN KEY (`QuestionnaireID`) REFERENCES GWU_Question(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES GWU_Question(`Question_Number`)
            ) ENGINE = INNODB;';
            $AnswerChoice_creation_query =
                    'CREATE TABLE IF NOT EXISTS  GWU_AnswerChoice (
                `OptionNumber` INT NOT NULL ,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                `AnsValue` VARCHAR( 255 ) NULL ,
                PRIMARY KEY (  `OptionNumber` ,  `QuestionnaireID` ,  `Question_Number` ),
                FOREIGN KEY (`QuestionnaireID`) REFERENCES GWU_Question(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES GWU_Question(`Question_Number`)
            ) ENGINE = INNODB;';
            $Flag_creation_query =
                    'CREATE TABLE IF NOT EXISTS  GWU_Flag (
                `FlagID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                 `FlagName` VARCHAR( 100 ) NOT NULL ,
                `FlagValue` VARCHAR( 100 ) NOT NULL
            ) ENGINE = INNODB;';
            $FlagSet_creation_query =
                    'CREATE TABLE IF NOT EXISTS  GWU_FlagSet (
                `FlagID` INT,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                `OptionNumber` INT NULL ,
                PRIMARY KEY (   `FlagID`,`OptionNumber` ,  `QuestionnaireID` ,  `Question_Number` ),
                 FOREIGN KEY (`QuestionnaireID`) REFERENCES GWU_AnswerChoice(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES GWU_AnswerChoice(`Question_Number`),
                FOREIGN KEY (`OptionNumber`) REFERENCES GWU_AnswerChoice(`OptionNumber`),
                FOREIGN KEY (`FlagID`) REFERENCES GWU_Flag(`FlagID`)
            ) ENGINE = INNODB;';
            $FlagCheck_creation_query =
                    'CREATE TABLE IF NOT EXISTS  GWU_FlagCheck (
                `FlagID` INT,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                PRIMARY KEY (   `FlagID`,  `QuestionnaireID` ,  `Question_Number` ),
                 FOREIGN KEY (`QuestionnaireID`) REFERENCES GWU_Question(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES GWU_Question(`Question_Number`),
                FOREIGN KEY (`FlagID`) REFERENCES GWU_Flag(`FlagID`)
            ) ENGINE = INNODB;';
            $Session_creation_query =
                    'CREATE TABLE IF NOT EXISTS  GWU_Session (
                `SessionID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `User_name` VARCHAR( 100 ) NULL ,
                `SurveyCompleted` BOOL NOT NULL ,
                `Duration` TIME NOT NULL ,
                `SurveyTakenDate` DATE NOT NULL ,
                `IP` VARCHAR( 20 ) NOT NULL ,
                `City` VARCHAR( 50 ) NULL,
                `Country` VARCHAR( 50 ) NULL
            ) ENGINE = INNODB;';
            $Response_creation_query =
                    'CREATE TABLE IF NOT EXISTS GWU_Response (
                `ResponceID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `SessionID` INT NOT NULL ,
                `QuestionnaireID` INT NOT NULL ,
                `Question_Number` INT NOT NULL ,
                `AnswerNumber` INT NULL ,
                `ResponceType` VARCHAR( 100 ) NULL ,
                `ResponceContent` TEXT NULL ,
                `CodeToProcessResponce` TEXT NULL ,
                `ProcessingResult` TEXT NULL,
                FOREIGN KEY (`QuestionnaireID`) REFERENCES GWU_Question(`QuestionnaireID`),
                FOREIGN KEY (`Question_Number`) REFERENCES GWU_Question(`Question_Number`),
                FOREIGN KEY (`AnswerNumber`) REFERENCES GWU_AnswerChoice(`OptionNumber`),
                FOREIGN KEY (`SessionID`) REFERENCES GWU_Session(`SessionID`)
            ) ENGINE = INNODB;';


            //excute the SQLs
            $wpdb->query($Questionnaire_creation_query);
            $wpdb->query($Question_creation_query);
            $wpdb->query($Action_creation_query);
            $wpdb->query($AnswerChoice_creation_query);
            $wpdb->query($Flag_creation_query);
            $wpdb->query($FlagSet_creation_query);
            $wpdb->query($FlagCheck_creation_query);
            $wpdb->query($Session_creation_query);
            $wpdb->query($Response_creation_query);
        }

        //builder show tbl function
        public function showQuestionnaireTables() {
	
			
            //string to hold the HTML code for output
            $output = '<body style="padding-top: 58px;">
		<div id = wrapper>
			<h1>The Builder Database</h1>';


            global $wpdb;

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
		
		public function displayQuestionnaires() {
			$objQair = new GWQuestionnaire();
			//$objQair->set_QuestionnaireID(2);
			$objQair->set_Title("NEW Questionaire");
			$objQair->save();
			$questionnaires = GWQuestionnaire::all();
			$output = '<body style="padding-top: 58px;">
					   <div id = wrapper>
							<h1>List of Questionnaires</h1>';
			$output .=' <br><div class=table>
			<br>
				<table class="print" width="100%" border="1">
					<tr>
						<th>Questionnaire ID</th>
						<th>Title</th>
					</tr>';
			foreach($questionnaires as $questionnaire) {
				
				$output .= '<tr>
				<td>'. $questionnaire->get_QuestionnaireID() .'</td>';
				$output .= '<td>'. $questionnaire->get_Title() .'</td></tr>';
				
			}
			
			$output .= '</table></div></div>';
			
			return $output;
					
			
			
		}
		
		public function displayQuestions() {
			/*$qtnObj = new GWQuestion();
			$qtnObj->set_QuestionnaireID(1);
			$qtnObj->set_Question_Number(111);
			$qtnObj->set_Text("This is a question###?");
			$qtnObj->update();*/
			
			GWWrapper::saveQuestion(100,1,"Multi","SAVED QUESTION",0);
			$questions = GWQuestion::all();
			$output = '<body style="padding-top: 58px;">
					   <div id = wrapper>
							<h1>List of Questions</h1>';
			$output .=' <br><div class=table>
			<br>
				<table class="print" width="100%" border="1">
					<tr>
						<th>Questionnaire ID</th>
						<th>Question ID</th>
						<th>QAID</th>
						<th>QID</th>
						<th>Text</th>
					</tr>';
			foreach($questions as $question) {
				
				$output .= '<tr>
				<td>'. $question->get_QuestionnaireID() .'</td>';
				$output .= '<td>'. $question->get_Question_Number() .'</td>';
				foreach(GWQuestion::get_primary_key() as $key) {
				$output .= '<td>'. $question->{$key}() .'</td>';
				}
				$output .= '<td>'. $question->get_Text() .'</td></tr>';
				
			}
			
			$output .= '</table></div></div>';
			
			return $output;
					
			
			
		}

    }

}
?>
