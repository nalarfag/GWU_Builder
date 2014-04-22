<?php
//include_once 'C:\xampp\htdocs\wordpress\wp-content\plugins\GWU_Builder\models\GWWrapper.php';
include_once dirname( __FILE__ ) . '/models/GWWrapper.php';
use WordPress\ORM\Model\GWWrapper;
add_shortcode('questionnaire', 'Response_questions');
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
function Response_questions($atts)
{
	extract(shortcode_atts(array( "id"=> '1'),$atts));
	$Wrapper= new GWWrapper();
	$QSession = null;
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
		$sessionID = $Wrapper->saveSession($current_user->user_login, Get_IP(), 'Washington', 'USA', '00:00:00.000000', date('Y-m-d'),0);
		$QSessions = $Wrapper->getSession($sessionID['SessionID']);
		$QSession = $QSessions[0];
		$_SESSION['QSession'] = $QSession;
		$_SESSION['sqno'] = '0';
	} else {	//Get current session object
		$QSession = $_SESSION['QSession'];
	}
	
	
	/*get the questionaire information through the $id, write down title*/

	
    $QuestionnaireID = $id;	//Get questionnaire id
	$Questions=$Wrapper->listQuestion($QuestionnaireID);
	if(empty($Questions))
             {
             $totalQuestionNum=0;
             }
             else
             {
                  $totalQuestionNum= sizeof($Questions);
             }
    $Questionnaires=$Wrapper->getQuestionnaire($QuestionnaireID);
	$Questionnaire=$Questionnaires[0];
	
    $output='<p><font color="#545454"><small>Questionnaire:</small></font><br/> <font size="20px"><strong>'.$Questionnaire->get_Title().'</strong></font></p><br/>';
	/*Check if it's the first question*/
	/*if it's new, set the $qno=0 else store last questionno in $qno */
	
	$qno=$_SESSION['sqno'];
	if($qno != '0' && $_POST["qno"]==$qno)
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
	if($qno == $totalQuestionNum && $_POST["qno"]==$qno)	//Test this condition to check if the last question is displayed properly.
	{
		unset($_SESSION['QSession']);
		$output = 'Thank your for participating our survey';
		
	}
	/*else $qno +=1 Get the question and answerchoice from the database with $qno and $QuestionnaireID 
	 * store in $quesion and $Anserchoices*/
	else
    {
    	if($_POST["qno"]==$qno || $qno=='0')
		{
			$qno += 1;
		    $_SESSION['sqno'] = $qno;
		}	
	    $questions=$Wrapper->getQuestion($qno, $QuestionnaireID);
        $question=$questions[0];
		$Answerchoices=$Wrapper->listAnswerChoice($QuestionnaireID,$qno);
		$Actions=$Wrapper->listActions($QuestionnaireID,$qno);
	    /*show the qno's quesion depends on it's type*/
	    if($question->get_AnsType()=='Text Box')
		          {
			          $output .='<form action="" method="post">
			          <input type="hidden" name="qno" value="'.$qno.'"/>
	                 <strong>'.$qno.". ".$question->get_Text().'</strong><hr/>
	                  <textarea  cols="30" rows="5" name="response" ></textarea><br/>
                      <br/><input type="submit" value="next"></form>';
				  }
		     elseif($question->get_AnsType()=='Multiple Choice, Single Value')
		          {
			         $output .='<form action="" method="post">
			            <input type="hidden" name="qno" value="'.$qno.'"/>
	                    <strong>'.$qno.". ".$question->get_Text().'</strong><hr/>';
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
		       elseif($question->get_AnsType()=='Multiple Choice, Multiple Value')
		         {
			         $output .='<form action="" method="post">
			             <input type="hidden" name="qno" value="'.$qno.'"/>
	                     <strong>'.$qno.". ".$question->get_Text().'</strong><hr/>';
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
		      else 
		        {
			         $output .= '<form action="" method="post">
			               <input type="hidden" name="qno" value="'.$qno.'"/>
	                       <strong>'.$qno.". ".$question->get_Text().'</strong><hr/><table><tr><td></td>';
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
              if(!empty($Actions))
			  {
			  	$output .='<hr/><p>references:<br/>';
				foreach ($Actions as $Action) 
				{
					if($Action->get_ActionType()=='Image')
					{
						$output.='<img src="'.$Action->get_LinkToAction().'"><br/><br/>';
					}
					else if($Action->get_ActionType()=='Video')
					{
						$output.='<video width="320" height="240" controls>
                               <source src="'.$Action->get_LinkToAction().'" type="video/mp4"></video><br/><br/>';
					}
				}
                $output .='</p>';
			  }
			  
			
		}
		
	/*return html*/	
	
	return $output;	
            
	
	
}


?>