
<?php
$url = WP_PLUGIN_URL . '/GWU_Builder/images/SingleChoice.png';
$adminURL = admin_url('admin-post.php');
$QuestionNum = GWUQuestionnaireAdmin::getNextQuestionNumber($_GET['Qid']);

require dirname(__FILE__) . '/Header.php';
?>
<div class='content_q'>
    <form method="post" action="<?php echo $adminURL; ?>">
        <input type="hidden" name="action" value="add_new_question" />
        <input type="hidden" name="answer_type" value="Multiple Choice, Single Value" />
        <input type="hidden" name="answer_type_short" value="multipleM" />
        <input type="hidden" name="QuestionnaireID" value="<?php echo $_GET['Qid']; ?>" />
        <table>
            <tr>
                <td class="style1">
                    <img alt="" src=<?php echo $url; ?>
                         class="Sampleimage" />
                </td>
            </tr>
            <tr>
                <td class="style1">

                    Question Number:    
                    <input type="text" name="question_Number" size="1" value="<?php echo $QuestionNum;?>" />
                </td>
            </tr>
            <tr>
                <td class="style1">
                    <p>
                        Question Text:</p>
                    <input type="text" name="question_text" size="55" />
                </td>
            </tr>
            <tr>
                <td class="style1">
                    <p>
                        Answers(one per line):</p>
                    <textarea name="answers" cols="55" rows="5"></textarea>
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
            <td>

            <tr>  <td align="right"> 	
                    <input type="submit" value="Submit" class="button-primary"/>
                </td> </tr>
        </table>
    </form>
</div>
<?php require dirname(__FILE__) . '/Footer.php'; ?>