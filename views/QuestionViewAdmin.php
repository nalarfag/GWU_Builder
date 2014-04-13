<script type='text/javascript'>

        
    jQuery( document ).ready( function($) {  
        $(this).find(".button-primary").hide();
                $(document).on('mouseenter', '.divbutton', function () {
                    $(this).find(".button-primary").show();
                }).on('mouseleave', '.divbutton', function () {
                    $(this).find(".button-primary").hide();
                });
            });
            
</script>
<div>

    
<?php
$adminURL= admin_url('admin-post.php');
 foreach ($questions as $question) {
                $Title = $question->get_Text();
                $type = $question->get_AnsType();
                $questionno = $question->get_QuestionNumber();
                $QuestionSeq=$question->get_QuestSequence();
                ?>
                  <div class="divbutton" >
                 <form id="view_question"  method="post" action="<?php echo $adminURL; ?>">
                <input type="hidden" name="action" value="question_handler" />
                <input type="hidden" name="QuestionnaireID" value="<?php echo $QuestionnaireID; ?>" />
                <input type="hidden" name="QuestionSeq" value="<?php echo $QuestionSeq; ?>" />
              
                   
              
                <table>
                  
                 <tr>
                <th colspan="100%" align="left">
                   <input type="submit" name="add " value="add" class="button-primary"/>
                    <input type="submit" name="edit" value="edit" class="button-primary"/>
                    <input type="submit" name="logic" value="logic" class="button-primary"/>
                    <input type="submit" name="action" value="action" class="button-primary"/>
                    <input type="submit" name="delete" value="delete" class="button-primary"/>
                </th>
                </tr>
                 <tr>
                <th colspan="100%" align="left">
                   <?php echo $questionno; ?> &nbsp;&nbsp;&nbsp; <?php echo $Title; ?>
                </th>
                </tr>
                    <?php
                $answerchoices = $Wrapper->listAnswerChoice($QuestionnaireID, $QuestionSeq);

                if ($type == 'Text Box') {
                  echo '
                 <tr>
                <td class="style1">
                <textarea  cols="30" rows="5"> </textarea></td>
                </tr>';
                } elseif ($type == 'NPS') {

                   echo '<tr><td></td>';
                    for ($i = 0; $i < 10; $i++) {
                        echo  '<td><input name="' . $questionno . '" type="radio"
                            value="' . $answerchoices[$i]->get_OptionNumber() . '"/>&nbsp;</td>';
                    }
                   echo '<td></td></tr>
                       <tr><td>' . $answerchoices[10]->get_AnsValue() . ' </td>';
                    for ($i = 0; $i < 10; $i++) {
                        echo '<td>' . $answerchoices[$i]->get_AnsValue() . '</td>';
                    }
                   echo '<td>' . $answerchoices[11]->get_AnsValue() . ' </td></tr>';
                } elseif ($type == 'Multiple Choice, Single Value') {
                  
                    foreach ($answerchoices as $answerchoice) {
                        $answerchoicescontent = $answerchoice->get_AnsValue();

                       echo '
                          <tr>  <td class="style1">
                          <input name="' . $questionno . '" type="radio" value="' . $answerchoice->get_OptionNumber() . '"/> 
                           &nbsp;&nbsp;' . $answerchoicescontent . '</td></tr>';
                    }
                    
                } else {
                    foreach ($answerchoices as $answerchoice) {
                        $answerchoicescontent = $answerchoice->get_AnsValue();

                       echo ' <tr>  <td class="style1">
                           <input name="' . $questionno . '" value="' . $answerchoice->get_OptionNumber() . '" type="checkbox"/>
                               &nbsp;&nbsp;' . $answerchoicescontent . '</td></tr>';
                    }
                }
                echo '</table> </form> </div><hr/>';
            }
            
                echo' <h2>    <a class="add-new-h2" 
			href="' . add_query_arg(
                    array('page' => 'GWU_add-Questionnaire-page',
                'id' => 'new', 'Qid' => $QuestionnaireID,
                'type' => 'multipleS'), admin_url('admin.php'))
            . '">Add New Question</a></h2>';
           

           
?>
</div>