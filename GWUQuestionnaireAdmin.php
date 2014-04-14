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
        }

        public function GWU_add_Questionnaire_menu_links() {

            add_menu_page('View Questionnaires', 'View Questionnaires', 'manage_options', 'GWU_Questionnaire-mainMenu-page', array($this, 'GWU_Questionnaire_mainpage_callback')
                    , plugins_url('images/GWUQuestionnaire.png', __FILE__));

            add_submenu_page('GWU_Questionnaire-mainMenu-page', 'Add New Questionnaire ', 'Add New Questionnaire', 'manage_options', 'GWU_add-Questionnaire-page', array($this, 'GWU_add_Questionnaire_mainpage_callback'));
        }

        public function GWU_Questionnaire_mainpage_callback() {
            $this->GWUShowAllQuestionnaire();
        }

        public function GWU_add_Questionnaire_mainpage_callback() {

            $this->GWUAddQuestionnaire();
        }

        // Register functions to be called when bugs are saved
        function GWU_Questionnaire_admin_init() {
           
            add_action('admin_post_add_new_question', array(&$this->gwuquestion, 'GWUAddNewQuestion'));
            add_action('admin_post_edit_question', array(&$this->gwuquestion, 'GWUEditQuestion'));
            add_action('admin_post_question_handler', array(&$this->gwuquestion, 'QuestionHandler'));
            add_action('admin_post_add_new_Questionnaire', array($this, 'GWUAddNewQuestionnaire'));
        }

       private function publishSelectedQuestionaire(){
            if(isset($_GET['id']) && $_GET['id'] == 'publish'&&isset($_GET['Qid'])&&is_numeric($_GET['Qid'])){
                global $wpdb;
                $qId = $_GET['Qid'];
                //get the title of selected questionnaire
                $result = $wpdb->get_row('select * from gwu_questionnaire where QuestionnaireID='.$qId);

                //if the questionnaire id is valid we should get its title
                if($result != null){
                    $my_post = array(
                        'post_title'    => $result->Title,
                        'post_content'  => '[questionnaire id="'.$qId.'"]',
                        'post_status'   => 'publish',
                        'post_type'     => 'page',
                        'comment_status'=> 'closed'
                    );
                    //insert a post with options and get the new post id
                    $postId = wp_insert_post( $my_post );
                    //save post page id into questionnaire's property, PostId
                    $success = $wpdb->update(
                        'gwu_questionnaire',
                        array(
                            'PostId' => $postId
                        ),
                        array( 'QuestionnaireID' => $qId )
                    );
                    if($success){
                        return "<h2>Publish succeed.</h2>";
                    }
                    else{
                        return "<h2>Publish failed.</h2>";
                    }
                }
                return "";

            }
            return "";

        }

        public function GWUShowAllQuestionnaire() {
            $message = $this->publishSelectedQuestionaire();

            $Wrapper = new GWWrapper();
            // string to hold the HTML code for output
            $output = '
		<div class="wrap">
			<h1>Questionnaire Set</h1>' . $message;

            // $tables=$builder_db->get_results("SHOW TABLES FROM builder");
            $Qestionnaires = $Wrapper->listQestionnaires();
            $output .= ' <br><div class=table>

            <br>
				<table class="wp-list-table widefat fixed"  width="90%" border="1">
					<tbody>
                                        <tr>
							<th width="100">Name</th>
							<th width="40">Date</th>
							<th width="70">AllowAnonymous</th>
							<th width="70">AllowMultiple</th>
							<th width="40">Category</th>
                                                        <th width="40">Creater</th>
                                                        <th width="40"></th>
                                                        <th width="40"></th>
						</tr>';

            foreach ($Qestionnaires as $Qestionnaire) {

                // $tableName=$table->Tables_in_builder;
                $id = $Qestionnaire->get_QuestionnaireID();
                $Name = $Qestionnaire->get_Title();
                $Date = $Qestionnaire->get_DateCreated();
                $Anonymous = $Qestionnaire->get_AllowAnonymous();
                $Multiple = $Qestionnaire->get_AllowMultiple();
                $Category = $Qestionnaire->get_Topic();
                $CreatorName = $Qestionnaire->get_CreatorName();
                $PostId = $Qestionnaire->get_PostId();
                $Link = get_permalink($PostId);

                $output .= '  <tr>
				<td align="center" nowrap="nowrap">' . $Name . '</td>
				<td align="center" xml:lang="en" dir="ltr" nowrap="nowrap">' . $Date . '</td>
				<td align="center" nowrap="nowrap">' . ($Anonymous ? 'Yes' : 'No') . '</td>
				<td align="center" nowrap="nowrap">' . ($Multiple ? 'Yes' : 'No') . '</td>
				<td  align="center" nowrap="nowrap">' . $Category . '</td>
                                    <td  align="center" nowrap="nowrap">' . $CreatorName . '</td>
                                    <td align="center" nowrap="nowrap"><a class="View-Q" 
			href="' . add_query_arg(
                                array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'view', 'Qid' => $id
                                ), admin_url('admin.php'))
                        . '">view</a> </td>
                    <td style="100px;" align="center" nowrap="nowrap">
                    <a class="View-Q" href="' . ($PostId < 1 ?
                                add_query_arg(
                                        array('page' => 'GWU_Questionnaire-mainMenu-page',
                                    'id' => 'publish', 'Qid' => $id
                                        ), admin_url('admin.php')) . '">Publish</a>' :
                                $Link . '">' . $Link . '</a>') .
                        '</tr>';
            }

            $address = add_query_arg(array(
                'page' => 'GWU_add-Questionnaire-page',
                'id' => 'newQuestionnaire'
                    ), admin_url('admin.php'));

            $output .= '		</tbody>
				</table>
			</div> 
                        </br>
                        </br><a class="add-new-h2"  href="' . $address . '">Add new questionaire</a>';
            echo $output;
        }

        public function GWUAddQuestionnaire() {

            // Add questionnaire if no parameter sent in URL -->
            if (empty($_GET['id']) || $_GET['id'] == 'newQuestionnaire') {


                include_once $this->pluginPath . '/views/AddQuesionnaire.php';
            } elseif (isset($_GET['id']) && ( $_GET['id'] == 'view' || is_numeric($_GET['id']) )) {

                $QuestionnaireID = $_GET['Qid'];
                $this->ShowOneQuestionnaire($QuestionnaireID);
            } elseif (isset($_GET['id']) && is_numeric($_GET['Qid']) &&
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


        public function ShowOneQuestionnaire($QuestionnaireID) {

            //show current question

            $Wrapper = new GWWrapper();
            $questionnaire = $Wrapper->getQuestionnaire($QuestionnaireID);
            //Top-level menu -->
            echo '<div id="Questionnaire-general" class="wrap">
                <h2>' . $questionnaire[0]->get_Title() . '
                </h2> <br/>';

            $this->gwuquestion->ShowQuestions($QuestionnaireID);
           

         echo' </div>';
        }

       
        public function GWUAddNewQuestionnaire() {

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

            $Wrapper = new GWWrapper();
            $Questionnaire = $Wrapper->saveQuestionnaire($Questionnaire_data['Title'], $Questionnaire_data['Topic'], $Questionnaire_data['CreaterName'], $Questionnaire_data['AllowAnonymous'], $Questionnaire_data['AllowMultiple'], $Questionnaire_data['DateDate'], $dateModified, $inactiveDate, $introText, $thankyouText, $Questionnaire_data['PostId'], $publishFlag, $publishDate);


            // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                        'id' => 'view', 'Qid' => $Questionnaire['QuestionnaireID']), admin_url('admin.php')));
            exit;
        }


    }

}
?>
