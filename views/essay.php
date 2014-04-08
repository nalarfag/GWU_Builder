<?php 
$url= WP_PLUGIN_URL . '/GWU_Builder/images/CommentBox.png';
$adminURL= admin_url('admin-post.php');

$content ='
        <form method="post" action="'. $adminURL.'">
	<input type="hidden" name="action" value="add_new_question" />
        <input type="hidden" name="answer_type" value="essay" />
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
                    <textarea name="question_text" cols="55" rows="2"></textarea>
                </td>
                 </tr>
                   
                  <tr>  <td align="right"> 	
                          <input type="submit" value="Submit" class="button-primary"/>
                </td> </tr>
	</table>
	</form>';

require('template.php');?>
