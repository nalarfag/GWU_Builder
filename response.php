<?php
//include_once 'C:\xampp\htdocs\wordpress\wp-content\plugins\GWU_Builder\models\GWWrapper.php';
include_once dirname( __FILE__ ) . '/models/GWWrapper.php';
use WordPress\ORM\Model\GWWrapper;
add_shortcode('questionnaire', 'Response_questions');

function Response_questions($atts)
{
	/*get the questionaire information through the $Qnid, write down title*/
	extract(shortcode_atts(array( "id"=> '1'),$atts));
	$Wrapper= new GWWrapper();
    $QuestionnaireID = $id;
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
				/*if($question->get_AnsType()=='Text Box')
		             $Wrapper->saveResponse(1,$QuestionnaireID,$question->get_QuestionNumber(),1,$question->get_AnsType(),$_POST["response"],null,null);
				elseif($question->get_AnsType()=='Multiple Choice, Single Value')
				{
					$Wrapper->saveResponse(1,$QuestionnaireID,$quesiton->get_QuestionNumber(), $_POST["response"],$question->get_AnsType(),$_POST[$_POST["response"]],null,null);
				}
				elseif($question->get_AnsType()=='Multiple Choice, Multiple Value')
				{
					foreach($_POST["response"] as $response)
					$Wrapper->saveResponse(1,$QuestionnaireID,$quesiton->get_QuestionNumber(),$response,$question->get_AnsType(),$_POST[$response],null,null);
				}
				else
				{
					$Wrapper->saveResponse(1,$QuestionnaireID,$quesiton->get_QuestionNumber(),$_POST["response"],$question->get_AnsType(),$_POST["response"],null,null);	
				}*/
	}

	
	/*if last question is the final question show thankyou*/
	if($qno == $totalQuestionNum)
	{
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
                      <br/><input type="submit" value="submit"></form>';
				  }
		     elseif($question->get_AnsType()=='Multiple Choice, Single Value')
		          {
			         $output .='<form action="" method="post">
	                    <input type="hidden" name="qno" value="'.$qno.'"/> 
	                    <p>'.$qno.". ".$question->get_Text().'</p>';
					    foreach($Answerchoices as $answerchoice)
					    {
					 	    $output .= '<br/><input type="hidden" name="'.$answerchoice->get_OptionNumber().'" value="'.$answerchoice->get_AnsValue().'"/>
					 	    <input name="response" type="radio" value="'.$answerchoice->get_OptionNumber() .'" />'.$answerchoice->get_AnsValue();
					      }
                        $output .='<br/><br/><input type="submit" value="submit"></form>';

		           }
		       elseif($question->get_AnsType()=='Multiple Choice, Multiple Value')
		         {
			         $output .='<form action="" method="post">
	                    <input type="hidden" name="qno" value="'.$qno.'"/> 
	                     <p>'.$qno.". ".$question->get_Text().'</p>';
				    	 foreach($Answerchoices as $answerchoice)
					      {
					 	     $output .= '<br/><input type="hidden" name="'.$answerchoice->get_OptionNumber().'" value="'.$answerchoice->get_AnsValue().'"/>
					 	     <input name="response[]" type="checkbox" value="'.$answerchoice->get_OptionNumber() .'" />'.$answerchoice->get_AnsValue();
					      }
                     $output .='<br/><br/><input type="submit" value="submit"></form>';
		            }
		      else 
		        {
			         $output .= '<form action="" method="post">
                            <input type="hidden" name="qno" value="'.$qno.'"/> 
	                       <p>'.$qno.". ".$question->get_Text().'</p><br/><table><tr><td></td>';
                        for ($i = 0; $i < 10; $i++) 
                        {
                           $output .= '
                           <td><input name="response" type="radio" value="'.$i .'"/>&nbsp;</td>';
                           }
                           $output .= '<td></td></tr><tr><td>' . $Answerchoices[10]->get_AnsValue() . ' </td>';
                        for ($i = 1; $i < 11; $i++) 
                         {
                           $output .= '<td>' . $i . '</td>';
						 }
                      $output .= '<td>' . $Answerchoices[11]->get_AnsValue() . ' </td></tr></table><br/><br/><input type="submit" value="submit"></form>';
				}
			
			
		}
		
	/*return html*/	
	
	return $output;	
            
	
	
}


?>