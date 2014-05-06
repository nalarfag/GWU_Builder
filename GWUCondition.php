<?php

include_once dirname(__FILE__) . '/models/GWCondition.php';
include_once dirname(__FILE__) . '/models/GWQuestion.php';
include_once dirname(__FILE__) . '/models/GWQuestionnaire.php';
include_once dirname(__FILE__) . '/models/GWAnswerChoice.php';
include_once dirname(__FILE__) . '/models/GWWrapper.php';

if (!defined('GWU_BUILDER_DIR'))
    define('GWU_BUILDER_DIR', WP_PLUGIN_DIR . '\\' . GWU_Builder);

use WordPress\ORM\Model\GWWrapper;


/**
 * Description of GWUCondition
 *
 * @author Darshan
 */
if (!class_exists('GWUCondition')) {

    class GWUCondition {
	
		public function SaveCondition() {
		
			$QuestionnaireID = ( isset($_POST['QuestionnaireID']) ? $_POST['QuestionnaireID'] : '' );
			$QuestionSeq = ( isset($_POST['QuestionSeq']) ? $_POST['QuestionSeq'] : '' );
			$jumpOnSuccess = ( isset($_POST['jumpOnSuccess']) ? $_POST['jumpOnSuccess'] : NULL );
			$jumpOnFailure = ( isset($_POST['jumpOnFailure']) ? $_POST['jumpOnFailure'] : NULL );
			$logicalCondition = ( isset($_POST['logicalCondition']) ? $_POST['logicalCondition'] : '' );
			
			$Wrapper = new GWWrapper();
			if($logicalCondition != '') {
				if(isset($_POST['ConditionID'])) {
			
					$condition = $Wrapper->getCondition(intval($_POST['ConditionID']))[0];
					$condition->set_LogicStatement(trim($logicalCondition));
					$condition->set_JumpQNoOnSuccess($jumpOnSuccess);
					$condition->set_JumpQNoOnFailure($jumpOnFailure);
					$condition->update();
				
				} else {
					$conditionID = $Wrapper->saveCondition($QuestionnaireID, trim($logicalCondition), $jumpOnFailure, $jumpOnSuccess);
					$question = $Wrapper->getQuestion($QuestionSeq, $QuestionnaireID)[0];
					$question->set_ConditionID(intval($conditionID['ConditionID']));
					$question->update();
				}
			}
			
			wp_redirect(add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                            'id' => 'view', 'Qid' => $QuestionnaireID
                                ), admin_url('admin.php')));
            exit;
		
		}
		
		public function getFlagValues() {
		
			$QuestionnaireID = $_POST['QuestionnaireID'];
			$FlagName = $_POST['FlagName'];
			$Wrapper = new GWWrapper();
			$flagValues = $Wrapper->getFlagValuesByQuestionnaire($QuestionnaireID, $FlagName);
			echo json_encode(array('success' => true, 'result' => $flagValues));

			die();
			
		}
	
	}

}

?>