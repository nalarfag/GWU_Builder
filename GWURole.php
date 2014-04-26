<?php
include_once dirname(__FILE__) . '/models/GWWrapper.php';
/**
 * Created by PhpStorm.
 * User: Ashley
 * Date: 4/17/14
 * Time: 9:49 PM
 * This class is to set role from WP.
 */
if (!defined('GWU_BUILDER_DIR'))
    define('GWU_BUILDER_DIR', WP_PLUGIN_DIR . '\\' . GWU_Builder);
use WordPress\ORM\Model\GWWrapper;

    class GWURole {
        function GWURole()
           {
            $Wrapper = new GWWrapper();
            //$question = $Wrapper->listQuestion($QuestionnaireID,true);
            //get the default administrator role
            $role = & get_role( 'administrator' );


            //create the custom role owner
             add_role(
                'owner',
                'Owner',
                array(
                'read'                  => true,
                'edit_pages'            => false,
                'delete_pages'          => false,
                'delete_publish_pages'  => false,
                'publish_pages'         => false,
            )
        );   
            $role = & get_role('owner');
            $role -> add_cap('read');
            $role -> add_cap('manage_options');
            $role -> add_cap( 'delete_others_pages' );
            $role -> add_cap( 'delete_pages');
            //$role -> add_cap( 'edit_pages');
           // $role -> add_cap( 'create_pages' );
            $role -> add_cap( 'publish_pages' );
            $role -> add_cap( 'delete_published_pages' );
            //$role -> add_cap( 'edit_other_pages' );
            $role -> add_cap( 'create_users' );
            $role -> add_cap('add_users');
            $role -> add_cap( 'edit_users' );
            $role -> add_cap( 'delete_users ' );
            
          
            //create the survey editor
            add_role(
                'survey_editor',
                'Survey Editor',
                array(
                  'publish_questionnaire',
                  'edit_questionnaire'
                )
            );
                $role = & get_role('survey_editor');
                $role -> add_cap('read');
                $role -> add_cap('manage_options');
                $role -> add_cap( 'delete_pages');
                $role -> add_cap( 'edit_pages');
                $role -> add_cap( 'create_pages' );
                $role -> add_cap( 'publish_pages' );

            //remove the unnecessary roles
            remove_role('subscriber');
            remove_role('editor');
            remove_role('author');
            remove_role('contributor');
             
        }



    }
     
   add_action('pre_user_query','GWU_pre_user_query');
    function GWU_pre_user_query($user_search) {
    $user = wp_get_current_user();

    if ( $user->roles[0] != 'administrator' ) {
        global $wpdb;

        $user_search->query_where =
            str_replace('WHERE 1=1',"WHERE 1=2"
                /*"WHERE 1=1 AND {$wpdb->users}.ID IN (
                 SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta
                    WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}user_level'
                   AND {$wpdb->usermeta}.meta_value < 10)"*/,
                $user_search->query_where
            );

    }
}
 //1.Need to be fixed. No function
    add_action('register_form','myplugin_register_form');
    function myplugin_register_form (){
        $first_name = ( isset( $_POST['first_name'] ) ) ? $_POST['first_name']: '';
        ?>
        <p>
            <label for="first_name"><?php _e('First Name','mydomain') ?><br />
                <input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr(stripslashes($first_name)); ?>" size="25" /></label>
        </p>
        <?php
    }

    //2. Add validation. In this case, we make sure first_name is required.
    add_filter('registration_errors', 'myplugin_registration_errors', 10, 3);
    function myplugin_registration_errors ($errors, $sanitized_user_login, $user_email) {

        if ( empty( $_POST['first_name'] ) )
            $errors->add( 'first_name_error', __('<strong>ERROR</strong>: You must include a first name.','mydomain') );

        return $errors;
    }

    //3. Finally, save our extra registration user meta.
    add_action('user_register', 'myplugin_user_register');
    function myplugin_user_register ($user_id) {
        if ( isset( $_POST['first_name'] ) )
            update_user_meta($user_id, 'first_name', $_POST['first_name']);
    }
   
    $gwu_roles = new GWURole();
?>
