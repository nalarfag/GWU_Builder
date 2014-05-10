<script type='text/javascript'>
  jQuery( document ).ready( function($) {
  var validation_holder;

  $("form#Questionnaire_form input[name='save']").click(function() {
  var validation_holder= validateQuestionnaireForm(this.form);
  if(validation_holder == 1) { // if have a field is blank, return false
  $("p.validate_msg").slideDown("fast");
  return false;
  }
  validation_holder = 0; // else return true
  /* validation end */

  }); // click end



  function validateQuestionnaireForm(form) {
  var validation_holder=0;
  var Title = $("form#Questionnaire_form input[name='questionnaire_title']").val();

  /* validation title */
  if(Title == "") {
  $("span.val_qtitle").html("Please enter a title for the Questionnaire").addClass('validate');
  validation_holder = 1;
  } else {
  $("span.val_qtitle").html("");
  }


  return validation_holder;
  }
  }); // jQuery End
  //show multiple radio if anonymous is selected to no
  jQuery( document ).ready( function($) {

  $('#anonymous_1').click(function () {
  $('#mutlipleRadio').hide();
  });
  $('#anonymous_2').click(function () {
  $('#mutlipleRadio').show();
  $('#multiple_1').prop('checked',true);
  });
  if ($("#anonymous_2").is(":checked")) {
  $('#mutlipleRadio').show();
  }
  } );

</script>

<?php
$adminURL = admin_url('admin-post.php');
if (isset($_GET['id']) && ( $_GET['id'] == 'edit' && is_numeric($_GET['Qid']) )) {
    $actionName = 'edit_Questionnaire';
    $QuestionnaireID = $_GET['Qid'];
    $questionnaire = $Wrapper->getQuestionnaire($QuestionnaireID);
    $Title = $questionnaire[0]->get_Title();
    $Topic = $questionnaire[0]->get_Topic();
    $AllowMultiple = $questionnaire[0]->get_AllowMultiple();
    $AllowAnnonymous = $questionnaire[0]->get_AllowAnnonymous();
} else {
    $actionName = 'add_new_Questionnaire';
    $AllowMultiple = true;
    $AllowAnnonymous = true;
}

$Topics = array('', 'Employee Feedback', 'Volunteer Feedback', 'Education',
    'Sports', 'Shopping', 'Other');
?>
<div id="Questionnaire-general" class="wrap">
  <h1>Add New Survey </h1>
  <form id="Questionnaire_form"
        class="create-new" style="  padding-left: 20%;
          padding-top: 50px;"
        method="post" action= ""
    <?php echo admin_url('admin-post.php'); ?>
    ">
    <input type="hidden" name="action" value=""<?php echo $actionName; ?>" />
    <input type="hidden" name="QuestionnaireID" value=""<?php echo $QuestionnaireID ?>" />
    <table>
      <tr>
        <td style="width: 200px">Survey Title</td>
        <td class="style1"style="width: 250px">
          <input type="text" id="questionnaire_title" name="questionnaire_title" size="30" value=""<?php echo $Title; ?>" />
        </td>
        <td>
          <span class="val_qtitle"></span>
        </td>
      </tr>

      <tr>
        <td>Can this survey be taken by anonymous user?</td>
        <td class="style1">
          <span>
            <input id="anonymous_1" name="anonymous" class="element radio"
                   type="radio" value="1" <?php if ($AllowAnnonymous == true) {
    echo "checked";
} ?> />
            <label class="choice" for="anonymous_1">Yes</label>
            <input id="anonymous_2" name="anonymous" class="element radio" type="radio" value="0"  <?php if ($AllowAnnonymous == false) {
    echo "checked";
} ?>/>
            <label class="choice" for="anonymous_2">No</label>

          </span>

        </td>
        <td></td>
      </tr>

      <tr id="mutlipleRadio" style="display:none">
        <td>Can user take the survey multiple times</td>

        <td class="style1">
          <span>
            <input id="multiple_1" name="multiple" class="element radio"
                   type="radio" value="1" <?php if ($AllowMultiple == true) {
    echo "checked";
} ?> />
            <label class="choice" for="multiple_1">Yes</label>
            <input id="multiple_2" name="multiple" class="element radio" type="radio" value="0" <?php if ($AllowMultiple == false) {
    echo "checked";
} ?>/>
            <label class="choice" for="multiple_2">No</label>

          </span>

        </td>
        <td></td>
      </tr>

      <tr>
        <td>Survey Topic:</td>

        <td class="style1">


          <input id="topic" name="topic" list="topics" />
          <datalist id="topics">
            <?php
                        for ($i = 0; $i < count($Topics); $i++) {
                            echo '<option value="' . $Topics[$i] . '" '
                            . ( $Topic == $Topics[$i] ? 'selected="selected"' : '' ) . '>'
                            . $Topics[$i]
                            . '</option>';
                        }
                        ?>
          </datalist>
        </td>
        <td></td>
      </tr>

      <tr>
        <td></td>
        <td align="right">
          <input type="submit" name="cancel" value="Cancel" class="button-primary"/>
          <input type="submit" name="save" value="Save" class="button-primary"/>
        </td>
        <td></td>
      </tr>

    </table>

  </form>
</div>

<link rel="stylesheet" type="text/css" href=""<?php echo WP_PLUGIN_URL . '/GWU_Builder/images/Menustyle.css' ?> media="screen" />
