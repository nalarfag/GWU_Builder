<?php

use WordPress\ORM\Model\GWWrapper;

$adminURL = admin_url('admin-post.php');
$delbtnurl = WP_PLUGIN_URL . '/GWU_Builder/images/delete.png';

$Wrapper=new GWWrapper();
$QuestionnaireID=$_GET['Qid']; 
$QuestionSeq=$_GET['qno']; 
$Questions=$Wrapper->listQuestion($QuestionnaireID);
$Question=$Wrapper->getQuestion($QuestionSeq,$QuestionnaireID)[0];
$condition = null;
$conditions = array();

$flags = $Wrapper->getFlagsByQuestionnaire($QuestionnaireID);
if(is_array($flags)) {
	$flags = array_unique($flags);
} else {
	$flags = array();
}

if(isset($Question) and $Question->get_ConditionID() !== null) {
	$condition=$Wrapper->getCondition($Question->get_ConditionID())[0];
	$conditions = ConditionParser::parseCondition($condition->get_LogicStatement());
	//var_dump($conditions);
}
//$conditionsCounter=count($conditions);
$conditionsCounter=0;


function array_filter_callback($element) {
	if($element->get_QuestSequence() >= $_GET['qno']) {
		return TRUE;
	} else {
		return FALSE;
	}
}

$Questions = array_filter($Questions, 'array_filter_callback');

?>

<script  type='text/javascript'>

	var noOfConditions = <?php echo $conditionsCounter; ?>;

	function removeCondition(removeConditionBtn) {
		var conditionDiv = removeConditionBtn.parentNode;
		var parentDiv = conditionDiv.parentNode;
		parentDiv.removeChild(conditionDiv);
		noOfConditions = noOfConditions - 1;
	}
	
	jQuery( document ).ready( function($) {
	
		
		$(document).on('change','.select_test',function(){
						
			var data = {
					action: 'get_flag_values',
					QuestionnaireID: <?php echo $QuestionnaireID; ?>,
					FlagName: $(this).val()
				};
			if($(this).val() != "") {
			
			var flag_tag = this;
				jQuery.ajax({
					type: "post",
					url: ajaxurl,
					data: data,
					dataType: 'json',
					success: function(response){
							var id = $(flag_tag).attr('id').split('_')[1];
							$('#flagValues_' + id).html("");
							if(response.success) {
								$.each(response.result, function(key, obj) {
									$('#flagValues_' + id).append('<option value="' + obj.FlagValue + '">' + obj.FlagValue + '</option>');
								});
							}
					}
				});
			} else {
				$('#flagValues_' + $(this).attr('id').split('_')[1]).html("");
			}

		});
		
		
		$("#logicform").submit(function(event) {
		
			var conditionString = '';
			var validationError = false;
			$("#conditionsDiv").children().each(function() {
		
				$(this).children().each(function() {
			
					if($(this).attr('id').indexOf('flagNames_') > -1) {
						if($(this).val()) {
							$("#errorMsg_" + $(this).attr('id').split("_")[1]).html('');
							conditionString += '( ' + $(this).val();
						} else {
							$("#errorMsg_" + $(this).attr('id').split("_")[1]).html('Select Flag name');
							validationError = true;
						}
					} else if($(this).attr('id').indexOf('flagValues_') > -1) {
						if($(this).val()) {
							$("#errorMsg_" + $(this).attr('id').split("_")[1]).html('');
							conditionString += ' ' + $(this).val() + ' )';
						} else {
							
							$("#errorMsg_" + $(this).attr('id').split("_")[1]).html('Select Flag value');
							validationError = true;
						}
					} else if($(this).attr('id').indexOf('logicalOperator_') > -1) {
						if($(this).val()) {
							$("#errorMsg_" + $(this).attr('id').split("_")[1]).html('');
							conditionString += ' ' + $(this).val() + ' ';
						} else {
							$("#errorMsg_" + $(this).attr('id').split("_")[1]).html('Select logical operator');
							validationError = true;
						}
					} else {
						if($(this).val()) {
							$("#errorMsg_" + $(this).attr('id').split("_")[1]).html('');
							conditionString += ' ' + $(this).val();
						} else {
							//$("#errorMsg_" + $(this).attr('id').split("_")[1]).html('Select conditional operator');
							//validationError = true;
						}
					}
			
				});
		
			});
		
			$("#logicalCondition").val(conditionString);
		
			if(validationError) {
				event.preventDefault();
			}
			
			if($('#jumpOnSuccess').val()) {
				$('#jumpOnSuccessError').html('');
			} else {
				$('#jumpOnSuccessError').html('Select question to jumpto on success');
				event.preventDefault();
			}
			
			if($('#jumpOnFailure').val()) {
				$('#jumpOnFailureError').html('');
			} else {
				$('#jumpOnFailureError').html('Select question to jumpto on failure');
				event.preventDefault();
			}
		});
		
	});
	
	function cancelAndReturnToQuestionnaire(){
	
		window.location = '<?php echo admin_url("admin.php?page=GWU_add-Questionnaire-page&id=view&Qid=".$QuestionnaireID);?>';
		
	}
	
	function addConditionLine() {
	
			noOfConditions = noOfConditions + 1;
			var flagNames='<select class="element select medium select_test" id="flagNames_' + noOfConditions + '" name="flagNames_' + noOfConditions + '" ">';
			flagNames+='<option value="">(Select Flag)</option>';
			var flagValues='<select class="element select medium" id="flagValues_' + noOfConditions + '" name="flagValues_' + noOfConditions + '">';
			
			<?php
				
				
				if(!empty($flags)) {
					foreach ($flags as $flag) {
			?>
						flagNames+='<option value="<?php echo $flag->get_FlagName(); ?>"><?php echo $flag->get_FlagName(); ?></option>';
						
			<?php
					}
				}
			?>
		
			flagNames += '</select>';
			flagValues += '</select>';
			var operator='<select class="element select medium" id="operator_' + noOfConditions + '" name="operator_' + noOfConditions + '">';
			operator += '<option value="=="> == (equals)</option>';
			operator += '<option value=">="> >= (greater than equals)</option>';
			operator += '<option value="<="> <= (less than equals)</option>';
			operator += '<option value=">"> > (greater than)</option>';
			operator += '<option value="<"> < (less than)</option>';
			operator += '</select>';
			
			var removeCondition = '<input type="image" id="removeCondition_' 
				+ noOfConditions + '" name="removeCondition_' + noOfConditions 
				+ '" value="" onClick="removeCondition(this)" src="<?php echo $delbtnurl; ?>" style="position:absolute;"/>';
		
			var logicalOperator = '<select id="logicalOperator_' + noOfConditions + '" name="logicalOperator_' + noOfConditions + '">';
			logicalOperator += '<option value="and"> AND </option>';
			logicalOperator += '<option value="or"> OR </option>';
			logicalOperator += '</select>';
			
			var errorMessage = '<div id="errorMsg_'+ noOfConditions + '" style="color:red"></div>';
			
			if(noOfConditions > 1) {
				jQuery("#conditionsDiv").append('<div id="condition_' + noOfConditions + '">' + logicalOperator + flagNames + operator + flagValues + removeCondition + errorMessage + '</div>');
			} else {
				jQuery("#conditionsDiv").append('<div id="condition_' + noOfConditions + '" style="padding-left:1.6cm;">' + flagNames + operator + flagValues + removeCondition + errorMessage + '</div>');
			}
	
	}
	
	jQuery( document ).ready( function($) {
	
		<?php

			foreach($conditions as $c) {
		?>
		
		//Start
		
			addConditionLine();
			
			<?php
				$flagValues = $Wrapper->getFlagValuesByQuestionnaire($QuestionnaireID, $c->getFlagName());
				foreach($flagValues as $flagValue) {
			?>
				$("#flagValues_" + noOfConditions).append('<option value="<?php echo $flagValue->get_FlagValue(); ?>"><?php echo $flagValue->get_FlagValue(); ?></option>');
						
			<?php
				}
			?>

			$("#operator_" + noOfConditions).val(<?php echo '"'.$c->getConditionOperator().'"'; ?>);
			$("#flagNames_" + noOfConditions).val(<?php echo '"'.$c->getFlagName().'"'; ?>);
			$("#flagValues_" + noOfConditions).val(<?php echo '"'.$c->getFlagValue().'"'; ?>);
			if(noOfConditions > 1) {
				$("#logicalOperator_" + noOfConditions-1).val(<?php echo '"'.$c->getLogicalOperator().'"'; ?>);
			}
		
		//End
		
		<?php
			}
		?>
	
		<?php
			foreach ($Questions as $Q) {
		?>
			$('#jumpOnSuccess').append('<option value="<?php echo $Q->get_QuestSequence(); ?>"><?php echo $Q->get_QuestionNumber(); ?></option>');
			$('#jumpOnFailure').append('<option value="<?php echo $Q->get_QuestSequence(); ?>"><?php echo $Q->get_QuestionNumber(); ?></option>');
		<?php
			}
			
			if($condition != null and $condition->get_JumpQNoOnSuccess() != null) {
		?>
				$("#jumpOnSuccess").val('<?php echo $condition->get_JumpQNoOnSuccess(); ?>');
		<?php
			}
			
			if($condition != null and $condition->get_JumpQNoOnFailure() != null) {
		?>
				$("#jumpOnFailure").val('<?php echo $condition->get_JumpQNoOnFailure(); ?>');
		<?php
			}
		?>
	
	});
	

</script>

	
<div id="form_container">
	
				
	<h3>Define the logic for question number <?php echo $Question->get_QuestionNumber();?></h3>
	<form id="logicform" class="logic"  method="post" action="<?php echo $adminURL; ?>">
		<input type="hidden" name="action" value="save_condition" />
		<input type="hidden" name="QuestionnaireID" value="<?php echo $QuestionnaireID; ?>"/>
		
		<?php
			if($condition != null){
		?>
			<input type="hidden" name="ConditionID" value="<?php echo $condition->get_ConditionID(); ?>" />
		<?php
			}
		?>
		
		<input type="hidden" name="QuestionSeq" value="<?php echo $QuestionSeq; ?>"/>
		<input type="hidden" name="logicalCondition" id="logicalCondition" value=""/>
		
		<div id="conditionsDiv">
		
		</div>
		<input type="button" id="addCondition" name="addCondition" value="Add Condition" onClick="addConditionLine()"/>
		<br/>
		<br/>
		<div>
			<label for="jumpOnSuccess">Select Question number to branch to on success:</label>
			<select id="jumpOnSuccess" name="jumpOnSuccess">
				<option value="">(Select Question)</option>
			</select>
			<label id="jumpOnSuccessError" style="color:red"></label>
		</div>
		<div>
			<label for="jumpOnFailure">Select Question number to branch to on failure:</label>
			<select id="jumpOnFailure" name="jumpOnFailure">
				<option value="">(Select Question)</option>
			</select>
			<label id="jumpOnFailureError" style="color:red"></label>
		</div>
		<div>
			<input type="submit" id="save" name="save" value="Save"/>
			<input type="button" id="cancel" name="cancel" value="Cancel" onClick="cancelAndReturnToQuestionnaire()"/>
		</div>
	</form>	
</div>