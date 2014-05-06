<?php
/**
 * Created by PhpStorm.
 * User: Ashley
 * Date: 4/19/14
 * Time: 10:39 AM
 */
Class GWUUsercap {

    function GWUUsercap(){
        add_filter( 'editable_roles', array(&$this,'editable_roles'));
        add_filter('map_meta_cap', array(&$this,'map_meta_cap'),10,4);
    }
    //Remove 'Administrator' from the list of roles if the current user is not an admin
    function editable_roles($roles){
            if( isset( $roles['administrator']) && !current_user_can('administrator')){
                unset($roles['administrator']);
            }
            return $roles;
        }
    //if someone is trying to edit or delete, dont allow
    function map_meta_cap( $caps, $cap, $user_id, $args ){
        switch ( $cap ){
            case 'edit_user':
            case 'remove_user':
            case 'promote_user':
            if( isset($args[0]) && $args[0] == $user_id )
                break;
            elseif( !isset($args[0]) )
                $caps[] = 'do_not_allow';
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
            case 'delete_user':
            case 'delete_users':
                if( !isset($args[0]) )
                    break;
                $other = new WP_User( absint($args[0]) );
                if( $other->has_cap( 'administrator' ) ){
                    if(!current_user_can('administrator')){
                        $caps[] = 'do_not_allow';
                    }
                }
                break;
            default:
                break;
        }
        return $caps;
    }
 }
    $gwu_user_cap = new GWUUsercap();

?>
