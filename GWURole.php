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
            $role -> add_cap( 'own_survey' );


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
            $role -> add_cap( 'edit_pages');
            $role -> add_cap( 'create_pages' );
            $role -> add_cap( 'publish_pages' );
            $role -> add_cap( 'delete_published_pages' );
            $role -> add_cap( 'edit_other_pages' );
            $role -> add_cap( 'create_users' );
            $role -> add_cap('add_users');
            $role -> add_cap( 'edit_users' );
            $role -> add_cap( 'delete_users' );
            $role -> add_cap( 'own_survey' );
             
            
          
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
                $role -> add_cap( 'delete_pages');
                $role -> add_cap( 'edit_pages');
                $role -> add_cap( 'create_pages' );
                $role -> add_cap( 'publish_pages' );
                $role -> add_cap( 'edit_survey' );
                $role->remove_cap('manage_options');

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
   
    $gwu_roles = new GWURole();
?>
