<?php
//include_once 'C:\xampp\htdocs\wordpress\wp-content\plugins\GWU_Builder\models\GWWrapper.php';
include_once dirname( __FILE__ ) . '/models/GWWrapper.php';
use WordPress\ORM\Model\GWWrapper;
/**
 * Description of response
 *
 * Display the questionnaire and save users' response
 *
 * @author Kaihua Wu(Michael)
 * Some part by Darshan suchin
 *
 */
add_shortcode('questionnaire', 'Response_questions');
/*get next question sequence function*/
function getNextQuestion($SessionID,$ConditionID)
{
	    $Wrapper= new GWWrapper();
		$FlagValues = array();
	 //1) get all the responses for this session id..i.e we need listResponses($SessionID)
	 //2) iterate all the responses and get the flag values
     //3) store all the flagnames value

		$arr_responsID = $Wrapper->listResponsesBySessionId($SessionID);
		if($arr_responsID!=null){
		foreach($arr_responsID as $temp) {
			$ResponseId = $temp->get_ResponseID();
			$arrAllResponses = $Wrapper->getFlagsByQuestionnaireQuestionOption($temp->get_QuestionnaireID(),$temp->get_QuestSequence(),$temp->get_OptionNumber());
			//$flagObject= new GWFlag();
			if($arrAllResponses!=null)
			{
				$flagObject = $arrAllResponses[0];
			 $FlagName = $flagObject->get_FlagName();
			 $FlagValue = $flagObject->get_FlagValue();
			 $FlagValues[$FlagName]= $FlagValue;
			}

		}}
		$Conditions=$Wrapper->getCondition($ConditionID);
		$Condition=$Conditions[0];
		$LogicString= $Condition->get_LogicStatement();
		$AndParts=explode(" or ",$LogicString);//compute "and" first
        $IfSucess = FALSE;//if one "and" is true,it's true
        $NextSeqNum = '1';
        foreach ($AndParts as $AndPart)
        {
		  $IfAndSucess = TRUE;//if one value in and is false,it's false
		  $ValueParts = explode(" and ",$AndPart);//compute each value in and
		  foreach ($ValueParts as $ValuePart) {
			  $Parts = explode(" ",$ValuePart);
			  If(!isset($FlagValues[$Parts[1]]))
			        $IfAndSucess = FALSE;
			  elseif($Parts[2] == '==')
			  {If($FlagValues[$Parts[1]] <> $Parts[3])
				    $IfAndSucess = FALSE;
			  	}
			  elseif($Parts[2] == '>=')
			     {If($FlagValues[$Parts[1]] < $Parts[3])
				    $IfAndSucess = FALSE;}
			  elseif($Parts[2] == '<=')
			     {If($FlagValues[$Parts[1]] > $Parts[3])
				    $IfAndSucess = FALSE;}
			  elseif($Parts[2] == '>')
			    {If($FlagValues[$Parts[1]] <= $Parts[3])
				    $IfAndSucess = FALSE;}
			  elseif($Parts[2] == '<')
			    {If($FlagValues[$Parts[1]] >= $Parts[3])
				    $IfAndSucess = FALSE;}
			}
		If($IfAndSucess == TRUE)
		{
		   $IfSucess = TRUE;}   						
     }
     If($IfSucess)
        $NextSeqNum= $Condition->get_JumpQNoOnSuccess();
     else{
	    $NextSeqNum= $Condition->get_JumpQNoOnFailure();}
	return $NextSeqNum;		
}
/*get ip function*/
function Get_IP()
{
	if ($_SERVER["HTTP_X_FORWARDED_FOR"])
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if ($_SERVER["HTTP_CLIENT_IP"])
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if ($_SERVER["REMOTE_ADDR"])
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "Unknown";
	return $ip;

}
/*remove edit button function*/
function wpse_remove_edit_post_link( $link ) 
{
    return '';
}
add_filter('edit_post_link', 'wpse_remove_edit_post_link');

/*main funciton to show the questionnaire one by one*/
function Response_questions($atts)
{
	extract(shortcode_atts(array( "id"=> '1'),$atts));
	$Wrapper= new GWWrapper();//use orm
	$QSession = null;
	$QuestionnaireID = $id;	//Get questionnaire id
	//get the questionaire information through the $id
	$Questionnaires=$Wrapper->getQuestionnaire($QuestionnaireID);
	$Questionnaire=$Questionnaires[0];
	/*if there is no session for qno then set it to zero.this parts is for the case have Qsession but do not have sqno*/
	if(!isset($_SESSION['sqno']))
	{
		$_SESSION['sqno'] = '0';
    }
	/*this part is for the case that change to another questionnaire*/
    if(!isset($_SESSION['Qid']))
	{
	 	$_SESSION['Qid']=$id;
	}     
	else if($_SESSION['Qid'] !=$id)
	{
		$_SESSION['Qid']=$id;
		unset($_SESSION['QSession']);
	}
	/*this part is to record the session for the first time*/
	if(!isset($_SESSION['QSession'])) {	//Create new Session object
	    $current_user= wp_get_current_user();
	    if($current_user->user_login=='' && $Questionnaire->get_AllowAnnonymous()==0)
		{
			return 'Sorry, you need to login to do this survey';
		}
		$sessionID = $Wrapper->saveSession($current_user->user_login, Get_IP(), 'Washington', 'USA', '00:00:00.000000', date('Y-m-d'),0);
		$QSessions = $Wrapper->getSession($sessionID['SessionID']);
		$QSession = $QSessions[0];
		$_SESSION['QSession'] = $QSession;
		$_SESSION['sqno'] = '0';
	} else {	//Get current session object
		$QSession = $_SESSION['QSession'];
	}






	$Questions=$Wrapper->listQuestion($QuestionnaireID);
	if(empty($Questions))
             {
             $totalQuestionNum=0;
             }
             else
             {
                  $totalQuestionNum= sizeof($Questions);
             }

	/* write down questionnaire title*/
    $output='<p><font color="#545454"><small>Questionnaire:</small></font><br/> <big><strong>'.$Questionnaire->get_Title().'</strong></big></p><br/>';
	/*Check if it's the first question*/
	/*if it's new, set the $qno=0 else store last questionno in $qno */

	$qno=$_SESSION['sqno'];
	if($qno != '0' && $_POST["qno"]==$qno && $_POST["IfJump"]!= 1)
	{

		$questions=$Wrapper->getQuestion($qno, $QuestionnaireID);
		$question=$questions[0];
			/*else store the last question's response depends on it's type*/
				if($question->get_AnsType()=='Text Box')
		             $Wrapper->saveResponse($qno, $QSession->get_SessionID(), $QuestionnaireID, 1, $question->get_AnsType(), $_POST["response"], null, null);
				elseif($question->get_AnsType()=='Multiple Choice, Single Value')
				{
					$Wrapper->saveResponse($qno, $QSession->get_SessionID(), $QuestionnaireID, $_POST["response"], $question->get_AnsType(), $_POST[$_POST["response"]], null, null);
				}
				elseif($question->get_AnsType()=='Multiple Choice, Multiple Value')
				{
					if(!empty($_POST["response"]))
					{
						foreach($_POST["response"] as $response)
					    $Wrapper->saveResponse($qno, $QSession->get_SessionID(), $QuestionnaireID, $response, $question->get_AnsType(), $_POST[$response], null, null);
				    }
				}
				else	//NPS
				{

					$Wrapper->saveResponse($qno, $QSession->get_SessionID(), $QuestionnaireID, $_POST["response"], $question->get_AnsType(), $_POST["response"], null, null);	
				}
	}/*$QSession->get_SessionID()*/

	/*if last question is the final question show thankyou*/
	if($qno == $totalQuestionNum && $_POST["qno"]==$qno || $totalQuestionNum==0)	
	{
		$gwsession = $_SESSION['QSession'];	//Get GWSession object from browser session
		$gwsession->set_SurveyCompleted(1);	//set properties to be updated
		$gwsession->update();	//This will update the GWSession object in the database
		unset($_SESSION['QSession']);
		return 'Thank your for participating our survey';

	}
	/*else $qno +=1 Get the question and answerchoice from the database with $qno and $QuestionnaireID 
	 * store in $quesion and $Anserchoices*/
	else
    {
    	if($_POST["qno"]==$qno || $qno=='0')
		{
			$qno += 1;
		    $_SESSION['sqno'] = $qno;
			$QueFoCons=$Wrapper->getQuestion($qno, $QuestionnaireID);
			$QueFoCon=$QueFoCons[0];
			if($QueFoCon->get_ConditionID() != null )
			{
				$JumpNum =	getNextQuestion($QSession->get_SessionID(),$QueFoCon->get_ConditionID());
				while($JumpNum != $qno)
				{
					$qno = $JumpNum;
					$QueFoCons=$Wrapper->getQuestion($qno, $QuestionnaireID);
			        $QueFoCon=$QueFoCons[0];
					if($QueFoCon->get_ConditionID() != null )
						$JumpNum =	getNextQuestion($QSession->get_SessionID(),$QueFoCon->get_ConditionID());
				}
				$_SESSION['sqno'] = $qno;
			}

		}	
	    $questions=$Wrapper->getQuestion($qno, $QuestionnaireID);
        $question=$questions[0];
		$Answerchoices=$Wrapper->listAnswerChoice($QuestionnaireID,$qno);
		$Actions=$Wrapper->listActions($QuestionnaireID,$qno);
		$IfMandatory='';
		$CheckFun='';
		$IfMCMVMandatory='';
		If($question->get_Mandatory()==1)
		{
			$IfMandatory='style="display: none"';
			if($question->get_AnsType()=='Text Box')
			{
				$IfMCMVMandatory='onsubmit="javascript:return chkTextBox();"';
				$CheckFun='<script>
                      function chkTextBox() {
                      	var obj = document.getElementById("response");
                      	var objYN = false; 
						var value = obj.value;
                        value = value.replace(/\s/g,"");
						if(value=="")
						{
							objYN=true;
						}
						if (objYN) {
							alert("This is a mandatory question.You have to write your answer");
							return false;
						} 
						else {
							return true;
						}
					  }</script>';
			}
			if($question->get_AnsType()=='Multiple Choice, Multiple Value')
			{
				$IfMCMVMandatory='onsubmit="javascript:return chkCheckBox();"';
				$CheckFun='<script>
                      function chkCheckBox() {
                      	var obj = document.getElementsByName("response[]"); 
                      	var objLen = obj.length;
                      	var objYN = false; 
                      	for (var i = 0; i < objLen; i++) {
                      		if (obj [i].checked == true) {
                      			objYN = true;
                      			break;
							}
						}
						if (!objYN) {
							alert("This is a mandatory question,you need at least choose one");
							return false;
						} 
						else {
							return true;
						}
					  }</script>';

			}
		}
		/*show question text*/
		$output .= $CheckFun.'<form action="" method="post" '.$IfMCMVMandatory.'>
		            <strong>'.$qno.". ".$question->get_Text().'</strong><br/>
		            <input type="checkbox" value=1 name="IfJump" '.$IfMandatory.'/><font '.$IfMandatory.'>Skip this quesion</font><hr/>';
		/*show action*/
		if(!empty($Actions))
	    {
	    	    $max=0;
				$links=array();
				$types = array();
	    	    foreach ($Actions as $Action) {
					if($Action->get_Sequence()>$max)
					   $max=$Action->get_Sequence();

				}
				for($i=1;$i<=$max;$i++)//put the action order by sequence, put the action with same sequence together
				{
					foreach ($Actions as $Action) {
						if($Action->get_Sequence()==$i)
						{
							if(!isset($links[$i]))
							 {
							 	$links[$i] = array();
								$types[$i] = array();
								$links[$i][0] = $Action->get_LinkToAction();
								$types[$i][0] = $Action->get_ActionType();
							 }
							 else 
							 {
							 	$j=0;
								while(isset($links[$i][$j]))
								{
									$j++;
							    }
								$links[$i][$j] = $Action->get_LinkToAction();
								$types[$i][$j] = $Action->get_ActionType();
							 }
						}
					}
				}
			  	$output .='<body onload="LoadAction()"><p>references:<br/>
			  	<style type="text/css">
                img
                {
                    max-height: 415px;
                    max-width: 560px
                }
                </style>
			  	<script type="text/javascript">
                var links = new Array();
                var types = new Array();
                var num = '.sizeof($links).';
                ';
				$j=0;
				for($i=1;$i<=$max;$i++)
				{
					if(isset($links[$i]))
					{
						$output .='links['.$j.'] = new Array();
						types['.$j.'] = new Array();';
						for($z=0;$z<sizeof($links[$i]);$z++)
						{
							$output .='links['.$j.']['.$z.']="'.$links[$i][$z].'";
							types['.$j.']['.$z.']="'.$types[$i][$z].'"
							';
						}
					    $j++;
					}
				}
                $output .='
                function ClearAllNode(parentNode)
                {
                    while (parentNode.firstChild) 
                    {
                    var oldNode = parentNode.removeChild(parentNode.firstChild);
                    oldNode = null;
                    }
                }
                function LoadAction() 
                {
                     document.getElementById("ActSeq").value = "0";
                     var ShowAct= document.getElementById("ShowAct");
                     ClearAllNode(ShowAct);
                     document.getElementById("NextAct").style.display = "none";
                     var NewAct=Array();
                     for(var i=0;i<links[0].length;i++)
                     {
                     	if(types[0][i]=="Image")
                     	{
                     		NewAct[i] = document.createElement("img")
                     		NewAct[i].src=links[0][i];
                     		NewAct[i].alt = "not";
                     		ShowAct.appendChild(NewAct[i]);
                     		var newline= document.createElement("br"); 
                     		ShowAct.appendChild(newline); 
						}
						else if(types[0][i]=="Video")
						{
							NewAct[i] = document.createElement("iframe")
							NewAct[i].style.width = "560px";
							NewAct[i].style.height = "415px";
							NewAct[i].src=links[0][i];
							ShowAct.appendChild(NewAct[i]);
							var newline= document.createElement("br"); 
							ShowAct.appendChild(newline); 
						}
					 }
					 if (num != 1) 
					 {
					 	document.getElementById("NextAct").style.display = "inline"
					 }
               }
               function changeaction() 
               {
               	document.getElementById("ActSeq").value++;
               	var ShowAct= document.getElementById("ShowAct");
               	ClearAllNode(ShowAct);
               	document.getElementById("NextAct").style.display = "none";
               	var ActSeq = document.getElementById("ActSeq").value;
               	if (ActSeq != num - 1) 
               	{
               		document.getElementById("NextAct").style.display = "inline"
				}
				var NewAct=Array();
				for(var i=0;i<links[ActSeq].length;i++)
				{
					if(types[ActSeq][i]=="Image")
					{
						NewAct[i] = document.createElement("img")
						NewAct[i].src = links[ActSeq][i];
						NewAct[i].alt = "not";
						ShowAct.appendChild(NewAct[i]);
						var newline = document.createElement("br");
						ShowAct.appendChild(newline); 
					}
					else if(types[ActSeq][i]=="Video")
					{
						NewAct[i] = document.createElement("iframe")
						NewAct[i].style.width = "560px";
						NewAct[i].style.height = "415px";
						NewAct[i].src = links[ActSeq][i];
						ShowAct.appendChild(NewAct[i]);
						var newline = document.createElement("br");
						ShowAct.appendChild(newline); 
					}
				}
               }
               </script>
               <input type="hidden" id="ActSeq" value="0"/>  
               <div id="ShowAct">
               </div>          
               <button id="NextAct" onclick="changeaction()" type="button">next</button>
               </p><hr/></body>
               ';

		 }
	    /*show the qno's quesion depends on it's type*/
	    if($question->get_AnsType()=='Text Box')//text
		          {
			          $output .='
			          <input type="hidden" name="qno" value="'.$qno.'"/>
	                 
	                  <textarea id="response" cols="60" rows="9" name="response" ></textarea><br/>
                      <br/><input type="submit" value="next"></form>';
				  }
		     elseif($question->get_AnsType()=='Multiple Choice, Single Value')//mcsv
		          {
			         $output .='
			            <input type="hidden" name="qno" value="'.$qno.'"/>';
						if(empty($Answerchoices))
						{
							$output .='<br/>your data is invalid because there is no answerchoice';
						}
						else
						{
							$no = 0;
							foreach($Answerchoices as $answerchoice)
					        {
					        	$no++;
								$checked='';
							    if($no==1)
								  $checked='checked="checked"';
					 	       $output .= '<br/><input type="hidden" name="'.$answerchoice->get_OptionNumber().'" value="'.$answerchoice->get_AnsValue().'" />
					 	       <input name="response" type="radio" value="'.$answerchoice->get_OptionNumber() .'"'.$checked.' />'.$answerchoice->get_AnsValue();
					        }
						}

                        $output .='<br/><br/><input type="submit" value="next"></form>';

		           }
		       elseif($question->get_AnsType()=='Multiple Choice, Multiple Value')//mcmv
		         {

			         $output .='
			             <input type="hidden" name="qno" value="'.$qno.'"/>';
						 if(empty($Answerchoices))
						{
							$output .='<br/>your data is invalid because there is no answerchoice';
						}
						else
						{
				    	 foreach($Answerchoices as $answerchoice)
					      {
					 	     $output .= '<br/><input type="hidden" name="'.$answerchoice->get_OptionNumber().'" value="'.$answerchoice->get_AnsValue().'"/>
					 	     <input name="response[]" type="checkbox" value="'.$answerchoice->get_OptionNumber() .'" />'.$answerchoice->get_AnsValue();
					      }
						}
                     $output .='<br/><br/><input type="submit" value="next"></form>';
		            }
		      else //nps
		        {
			         $output .= '
			               <input type="hidden" name="qno" value="'.$qno.'"/><table><tr><td></td>';
						if(empty($Answerchoices)||sizeof($Answerchoices)<13)
						{
							$output .='<br/>your data is invalid because the answerchoices is not enough, please update your db';
						}
						else
						{ 
                          for ($i = 0; $i < 11; $i++) 
                          {
                        	
							 $checked='';
							 if($i==0)
								  $checked='checked="checked"';
                             $output .= '<td><input name="response" type="radio" value="'.$i .'" '.$checked.'/>&nbsp;</td>';
                           }
                          $output .= '<td></td></tr><tr><td>' . $Answerchoices[11]->get_AnsValue() . ' </td>';
                          for ($i = 0; $i < 11; $i++) 
                           {
                             $output .= '<td>' . $i . '</td>';
						   }
                           $output .= '<td>' . $Answerchoices[12]->get_AnsValue(). ' </td></tr></table>';
						}
                        $output .= '<br/><br/><input type="submit" value="next"></form>';
				}



		}
             /*return html*/	

	        return $output;	 
}


?>