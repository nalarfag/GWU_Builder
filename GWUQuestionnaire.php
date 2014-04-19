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
    
     public function __construct() {
         
         $this->gwuquestion=new GWUQuestion();
     }
    
    public function ShowAllQuestionnaire() {
            $message = $this->publishSelectedQuestionaire();

               $Wrapper = new GWWrapper();
               $Qestionnaires = $Wrapper->listQestionnaires();
               
            include_once dirname(__FILE__) . '/views/QuestionnaireViewAdmin.php';
            
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
		     $cureentDataTime = date('Y-m-d H:i:s');
                    //save post page id into questionnaire's property, PostId
                    $success = $wpdb->update(
                        'gwu_questionnaire',
                        array(
			    'PostId' => $postId,
			    'PublishFlag' => true,
			    'PublishDate'=>$cureentDataTime
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
        
    public function ShowOneQuestionnaire($QuestionnaireID) {

            //show current question

            $Wrapper = new GWWrapper();
            $questionnaire = $Wrapper->getQuestionnaire($QuestionnaireID);
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

            $Wrapper = new GWWrapper();
            $Questionnaire = $Wrapper->saveQuestionnaire($Questionnaire_data['Title'], $Questionnaire_data['Topic'], $Questionnaire_data['CreaterName'], $Questionnaire_data['AllowAnonymous'], $Questionnaire_data['AllowMultiple'], $Questionnaire_data['DateDate'], $dateModified, $inactiveDate, $introText, $thankyouText, $Questionnaire_data['PostId'], $publishFlag, $publishDate);


            // Redirect the page to the admin form
            wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                        'id' => 'view', 'Qid' => $Questionnaire['QuestionnaireID']), admin_url('admin.php')));
            exit;
        }

        
        
         public function copyQuestionnaire($QuestionnaireID) {
             
         }
}
}

?>
