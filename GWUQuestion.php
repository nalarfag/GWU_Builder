<?php

include_once dirname(__FILE__) . '/models/GWQuestion.php';
include_once dirname(__FILE__) . '/models/GWQuestionnaire.php';
include_once dirname(__FILE__) . '/models/GWAnswerChoice.php';
include_once dirname(__FILE__) . '/models/GWWrapper.php';

if (!defined('GWU_BUILDER_DIR'))
    define('GWU_BUILDER_DIR', WP_PLUGIN_DIR . '\\' . GWU_Builder);

use WordPress\ORM\Model\GWWrapper;

/**
 * Description of GWUQuestion
 *
 * @author Nada Alarfag
 */
if (!class_exists('GWUQuestion')) {

    class GWUQuestion {

        public static function getNextQuestionNumber($QuestionnaireID) {
            $Wrapper = new GWWrapper();
            $Questions = $Wrapper->listQuestion($QuestionnaireID);

            if (empty($Questions)) {
                $nextQuestionNum = 1;
            } else {
                $nextQuestionNum = sizeof($Questions) + 1;
            }

            return $nextQuestionNum;
        }

        public function GWUAddNewQuestion() {
            // Place all user submitted values in an array
            $Question_data = array();
             
            $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
            $answer_type_short = ( isset($_POST['answer_type_short']) ? $_POST['answer_type_short'] : '' );


            if(isset($_POST['close']))
            {      
                
            // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                        'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
            exit;
            }
              
           
            $Question_data['questionNumber'] = ( isset($_POST['question_Number']) ? $_POST['question_Number'] : '' );
            $Question_data['Text'] = ( isset($_POST['question_text']) ? $_POST['question_text'] : '' );
            $Question_data['AnsType'] = ( isset($_POST['answer_type']) ? $_POST['answer_type'] : '' );
            $Question_data['QuestionnaireID'] = $QuestionnaireID;
            $Question_data['Mandatory'] = ( isset($_POST['Mandatory']) ? $_POST['Mandatory'] : '' );
            $answersChoices = ( isset($_POST['p_choice']) ? $_POST['p_choice'] : '' );


            $questSequence = $Question_data['questionNumber']; //Temporarily questSequence is same as questionNumber
            //save question
            $Wrapper = new GWWrapper();
            $Wrapper->saveQuestion($questSequence, $Question_data['QuestionnaireID'], $conditionID, $Question_data['questionNumber'], $Question_data['AnsType'], $Question_data['Text'], $Question_data['Mandatory']);

            $counter = 1;

            if ($answer_type_short == 'multipleS' || $answer_type_short == 'multipleM') {
                foreach ($answersChoices as $choice) {
                    $Wrapper->saveAnswerChoice($QuestionnaireID, $Question_data['questionNumber'], $counter, $choice);
                    $counter++;
                }
            } elseif ($answer_type_short == 'NPS') {

                for ($counter; $counter <= 10; $counter++) {


                    $Wrapper->saveAnswerChoice($QuestionnaireID, $Question_data['questionNumber'], $counter, $counter);
                }

                $ansValue_Detractor = ( isset($_POST['Detractor']) ? $_POST['Detractor'] : '' );
                $Wrapper->saveAnswerChoice($QuestionnaireID, $Question_data['questionNumber'], $counter, $ansValue_Detractor);
                $counter++;

                $ansValue_Promoter = ( isset($_POST['Promoter']) ? $_POST['Promoter'] : '' );
                $Wrapper->saveAnswerChoice($QuestionnaireID, $Question_data['questionNumber'], $counter, $ansValue_Promoter);
            }


             if(isset($_POST['save']))
            {      
                
            // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                        'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
            exit;
            }
                 elseif (isset($_POST['saveAdd'])) {
                   
                     add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new', 
                                    'Qid' => $_GET['Qid'],
                                    'type' => 'NPS'),
                                admin_url('admin.php')); 
                   // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                       'id' => 'new', 'Qid' => $QuestionnaireID,'type' => $answer_type_short), admin_url('admin.php')));
            exit;
             }
             
             exit;
        
        }
        
        public function QuestionHandler()
        {
            $questSequence=( isset($_POST['QuestionSeq']) ? $_POST['QuestionSeq'] : '' );
            $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
            $questionType= ( isset($_POST['QuestioType']) ? $_POST['QuestioType'] : '' );
            if(isset($_POST['add']))
            {
                echo 'add';
            }
            if(isset($_POST['edit']))
            {
                    // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                       'id' => 'editQ', 'Qid' => $QuestionnaireID, 
                       'qno'=>$questSequence
                ), admin_url('admin.php')));
            exit;
              
            }
            if(isset($_POST['delete']))
            {
                echo 'delete';
            }
            if(isset($_POST['logic']))
            {
                echo 'logic';
            }
            if(isset($_POST['addAction']))
            {
                echo 'action';
            }
            
        }

         //show question function
        public function ShowQuestions($QuestionnaireID) {
        //string to hold the HTML code for output
            $Wrapper = new GWWrapper();
            $questions = $Wrapper->listQuestion($QuestionnaireID);
         
            if ($questions == false)
                return;
            
            include_once dirname(__FILE__) . '/views/QuestionViewAdmin.php';
        }
        
        public function GWUEditQuestion()
        {
        
            
            $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
          


            if(isset($_POST['cancel']))
            {      
                
            // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                        'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
            exit;
            }
          
            if(isset($_POST['save']))
            {
              
                  $QuestionSeq = ( isset($_POST['QuestionSeq']) ? $_POST['QuestionSeq'] : '' );
                 $type = ( isset($_POST['type']) ? $_POST['type'] : '' );
                 $questionNo= ( isset($_POST['question_Number']) ? $_POST['question_Number'] : '' );
                 $text= ( isset($_POST['question_text']) ? $_POST['question_text'] : '' );
                 $Mandatory = ( isset($_POST['Mandatory']) ? $_POST['Mandatory'] : '' );
                 $answersChoices = ( isset($_POST['p_choice']) ? $_POST['p_choice'] : '' );
                 
                 global $wpdb;
                 //save question
               $wpdb->update( 'gwu_question', 
                    array( 
		'QuestionNumber' => $questionNo,
		'Text' => $text,
                'Mandatory'=>$Mandatory     
                ), 
                array( 'QuestionnaireID' => $QuestionnaireID, 'QuestSequence'=> $QuestionSeq  ));
               
               $Wrapper = new GWWrapper();
               
               $counter = 1;
             
            if ($type == 'Multiple Choice, Single Value' || $type == 'Multiple Choice, Multiple Value') {
               
                foreach ($answersChoices as $choice) {
                    
                $wpdb->replace( 'gwu_answerChoice', 
                    array( 
		'AnsValue' => $choice , 'QuestionnaireID' => $QuestionnaireID, 'QuestSequence'=> $QuestionSeq ,
                    'OptionNumber'=>$counter , 'Deleted'=>'false')
                );

               
                    $counter++;
                }
            } elseif ($type == 'NPS') {


                $ansValue_Detractor = ( isset($_POST['Detractor']) ? $_POST['Detractor'] : '' );
                 $ansValue_Promoter = ( isset($_POST['Promoter']) ? $_POST['Promoter'] : '' );

                 $wpdb->update( 'gwu_answerChoice', 
                    array( 
		'AnsValue' => $ansValue_Detractor ),
                         array('QuestionnaireID' => $QuestionnaireID, 'QuestSequence'=> $QuestionSeq ,
                    'OptionNumber'=>11 )
                );

                 $wpdb->update( 'gwu_answerChoice', 
                    array( 
		'AnsValue' => $ansValue_Promoter ),
                         array('QuestionnaireID' => $QuestionnaireID, 'QuestSequence'=> $QuestionSeq ,
                    'OptionNumber'=>12 )
                );

            }

               
            }
             // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                        'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
              exit;
  
        }

    }

}
?>
