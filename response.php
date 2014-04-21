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
	if(!isset($_SESSION['QSession'])) {	//Create new Session object
		$sessionID = $Wrapper->saveSession($current_user->user_login, Get_IP(), 'Washington', 'USA', '00:00:00.000000', date('Y-m-d'),0);
		$QSessions = $Wrapper->getSession($sessionID['SessionID']);
		$QSession = $QSessions[0];
		$_SESSION['QSession'] = $QSession;
	} else {	//Get current session object
		$QSession = $_SESSION['QSession'];
	}
	
	
	/*get the questionaire information through the $Qnid, write down title*/

	
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
	
    //$output='<p>'.$Questionnaire->get_Title().'</p><br/><hr/>';
	/*Check if it's the first question*/
	/*if it's new, set the $qno=0 else store last questionno in $qno */
	$qno='0';
	if(isset($_POST["qno"]))
	{
		$qno= $_POST["qno"];
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
					foreach($_POST["response"] as $response)
					$Wrapper->saveResponse($qno, $QSession->get_SessionID(), $QuestionnaireID, $response, $question->get_AnsType(), $_POST[$response], null, null);
				}
				else	//NPS
				{
					$Wrapper->saveResponse($qno, $QSession->get_SessionID(), $QuestionnaireID, $_POST["response"], $question->get_AnsType(), $_POST["response"], null, null);	
				}
	}/*$QSession->get_SessionID()*/
	
	/*if last question is the final question show thankyou*/
	if($qno == $totalQuestionNum)	//Test this condition to check if the last question is displayed properly.
	{
		unset($_SESSION['QSession']);
		$output = 'Thank your for participating our survey';
		
	}
	/*else $qno +=1 Get the question and answerchoice from the database with $qno and $QuestionnaireID 
	 * store in $quesion and $Anserchoices*/
	else
    {
		$qno += 1;
		$questions=$Wrapper->getQuestion($qno, $QuestionnaireID);
		/*show the qno's quesion depends on it's type*/
		$Answerchoices=$Wrapper->listAnswerChoice($QuestionnaireID,$qno);
		$question=$questions[0];
	    if($question->get_AnsType()=='Text Box')
		          {
			          $output .='<form action="" method="post">
	                  <input type="hidden" name="qno" value="'.$qno.'"/> 
	                 <p>'.$qno.". ".$question->get_Text().'</p>
	                  <textarea  cols="30" rows="5" name="response" ></textarea><br/>
                      <br/><input type="submit" value="next"></form>';
				  }
		     elseif($question->get_AnsType()=='Multiple Choice, Single Value')
		          {
			         $output .='<form action="" method="post">
	                    <input type="hidden" name="qno" value="'.$qno.'"/> 
	                    <p>'.$qno.". ".$question->get_Text().'</p>';
						if(empty($Answerchoices))
						{
							$output .='<br/>your data is invalid because there is no answerchoice';
						}
						else
						{
							foreach($Answerchoices as $answerchoice)
					        {
					 	       $output .= '<br/><input type="hidden" name="'.$answerchoice->get_OptionNumber().'" value="'.$answerchoice->get_AnsValue().'"/>
					 	       <input name="response" type="radio" value="'.$answerchoice->get_OptionNumber() .'" />'.$answerchoice->get_AnsValue();
					        }
						}
					    
                        $output .='<br/><br/><input type="submit" value="next"></form>';

		           }
		       elseif($question->get_AnsType()=='Multiple Choice, Multiple Value')
		         {
			         $output .='<form action="" method="post">
	                    <input type="hidden" name="qno" value="'.$qno.'"/> 
	                     <p>'.$qno.". ".$question->get_Text().'</p>';
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
		      else {
            $output .= '<form action="" method="post">
                            <input type="hidden" name="qno" value="' . $qno . '"/> 
	                       <p>' . $qno . ". " . $question->get_Text() . '</p><br/>
                                   <table><tr>';
            if (empty($Answerchoices)) {
                $output .='<br/>your data is invalid because there is no answerchoice';
            } else {
                for ($i = 0; $i <= 10; $i++) {
                    $output .= '
                           <td><input name="response" type="radio" value="' . $Answerchoices[$i]->get_OptionNumber() . '"/>&nbsp;</td>';
                }
                 $output .= '</tr><tr>';
                for ($i = 0; $i <= 10; $i++) {
                    $output .= '<td>' . $Answerchoices[$i]->get_AnsValue() . '</td>';
                }

                 $output .= '</tr>';
                 $output .= '<tr>
		       <td colspan="6" align="left">' . $Answerchoices[11]->get_AnsValue() . '</td>
		       <td colspan="5" align="right"  style="text-align: right;">' . $Answerchoices[12]->get_AnsValue() . '</td>
                            </tr> </table><br/><br/><input type="submit" value="submit"></form>';
            }
        }
    }
	/*return html*/	
	
	return $output;	
            
	
	
}


?>