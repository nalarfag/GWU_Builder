<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



include_once dirname(__FILE__) . '/models/GWAction.php';
include_once dirname(__FILE__) . '/models/GWQuestion.php';
include_once dirname(__FILE__) . '/models/GWQuestionnaire.php';
include_once dirname(__FILE__) . '/models/GWAnswerChoice.php';
include_once dirname(__FILE__) . '/models/GWWrapper.php';

if (!defined('GWU_BUILDER_DIR'))
    define('GWU_BUILDER_DIR', WP_PLUGIN_DIR . '\\' . GWU_Builder);

use WordPress\ORM\Model\GWWrapper;



if (!class_exists('GWUAction')) {

    class GWUAction {
	
		public function saveAction() {
                    
                        //$ActionID;
                        $QuestSequence = ( isset($_POST['QuestionSeq']) ? $_POST['QuestionSeq'] : '' );
                        $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
                        $ActionType = ( isset($_POST['ActionType']) ? $_POST['ActionType'] : NULL );
                        $LinkToAction= ( isset($_POST['LinkToAction']) ? $_POST['LinkToAction'] : NULL );
                        $Duration = ( isset($_POST['Duration']) ? $_POST['Duration'] : NULL );
                        $Sequence = ( isset($_POST['Sequence']) ? $_POST['Sequence'] : NULL );
                        $Content = ( isset($_POST['Content']) ? $_POST['Content'] : NULL );
                        $Deleted = ( isset($_POST['Deleted']) ? $_POST['Deleted'] : 'false' );
			
			$Wrapper = new GWWrapper();
			if($LinkToAction != '') {
				if(isset($_POST['ActionID'])) {
			
					$action = $Wrapper->getActions(intval($_POST['ActionID']))[0];
					$action->set_ActionType($ActionType);
					$action->set_LinkToAction($LinkToAction);
					$action->set_Duration($Duration);
                                        $action->set_Sequence($Sequence);
                                        $action->set_Content($Content);
                                        $action->set_Deleted($Deleted);
					$action->update();
				
				} else {
					$ActionID = $Wrapper->saveAction($QuestSequence, $QuestionnaireID, $ActionType, $LinkToAction, $Duration, $Sequence, $Content, $Deleted);
					$question = $Wrapper->getQuestion($QuestSequence, $QuestionnaireID)[0];
					$question->set_ActionID(intval($ActionID['ActionID']));
					$question->update();
				}
			}
			
			echo json_encode(array('success' => true, 'result' => $ActionID));

			die();
		
		}
		
		public function getActions() {
		
			$QuestionnaireID = $_POST['QuestionnaireID'];
			$QuestSequence = $_POST['QuestSequence'];
			$Wrapper = new GWWrapper();
			$actions = $Wrapper->listActions($QuestionnaireID, $QuestSequence);
			echo json_encode(array('success' => true, 'result' => $actions));

			die();
			
		}
                
                public function doneAction() {
                    $QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );

                    wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'view', 'Qid' => $QuestionnaireID
                                ), admin_url('admin.php')));
                }
                
                public function removeAction() {
                    
                    //file_put_contents("/tmp/log.txt", 'Inside removeActionFunction', FILE_APPEND);
                    
                    $Wrapper = new GWWrapper();
                    $ActionID = ( isset($_POST['ActionID']) ? $_POST['ActionID'] : '' );
                    $actions = $Wrapper->getActions(intval($ActionID));
                    //$action = new GWAction();
                    $action = $actions[0];
                    //file_put_contents("/var/www/html/wordpress/wp-content/plugins/GWU_Builder/models/log.txt", $action->get_ActionID(), FILE_APPEND);
                    //$result = $action->delete();
                    
                    //$action = $Wrapper->getActions(7)[0];
                    //file_put_contents("/var/www/html/wordpress/wp-content/plugins/GWU_Builder/models/log.txt", $action->get_ActionID(), FILE_APPEND);
                    //var_dump($action);
                    $action->delete();
                    
                    echo json_encode(array('success' => true));
                    die();
                }
	
	}

}

?>