<?php
namespace WordPress\ORM\Model;



class GWWrapper
{
	public static function listQuestion($questionnaireID){
		//return GWQuestion::all();
		$keys = array('QuestionnaireID' => $questionnaireID);
		return GWQuestion::find($keys);
	}
	
	public static function getQuestion($questSequence, $questionnaireID){
		$keys = array('QuestSequence' => $questSequence, 'QuestionnaireID' => $questionnaireID);
		return GWQuestion::find($keys);
	}
	
	public static function saveQuestion( $questSequence, $questionnaireID, $conditionID, $questionNumber, $ansType, $text, $mandatory, $deleted = 'false'){
		$gwQuestion = new GWQuestion();
		$gwQuestion->set_QuestSequence($questSequence);
		$gwQuestion->set_QuestionnaireID($questionnaireID);
		$gwQuestion->set_ConditionID($conditionID);
		$gwQuestion->set_QuestionNumber($questionNumber);
		$gwQuestion->set_AnsType($ansType);
		$gwQuestion->set_Text($text);
		$gwQuestion->set_Mandatory($mandatory);
		$gwQuestion->set_Deleted($deleted);
		$gwQuestion->save();
		
		return array('QuestSequence' => $questSequence, 'QuestionnaireID' => $questionnaireID);
	}
	
	public static function listFlag(){
		return GWFlag::all();
	}
	
	public static function getFlag($flagID){
		$keys = array('FlagID' => $flagID);
		return GWFlag::find($keys);
	}
	
	public static function saveFlag($optionNumber, $questSequence, $questionnaireID, $flagName, $flagValue, $deleted = 'false'){
		$gwFlag = new GWFlag();
		//$gwFlag->set_FlagID($flagID);
		$gwFlag->set_OptionNumber($optionNumber);
		$gwFlag->set_QuestSequence($questSequence);
		$gwFlag->set_QuestionnaireID($questionnaireID);
		$gwFlag->set_FlagName($flagName);
		$gwFlag->set_FlagValue($flagValue);
		$gwFlag->set_Deleted($deleted);
		$returnVal = $gwFlag->save();
		
		return array('FlagID' => $returnVal);
	}
	
	/*public static function listFlagSet(){
		return GWFlagSet::all();
	}
	
	public static function getFlagSet($flagID, $questionnaireID, $questionNumber, $optionNumber){
		$keys = array('FlagID' => $flagID, 'QuestionnaireID' => $questionnaireID, 
		'Question_Number' => $questionNumber, 'OptionNumber' => $optionNumber);
		return GWFlagSet::find($keys);
	}
	
	public static function saveFlagSet($flagID, $questionnaireID, $questionNumber, $optionNumber){
		$gwFlagSet = new GWFlagSet();
		$gwFlagSet->set_FlagID($flagID);
		$gwFlagSet->set_QuestionnaireID($questionnaireID);
		$gwFlagSet->set_Question_Number($questionNumber);
		$gwFlagSet->set_OptionNumber($optionNumber);
		$gwFlagSet->save();
		
		return array('FlagID' => $flagID, 'QuestionnaireID' => $questionnaireID, 
		'Question_Number' => $questionNumber, 'OptionNumber' => $optionNumber);
	}
	
	public static function listFlagCheck(){
		return GWFlagCheck::all();
	}
	
	public static function getFlagCheck($flagID, $questionnaireID, $questionNumber){
		$keys = array('FlagID' => $flagID, 'QuestionnaireID' => $questionnaireID, 
		'Question_Number' => $questionNumber);
		return GWFlagCheck::find($keys);
	}
	
	public static function saveFlagCheck($flagID, $questionnaireID, $questionNumber, $optionNumber){
		$gwFlagCheck = new GWFlagCheck();
		$gwFlagCheck->set_FlagID($flagID);
		$gwFlagCheck->set_QuestionnaireID($questionnaireID);
		$gwFlagCheck->set_Question_Number($questionNumber);
		
		$gwFlagCheck->save();
		
		return array('FlagID' => $flagID, 'QuestionnaireID' => $questionnaireID, 
		'Question_Number' => $questionNumber);
	}
	
	
		public static function listSessions() {
	
		return GWSession::all();
		
	}
	*/
	public static function getSession($sessionID) {
	
		$keys = array('SessionID' => $sessionID);
		return GWSession::find($keys);
		
	}
	
	public static function saveSession($userName, $IP, $city, $country, $duration, $surveyTakenDate, $surveyCompleted) {
	
		$session = new GWSession();
		//$session->set_SessionID($sessionID);
		$session->set_UserName($userName);
		$session->set_IP($IP);
		$session->set_City($city);
		$session->set_Country($country);
		$session->set_Duration($duration);
		$session->set_Country($country);
		$session->set_SurveyTakenDate($surveyTakenDate);
		$session->set_SurveyCompleted($surveyCompleted);
		$returnVal = $session->save();
		
		return array('SessionID' => $returnVal);
		
	}
	
	public static function listResponses() {
	
		return GWResponse::all();
	
	}
	
	public static function getResponse($responseID) {
	
		$keys = array('ResponceID' => $responseID);
		return GWResponse::find($keys);
	
	}
	
	public static function saveResponse($questSequence, $sessionID, $questionnaireID, $optionNumber, $responseType, $responseContent, $codeToProcessResponse, $processingResult) {
	
		$response = new GWResponse();
		//$response->set_ResponseID($responseID);
		$response->set_QuestSequence($questSequence);
		$response->set_SessionID($sessionID);
		$response->set_QuestionnaireID($questionnaireID);
		$response->set_OptionNumber($optionNumber);
		$response->set_ResponseType($responseType);
		$response->set_ResponseContent($responseContent);
		$response->set_CodeToProcessResponse($codeToProcessResponse);
		$response->set_ProcessingResult(processingResult);
		$returnVal = $response->save();
		
		return array('ResponceID' => $returnVal);
	
	}
	
	public static function listQestionnaires() {
		return GWQuestionnaire::all();
	}
	
	public static function getQuestionnaire($questionnaireID) {
		$keys = array('QuestionnaireID' => $questionnaireID);
		return GWQuestionnaire::find($keys);
	}
	
	public static function saveQuestionnaire($Title, $Topic, $creatorName, $allowMultiple, $allowAnnonymous, $dateCreated, $DateModified, $inactiveDate, $introText, $thankyouText, $Link, $publishFlag, $publishDate, $deleted = 'false') {
		$questionnaire = new GWQuestionnaire();
		$questionnaire->set_Title($Title);
		$questionnaire->set_Topic($Topic);
		$questionnaire->set_CreatorName($creatorName);
		$questionnaire->set_AllowMultiple($allowMultiple);
		$questionnaire->set_AllowAnnonymous($allowAnnonymous);
		$questionnaire->set_DateCreated($dateCreated);
		$questionnaire->set_DateModified(dateModified);
		$questionnaire->set_InactiveDate($inactiveDate);
		$questionnaire->set_IntroText($introText);
		$questionnaire->set_ThankyouText($thankyouText);
		$questionnaire->set_Link($link);
		$questionnaire->set_PublishFlag($publishFlag);
		$questionnaire->set_PublishDate($publishDate);
		$questionnaire->set_Deleted($deleted);
		
		
		$returnVal = $questionnaire->save();
	
		return array('QuestionnaireID' => $returnVal);
	}

	//For Action
	public static function listActions($QuestionnaireID, $Question_Number){
		$keys = array('QuestionnaireID'=>$QuestionnaireID,'Question_Number'=>$Question_Number);
		return 	GWAction::find($keys);
	}
	public static function getActions($ActionID){
		$keys = array('ActionID' => $ActionID);
		return GWAction::find($keys);
	}
	
	public static function saveAction($questSequence, $questionnaireID, $actionType, $linkToAction, $duration, $sequence, $content, $deleted = 'false') {
		$action = new GWAction();
		//$action->set_ActionID($ActionID);
		$action->set_QuestSequence($questSequence);
		$action->set_QuestionnaireID($questionnaireID);
		$action->set_ActionType($actionType);
		$action->set_LinkToAction($linkToAction);
		$action->set_Duration($duration);
		$action->set_Sequence($sequence);
		$action->set_Content($content);
		$action->set_Deleted($deleted);
		$returnVal = $action->save();
		
		return array('ActionID' => $returnVal);
	}

	// For GWAnswerChoice
	public static function listAnswerChoice($QuestionnaireID,$Question_Number){
		$keys = array('QuestionnaireID'=>$QuestionnaireID,'Question_Number'=>$Question_Number);
		return 	GWAnswerChoice::find($keys);
	}
	
	public static function saveAnswerChoice($questionnaireID, $questSequence, $optionNumber, $ansValue, $deleted = 'false') {


		$answerChoice = new GWAnwerChoice();

		$answerChoice->set_QuestionnaireID($questionnaireID);
		$answerChoice->set_QuestSequence($questSequence);
		$answerChoice->set_OptionNumber($optionNumber);
		$answerChoice->set_AnsValue($ansValue);
		$answerChoice->set_Deleted($deleted);
		
		$answerChoice->save();
                
		return array('OptionNumber' => $OptionNumber,'QuestionnaireID'=>$QuestionnaireID,
                    'QuestSequence'=>$questSequence);
	}
	
	
	public static function listConditions() {
		return GWCondition::all();
	}
	
	public static function getCondition($conditionID) {
		$keys = array('ConditionID' => $conditionID);
		return GWQuestionnaire::find($keys);
	}
	
	public static function saveCondition($conditionID, $questionnaireID, $logicStatement, $ansValue, $deleted = 'false') {


		$condition = new GWCondition();

		$condition->set_ConditionID($conditionID);
		$condition->set_QuestionnaireID($questionnaireID);
		$condition->set_LogicStatement($logicStatement);
		$condition->set_JumpQNoOnFailure($jumpQNoOnFailure);
		$condition->set_JumpQNoOnSuccess($JumpQNoOnSuccess);
		$condition->set_Deleted($deleted);
		
		$condition->save();
                
		return array('ConditionID' => $conditionID);
	}
		
}	

?>