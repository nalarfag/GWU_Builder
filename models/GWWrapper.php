<?php
namespace WordPress\ORM\Model;



class GWWrapper
{
	public static function listQuestion($questionnaireID){
		//return GWQuestion::all();
		$keys = array('QuestionnaireID' => $questionnaireID);
		return GWQuestion::find($keys);
	}
	
	public static function getQuestion($questionNumber, $questionnaireID){
		$keys = array('Question_Number' => $questionNumber, 'QuestionnaireID' => $questionnaireID);
		return GWQuestion::find($keys);
	}
	
	public static function saveQuestion($questionNumber, $questionnaireID, $ansType, $text, $mandatory){
		$gwQuestion = new GWQuestion();
		$gwQuestion->set_Question_Number($questionNumber);
		$gwQuestion->set_QuestionnaireID($questionnaireID);
		$gwQuestion->set_AnsType($ansType);
		$gwQuestion->set_Text($text);
		$gwQuestion->set_Mandatory($mandatory);
		$gwQuestion->save();
		
		return array('Question_Number' => $questionNumber, 'QuestionnaireID' => $questionnaireID);
	}
	
	public static function listFlag(){
		return GWFlag::all();
	}
	
	public static function getFlag($flagID){
		$keys = array('FlagID' => $flagID);
		return GWFlag::find($keys);
	}
	
	public static function saveFlag($flagName, $flagValue){
		$gwFlag = new GWFlag();
		//$gwFlag->set_FlagID($flagID);
		$gwFlag->set_FlagName($flagName);
		$gwFlag->set_FlagValue($flagValue);
		$returnVal = $gwFlag->save();
		
		return array('FlagID' => $returnVal);
	}
	
	public static function listFlagSet(){
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
	
	public static function getSession($sessionID) {
	
		$keys = array('SessionID' => $sessionID);
		return GWSession::find($keys);
		
	}
	
	public static function saveSession($user_name, $surveyCompleted, $duration, $surveyTakenDate, $ip, $city, $country) {
	
		$session = new GWSession();
		$session->set_User_name($user_name);
		$session->set_SurveyCompleted($surveyCompleted);
		$session->set_Duration($duration);
		$session->set_SurveyTakenDate($surveyTakenDate);
		$session->set_IP($ip);
		$session->set_City($city);
		$session->set_Country($country);
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
	
	public static function saveResponse($sessionID, $questionnaireID, $question_Number, $answerNumber, $responceType, $responceContent, $codeToProcessResponce, $processingResult) {
	
		$response = new GWResponse();
		$response->set_SessionID($sessionID);
		$response->set_QuestionnaireID($questionnaireID);
		$response->set_Question_Number($question_Number);
		$response->set_AnswerNumber($answerNumber);
		$response->set_ResponceType($responceType);
		$response->set_ResponceContent($responceContent);
		$response->set_CodeToProcessResponce($codeToProcessResponce);
		$response->set_ProcessingResult($processingResult);
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
	
	public static function saveQuestionnaire($Title, $Topic, $AllowAnonymous, $AllowMultiple, $CreatorName, $DateCreated) {
		$questionnaire = new GWQuestionnaire();
		$questionnaire->set_Title($Title);
		$questionnaire->set_Topic($Topic);
		$questionnaire->set_AllowAnonymous($AllowAnonymous);
		$questionnaire->set_AllowMultiple($AllowMultiple);
		$questionnaire->set_CreatorName($CreatorName);
		$questionnaire->set_DateCreated($DateCreated);
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
	
	public static function saveAction($QuestionnaireID, $ActionType, $Question_Number, $Content, $Duration, $LinkToAction, $Sequence) {
		$action = new GWAction();
		//$action->set_ActionID($ActionID);
		$action->set_QuestionnaireID($QuestionnaireID);
		$action->set_ActionType($ActionType);
		$action->set_Question_Number($Question_Number);
		$action->set_Content($Content);
		$action->set_Duration($Duration);
		$action->set_LinkToAction($LinkToAction);
		$action->set_Sequence($Sequence);
		$returnVal = $action->save();
		
		return array('ActionID' => $returnVal);
	}

	// For GWAnswerChoice
	public static function listAnswerChoice($QuestionnaireID,$Question_Number){
		$keys = array('QuestionnaireID'=>$QuestionnaireID,'Question_Number'=>$Question_Number);
		return 	GWAnswerChoice::find($keys);
	}
	
	public static function saveAnswerChoice($OptionNumber, $QuestionnaireID, $Qustion_Number, $AnsValue) {
		$answerChoice = new GWAnswerChoice();
		$answerChoice->set_OptionNumber($OptionNumber);
		$answerChoice->set_QuestionnaireID($QuestionnaireID);
		$answerChoice->set_Question_Number($Qustion_Number);
		$answerChoice->set_AnsValue($AnsValue);
		$answerChoice->save();
                
		return array('OptionNumber' => $OptionNumber,'QuestionnaireID'=>$QuestionnaireID,
                    'Question_Number'=>$Qustion_Number);
	}
		
}	

?>