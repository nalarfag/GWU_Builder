<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GWUQuestionnaireAdmin
 * 
 * Create admin menu for the questionnaire
 *
 * @author Nada Alarfag, Ashley, Michel, Sunny, Neerag
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
            // Set Plugin Path
            $this->pluginPath = dirname(__FILE__);

            // Set Plugin URL
            $this->pluginUrl = WP_PLUGIN_URL . '/GWU_Builder';
        }

        public function GWU_add_Questionnaire_menu_links() {

            add_menu_page('View Questionnaires', 'View Questionnaires', 'manage_options', 'GWU_Questionnaire-mainMenu-page', array($this, 'GWU_Questionnaire_mainpage_callback')
                    , plugins_url('images/GWUQuestionnaire.png', __FILE__));

            add_submenu_page('GWU_Questionnaire-mainMenu-page', 'Add New Questionnaire ', 'Add New Questionnaire', 'manage_options', 'GWU_add-Questionnaire-page', array($this, 'GWU_add_Questionnaire_mainpage_callback'));
        }

        public function GWU_Questionnaire_mainpage_callback() {
            $this->GWUShowQuestionnaire();
        }

        public function GWU_add_Questionnaire_mainpage_callback() {

            $this->GWUAddQuestionnaire();
        }

        // Register functions to be called when bugs are saved
        function GWU_Questionnaire_admin_init() {
            add_action('admin_post_add_new_question', array($this, 'GWUAdd_Question'));
            add_action('admin_post_add_new_Questionnaire', array($this, 'GWUAdd_Questionnaire'));
        }

        public function GWUAddQuestionnaire() {


            // Add questionnaire if no parameter sent in URL -->
            if (empty($_GET['id']) || $_GET['id'] == 'newQuestionnaire') {


                include_once $this->pluginPath . '/views/addQuesionnaire.php';
            } elseif (isset($_GET['id']) && ( $_GET['id'] == 'view' || is_numeric($_GET['id']) )) {

                //Top-level menu -->
                echo '<div id="Questionnaire-general" class="wrap">
                <h2>Questionnaire #title
                </h2>';
                //show current question
                $QuestionnaireID = $_GET['Qid'];
                echo'  <p> current Questions </p>
           
                     <h2>    <a class="add-new-h2" 
			href="' . add_query_arg(
                        array('page' => 'GWU_add-Questionnaire-page',
                    'id' => 'new', 'Qid' => $QuestionnaireID,
                    'type' => 'mutlipleS'), admin_url('admin.php'))
                . '">Add New Question</a></h2>';
            } elseif (isset($_GET['id']) && is_numeric($_GET['Qid']) &&
                    ( $_GET['id'] == 'new' || is_numeric($_GET['Qno']) )) {

                $mode = 'new';
                /*
                  // Display question creation and editing form if question is new
                  // or numeric id was sent
                  $QuestionnaireID = $_GET['Qid'];
                  $Question_num = $_GET['Qno'];
                  $AnsType = $_GET['type'];
                  $question_data = array();
                  $answer_data = array();


                  // Query database if numeric id is present

                  if (is_numeric($Question_num) && is_numeric($QuestionnaireID)) {
                  //$bug_query = 'select * from ' . $wpdb->get_blog_prefix();
                  //	$bug_query .= 'ch7_bug_data where bug_id = ' . $bug_id;
                  //$bug_data = $wpdb->get_row(  $bug_query , ARRAY_A );
                  //	if ( $bug_data ) $mode = 'edit';
                  } else {
                  $question_data['Text'] = '';
                  $question_data['AnsType'] = $AnsType;
                  $question_data['QuestionnaireID'] = $Question_num;
                  }
                 * 
                 */

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
                    //echo '<h3>Edit Bug #' . $bug_data['bug_id'] . ' - ';
                    //echo $bug_data['bug_title'] . '</h3>';
                }
            }
            echo' </div>';
        }

        public function GWUAdd_Questionnaire() {

            // Place all user submitted values in an array
            $Questionnaire_data = array();

            $Questionnaire_data['Title'] = ( isset($_POST['questionnaire_title']) ? $_POST['questionnaire_title'] : '' );
            $Questionnaire_data['Topic'] = ( isset($_POST['topic']) ? $_POST['topic'] : '' );
            $Questionnaire_data['AllowAnonymous'] = ( isset($_POST['anonymous']) ? $_POST['anonymous'] : '' );
            ;
            $Questionnaire_data['AllowMultiple'] = ( isset($_POST['multiple']) ? $_POST['multiple'] : '' );
            ;

            //view the data
            echo '<pre>';
            var_dump($Questionnaire_data);
            echo '<pre>';


            //must add data to DB and remove the following comment 
            //should get questionnaire_id 
            /*
              // Redirect the page to the admin form
              wp_redirect( add_query_arg(array( 'page' =>'GWU_add-Questionnaire-page',
              'id'=>'view', 'Qid'=> $QuestionnaireID),
              admin_url( 'admin.php' ) ) );
              exit;
             * */
        }

        public function GWUAdd_Question() {
            // Place all user submitted values in an array
            $Question_data = array();
            $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
            $answer_type = ( isset($_POST['answer_type']) ? $_POST['answer_type'] : '' );


            $Question_data['Text'] = ( isset($_POST['question_text']) ? $_POST['question_text'] : '' );
            $Question_data['AnsType'] = $answer_type;
            $Question_data['QuestionnaireID'] = $QuestionnaireID;
            $Answer_data = array();
            $Answers = preg_split('/(\r?\n)+/', $_POST['answers']);
            $counter = 1;

            if ($answer_type == 'multipleS' || $answer_type == 'multipleM') {
                foreach ($Answers as $answer) {
                    $Answer_data[$counter] = array();
                    $Answer_data[$counter]['OptionNumber'] = $counter;

                    $Answer_data[$counter]['QuestionnaireID'] = $QuestionnaireID;
                    $Answer_data[$counter]['AnsValue'] = $answer;

                    $counter++;
                }
            } elseif ($answer_type == 'NPS') {
                echo 'hi';
                for ($counter; $counter <= 10; $counter++) {
                    $Answer_data[$counter] = array();
                    $Answer_data[$counter]['OptionNumber'] = $counter;

                    $Answer_data[$counter]['QuestionnaireID'] = $QuestionnaireID;
                    $Answer_data[$counter]['AnsValue'] = $counter;
                }
                $Answer_data[$counter] = array();
                $Answer_data[$counter]['OptionNumber'] = $counter;

                $Answer_data[$counter]['QuestionnaireID'] = $QuestionnaireID;
                $Answer_data[$counter]['AnsValue'] = ( isset($_POST['Detractor']) ? $_POST['Detractor'] : '' );
                $counter++;
                $Answer_data[$counter] = array();
                $Answer_data[$counter]['OptionNumber'] = $counter;

                $Answer_data[$counter]['QuestionnaireID'] = $QuestionnaireID;
                $Answer_data[$counter]['AnsValue'] = ( isset($_POST['Promoter']) ? $_POST['Promoter'] : '' );
            }
            //view the data
            echo '<pre>';
            var_dump($Answers);
            var_dump($Question_data);
            var_dump($Answer_data);
            echo '<pre>';


            //must add data to DB and remove the following comment 
            /*
              // Redirect the page to the admin form
              wp_redirect( add_query_arg(array( 'page' =>'GWU_add-Questionnaire-page',
              'id'=>'view', 'Qid'=> $QuestionnaireID),
              admin_url( 'admin.php' ) ) );
              exit;
             * */
        }

        public function GWUShowQuestionnaire() {
            global $wpdb;
            // string to hold the HTML code for output
            $output = '
		<div class="wrap">
			<h1>Questionnaire Set</h1>';

            // $tables=$builder_db->get_results("SHOW TABLES FROM builder");
            $tables = $wpdb->get_results("select * from gwu_questionnaire");
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
                                                        <th width="40"></th>
						</tr>';

            foreach ($tables as $table) {

                // $tableName=$table->Tables_in_builder;
                $id = $table->QuestionnaireID;
                $Name = $table->Title;
                $Date = $table->DateCreated;
                $Anonymous = $table->AllowAnonymous;
                $Multiple = $table->AllowMultiple;
                $Category = $table->Topic;

                $output .= '  <tr>
				<td align="center" nowrap="nowrap">' . $Name . '</td>
				<td align="center" xml:lang="en" dir="ltr" nowrap="nowrap">' . $Date . '</td>
				<td align="center" nowrap="nowrap">' . ($Anonymous ? 'Yes' : 'No') . '</td>
				<td align="center" nowrap="nowrap">' . ($Multiple ? 'Yes' : 'No') . '</td>
				<td  align="center" nowrap="nowrap">' . $Category . '</td>
                                    <td align="center" nowrap="nowrap"><a class="View-Q" 
			href="' . add_query_arg(
                                array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'view', 'Qid' => $id
                                ), admin_url('admin.php'))
                        . '">view </td>
				
		    </tr>';
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

    }

}
?>
