
<?php 
$url= WP_PLUGIN_URL . '/GWU_Builder/images/MultiChoice.png';
$adminURL= admin_url('admin-post.php');
$QuestionNum= GWUQuestionnaireAdmin::getNextQuestionNumber($_GET['Qid']);

$content ='
        <form method="post" action="'. $adminURL.'">
	<input type="hidden" name="action" value="add_new_question" />
        <input type="hidden" name="answer_type" value="Multiple Choice, Multiple Value" />
        <input type="hidden" name="answer_type_short" value="multipleM" />
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
                    
                        Question Number:    
                    <input type="text" name="question_Number" size="1" value="'.$QuestionNum.'" />
                </td>
                </tr>
                   <tr>
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
                <td class="style1">Mondatary: 

                
                    <span>
                        <input id="element_4_1" name="Mondatary" class="element radio" type="radio" value="1" />
                        <label class="choice" for="Mondatary_1">yes</label>
                        <input id="element_4_2" name="Mondatary" class="element radio" type="radio" value="0" />
                        <label class="choice" for="Mondatary_2">no</label>

                    </span> 
                     
                </td>
                </tr>
                  <tr>  <td align="right"> 	
                          <input type="submit" value="Submit" class="button-primary"/>
                </td> </tr>
	</table>
	</form>';

require('template.php');?>