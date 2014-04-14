<?php
include_once dirname(__FILE__).'/models/GWWrapper.php';
use WordPress\ORM\Model\GWWrapper;
add_shortcode('flag','Create_flag');
function Create_flag($atts)
{
	extract(shortcode_atts(array("QuestionnaireID"=>'1', "QuestionNO"=>'1'),$atts));
	$Wrapper=new GWWrapper();
	$Questions=$Wrapper->listQuestion($QuestionnaireID);
    //$CurrentQuestion=$Wrapper->getQuestion($QuestionNO,$QuestionnaireID);
	$fno='0';
	if(isset($_POST['flagnumber']))
	$fno=$_POST['flagnumber'];
	$fno++;

$output='

<html >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add logic:</title>

</head>
<body id="main_body" >
	
	
<div id="form_container">
	
		<h1><a>Add logic:</a></h1>
		<form id="logicform" class="logic"  method="post" action="">
		<input type="hidden"	name="flagnumber" value="'.$fno.'">		
					<div class="form_description">
			
			</div>						
			<ul >
			
					<li id="li_1" >';	
		
		for($i=1;$i<$fno+1;$i++)
		
		{
				
		$output.='<h2>Create flag conditon  '.$i.':</h2>
			<p>Define the logic for the selected question:</p>';
			
			if($i>1)
	{
			$output.='<li id="li_7" >
		<label class="description" for="element_7"> </label>
		<div>
		<select class="element select medium" id="element_7" name="element_7"> 
			<option value="" selected="selected"></option>
<option value="1" >and</option>
<option value="2" >or</option>

		</select>';
	}
		$output.='</div> 
		</li>
			<li id="li_6" >
		<label class="description" for="element_6">enter flag name: </label>
		<div>
			<input id="element_1" name="element_1" class="element text medium" type="text" maxlength="255" value=""/> 
		</div> 
		</li>	
		
			<label class="description" for="element_1">Select Flag :</label>
				
		<div>
		<select class="element select medium" id="element_1" name="element_1"> 
			<option value="" selected="selected"></option>
<option value="1" >F1</option>
<option value="2" >F2</option>
<option value="3" >F3</option>

		</select>
		</div> 
		</li>	
		<label class="description" for="element_8">Select Question:</label>
				
		<div>
		<select class="element select medium" id="element_8" name="element_8"> 
			<option value="" selected="selected"></option>';
			$j='1';
			if(!empty($Questions))
			{
			foreach($Questions as $Question)
			{
				
				$output.='<option value="'.$j.'" >'.$question->get_SequenceNumber().$question->get_Text().'</option>';
			    $j+=1; 
			}}

		$output.='</select>
		</div> 
		</li>			<li id="li_2" >
		<label class="description" for="element_2">Condition: </label>
		<div>
		<select class="element select medium" id="element_2" name="element_2"> 
			<option value="" selected="selected"></option>
<option value="1" >= (equals)</option>
<option value="2" ><= (greater than equals)</option>
<option value="3" >>= (less than equals)</option>
<option value="4" >< (greater than)</option>
<option value="5" >>(less than)</option>

		</select>
		</div> 
		</li>		<li id="li_3" >
		<label class="description" for="element_3">Answer choice: </label>
		<div>
		<select class="element select medium" id="element_3" name="element_3"> 
			<option value="" selected="selected"></option>';
			$AnswerChoices=$Wrapper->listAnswerChoice($QuestionnaireID,$QuestionNO);
			$j='1';
			if(!empty($AnswerChoices))
			{
			foreach($AnswerChoices as $AnswerChoice)
			
			{
				
				$output.='<option value="'.$j.'" >'.$AnswerChoice->get_OptionNumber().$AnswerChoice->get_AnsValue().'</option>';
			    $j+=1; 
			}}
$output.='
		</select>
		</div> 
		</li>
		
		
        <li><input id="saveForm" class="button_text" type="submit" name="submit" value="Add another logic" />';	
		}
		
		$output.='
		
        <h2>Set logic:</h2>
        <p>Set the logic for the selected question:</p>	
        <li id="li_4" >
		<label class="description" for="element_4">If the logic is true, then jump to: </label>
		<div>
		<select class="element select medium" id="element_4" name="element_4"> 
			<option value="" selected="selected"></option>
<option value="1" >Question number</option>
<option value="2" >go to Thank You page</option>

		</select>
		</div> 
		</li>		<li id="li_5" >
		<label class="description" for="element_5">otherwise jump to: </label>
		<div>
		<select class="element select medium" id="element_5" name="element_5"> 
			<option value="" selected="selected"></option>
<option value="1" >Question number</option>
<option value="2" >go to Thank You page</option>

		</select>
		</div> 
		</li>
			
					<li class="buttons">
			    <input type="hidden" name="form_id" value="826940" />
			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Create form" />
		</li>
			</ul>
		</form>	
	</div>
	</body>
</html>
';
return $output;}
?>