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

        public function __construct() {

            /**
             * Add a new menu under Manage, visible for all users with template viewing level.
             */
            add_action('admin_menu', array($this, 'GWU_add_Questionnaire_menu_links'));

            // Register function to be called when administration pages init takes place
            add_action('admin_init', array($this, 'GWU_Questionnaire_admin_init'));
            add_shortcode('questionnaire', array($this, 'showQuestionnaireForPost'));
            // Set Plugin Path
            $this->pluginPath = dirname(__FILE__);

            // Set Plugin URL
            $this->pluginUrl = WP_PLUGIN_URL . '/GWU_Builder';
        }

        public function GWU_add_Questionnaire_menu_links() {

            add_menu_page('View Questionnaires', 'View Questionnaires', 'manage_options',
                'GWU_Questionnaire-mainMenu-page', array($this, 'GWU_Questionnaire_mainpage_callback')
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
            add_action('admin_post_add_new_question', array($this, 'GWUAddNewQuestion'));
            add_action('admin_post_add_new_Questionnaire', array($this, 'GWUAddNewQuestionnaire'));
        }

        public function showQuestionnaireForPost( $atts ) {
            extract( shortcode_atts( array(
                'id' => '-1'
            ), $atts ));
            return $this->ShowQuestions($id);
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
                        'post_status'   => 'publish'
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
			<h1>Questionnaire Set</h1>'.$message;

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
                $Link = get_permalink( $PostId );

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
                    <a class="View-Q" href="'.($PostId < 1?
                        add_query_arg(
                            array('page' => 'GWU_Questionnaire-mainMenu-page',
                                'id' => 'publish', 'Qid' => $id
                            ), admin_url('admin.php')).'">Publish</a>'
                        :
                        $Link.'">'.$Link.'</a>').
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

                    if (isset($_GET['type']) && $_GET['type'] == 'mutlipleS') {
                        include_once $this->pluginPath . '/views/mutlipleS.php';
                    }

                    if (isset($_GET['type']) && $_GET['type'] == 'mutlipleM') {
                        include_once $this->pluginPath . '/views/mutlipleM.php';
                    }

                    if (isset($_GET['type']) && $_GET['type'] == 'essay') {
                        include_once $this->pluginPath . '/views/essay.php';
                    }
                    if (isset($_GET['type']) && $_GET['type'] == 'NPS') {
                        include_once $this->pluginPath . '/views/NPS.php';
                    }
                } elseif ($mode == 'edit') {

                    //for later 
                }
            }
        }

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

        public function ShowOneQuestionnaire($QuestionnaireID) {

            //show current question

            $Wrapper = new GWWrapper();
            $questionnaire = $Wrapper->getQuestionnaire($QuestionnaireID);
            //Top-level menu -->
            echo '<div id="Questionnaire-general" class="wrap">
                <h2>' . $questionnaire[0]->get_Title() . '
                </h2>';

            $output = $this->ShowQuestions($QuestionnaireID);
            echo $output;

            echo' <h2>    <a class="add-new-h2" 
			href="' . add_query_arg(
                    array('page' => 'GWU_add-Questionnaire-page',
                        'id' => 'new', 'Qid' => $QuestionnaireID,
                        'type' => 'mutlipleS'), admin_url('admin.php'))
                . '">Add New Question</a></h2>';
            echo' </div>';
        }

        //show question function
        public function ShowQuestions($QuestionnaireID) {
//string to hold the HTML code for output
            $Wrapper = new GWWrapper();
            $questions = $Wrapper->listQuestion($QuestionnaireID);
            $output = '<form><hr/>';
            if($questions==false)
                return;
            foreach ($questions as $question) {
                $Title = $question->get_Text();
                $type = $question->get_AnsType();
                $questionno = $question->get_Question_Number();

                $output .= $questionno . '&nbsp;&nbsp;&nbsp;  ' . $Title . '<br/>';

                $answerchoices = $Wrapper->listAnswerChoice($QuestionnaireID, $questionno);

                if ($type == 'Text Box') {
                    $output .= '<textarea  cols="30" rows="5"> </textarea><br/><hr/>';
                } elseif ($type == 'NPS') {

                    $output .= '<table><tr><td></td>';
                    for ($i = 0; $i < 10; $i++) {
                        $output .= '<td><input name="'.$questionno.'" type="radio"
                            value="'.$answerchoices[$i]->get_OptionNumber().'"/>&nbsp;</td>';
                    }
                    $output .= '<td></td></tr><tr><td>' . $answerchoices[10]->get_AnsValue() . ' </td>';
                    for ($i = 0; $i < 10; $i++) {
                        $output .= '<td>' . $answerchoices[$i]->get_AnsValue()  . '</td>';
                    }
                    $output .= '<td>' . $answerchoices[11]->get_AnsValue() . ' </td></tr></table>';
                } elseif ($type == 'Multiple Choice, Single Value') {

                    foreach ($answerchoices as $answerchoice) {
                        $answerchoicescontent = $answerchoice->get_AnsValue();

                        $output .= '<input name="'.$questionno.'" type="radio" value="'.$answerchoice->get_OptionNumber().'"/>&nbsp;&nbsp;' . $answerchoicescontent . '<br/>';
                    }
                } else {
                    foreach ($answerchoices as $answerchoice) {
                        $answerchoicescontent = $answerchoice->get_AnsValue();

                        $output .= '<input name="'.$questionno.'" value="'.$answerchoice->get_OptionNumber().'" type="checkbox"/>&nbsp;&nbsp;' . $answerchoicescontent . '<br/>';
                    }
                }
                $output .= '<hr/>';
            }

            return $output;
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

            $Wrapper = new GWWrapper();
            $Questionnaire = $Wrapper->saveQuestionnaire($Questionnaire_data['Title'], $Questionnaire_data['Topic'], $Questionnaire_data['AllowAnonymous'], $Questionnaire_data['AllowMultiple'], $Questionnaire_data['CreaterName'], $Questionnaire_data['DateDate'], $Questionnaire_data['PostId']);



            // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                    'id' => 'view', 'Qid' => $Questionnaire['QuestionnaireID']),
                admin_url('admin.php')));
            exit;
        }

        public function GWUAddNewQuestion() {
            // Place all user submitted values in an array
            $Question_data = array();
            $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
            $answer_type_short = ( isset($_POST['answer_type_short']) ? $_POST['answer_type_short'] : '' );


            $Question_data['questionNumber'] = ( isset($_POST['question_Number']) ? $_POST['question_Number'] : '' );
            $Question_data['Text'] = ( isset($_POST['question_text']) ? $_POST['question_text'] : '' );
            $Question_data['AnsType'] = ( isset($_POST['answer_type']) ? $_POST['answer_type'] : '' );
            $Question_data['QuestionnaireID'] = $QuestionnaireID;
            $Question_data['Mandatory'] = ( isset($_POST['Mandatory']) ? $_POST['Mandatory'] : '' );


            //save question
            $Wrapper = new GWWrapper();
            $Wrapper->saveQuestion($Question_data['questionNumber'], $Question_data['QuestionnaireID'], $Question_data['AnsType'], $Question_data['Text'], $Question_data['Mandatory']);

            $Answers = preg_split('/(\r?\n)+/', $_POST['answers']);
            $counter = 1;

            if ($answer_type_short == 'multipleS' || $answer_type_short == 'multipleM') {
                foreach ($Answers as $answer) {

                    $Wrapper->saveAnswerChoice($counter, $QuestionnaireID, $Question_data['questionNumber'], $answer);
                    $counter++;
                }
            } elseif ($answer_type_short == 'NPS') {

                for ($counter; $counter <= 10; $counter++) {


                    $Wrapper->saveAnswerChoice($counter, $QuestionnaireID, $Question_data['questionNumber'], $counter);
                }

                $ansValue_Detractor = ( isset($_POST['Detractor']) ? $_POST['Detractor'] : '' );
                $Wrapper->saveAnswerChoice($counter, $QuestionnaireID, $Question_data['questionNumber'], $ansValue_Detractor);
                $counter++;

                $ansValue_Promoter = ( isset($_POST['Promoter']) ? $_POST['Promoter'] : '' );
                $Wrapper->saveAnswerChoice($counter, $QuestionnaireID, $Question_data['questionNumber'], $ansValue_Promoter);

            }


            //must add data to DB and remove the following comment 
            // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                'id' => 'view', 'Qid' => $QuestionnaireID), admin_url('admin.php')));
            exit;
        }
    }

}
?>
