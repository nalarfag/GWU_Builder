<script type="text/javascript">
  jQuery( document ).ready( function($) {
  $('.buildDropDown').change(function(){
  var $button = $('input',$(this).parent().next('td'));
  $button.removeAttr("disabled");
  $button.attr("value", "Update");
  $button.show();
  });

  $('.updateAssignment').click(function(){
  var $button = $(this);
  $button.attr("value", "Saved");
  $button.attr("disabled", "disabled");


  });
  });
</script>
<div class="wrapper">

  <h1>Add New Assignment</h1>

  <br>
    <div class="table">

      <table class="wp-list-table widefat fixed pages" border="1">

        <thead>
          <tr>
            <th width="44%">Questionnaire</th>
            <th width="39%">Editor</th>
            <th width="22%"></th>
          </tr>
        </thead>
        <tbody>
           
      
        

<?php include_once dirname(__FILE__) . '/models/GWQuestionnaire.php';
      include_once dirname(__FILE__) . '/models/GWWrapper.php';
     
       
           if (!defined('GWU_BUILDER_DIR'))
           define('GWU_BUILDER_DIR', WP_PLUGIN_DIR . '\\' . GWU_Builder);
           
           
           
           use WordPress\ORM\Model\GWWrapper;
        

            global $wpdb;
            $query = "SELECT * FROM gwu_questionnaire";
            $Qestionnaires = $wpdb->get_results($query);
            
            $user_ID = get_current_user_id(); 
            $editor = array('role' => 'survey_editor',
                            'meta_key' => 'ownerID',
                            'meta_value' => $user_ID);
                      
            $blogusers = get_users($editor);

            foreach ($Qestionnaires as $Qestionnaire) {
                $ID = $Qestionnaire->QuestionnaireID;
                $Name = $Qestionnaire->Title;
                $CreatorName = $Qestionnaire->CreatorName;
                $EditorID = $Qestionnaire->EditorId;

                $current_user = wp_get_current_user();
                //Owner Creates Questionnaire
                if ($CreatorName == $current_user->user_login ){
                    echo '<tr>';
                    echo ' <td  align="center" nowrap="nowrap">'.$Name.'</td>';
                    echo ' <td align="center" nowrap="nowrap"> <select class="element select medium buildDropDown" id="topic" name="topic">';
                    //Select all editorsname
		    echo '<option value="">—— Select an editor ——</option>';
                    foreach ($blogusers as $editor) {
                        echo '<option value="' . $editor->user_login . '" '
                            . ( $CreatorName == $editor->user_login ? 'selected="selected"' : '' ) . '>'
                            . $editor->user_login
                            . '</option>';
                    }
                    echo ' </select></td>';
                    echo '<td align="center" nowrap="nowrap"><input class="updateAssignment" style="display: none;" type="button" value="Update"/></td></tr>';
                }
            }    
            
          global $wpdb;
                //save editor

                $wpdb->update('gwu_questionnaire', array(

                    'EditorID' => $editor->ID,
                    'OwnerID' => $user_ID

                        ), array('QuestionnaireID' => $ID));

   ?>
        </tbody>
      </table>
    </div>
  </div>