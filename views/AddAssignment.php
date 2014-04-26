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
  $button.attr("value", "Saving...");
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

          <?php

            global $wpdb;
            $query = "SELECT * FROM gwu_questionnaire";
            $Qestionnaires = $wpdb->get_results($query);

            $blogusers = get_users();

            foreach ($Qestionnaires as $Qestionnaire) {

                $Name = $Qestionnaire->Title;
                $CreatorName = $Qestionnaire->CreatorName;

                $current_user = wp_get_current_user();
                if ($CreatorName == $current_user->user_login ){
                    echo '<tr>';
                    echo ' <td  align="center" nowrap="nowrap">'.$Name.'</td>';
                    echo ' <td align="center" nowrap="nowrap"> <select class="element select medium buildDropDown" id="topic" name="topic">';

                    foreach ($blogusers as $user) {
                        echo '<option value="' . $user->user_login . '" '
                            . ( $CreatorName == $user->user_login ? 'selected="selected"' : '' ) . '>'
                            . $user->user_login
                            . '</option>';
                    }
                    echo ' </select></td>';
                    echo '<td align="center" nowrap="nowrap"><input class="updateAssignment" style="display: none;" type="button" value="Update"/></td></tr>';
                }
            }    ?>
        </tbody>
      </table>
    </div>
  </div>