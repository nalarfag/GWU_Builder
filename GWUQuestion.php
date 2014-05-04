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

        protected $Wrapper;

        public function __construct() {

            $this->Wrapper = new GWWrapper();
        }

        public static function getNextQuestionNumber($QuestionnaireID) {
            $Wrapper = new GWWrapper();
            $Questions = $Wrapper->listQuestion($QuestionnaireID, true);

            if (empty($Questions)) {
                $nextQuestionNum = 1;
            } else {
                $nextQuestionNum = sizeof($Questions) + 1;
            }

            return $nextQuestionNum;
        }

        public function shiftQuestionsForAdd($QuestionnaireID, $questionSeq) {

            $nextSeq = $this->getNextQuestionNumber($QuestionnaireID);
            $curSeq = $nextSeq - 1;
            global $wpdb;

            while ($curSeq >= $questionSeq) {
                //save question
                $wpdb->update('gwu_question', array(
                    'QuestSequence' => $nextSeq
                        ), array('QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $curSeq));

                $nextSeq = $curSeq;
                $curSeq--;
            }
        }

        public function shiftQuestionsForDelete($QuestionnaireID, $questionSeq) {

            $lastSeq = $this->getNextQuestionNumber($QuestionnaireID) + 1;
            $curSeq = $questionSeq;
            $nextSeq = $curSeq + 1;

            global $wpdb;


            while ($curSeq < $lastSeq) {
                //save question
                $wpdb->update('gwu_question', array(
                    'QuestSequence' => $curSeq
                        ), array('QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $nextSeq));

                $curSeq = $nextSeq;
                $nextSeq++;
            }
        }

        public function AddNewQuestion() {


            $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
            $answer_type_short = ( isset($_POST['answer_type_short']) ? $_POST['answer_type_short'] : '' );


            if (isset($_POST['close'])) {

                // Redirect the page to the admin form
                wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
                exit;
            }

            $questSequence = ( isset($_POST['questionSeq']) ? $_POST['questionSeq'] : '' );
            $questionNumber = ( isset($_POST['question_Number']) ? $_POST['question_Number'] : '' );
            $QuestionText = ( isset($_POST['question_text']) ? $_POST['question_text'] : '' );
            $QuestionAnsType = ( isset($_POST['answer_type']) ? $_POST['answer_type'] : '' );

            $Mandatory = ( isset($_POST['Mandatory']) ? $_POST['Mandatory'] : '' );
            $answersChoices = ( isset($_POST['p_choice']) ? $_POST['p_choice'] : '' );
            $flagNames = ( isset($_POST['p_flagName']) ? $_POST['p_flagName'] : '' );
            $flagValues = ( isset($_POST['p_flagValue']) ? $_POST['p_flagValue'] : '' );

            if ($questSequence != $this->getNextQuestionNumber($QuestionnaireID)) {
                $this->shiftQuestionsForAdd($QuestionnaireID, $questSequence);
            }

            //save question
            $this->Wrapper->saveQuestion($questSequence, $QuestionnaireID, null, $questionNumber, $QuestionAnsType, $QuestionText, $Mandatory);



            $counter = 1;

            if ($answer_type_short == 'multipleS' || $answer_type_short == 'multipleM') {
                foreach ($answersChoices as $index => $choice) {
                    if (trim($choice) != '') {
                        $this->Wrapper->saveAnswerChoice($QuestionnaireID, $questSequence, $counter, $choice);
                        if (trim($flagNames[$index]) != '') {
                            $this->Wrapper->saveFlag($counter, $questSequence, $QuestionnaireID, $flagNames[$index], $flagValues[$index]);
                        }
                        $counter++;
                    }
                }
            } elseif ($answer_type_short == 'NPS') {

                for ($counter = 0; $counter <= 10; $counter++) {

                    if (strlen($questionNumber) > 4)
                        $questionNumber = 'Q' + $questSequence;
                    $this->Wrapper->saveAnswerChoice($QuestionnaireID, $questSequence, $counter, $counter);
                    if ($counter >= 0 && $counter <= 6)
                        $this->Wrapper->saveFlag($counter, $questSequence, $QuestionnaireID, $questionNumber . '_NPSDetractor_' . $counter, $counter);
                    if ($counter == 7 || $counter == 8)
                        $this->Wrapper->saveFlag($counter, $questSequence, $QuestionnaireID, $questionNumber . '_NPSPassive_' . $counter, $counter);
                    if ($counter == 9 || $counter == 10)
                        $this->Wrapper->saveFlag($counter, $questSequence, $QuestionnaireID, $questionNumber . '_NPSPromoter_' . $counter, $counter);
                }

                $ansValue_Detractor = ( isset($_POST['Detractor']) ? $_POST['Detractor'] : '' );
                $this->Wrapper->saveAnswerChoice($QuestionnaireID, $questSequence, $counter, $ansValue_Detractor);
                $counter++;

                $ansValue_Promoter = ( isset($_POST['Promoter']) ? $_POST['Promoter'] : '' );
                $this->Wrapper->saveAnswerChoice($QuestionnaireID, $questSequence, $counter, $ansValue_Promoter);
            }


            $this->updateQuestionnaireModifedDate($QuestionnaireID);

            if (isset($_POST['save'])) {

                // Redirect the page to the admin form
                wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
                exit;
            } elseif (isset($_POST['saveAdd'])) {
                $nextSeq = $questSequence + 1;
                // Redirect the page to the admin form
                wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'new', 'Qid' => $QuestionnaireID,
                            'qno' => $nextSeq, 'type' => $answer_type_short), admin_url('admin.php')));
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
                //echo 'logic';
				wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'Qlogic', 'Qid' => $QuestionnaireID,
                            'qno' => $questSequence
                                ), admin_url('admin.php')));
                exit;
            }
            if (isset($_POST['addAction'])) {
                echo 'action';
		wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'Qaction', 'Qid' => $QuestionnaireID,
                            'qno' => $questSequence
                                ), admin_url('admin.php')));
            }
        }

        //show question for admin page function
        public function ViewQuestionsForAdmin($QuestionnaireID) {
            //string to hold the HTML code for output
            $questions = $this->Wrapper->listQuestion($QuestionnaireID);
            $Questionnaire = $this->Wrapper->getQuestionnaire($QuestionnaireID);
	    $PublishedFlag=$Questionnaire[0]->get_PublishFlag();

            if ($questions == false) {
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

        public function DeleteQuestion() {
            $value = ( isset($_POST['value']) ? $_POST['value'] : '' );
            $divID = ( isset($_POST['id']) ? $_POST['id'] : '' );
            $divIDArray = explode('_', $divID);
            $QuestionnaireID = $divIDArray[1];
            $questSequence = $divIDArray[2];



            global $wpdb;

            $wpdb->delete('gwu_question', array(
                'QuestSequence' => $questSequence, 'QuestionnaireID' => $QuestionnaireID
            ));

            $this->shiftQuestionsForDelete($QuestionnaireID, $questSequence);

            $this->updateQuestionnaireModifedDate($QuestionnaireID);


            echo 'question_' . $QuestionnaireID . '_' . $questSequence;
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
                $flagNames = ( isset($_POST['p_flagName']) ? $_POST['p_flagName'] : '' );
                $flagValues = ( isset($_POST['p_flagValue']) ? $_POST['p_flagValue'] : '' );

                global $wpdb;
                //save question
                $wpdb->update('gwu_question', array(
                    'QuestionNumber' => $questionNo,
                    'Text' => $text,
                    'Mandatory' => $Mandatory
                        ), array('QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq));

                $this->updateQuestionnaireModifedDate($QuestionnaireID);
                $optionNumber = 1;

                if ($type == 'Multiple Choice, Single Value' || $type == 'Multiple Choice, Multiple Value') {
                    $currentAnswersChoices = $this->Wrapper->listAnswerChoice($QuestionnaireID, $QuestionSeq);
                    foreach ($answersChoices as $index => $choice) {
                        if (trim($choice) != '') {
                            $wpdb->replace('gwu_answerChoice', array(
                                'AnsValue' => $choice, 'QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq,
                                'OptionNumber' => $optionNumber, 'Deleted' => 'false')
                            );

                            if (trim($flagNames[$index]) != '') {
                                $wpdb->replace('gwu_flag', array(
                                    'FlagName' => $flagNames[$index], 'FlagValue' => $flagValues[$index], 'QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq,
                                    'OptionNumber' => $optionNumber, 'Deleted' => 'false'));
                            } else {
                                $flagExist = $this->Wrapper->getFlagsByQuestionnaireQuestionOption($QuestionnaireID, $QuestionSeq, $optionNumber);
                                if ($flagExist != false) {
                                    $wpdb->delete('gwu_flag', array(
                                        'QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq,
                                        'OptionNumber' => $optionNumber));
                                }
                            }

                            $optionNumber++;
                        } else {
                            $answerExist = $this->Wrapper->getAnswerChoiceByQuestionnaireQuestionOption($QuestionnaireID, $QuestionSeq, $optionNumber);
                            if ($answerExist != false) {
                                $wpdb->delete('gwu_flag', array(
                                    'QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq,
                                    'OptionNumber' => $optionNumber));

                                $wpdb->delete('gwu_answerChoice', array(
                                    'QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq,
                                    'OptionNumber' => $optionNumber));
                            }
                        }
                    }
                    //if the user remove one of the answer, 
                    //the answer choice should be shited
                    //delete the redudcant answer choice that result from shifting
                    while ($optionNumber <= count($currentAnswersChoices)) {
                        $wpdb->delete('gwu_flag', array(
                            'QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq,
                            'OptionNumber' => $optionNumber));

                        $wpdb->delete('gwu_answerChoice', array(
                            'QuestionnaireID' => $QuestionnaireID, 'QuestSequence' => $QuestionSeq,
                            'OptionNumber' => $optionNumber));
                        $optionNumber++;
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

        public function updateQuestionnaireModifedDate($QuestionnaireID) {
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
