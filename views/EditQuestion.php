<?php
$adminURL = admin_url('admin-post.php');

$QuestionnaireID = $_GET['Qid'];
$QuestionSeq = $_GET['qno'];
$questions = $Wrapper->getQuestion($QuestionSeq, $QuestionnaireID);
$text = $questions[0]->get_Text();
$number = $questions[0]->get_QuestionNumber();
$Mandatory = $questions[0]->get_Mandatory();
$type = $questions[0]->get_AnsType();
$answerchoices = $Wrapper->listAnswerChoice($QuestionnaireID, $QuestionSeq);
?>

<div class='wrap'  style="  padding-left: 25%;
     padding-top: 50px;" >
    <form id="add_question" method="post"  action="<?php echo $adminURL; ?>">
        <input type="hidden" name="action" value="edit_question" />
        <input type="hidden" name="type" value="<?php echo $type ?>" />
        <input type="hidden" name="QuestionnaireID" value="<?php echo $QuestionnaireID ?>" />
        <input type="hidden" name="QuestionSeq" value="<?php echo $QuestionSeq ?>" />
        <table>

            <tr>
                <td class="style1">

                    Question Number:    
                    <input type="text" id="question_Number" name="question_Number" size="1" value="<?php echo $number ?>" />
                    <span class="val_qno"></span></td>
                <td></td>
            </tr>
            <tr><td> <p>Question Text:<span class="val_qtextA"></span></p></td></tr>
            <tr>
                <td class="style1">
                    <textarea id="question_text" name="question_text" cols="50" rows="2"><?php echo $text ?></textarea>

                </td>
                <td><span class="val_flags"></span></td>
            </tr>

            <?php if ($type == 'NPS') { ?> 
                <tr>
                    <td class="style1">
                        Detractor:
                        <input type="text" id="Detractor" name="Detractor" size="20" 
                               value="<?php echo $answerchoices[11]->get_AnsValue(); ?>" />
                    </td> <td><span class="val_Detractor"></span></td>
                <tr>
                    <td class="style1">
                        Promoter:
                        <input type="text" id="Promoter" name="Promoter" size="20"
                               value="<?php echo $answerchoices[12]->get_AnsValue(); ?>"/>
                    </td> </td> <td><span class="val_Promoter"></span></td>
                </tr>

            <?php } elseif ($type == 'Multiple Choice, Single Value' ||
                    $type == 'Multiple Choice, Multiple Value') {
                ?>

                <tr><td> Answers Choices:<span class="val_qchoice"></span></td><td>Flag name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Flag value</td></tr>
                <tr>
                    <td>


                        <div id="p_choices">
                            <?php
                            foreach ($answerchoices as $answerchoice) {
                                $answerchoicescontent = $answerchoice->get_AnsValue();
                                $optionNum = $answerchoice->get_OptionNumber();
                                echo '
                         <p> <label for="p_choices"><input type="text" id="p_choice_' . $optionNum . '"
							  size="50" maxlength="255"  name="p_choice[]" value="' . $answerchoicescontent . '"
                                                          placeholder="choice Value" /></label>
                           </p>';
                            }
                            ?>

                        </div>
                        <a class="add-new-h2" href="#"   id="addChoice">Add Another Choice</a>

                    </td>
                    <td>
                        <div id="p_flags">

                            <?php
                            foreach ($answerchoices as $answerchoice) {
                                $optionNum = $answerchoice->get_OptionNumber();
                                $flag = $Wrapper->getFlagsByQuestionnaireQuestionOption($QuestionnaireID, $QuestionSeq, $optionNum);
                                $flagName = ($flag != false ? $flag[0]->get_FlagName() : '');
                                $flagValue = ($flag != false ? $flag[0]->get_FlagValue() : '');
                                echo '
                         <p> <label for="p_flagsName"><input type="text" id="p_flagName_' . $optionNum . '"
							 size="6" maxlength="20" name="p_flagName[]" value="' . $flagName . '"
                                                          placeholder="flag name" /></label>
                           <label for="p_flagsValue"><input type="text" id="p_flagValue_' . $optionNum . '"
							 size="6" maxlength="20" name="p_flagValue[]" value="' . $flagValue . '"
                                                          placeholder="flag Value" /></label>
                           </p>';
                            }
                            ?>


                        </div> 
                        </br>

                    </td>

                </tr>


<?php } ?>

            <tr>
                <td class="style1">Mandatory: 
                    <span>
                        <input id="Mandatory_1" name="Mandatory" class="element radio" type="radio" value="1"
<?php if ($Mandatory == 1) echo 'checked' ?>/>
                        <label class="choice" for="Mandatory_1">yes</label>
                        <input id="Mandatory2" name="Mandatory" class="element radio" type="radio" value="0"  <?php if ($Mandatory == 0) echo 'checked' ?> />
                        <label class="choice" for="Mandatory_2">no</label>

                    </span> 

                </td>
            </tr>

            <tr>

            </tr>
            <tr>
                <td align="right">

                    <input type="submit" name="cancel" value="Cancel" class="button-primary"/>
                    <input type="submit" name="save" value="Save" class="button-primary"/>
                </td>
                <td></td>
            </tr>
        </table>


    </form>
</div>

<script type="text/javascript"  src=<?php echo WP_PLUGIN_URL . '/GWU_Builder/images/QuestionForm.js' ?> ></script>
<link rel="stylesheet" type="text/css" href=<?php echo WP_PLUGIN_URL . '/GWU_Builder/images/Menustyle.css' ?> media="screen" />

