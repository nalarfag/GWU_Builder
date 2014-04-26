<?php

include_once dirname(__FILE__) . '/models/GWQuestion.php';
include_once dirname(__FILE__) . '/models/GWQuestionnaire.php';
include_once dirname(__FILE__) . '/models/GWWrapper.php';

if (!defined('GWU_BUILDER_DIR'))
    define('GWU_BUILDER_DIR', WP_PLUGIN_DIR . '\\' . GWU_Builder);

use WordPress\ORM\Model\GWQuestionnaire;
use WordPress\ORM\Model\GWQuestion;
use WordPress\ORM\Model\GWWrapper;

/**
 * Description of GWUQuestionnaireTables
 *
 * Add the tables to the database and view them through short code
 *
 * @author Nada Alarfag , Monisha  
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
                    'CREATE TABLE IF NOT EXISTS  gwu_questionnaire (
            `QuestionnaireID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
              `Title` VARCHAR(200) NOT NULL,
              `Topic` VARCHAR(200) NULL,
              `CreatorName` VARCHAR(50) NOT NULL,
              `AllowMultiple` BOOL NOT NULL,
              `AllowAnnonymous` BOOL NOT NULL,
              `DateCreated` DATE NOT NULL,
              `DateModified` DATETIME NULL,
              `InactiveDate` DATE NULL,
              `IntroText` text NULL,
              `ThankyouText` text NULL,
              `PostId` VARCHAR(200) NULL,
              `PublishFlag` BOOL NULL,
              `PublishDate` DATE NULL,
              `Deleted` BOOL NULL,
              `OwnerId` INTEGER UNSIGNED NULL,
              `EditorId` INTEGER UNSIGNED NULL
              PRIMARY KEY(`QuestionnaireID`)
        )ENGINE = INNODB;';
            $Question_creation_query =
                    'CREATE TABLE IF NOT EXISTS  gwu_question (
              `QuestionnaireID` INTEGER UNSIGNED NOT NULL,
              `QuestSequence` INTEGER UNSIGNED NOT NULL  ,
              `ConditionID` INTEGER UNSIGNED  NULL,
              `QuestionNumber` VARCHAR(10) NOT NULL,
              `AnsType` VARCHAR(100) NULL,
              `Text` text NULL,
              `Mandatory` BOOL NULL,
              `Deleted` BOOL NULL,
              PRIMARY KEY(`QuestSequence`,`QuestionnaireID`),
              FOREIGN KEY(`QuestionnaireID`)
                REFERENCES gwu_questionnaire(`QuestionnaireID`)
                  ON DELETE CASCADE
                  ON UPDATE CASCADE
        )ENGINE = INNODB;';

            $Condition_creation_query =
                    'CREATE TABLE IF NOT EXISTS gwu_condition (
                  `ConditionID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
                  `QuestionnaireID` INTEGER UNSIGNED NOT NULL,
                  `LogicStatement` VARCHAR(100) NULL,
                  `JumpQNoOnFailure` INTEGER UNSIGNED NULL,
                  `JumpQNoOnSuccess` INTEGER UNSIGNED NULL,
                  `Deleted` BOOL NULL,
                  PRIMARY KEY(`ConditionID`),
		 FOREIGN KEY(`QuestionnaireID`,`JumpQNoOnFailure`) REFERENCES gwu_question(`QuestionnaireID`,`QuestSequence`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
		  FOREIGN KEY(`QuestionnaireID`,`JumpQNoOnSuccess`) REFERENCES gwu_question(`QuestionnaireID`,`QuestSequence`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
                ) ENGINE = INNODB';

            // $Set_ForeignKey_Query = 'ALTER TABLE gwu_question ADD CONSTRAINT FOREIGN KEY GWU_Question_FKIndex2 (`ConditionID`) references gwu_condition (`ConditionID`);';

            $Action_creation_query =
                    'CREATE TABLE IF NOT EXISTS  gwu_action (
           `ActionID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
              `QuestSequence` INTEGER UNSIGNED NULL,
              `QuestionnaireID` INTEGER UNSIGNED NOT NULL,
              `ActionType` VARCHAR(20) NULL,
              `LinkToAction` VARCHAR(100) NULL,
              `Duration` TIME NULL,
              `Sequence` INTEGER UNSIGNED NULL,
              `Content` text NULL,
              `Deleted` BOOL NULL,
              PRIMARY KEY(`ActionID`),
	      FOREIGN KEY(`QuestionnaireID`,`QuestSequence`)
		REFERENCES gwu_question(`QuestionnaireID`,`QuestSequence`)
                  ON DELETE CASCADE
                  ON UPDATE CASCADE
        ) ENGINE = INNODB;';
            $AnswerChoice_creation_query =
                    'CREATE TABLE IF NOT EXISTS  gwu_answerChoice (
              `QuestionnaireID` INTEGER UNSIGNED NOT NULL,
              `QuestSequence` INTEGER UNSIGNED NOT NULL,
              `OptionNumber` INTEGER UNSIGNED NOT NULL,
	      `AnsValue` VARCHAR(255) NULL,
              `Deleted` BOOL NULL,
              PRIMARY KEY(`QuestionnaireID`, `QuestSequence`, `OptionNumber`),
	      FOREIGN KEY(`QuestionnaireID`,`QuestSequence`)
		REFERENCES gwu_question(`QuestionnaireID`,`QuestSequence`)
                ON DELETE CASCADE
                  ON UPDATE CASCADE
        ) ENGINE = INNODB;
';
            $query = 'ALTER TABLE  `gwu_answerChoice` ADD INDEX (  `OptionNumber` );';
            $Flag_creation_query =
                    '
                    CREATE TABLE IF NOT EXISTS  gwu_flag (
          `FlagID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
              `OptionNumber` INTEGER UNSIGNED NOT NULL,
              `QuestSequence` INTEGER UNSIGNED NOT NULL,
              `QuestionnaireID` INTEGER UNSIGNED NOT NULL,
              `FlagName` VARCHAR(20) NOT NULL,
              `FlagValue` VARCHAR(20) NOT NULL,
              `Deleted` BOOL NULL,
              PRIMARY KEY(`FlagID`),
	      FOREIGN KEY(`QuestionnaireID`, `QuestSequence`,`OptionNumber`)
		REFERENCES gwu_answerChoice(`QuestionnaireID`, `QuestSequence`,`OptionNumber`)
                  ON DELETE CASCADE
                  ON UPDATE CASCADE
        ) ENGINE = INNODB';

            $Session_creation_query =
                    'CREATE TABLE IF NOT EXISTS  gwu_session (
             `SessionID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
              `UserName` VARCHAR(100) NULL,
              `IP` VARCHAR(50) NULL,
              `City` VARCHAR(50) NULL,
              `Country` VARCHAR(50) NULL,
              `Duration` TIME NULL,
              `SurveyTakenDate` DATE NULL,
              `SurveyCompleted` BOOL NULL,
              PRIMARY KEY(`SessionID`)
        ) ENGINE = INNODB;';
            $Response_creation_query =
                    'CREATE TABLE IF NOT EXISTS gwu_response (
            `ResponseID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
              `QuestSequence` INTEGER UNSIGNED NOT NULL,
              `SessionID` INTEGER UNSIGNED NOT NULL,
              `QuestionnaireID` INTEGER UNSIGNED NOT NULL,
              `OptionNumber` INTEGER UNSIGNED NULL,
              `ResponseType` VARCHAR(100) NOT NULL,
              `ResponseContent` text NULL,
              `CodeToProcessResponse` text NULL,
              `ProcessingResult` text NULL,
              PRIMARY KEY(`ResponseID`),
	       FOREIGN KEY(`QuestionnaireID`, `QuestSequence`)
		REFERENCES gwu_question(`QuestionnaireID`, `QuestSequence`)
                  ON DELETE CASCADE
                  ON UPDATE CASCADE,
              FOREIGN KEY( `OptionNumber`)
                REFERENCES gwu_answerChoice(`OptionNumber`)
                  ON DELETE CASCADE
                  ON UPDATE CASCADE,
              FOREIGN KEY(`SessionID`)
                REFERENCES gwu_session(`SessionID`)
                  ON DELETE CASCADE
                  ON UPDATE CASCADE
        ) ENGINE = INNODB;';


            //excute the SQLs
            //excute the SQLs
            $wpdb->query($Questionnaire_creation_query);
            $wpdb->query($Question_creation_query);
            $wpdb->query($Condition_creation_query);
            //  $wpdb->query($Set_ForeignKey_Query);
            $wpdb->query($Action_creation_query);
            $wpdb->query($AnswerChoice_creation_query);
            $wpdb->query($query);
            $wpdb->query($Session_creation_query);
            $wpdb->query($Flag_creation_query);
            $wpdb->query($Response_creation_query);

            //    $this->Questionnaire_insert_sample();
            //  $this->Flag_Condition_Insert();
        }

// Function to insert data to the table
        function Questionnaire_insert_sample() {
            // Prepare SQL query to insert the data
            // using received table prefix
            //create an array
            //foreach: loop
            //wpdb:excute the array

            global $wpdb;
            $Insert_queries = array(
                "insert into gwu_questionnaire(QuestionnaireID,Title,Topic,CreatorName,AllowMultiple,AllowAnnonymous,DateCreated,DateModified,InactiveDate,IntroText,ThankyouText,PostId,PublishFlag,PublishDate,Deleted)
values(1,'University Student Graduate','Education','Tejasvi',True,True,'2014-04-04','2014-04-04','2016-08-18','Welcome','Thank You',NULL,NULL,NULL,False)",
                "insert into gwu_questionnaire(QuestionnaireID,Title,Topic,CreatorName,AllowMultiple,AllowAnnonymous,DateCreated,DateModified,InactiveDate,IntroText,ThankyouText,PostId,PublishFlag,PublishDate,Deleted)
values(2,'Employee Job Satisfaction','Employee Feedback','Ashley',True,True,'2013-03-16','2013-05-25','2018-04-20','Welcome','Thank You',NULL,NULL,NULL,False)",
                "insert into gwu_questionnaire(QuestionnaireID,Title,Topic,CreatorName,AllowMultiple,AllowAnnonymous,DateCreated,DateModified,InactiveDate,IntroText,ThankyouText,PostId,PublishFlag,PublishDate,Deleted)
values(3,'Non-Profit Volunteer','Volunteer Feedback','Sachin',True,True,'2014-03-22','2014-03-22','2020-12-31','Welcome','Thank You',NULL,NULL,NULL,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(1,1,NULL,'1','Multiple Choice, Single Value','How effective was the teaching within your major at this university?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(1,2,NULL,'2','Multiple Choice, Single Value','How effective was the teaching outside your major at this university?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(1,3,NULL,'3','Text Box','What is your most favorite experience in this university?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(1,4,NULL,'4','Text Box','What is your least favorite experience in this university?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(1,5,NULL,'5','Multiple Choice, Single Value','How likely are you to recommend this university to others?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(2,1,NULL,'i','Multiple Choice, Single Value','How challenging is your job?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(2,2,NULL,'ii','Multiple Choice, Multiple Value','What were your major responsibilities you handled?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(2,3,NULL,'iii','Text Box','Provide any changes you would like to see in your working environment to make your work comfortable',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(2,4,NULL,'iv','NSP','How would you rate your employer(on the scale of 10)?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(2,5,NULL,'v','Multiple Choice, Single Value','How likely are you to look for another job outside the company?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(3,1,NULL,'a','Multiple Choice, Single Value','How meaningful was the volunteer work you did for this organization?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(3,2,NULL,'b','Multiple Choice, Single Value','How easy was it to get along with the other volunteers at this organization?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(3,3,NULL,'c','Text Box',' In a typical month, about how many hours do you volunteer?',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(3,4,NULL,'d','Text Box','Give breif discription of your responsibilities',False,False)",
                "insert into gwu_question(QuestionnaireID,QuestSequence,ConditionID,QuestionNumber,AnsType,Text,Mandatory,Deleted)
values(3,5,NULL,'e','Multiple Choice, Multiple Value','Select the tasks you performed from the below list',False,False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,1,1,'Extremely Effective',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,1,2,'Moderately effective',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,1,3,'Slightly effective',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,1,4,'Not at all effective',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,2,1,'Extremely Effective',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,2,2,'Moderately effective',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,2,3,'Not at all effective',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,2,4,'Not applicable',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,5,1,'Extremely likely',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,5,2,'Moderately likely',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,5,3,'Slightly likely',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(1,5,4,'Not at all likely',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,1,1,'Extremely challenging',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,1,2,'Moderately challenging',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,1,3,'Slightly challenging',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,1,4,'Not at all challenging',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,2,1,'Developing',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,2,2,'Testing',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,2,3,'Analysing',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,2,4,'Intergrating',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,2,5,'Managing',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,2,6,'Reporting',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,2,7,'Other',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,4,1,'1',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,4,2,'2',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,4,3,'3',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,4,4,'4',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,4,5,'5',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,4,6,'6',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,4,7,'7',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,4,8,'8',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,4,9,'9',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,4,10,'10',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,5,1,'Extremely likely',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,5,2,'Moderately likely',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,5,3,'Slightly likely',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(2,5,4,'Not at all likely',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,1,1,'Extremely Meaningful',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,1,2,'Very Meaningful',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,1,3,'Moderate Meaningful',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,1,4,'Slightly Meaningful',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,1,5,'Not at all Meaningful',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,2,1,'Extremely easy',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,2,2,'Very easy',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,2,3,'Moderate easy',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,2,4,'Slightly easy',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,2,5,'Not at all easy',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,5,1,'Served Homeless',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,5,2,'Managed any event',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,5,3,'Participated in Hunger Strike',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,5,4,'Participated in Fund Raising Events',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,5,5,'Other',False)",
                "insert into gwu_answerChoice(QuestionnaireID,QuestSequence,OptionNUmber,AnsValue,Deleted) values(3,5,6,'None',False)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(1,'Monisha','120.32.255.03','Washington','USA','00:10:00','2014-05-03',True)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(2,'Nada','120.32.255.05','Washington','USA','00:12:00','2014-04-15',True)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(3,null,'120.36.255.17','Virginia','USA', '00:09:00','2014-07-12',True)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(4,null,'120.36.255.17','San Fransisco','USA', '00:20:00','2014-08-15',False)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(5,null,'120.32.255.22','Delhi','India', '00:19:00','2014-09-11',True)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(8,null,'130.45.567.89','Beijing','China', '00:09:00','2013-05-12',True)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(9,'Sunny','176.34.56.234','Las Vegas','USA', '00:12:00','2013-08-24',True)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(10,'Iswarya','135.234.34.56','Bangalore','India', '00:20:00','2014-01-31',True)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(11,null,'234.456.34.67','Arizona','USA','00:07:00','2013-06-18',False)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(6,'Neeraj','120.32.255.03','Dubai','Emirates', '00:19:00','2014-08-08',True)",
                "insert into gwu_session(SessionID,UserName,IP,City,Country,Duration,SurveyTakenDate,SurveyCompleted) 
values(7,'Chandan','120.32.255.05','San Diego','USA', '00:18:00','2014-01-05',True)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(1,1,1,1,1,'Multiple Choice, Single Value','Extremely Effective',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(2,2,1,1,4,'Multiple Choice, Single Value','Not Applicable',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(3,3,1,1,null,'Text Box','Good Library,Efficient Classrooms',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(4,4,1,1,null,'Text Box','N/A',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(5,5,1,1,1,'Multiple Choice, Single Value','Extremly Likely',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(6,1,2,1,2,'Multiple Choice, Single Value','Moderately effective',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(7,2,2,1,2,'Multiple Choice, Single Value','Moderately effective',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(8,3,2,1,null,'Text Box','Good management Good secutiry',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(9,4,2,1,null,'Text Box','Lack of resources',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(10,5,2,1,2,'Multiple Choice, Single Value','Moderately likely',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(11,1,3,1,4,'Multiple Choice, Single Value','Not at all effective',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(12,2,3,1,2,'Multiple Choice, Single Value','Moderately effective',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(13,3,3,1,null,'Text Box','N/A',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(14,4,3,1,null,'Text Box','Lack of good faculty, lack of resourses, not good library',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(15,5,3,1,4,'Multiple Choice, Single Value','Not at all likely',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(16,1,4,1,1,'Multiple Choice, Single Value','Extremely Effective',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(17,2,4,1,1,'Multiple Choice, Single Value','Extremely Effective',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(18,3,4,1,null,'Text Box','Great library,Good food within campus, great,good alumini',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(19,1,5,1,3,'Multiple Choice, Single Value','Slightly Effective',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(20,2,5,1,3,'Multiple Choice, Single Value','Not at all effective',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(21,3,5,1,null,'Text Box','n/a',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(22,4,5,1,null,'Text Box','Not good library',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(23,5,5,1,4,'Multiple Choice, Single Value','Not at all Likely',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(24,1,8,2,1,'Multiple Choice, Single Value','Extremely challenging',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(25,2,8,2,1,'Multiple Choice, Multiple Value','Developing',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(26,2,8,2,2,'Multiple Choice, Multiple Value','Testing',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(27,2,8,2,6,'Multiple Choice, Multiple Value','Reporting',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(28,3,8,2,null,'Text Box','N/A',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(29,4,8,2,9,'NSP','9',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(30,5,8,2,4,'Multiple Choice, Single Value','Not at all likely',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(31,1,9,2,3,'Multiple Choice, Single Value','Slightly challenging',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(32,2,9,2,3,'Multiple Choice, Multiple Value','Managing',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(33,2,9,2,4,'Multiple Choice, Multiple Value','Integrating',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(34,3,9,2,null,'Text Box','Provide night shuttle to drop employees after 9 PM',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(35,4,9,2,3,'NSP','3',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(36,5,9,2,2,'Multiple Choice, Single Value','Moderately likely',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(37,1,10,2,2,'Multiple Choice, Single Value','Moderately challenging',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(38,2,10,2,2,'Multiple Choice, Multiple Value','Testing',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(40,3,10,2,null,'Text Box','N/A',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(41,4,10,2,6,'NSP','6',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(42,5,10,2,2,'Multiple Choice, Single Value','Moderately likely',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(43,1,11,2,4,'Multiple Choice, Single Value','Not at all challenging',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(44,2,11,2,6,'Multiple Choice, Multiple Value','Reporting',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(45,1,6,3,1,'Multiple Choice, Single Value','Extremely Meaningful',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(46,2,6,3,3,'Multiple Choice, Single Value','Moderately Easy',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(47,3,6,3,null,'Text Box','15',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(48,4,6,3,null,'Text Box','I have done receptionist job in old age home',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(49,5,6,3,1,'Multiple Choice, Multi Value','Served Homeless',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(50,5,6,3,5,'Multiple Choice, Multi Value','Other',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(51,1,7,3,4,'Multiple Choice, Single Value','Slightly Meaningful',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(52,2,7,3,4,'Multiple Choice, Single Value','Slightly easy',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(53,3,7,3,null,'Text Box','1',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(54,4,7,3,null,'Text Box','I was given responsibility to make food for homeless',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(55,5,7,3,1,'Multiple Choice, Multi Value','Served Homeless',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(56,5,7,3,5,'Multiple Choice, Multi Value','Other',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(57,1,1,3,3,'Multiple Choice, Single Value','Moderate Meaningful',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(58,2,1,3,2,'Multiple Choice, Single Value','Very easy',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(59,3,1,3,null,'Text Box','22',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(60,4,1,3,null,'Text Box','I was given responsibility to manage all the events of the organization and making the schedule of the volunteers',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(61,5,1,3,2,'Multiple Choice, Multi Value','Managed any Event',null,null)",
                "insert into gwu_response(ResponseID,QuestSequence,SessionID,QuestionnaireID,OptionNumber,ResponseType,ResponseContent,CodeToProcessResponse,ProcessingResult)
values(62,5,1,3,5,'Multiple Choice, Multi Value','Other',null,null)");

            foreach ($Insert_queries as $i => $q) {
                $wpdb->query($q);
            }
        }

// Function to insert data to the flag and condition table
        function Flag_Condition_Insert() {
            // Prepare SQL query to insert the data
            // using received table prefix
            //create an array
            //foreach: loop
            //wpdb:excute the array

            global $wpdb;
            $Insert_flag_condition_data = array(
                "insert into gwu_flag(FlagID,OptionNumber,QuestSequence,QuestionnaireID,FlagName,FlagValue,Deleted) values (1,1,1,1,'F1','1',False)",
                "insert into gwu_flag(FlagID,OptionNumber,QuestSequence,QuestionnaireID,FlagName,FlagValue,Deleted) values (2,4,1,1,'F1','0',False)",
                "insert into gwu_flag(FlagID,OptionNumber,QuestSequence,QuestionnaireID,FlagName,FlagValue,Deleted) values (3,1,2,1,'F2','1',False)",
                "insert into gwu_flag(FlagID,OptionNumber,QuestSequence,QuestionnaireID,FlagName,FlagValue,Deleted) values (4,4,2,1,'F2','0',False)",
                "insert into gwu_flag(FlagID,OptionNumber,QuestSequence,QuestionnaireID,FlagName,FlagValue,Deleted) values (5,8,4,2,'F3','1',False)",
                "insert into gwu_flag(FlagID,OptionNumber,QuestSequence,QuestionnaireID,FlagName,FlagValue,Deleted) values (6,9,4,2,'F3','1',False)",
                "insert into gwu_flag(FlagID,OptionNumber,QuestSequence,QuestionnaireID,FlagName,FlagValue,Deleted) values (7,10,4,2,'F3','1',False)",
                "insert into gwu_flag(FlagID,OptionNumber,QuestSequence,QuestionnaireID,FlagName,FlagValue,Deleted) values (8,0,4,2,'F3','0',False)",
                "insert into gwu_flag(FlagID,OptionNumber,QuestSequence,QuestionnaireID,FlagName,FlagValue,Deleted) values (9,1,4,2,'F3','0',False)",
                "insert into gwu_flag(FlagID,OptionNumber,QuestSequence,QuestionnaireID,FlagName,FlagValue,Deleted) values (10,2,4,2,'F3','0',False)",
                "insert into gwu_condition(ConditionID,QuestionnaireID,LogicStatement,JumpQNoOnFailure,JumpQNoOnSuccess,Deleted) values(1,1,'F1 = 1 and F2 = 1',4,3,False)",
                "insert into gwu_condition(ConditionID,QuestionnaireID,LogicStatement,JumpQNoOnFailure,JumpQNoOnSuccess,Deleted) values(2,2,'F3 = 1',null,5,False)");

            foreach ($Insert_flag_condition_data as $i => $q) {
                $wpdb->query($q);
            }
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
                $output.='      </tbody>
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
            foreach ($questionnaires as $questionnaire) {

                $output .= '<tr>
                <td>' . $questionnaire->get_QuestionnaireID() . '</td>';
                $output .= '<td>' . $questionnaire->get_Title() . '</td></tr>';
            }

            $output .= '</table></div></div>';

            return $output;
        }

        public function displayQuestions() {
            /* $qtnObj = new GWQuestion();
              $qtnObj->set_QuestionnaireID(1);
              $qtnObj->set_Question_Number(111);
              $qtnObj->set_Text("This is a question###?");
              $qtnObj->update(); */

            GWWrapper::saveQuestion(100, 1, "Multi", "SAVED QUESTION", 0);
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
            foreach ($questions as $question) {

                $output .= '<tr>
                <td>' . $question->get_QuestionnaireID() . '</td>';
                $output .= '<td>' . $question->get_Question_Number() . '</td>';
                foreach (GWQuestion::get_primary_key() as $key) {
                    $output .= '<td>' . $question->{$key}() . '</td>';
                }
                $output .= '<td>' . $question->get_Text() . '</td></tr>';
            }

            $output .= '</table></div></div>';

            return $output;
        }

    }

}
?>
