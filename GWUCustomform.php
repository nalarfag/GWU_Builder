<?php
add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { ?>

<h3>Extra profile information</h3>

<table class="form-table">

  <tr>
    <th>
      <label for="owner">Owner</label>
    </th>

    <td>
      <input type="text" name="owner" id="owner" value=""<?php echo esc_attr( get_the_author_meta( 'owner', $user->ID ) ); ?>" class="regular-text" /><br />
      <span class="description">Please enter your Owner's name.</span>
    </td>
  </tr>

</table>
<?php }

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_usermeta( $user_id, 'owner', $_POST['owner'] );
}


?>