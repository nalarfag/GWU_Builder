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
		$page = get_page_by_title('Surveys');
                $my_post = array(
                    'post_title' => $result->Title,
                    'post_content' => '[questionnaire id="' . $qId . '"]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'comment_status' => 'closed',
		    'post_parent' => $page->ID,
                );
                //insert a post with options and get the new post id
                $postId = wp_insert_post($my_post);
		if ($postId) {
		    update_post_meta($postId, '_wp_page_template', 'Questionnaire-template.php');
		}
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

		    echo '<div id="success">true</div>';
                } else {
		    echo '<div id="success">false</div>';
                }
            }
            die();
        }

        public function DeactivateQuestionnaire() {
            $divID = ( isset($_POST['id']) ? $_POST['id'] : '' );
            $divIDArray = explode('_', $divID);
            $QuestionnaireID = $divIDArray[0];
            $questionnaire = $this->Wrapper->getQuestionnaire($QuestionnaireID);

            wp_delete_post($questionnaire[0]->get_PostId());
            $cureentDataTime = date('Y-m-d H:i:s');
            global $wpdb;
            $success = $wpdb->update(
                    'gwu_questionnaire', array(
                'PostId' => -1,
                'InactiveDate' => $cureentDataTime
                    ), array('QuestionnaireID' => $QuestionnaireID)
            );
            if ($success) {

		echo '<div id="success">true</div>';
            } else {
		echo '<div id="success">false</div>';
            }
            die();
        }

        public function ReactivateQuestionnaire() {
            $divID = ( isset($_POST['id']) ? $_POST['id'] : '' );
            $divIDArray = explode('_', $divID);
            $QuestionnaireID = $divIDArray[0];
            $questionnaire = $this->Wrapper->getQuestionnaire($QuestionnaireID);

	    $page = get_page_by_title('Surveys');
            $my_post = array(
                'post_title' => $questionnaire[0]->get_Title(),
                'post_content' => '[questionnaire id="' . $QuestionnaireID . '"]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'comment_status' => 'closed',
                'post_parent' => $page->ID
            );
            //insert a post with options and get the new post id
            $postId = wp_insert_post($my_post);
	    if ($postId) {
		update_post_meta($postId, '_wp_page_template', 'Questionnaire-template.php');
	    }
            global $wpdb;
            $success = $wpdb->update(
                    'gwu_questionnaire', array(
                'PostId' => $postId,
                'InactiveDate' => null
                    ), array('QuestionnaireID' => $QuestionnaireID)
            );
            if ($success) {
		echo '<div id="success">true</div>';
            } else {
		echo '<div id="success">false</div>';
            }
            die();
        }

        public function ShowOneQuestionnaire($QuestionnaireID) {

            //show current question


            $questionnaire = $this->Wrapper->getQuestionnaire($QuestionnaireID);
            //Top-level menu -->
            echo '<div id="Questionnaire-general" class="wrap">
                <h2>' . $questionnaire[0]->get_Title() . '
                </h2><br/>';

            $this->gwuquestion->ViewQuestionsForAdmin($QuestionnaireID);


            echo' </div>';
        }

        public function AddNewQuestionnaire() {
            
            // Place all user submitted values in an array

            $Title = ( isset($_POST['questionnaire_title']) ? $_POST['questionnaire_title'] : '' );
            $PostId = -1;
            $Topic = ( isset($_POST['topic']) ? $_POST['topic'] : '' );
            $AllowAnonymous = ( isset($_POST['anonymous']) ? $_POST['anonymous'] : '' );
            $AllowMultiple = ( isset($_POST['multiple']) ? $_POST['multiple'] : '' );
            $DateDate = date('Y-m-d H:i:s');
            $current_user = wp_get_current_user();
            $CreaterName = $current_user->user_login;

            //Temp data because UI is not yet modified to capture the following fields.
            $dateModified = date('Y-m-d H:i:s');
            $inactiveDate = '';
            $introText = 'Introduction';
            $thankyouText = 'Thank You!';
            //$PostId
            $publishFlag = '';
            $publishDate = '';

            //user is an editor
            if (current_user_can('edit_survey')) {
                $EditorID = get_current_user_id();
                $OwnerID = get_user_meta($EditorID, 'ownerID', true);
            } //user is owner or adminstrator
            elseif (current_user_can('own_survey')) {
                $EditorID = get_current_user_id();
                $OwnerID = get_current_user_id();
            }

	    if (isset($_POST['cancel'])) {
                // Redirect the page to the main questionnaire page
                wp_redirect(add_query_arg(array('page' => 'GWU_Questionnaire-mainMenu-page'
                                ), admin_url('admin.php')));
                exit;
            }

            if (isset($_POST['save'])) {



		$Questionnaire = $this->Wrapper->saveQuestionnaire($Title, $Topic, $CreaterName, $AllowAnonymous, $AllowMultiple, $DateDate, $dateModified, $inactiveDate, $introText, $thankyouText, $PostId, $publishFlag, $publishDate, $OwnerID, $EditorID);
	    }


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
            $questionnaire = $this->Wrapper->getQuestionnaire($QuestionnaireID);

            wp_delete_post($questionnaire[0]->get_PostId());
            global $wpdb;

            $wpdb->delete('gwu_questionnaire', array(
                'QuestionnaireID' => $QuestionnaireID
            ));

	    $wpdb->delete('wp_question_response', array(
                'question_dim_questionnaire_id' => $QuestionnaireID
            ));

            $wpdb->delete('wp_question_dim', array(
                'questionnaire_id' => $QuestionnaireID
            ));

            $wpdb->delete('wp_questionnaire_dim', array(
                'questionnaire_id' => $QuestionnaireID
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