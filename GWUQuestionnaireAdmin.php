<?php

include_once dirname(__FILE__) . '/models/GWQuestion.php';
include_once dirname(__FILE__) . '/models/GWQuestionnaire.php';
include_once dirname(__FILE__) . '/models/GWAnswerChoice.php';
include_once dirname(__FILE__) . '/models/GWWrapper.php';

if (!defined('GWU_BUILDER_DIR'))
    define('GWU_BUILDER_DIR', WP_PLUGIN_DIR . '\\' . GWU_Builder);

use WordPress\ORM\Model\GWWrapper;

/**
 * Description of GWUQuestionnaireAdmin
 *
 * Create admin menu for the questionnaire
 *
 * @author Nada Alarfag
 * Some part by Ashley, Michel, Sunny, Neerag
 *
 */
if (!class_exists('GWUQuestionnaireAdmin')) {

    class GWUQuestionnaireAdmin {

        protected $pluginPath;
        protected $pluginUrl;
        protected $gwuquestion;
        protected $gwuquestionnaire;


        public function __construct() {

            /**
             * Add a new menu under Manage, visible for all users with template viewing level.
             */
            add_action('admin_menu', array($this, 'GWU_add_Questionnaire_menu_links'));

            // Register function to be called when administration pages init takes place
            add_action('admin_init', array($this, 'GWU_Questionnaire_admin_init'));

            // Set Plugin Path
            $this->pluginPath = dirname(__FILE__);

            // Set Plugin URL
            $this->pluginUrl = WP_PLUGIN_URL . '/GWU_Builder';
            $this->gwuquestion=new GWUQuestion();
            $this->gwuquestionnaire= new GWUQuestionnaire();
        }

        public function GWU_add_Questionnaire_menu_links() {

            add_menu_page('View Questionnaires', 'View Questionnaires', 'manage_options', 'GWU_Questionnaire-mainMenu-page', array($this, 'GWU_Questionnaire_mainpage_callback')
                    , plugins_url('images/GWUQuestionnaire.png', __FILE__));

            add_submenu_page('GWU_Questionnaire-mainMenu-page', 'Add New Questionnaire ', 'Add New Questionnaire', 'manage_options', 'GWU_add-Questionnaire-page', array($this, 'GWU_add_Questionnaire_mainpage_callback'));
        }

        public function GWU_Questionnaire_mainpage_callback() {
            $this->gwuquestionnaire->ShowAllQuestionnaire();
        }

        public function GWU_add_Questionnaire_mainpage_callback() {

            $this->AddQuestionnairePageHandler();
        }

        // Register functions to be called when bugs are saved
        function GWU_Questionnaire_admin_init() {
           
            add_action('admin_post_add_new_question', array(&$this->gwuquestion, 'AddNewQuestion'));
            add_action('admin_post_edit_question', array(&$this->gwuquestion, 'EditQuestion'));
            add_action('admin_post_question_handler', array(&$this->gwuquestion, 'QuestionHandler'));
            add_action('admin_post_add_new_Questionnaire', array(&$this->gwuquestionnaire, 'AddNewQuestionnaire'));
			add_action( 'wp_ajax_delete_question', array(&$this->gwuquestion, 'DeleteQuestion' ));
        }

    

        
        public function AddQuestionnairePageHandler() {

            // Add questionnaire if no parameter sent in URL -->
            if (empty($_GET['id']) || $_GET['id'] == 'newQuestionnaire') {
			
                include_once $this->pluginPath . '/views/AddQuesionnaire.php';
				
            } elseif (isset($_GET['id']) && ( $_GET['id'] == 'view' || is_numeric($_GET['id']) )) {

                $QuestionnaireID = $_GET['Qid'];
                $this->gwuquestionnaire->ShowOneQuestionnaire($QuestionnaireID);
				
            } elseif (isset($_GET['id']) && ( $_GET['id'] == 'duplicate' || is_numeric($_GET['id']) )) {

                $QuestionnaireID = $_GET['Qid'];
                GWWrapper::copyQuestionnaire($QuestionnaireID);
            } 
            elseif (isset($_GET['id']) && is_numeric($_GET['Qid']) &&
                    ( $_GET['id'] == 'new' || is_numeric($_GET['Qno']) )) {

                $mode = 'new';


                // Display title based on current mode
                if ($mode == 'new') {

                    if (isset($_GET['type']) && $_GET['type'] == 'multipleS') {
                        include_once $this->pluginPath . '/views/mutlipleS.php';
                    }

                    if (isset($_GET['type']) && $_GET['type'] == 'multipleM') {
                        include_once $this->pluginPath . '/views/mutlipleM.php';
                    }

                    if (isset($_GET['type']) && $_GET['type'] == 'essay') {
                        include_once $this->pluginPath . '/views/essay.php';
                    }
                    if (isset($_GET['type']) && $_GET['type'] == 'NPS') {
                        include_once $this->pluginPath . '/views/NPS.php';
                    }
                } 
            }
            
            elseif (isset($_GET['id']) && is_numeric($_GET['Qid']) &&
                    ( $_GET['id'] == 'editQ' || is_numeric($_GET['Qno']) )) {

                $Wrapper= new GWWrapper(); 
                        include_once $this->pluginPath . '/views/EditQuestion.php';

                
            }
            
        }   

    }

}
?>
