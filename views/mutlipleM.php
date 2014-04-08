
<?php 
$url= WP_PLUGIN_URL . '/GWU_Builder/images/MultiChoice.png';
$adminURL= admin_url('admin-post.php');

$content ='
        <form method="post" action="'. $adminURL.'">
	<input type="hidden" name="action" value="add_new_question" />
        <input type="hidden" name="answer_type" value="multipleM" />
         <input type="hidden" name="QuestionnaireID" value="'.$_GET['Qid'].'" />
            <table>
                 <tr>
                <td class="style1">
                    <img alt="" src='.$url.' 
                         class="Sampleimage" />
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
                  <tr>  <td align="right"> 	
                          <input type="submit" value="Submit" class="button-primary"/>
                </td> </tr>
	</table>
	</form>';

require('template.php');?>