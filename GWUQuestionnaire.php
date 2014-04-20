<?php

include_once dirname(__FILE__) . '/models/GWQuestion.php';
include_once dirname(__FILE__) . '/models/GWQuestionnaire.php';
include_once dirname(__FILE__) . '/models/GWAnswerChoice.php';
include_once dirname(__FILE__) . '/models/GWWrapper.php';

if (!defined('GWU_BUILDER_DIR'))
    define('GWU_BUILDER_DIR', WP_PLUGIN_DIR . '\\' . GWU_Builder);

use WordPress\ORM\Model\GWWrapper;

/**
 * Description of GWUQuestionnaire
 *
 * @author Nada Alarfag
 */
if (!class_exists('GWUQuestionnaire')) {

    class GWUQuestionnaire {

        protected $gwuquestion;
        protected $Wrapper;

        public function __construct() {

            $this->gwuquestion = new GWUQuestion();
            $this->Wrapper = new GWWrapper();
        }

        public function ShowAllQuestionnaire() {

            include_once dirname(__FILE__) . '/views/QuestionnaireViewAdmin.php';
        }

        public function PublishQuestionnaire() {
            global $wpdb;
            $divID = ( isset($_POST['id']) ? $_POST['id'] : '' );
            $divIDArray = explode('_', $divID);
            $qId = $divIDArray[0];
            //get the title of selected questionnaire
            $result = $wpdb->get_row('select * from gwu_questionnaire where QuestionnaireID=' . $qId);

            //if the questionnaire id is valid we should get its title
            if ($result != null) {
                $my_post = array(
                    'post_title' => $result->Title,
                    'post_content' => '[questionnaire id="' . $qId . '"]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'comment_status' => 'closed'
                );
                //insert a post with options and get the new post id
                $postId = wp_insert_post($my_post);
                $cureentDataTime = date('Y-m-d H:i:s');
                //save post page id into questionnaire's property, PostId
                $success = $wpdb->update(
                        'gwu_questionnaire', array(
                    'PostId' => $postId,
                    'PublishFlag' => true,
                    'PublishDate' => $cureentDataTime
                        ), array('QuestionnaireID' => $qId)
                );
                if ($success) {

                    echo true;
                } else {
                    echo false;
                }
            }
            die();
        }

        public function ShowOneQuestionnaire($QuestionnaireID) {

            //show current question


            $questionnaire = $this->Wrapper->getQuestionnaire($QuestionnaireID);
            //Top-level menu -->
            echo '<div id="Questionnaire-general" class="wrap">
                <h2>' . $questionnaire[0]->get_Title() . '
                </h2> <br/>';

            $this->gwuquestion->ViewQuestionsForAdmin($QuestionnaireID);


            echo' </div>';
        }

        public function AddNewQuestionnaire() {

            // Place all user submitted values in an array
            $Questionnaire_data = array();

            $Questionnaire_data['Title'] = ( isset($_POST['questionnaire_title']) ? $_POST['questionnaire_title'] : '' );
            $questionnaire_date['PostId'] = -1;
            $Questionnaire_data['Topic'] = ( isset($_POST['topic']) ? $_POST['topic'] : '' );
            $Questionnaire_data['AllowAnonymous'] = ( isset($_POST['anonymous']) ? $_POST['anonymous'] : '' );
            $Questionnaire_data['AllowMultiple'] = ( isset($_POST['multiple']) ? $_POST['multiple'] : '' );
            $Questionnaire_data['DateDate'] = date('Y-m-d H:i:s');
            $current_user = wp_get_current_user();
            $Questionnaire_data['CreaterName'] = $current_user->user_login;

            //Temp data because UI is not yet modified to capture the following fields.
            $dateModified = date('Y-m-d H:i:s');
            $inactiveDate = '';
            $introText = 'Introduction';
            $thankyouText = 'Thank You!';
            //$PostId
            $publishFlag = '';
            $publishDate = '';

            $Questionnaire = $this->Wrapper->saveQuestionnaire($Questionnaire_data['Title'], $Questionnaire_data['Topic'], $Questionnaire_data['CreaterName'], $Questionnaire_data['AllowAnonymous'], $Questionnaire_data['AllowMultiple'], $Questionnaire_data['DateDate'], $dateModified, $inactiveDate, $introText, $thankyouText, $Questionnaire_data['PostId'], $publishFlag, $publishDate);


            // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                        'id' => 'view', 'Qid' => $Questionnaire['QuestionnaireID']), admin_url('admin.php')));
            exit;
        }

        //function called by ajax when the user click on duplicate, 
        //this function call copy questionnaire on the wrapper
        public function DuplicateQuestionnaire() {
            $divID = ( isset($_POST['id']) ? $_POST['id'] : '' );
            $divIDArray = explode('_', $divID);
            $QuestionnaireID = $divIDArray[0];
            $this->Wrapper->copyQuestionnaire($QuestionnaireID);
            die();
        }

        //Delete the selected questionnaire
        public function DeleteQuestionnaire() {
            $divID = ( isset($_POST['id']) ? $_POST['id'] : '' );
            $divIDArray = explode('_', $divID);
            $QuestionnaireID = $divIDArray[0];


            global $wpdb;

            $wpdb->delete('gwu_questionnaire', array(
                'QuestionnaireID' => $QuestionnaireID
            ));

            die();
        }

        //edit the Questionnaire based on the new value
        public function EditQuestionnaire() {

            $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );

            if (isset($_POST['cancel'])) {
                // Redirect the page to the main questionnaire page
                wp_redirect(add_query_arg(array('page' => 'GWU_Questionnaire-mainMenu-page'
                                ), admin_url('admin.php')));
                exit;
            }

            if (isset($_POST['save'])) {
                $Title = ( isset($_POST['questionnaire_title']) ? $_POST['questionnaire_title'] : '' );
                $Topic = ( isset($_POST['topic']) ? $_POST['topic'] : '' );
                $AllowMultiple = ( isset($_POST['multiple']) ? $_POST['multiple'] : '' );
                $AllowAnnonymous = ( isset($_POST['anonymous']) ? $_POST['anonymous'] : '' );
                $dateModified = date('Y-m-d H:i:s');

                global $wpdb;
                //save question
                $wpdb->update('gwu_questionnaire', array(
                    'Title' => $Title,
                    'Topic' => $Topic,
                    'AllowMultiple' => $AllowMultiple,
                    'AllowAnnonymous' => $AllowAnnonymous,
                    'dateModified' => $dateModified
                        ), array('QuestionnaireID' => $QuestionnaireID));
            }
            // Redirect the page to the main questionnaire page
            wp_redirect(add_query_arg(array('page' => 'GWU_Questionnaire-mainMenu-page'
                            ), admin_url('admin.php')));
            exit;
        }

    }

}
?>