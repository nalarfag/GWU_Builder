
<?php 
$url= WP_PLUGIN_URL . '/GWU_Builder/images/NPS.png';
$adminURL= admin_url('admin-post.php');
$QuestionNum= GWUQuestionnaireAdmin::getNextQuestionNumber($_GET['Qid']);


require dirname(__FILE__) . '/Header.php';
?>
<div class='content_q'>
        <form method="post" action="<?php echo $adminURL; ?>">
	<input type="hidden" name="action" value="add_new_question" />
        <input type="hidden" name="answer_type" value="NPS" />
        <input type="hidden" name="answer_type_short" value="NPS" />
         <input type="hidden" name="QuestionnaireID" value="<?php echo $_GET['Qid']; ?>" />
            <table>
                 <tr>
                <td class="style1">
                    <img alt="" src=<?php echo $url; ?>
                         class="Sampleimage" />
                </td>
                </tr>
                <tr>
                  <tr>
                <td class="style1">
                    
                        Question Number:    
                    <input type="text" name="question_Number" size="1" value="<?php echo $QuestionNum;?>" />
                </td>
                </tr>
                <td class="style1">
                    <p>
                        Question Text:</p>
                    <input type="text" name="question_text" size="55" />
                </td>
                </tr>
                  <tr>
            <td class="style1">
                 <p>Detractor / Promoter Heading</p>
                 </td>
            </tr>
                <tr>
                <td class="style1">
                    Detractor:
                    <input type="text" name="Detractor" size="20" />
                </td>
                 <tr>
                <td class="style1">
                    Promoter:
                    <input type="text" name="Promoter" size="20" />
                </td>
                </tr>
                       <tr>
                <td class="style1">Mandatory: 


                    <span>
                        <input id="element_4_1" name="Mandatory" class="element radio" type="radio" value="1" />
                        <label class="choice" for="Mandatory_1">yes</label>
                        <input id="element_4_2" name="Mandatory" class="element radio" type="radio" value="0" />
                        <label class="choice" for="Mandatory_2">no</label>

                    </span> 
                     
                </td>
                </tr>
                  <tr>  <td align="right"> 	
                          <input type="submit" value="Submit" class="button-primary"/>
                </td> </tr>
	</table>
    </form>
</div>



<?php require dirname(__FILE__) . '/Footer.php'; ?>