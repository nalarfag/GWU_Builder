<script type='text/javascript'>

        
    jQuery( document ).ready( function($) {  
        $(this).find(".button-primary").hide();
                $(document).on('mouseenter', '.divbutton', function () {
                    $(this).find(".button-primary").show();
                }).on('mouseleave', '.divbutton', function () {
                    $(this).find(".button-primary").hide();
                });
            });
            
 jQuery( document ).ready( function($) { 
 var id =1;
 $("#dialog-success").dialog({
	dialogClass   : "wp-dialog",
            autoOpen: false,
	      resizable: false,
            width: 300,
            modal: true,
             buttons: {
                "OK": function () { 
                    $(this).dialog("close");
                    }
                    }
 });
    $("#dialog-confirm-multiple").dialog({
	dialogClass   : "wp-dialog",
            autoOpen: false,
	      resizable: false,
            width: 300,
            modal: true,
            buttons: {
                "Yes": function () { 
		    var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		    var val=$('#view_question').serialize() ;
                    $(this).dialog("close");
		     $.ajax({
			  type: "POST",
			    url: ajax_url,
			    action: 'delete_question',
			  data:
			      {
			       action: 'delete_question',
			      value: val, 
                              id:id
			      }
			  ,
			  success: function(data) {
                              // id = $(this).closest().attr("id");
                              $("#dialog-success").dialog('open');
			   // $("#"+data).remove();
                           $("#QuestionView").load(location.href + " #QuestionView");
			  }

		});

                },
                "No": function () {
                    $(this).dialog("close");
                }
            }
        });
	$(document).on("click","#delete",function(e,ui){
	//$("#delete").click(function (e,ui) {
	 //   debugger;
            if(e.originalEvent) {
                e.preventDefault();
                id = $(this).closest('div').parent().attr('id');
             //   console.debug(id);
                $("#dialog-confirm-multiple").dialog('open');
                return false;
            }
        });
    });
            
</script>
<div id="QuestionView">

    
<?php
$adminURL= admin_url('admin-post.php');
 foreach ($questions as $question) {
                $Title = $question->get_Text();
                $type = $question->get_AnsType();
                $questionno = $question->get_QuestionNumber();
                $QuestionSeq=$question->get_QuestSequence();
		$Mandatory= ($question->get_Mandatory()==1 ?'*' : '' );

                ?>
		<div id="question_<?php echo $QuestionnaireID; ?>_<?php echo $QuestionSeq; ?>" class="wrap">
                  <div class="divbutton" >
                 <form id="view_question"  method="post" action="<?php echo $adminURL; ?>">
                <input type="hidden" name="action" value="question_handler" />
                <input type="hidden" name="QuestionnaireID" value="<?php echo $QuestionnaireID; ?>" />
                <input type="hidden" name="QuestionSeq" value="<?php echo $QuestionSeq; ?>" />
              
                   
              
                <table>
		   <?php if($PublishedFlag!=1) {?>
		 <tr>
                <th colspan="100%" align="left">

                   <input type="submit" name="add" value="Add" class="button-primary"/>
                    <input type="submit" name="edit" value="Edit" class="button-primary"/>
                    <input type="submit" name="logic" value="Logic" class="button-primary"/>
                    <input type="submit" name="addAction" value="Action" class="button-primary"/>
                    <input type="submit" id="delete" name="delete" value="Delete" class="button-primary"/>
                </th>
		</tr> <?php }?>
                 <tr>
                <th colspan="100%" align="left">
		   <?php echo $Mandatory;
		   echo $questionno; ?> &nbsp;&nbsp;&nbsp; <?php echo $Title; ?>
                </th>
                </tr>
                    <?php
                $answerchoices =  $this->Wrapper->listAnswerChoice($QuestionnaireID, $QuestionSeq);

                if ($type == 'Text Box') {
                      echo '  <input type="hidden" name="QuestioType" value="essay" />';
                  echo '
                 <tr>
                <td class="style1">
                <textarea  cols="30" rows="5"> </textarea></td>
                </tr>';
                } elseif ($type == 'NPS') {
                  echo '  <input type="hidden" name="QuestioType" value="NPS" />';
		   echo '<tr>';
                    for ($i = 0; $i <= 10; $i++) {
                        echo  '<td><input name="' . $questionno . '" type="radio"
                            value="' . $answerchoices[$i]->get_OptionNumber() . '"/>&nbsp;</td>';
                    }
		   echo '</tr>
		       <tr>';
                    for ($i = 0; $i <= 10; $i++) {
                        echo '<td>' . $answerchoices[$i]->get_AnsValue() . '</td>';
                    }
		   echo '</tr>';
		   echo '<tr>
		       <td colspan="6" align="left">'.$answerchoices[11]->get_AnsValue().'</td>
		       <td colspan="5" align="right">'.$answerchoices[12]->get_AnsValue().'</td>

		       </tr>';
                } elseif ($type == 'Multiple Choice, Single Value') {
                   echo '  <input type="hidden" name="QuestioType" value="multipleS" />';
                    foreach ($answerchoices as $answerchoice) {
                        $answerchoicescontent = $answerchoice->get_AnsValue();

                       echo '
                          <tr>  <td class="style1">
                          <input name="' . $questionno . '" type="radio" value="' . $answerchoice->get_OptionNumber() . '"/> 
                           &nbsp;&nbsp;' . $answerchoicescontent . '</td></tr>';
                    }
                    
                } else {
                      echo '  <input type="hidden" name="QuestioType" value="multipleM" />';
                    foreach ($answerchoices as $answerchoice) {
                        $answerchoicescontent = $answerchoice->get_AnsValue();

                       echo ' <tr>  <td class="style1">
                           <input name="' . $questionno . '" value="' . $answerchoice->get_OptionNumber() . '" type="checkbox"/>
                               &nbsp;&nbsp;' . $answerchoicescontent . '</td></tr>';
                    }
                }
		echo '</table> </form> </div><hr/></div>';
            }
	      if($PublishedFlag!=1) {
                echo' <h2>    <a class="add-new-h2" 
			href="' . add_query_arg(
                    array('page' => 'GWU_add-Questionnaire-page',
                'id' => 'new', 'Qid' => $QuestionnaireID,
                'type' => 'multipleS'), admin_url('admin.php'))
            . '">Add New Question</a></h2>';
	      }

           
?>
</div>
    
     <div id="dialog-confirm-multiple" title="Confirmation Required">
    <p>This item will be permanently deleted and cannot be recovered. Are you sure?</p>
  </div>
    
    <div id="dialog-success" title="Item deleted">
    <p>The item was successfully deleted</p>
  </div>
