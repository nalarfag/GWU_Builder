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
            $Questions = $Wrapper->listQuestion($QuestionnaireID,true);

            if (empty($Questions)) {
                $nextQuestionNum = 1;
            } else {
                $nextQuestionNum = sizeof($Questions) + 1;
            }

            return $nextQuestionNum;
        }

	  public function shiftQuestionsForAdd($QuestionnaireID,$questionSeq) {
          
              $nextSeq=$this->getNextQuestionNumber($QuestionnaireID);
              $curSeq=$nextSeq-1;
                            global $wpdb;

              while($curSeq>=$questionSeq)
              {
                //save question
                $wpdb->update('gwu_question', array(
                    'QuestSequence' => $nextSeq
                        ), array('QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $curSeq));
              
                 $nextSeq=$curSeq;
	      $curSeq--;
	      }

	  }

	  public function shiftQuestionsForDelete($QuestionnaireID,$questionSeq) {

	      $lastSeq=$this->getNextQuestionNumber($QuestionnaireID)+1;
	      $curSeq=$questionSeq;
	      $nextSeq=$curSeq+1;

			    global $wpdb;


	      while($curSeq<$lastSeq)
	      {
		//save question
		$wpdb->update('gwu_question', array(
		    'QuestSequence' => $curSeq
			), array('QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $nextSeq));

		 $curSeq=$nextSeq;
		$nextSeq++;
              }
              
          }
          
        public function AddNewQuestion() {
            // Place all user submitted values in an array
            $Question_data = array();

            $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
            $answer_type_short = ( isset($_POST['answer_type_short']) ? $_POST['answer_type_short'] : '' );


            if (isset($_POST['close'])) {

                // Redirect the page to the admin form
                wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
                exit;
            }

            $questSequence=( isset($_POST['questionSeq']) ? $_POST['questionSeq'] : '' );
            $Question_data['questionNumber'] = ( isset($_POST['question_Number']) ? $_POST['question_Number'] : '' );
            $Question_data['Text'] = ( isset($_POST['question_text']) ? $_POST['question_text'] : '' );
            $Question_data['AnsType'] = ( isset($_POST['answer_type']) ? $_POST['answer_type'] : '' );
            $Question_data['QuestionnaireID'] = $QuestionnaireID;
            $Question_data['Mandatory'] = ( isset($_POST['Mandatory']) ? $_POST['Mandatory'] : '' );
            $answersChoices = ( isset($_POST['p_choice']) ? $_POST['p_choice'] : '' );
            
            if($questSequence != $this->getNextQuestionNumber($QuestionnaireID))
            {
		$this->shiftQuestionsForAdd($QuestionnaireID,$questSequence);
            }
            
            //save question
            $Wrapper = new GWWrapper();
            $Wrapper->saveQuestion($questSequence, $Question_data['QuestionnaireID'], null, $Question_data['questionNumber'], $Question_data['AnsType'], $Question_data['Text'], $Question_data['Mandatory']);



            $counter = 1;

            if ($answer_type_short == 'multipleS' || $answer_type_short == 'multipleM') {
                foreach ($answersChoices as $choice) {
                    $Wrapper->saveAnswerChoice($QuestionnaireID, $questSequence, $counter, $choice);
                    $counter++;
                }
            } elseif ($answer_type_short == 'NPS') {

                for ($counter; $counter <= 10; $counter++) {


                    $Wrapper->saveAnswerChoice($QuestionnaireID, $questSequence, $counter, $counter);
                }

                $ansValue_Detractor = ( isset($_POST['Detractor']) ? $_POST['Detractor'] : '' );
                $Wrapper->saveAnswerChoice($QuestionnaireID, $questSequence, $counter, $ansValue_Detractor);
                $counter++;

                $ansValue_Promoter = ( isset($_POST['Promoter']) ? $_POST['Promoter'] : '' );
                $Wrapper->saveAnswerChoice($QuestionnaireID, $questSequence, $counter, $ansValue_Promoter);
            }


	    $this->updateQuestionnaireModifedDate($QuestionnaireID);

            if (isset($_POST['save'])) {

                // Redirect the page to the admin form
                wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
                exit;
            } elseif (isset($_POST['saveAdd'])) {
                $nextSeq=$questSequence+1;
                // Redirect the page to the admin form
                wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'new', 'Qid' => $QuestionnaireID, 
                    'qno' => $nextSeq,'type' => $answer_type_short), admin_url('admin.php')));
                exit;
            }

            exit;
        }

        public function QuestionHandler() {
            $questSequence = ( isset($_POST['QuestionSeq']) ? $_POST['QuestionSeq'] : '' );
            $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
          
            if (isset($_POST['add'])) {
               // Redirect the page to the admin form
                wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'new', 'Qid' => $QuestionnaireID,
                            'qno' => $questSequence, 'type' => 'multipleS'
                                ), admin_url('admin.php')));
                exit;
            }
            if (isset($_POST['edit'])) {
                // Redirect the page to the edit form
                wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'editQ', 'Qid' => $QuestionnaireID,
                            'qno' => $questSequence
                                ), admin_url('admin.php')));
                exit;
            }
	   /* if (isset($_POST['delete'])) {

		// $this->DeleteQuestion($questSequence, $QuestionnaireID);
            }
	    *
	    */
            if (isset($_POST['logic'])) {
                echo 'logic';
            }
            if (isset($_POST['addAction'])) {
                echo 'action';
            }
        }

	//show question for admin page function
	public function ViewQuestionsForAdmin($QuestionnaireID) {
            //string to hold the HTML code for output
            $Wrapper = new GWWrapper();
            $questions = $Wrapper->listQuestion($QuestionnaireID);
	    $Questionnaire=$Wrapper->getQuestionnaire($QuestionnaireID);
	    $PublishedFlag=$Questionnaire[0]->get_PublishFlag();

            if ($questions == false)
            {
                 echo' <h2>    <a class="add-new-h2" 
			href="' . add_query_arg(
                    array('page' => 'GWU_add-Questionnaire-page',
                'id' => 'new', 'Qid' => $QuestionnaireID,
                'type' => 'multipleS'), admin_url('admin.php'))
            . '">Add New Question</a></h2>';
                return;
            }

            include_once dirname(__FILE__) . '/views/QuestionViewAdmin.php';
        }


	public function DeleteQuestion()
	{
	    $value=( isset($_POST['value']) ? $_POST['value'] : '' );
            $divID=( isset($_POST['id']) ? $_POST['id'] : '' );
            $divIDArray=explode( '_', $divID ) ;
	    $QuestionnaireID = $divIDArray[1];
             $questSequence = $divIDArray[2];
           
            

	      global $wpdb;

	    $wpdb->delete('gwu_question', array(
		    'QuestSequence' => $questSequence,'QuestionnaireID' => $QuestionnaireID
			));

	      $this->shiftQuestionsForDelete($QuestionnaireID, $questSequence);

	      $this->updateQuestionnaireModifedDate($QuestionnaireID);


	      echo 'question_'.$QuestionnaireID.'_'.$questSequence;
	      die();

	}
        public function EditQuestion() {

            $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
            
            if (isset($_POST['cancel'])) {
                // Redirect the page to the admin form
                wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
                exit;
            }

            if (isset($_POST['save'])) {

                $QuestionSeq = ( isset($_POST['QuestionSeq']) ? $_POST['QuestionSeq'] : '' );
                $type = ( isset($_POST['type']) ? $_POST['type'] : '' );
                $questionNo = ( isset($_POST['question_Number']) ? $_POST['question_Number'] : '' );
                $text = ( isset($_POST['question_text']) ? $_POST['question_text'] : '' );
                $Mandatory = ( isset($_POST['Mandatory']) ? $_POST['Mandatory'] : '' );
                $answersChoices = ( isset($_POST['p_choice']) ? $_POST['p_choice'] : '' );

                global $wpdb;
                //save question
                $wpdb->update('gwu_question', array(
                    'QuestionNumber' => $questionNo,
                    'Text' => $text,
                    'Mandatory' => $Mandatory
                        ), array('QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq));

	       $this->updateQuestionnaireModifedDate($QuestionnaireID);
                $counter = 1;

                if ($type == 'Multiple Choice, Single Value' || $type == 'Multiple Choice, Multiple Value') {

                    foreach ($answersChoices as $choice) {

                        $wpdb->replace('gwu_answerChoice', array(
                            'AnsValue' => $choice, 'QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq,
                            'OptionNumber' => $counter, 'Deleted' => 'false')
                        );


                        $counter++;
                    }
                } elseif ($type == 'NPS') {


                    $ansValue_Detractor = ( isset($_POST['Detractor']) ? $_POST['Detractor'] : '' );
                    $ansValue_Promoter = ( isset($_POST['Promoter']) ? $_POST['Promoter'] : '' );

                    $wpdb->update('gwu_answerChoice', array(
                        'AnsValue' => $ansValue_Detractor), array('QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq,
                        'OptionNumber' => 11)
                    );

                    $wpdb->update('gwu_answerChoice', array(
                        'AnsValue' => $ansValue_Promoter), array('QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq,
                        'OptionNumber' => 12)
                    );
                }
            }
            // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                        'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
            exit;
        }

	public function updateQuestionnaireModifedDate($QuestionnaireID)
	{
	    global $wpdb;
	      $cureentDataTime = date('Y-m-d H:i:s');
		//save question
		$wpdb->update('gwu_questionnaire', array(
		    'DateModified' => $cureentDataTime
			), array('QuestionnaireID' => $QuestionnaireID));
	}
    }

}
?>
