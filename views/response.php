<?php
/*
  Plugin Name:  My test
  Plugin URI:
  Description: This plugin create the necessary tables for the builder part
 *  of the Questionnaire plugin
  Version: 1.0
  Author: Builder team
  Author URI:
 */


add_shortcode('showquestionnaireforpost', 'Response_questions');

function Response_questions($atts,$content = null)
{   "Qnid" => ''
	/*get the questionnaire information through the $Qnid, write down title*/
	$output='<p>' . Title . '</p><hr/>';
	/*Check if it's the first question*/
	/*if it's new, set the $qno=0 else store last questionno in $qno*/
	$qno='0';
	if(isset($_POST["qno"]))
	{
		$qno = $_POST["qno"];
	}
	/*else store the last question's response depends on it's type*/
	/*if last question is the final question show thankyou*/
	if($qno =='maxqno')
	{
		$output = 'Thanks for your participation';
	}
	/*else $qno +=1 Get the question and answerchoice from the database with $qno and $Qnid store in $quesion and $Anserchoices*/
	else
    {
		$qno += 1;
		
		/*show the qno's quesion depends on it's type*/
		if($question->AnsType=='Text Box')
		{
			$output .='<form action="" method="post">'.$qno.'
	                 <input type="hidden" name="qno" value="'.$qno.'"/><br/> 
	                 <p>'.$qno.$question->title.'</p>
	                 <textarea  cols="30" rows="5" name="response"/><br/>
                     <input type="submit" /></form>';
		}
		elseif($question->Type=='Multiple Choice, Single Value')
		{
			$output .='<form action="" method="post">'.$qno.'
	                 <input type="hidden" name="qno" value="'.$qno.'"/><br/> 
	                 <p>'.$qno.$question->title.'</p>';
					 foreach($answerchoices as $answerchoice)
					 {
					 	 $output .= '<br/><input name="response" type="radio" value="'.$answerchoice->Option_Number .'" />';
					 }
            $output .='<br/><input type="submit" /></form>';

		}
		elseif($question->Type=='Multiple Choice, Multiple Value')
		{
			$output .='<form action="" method="post">'.$qno.'
	                 <input type="hidden" name="qno" value="'.$qno.'"/><br/> 
	                 <p>'.$qno.$question->title.'</p>';
					 foreach($answerchoices as $answerchoice)
					 {
					 	 $output .= '<br/><input name="response" type="checkbox" value="'.$answerchoice->Option_Number .'" />';
					 }
            $output .='<br/><input type="submit" /></form>';
		}
		else 
		{
			$output .= '<table><tr><td></td>';
                    for ($i = 0; $i < 10; $i++) {
                        $output .= '<form action="" method="post">
                        <input type="hidden" name="qno" value="'.$qno.'"/><br/> 
	                   <p>'.$qno.$question->title.'</p>
                        <td><input "response" type="radio" value="'.$i .'"/>&nbsp;</td>';
                    }
                    $output .= '<td></td></tr><tr><td>' . $answerchoices[10]->get_AnsValue() . ' </td>';
                    for ($i = 1; $i < 11; $i++) {
                        $output .= '<td>' . $i . '</td>';
                    }
                    $output .= '<td>' . $answerchoices[11]->get_AnsValue() . ' </td></tr></table><br/><input type="submit" /></form>';
		}
		
	}
	/*return html*/	
	
	return $output;	
}


?>