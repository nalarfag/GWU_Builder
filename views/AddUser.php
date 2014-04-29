<?php
/**
 * Created by PhpStorm.
 * User: Ashley
 * Date: 4/18/14
 * Time: 1:56 PM
 */


use WordPress\ORM\GWBaseModel;
if (isset($_GET['id'])&& $_GET['id'] == 'newassignment'){
    include_once dirname(__FILE__) . '/AddAssignment.php';
}
else{
$blogusers = get_users();

$NewUserAddress = add_query_arg(array(
    'page' => 'GWU_view-Users-page',
    'id' => 'newassignment'
), admin_url('admin.php'));



?>



<div class = "wrapper">


  <h1>All Users</h1>
  <div class="table">
    <table class="wp-list-table widefat fixed pages"  width="90%" border="1">
      <tbody>
        <thead>
          <tr>
            <th width="20%">Username</th>
            <th width="35%">Email</th>
            <th width="20%">Role</th>
            <th width="15%">Name</th>

          </tr>
        </thead>
        <tfoot>




          <?php
/*[ID] => 1
[user_login] => admin
[user_pass] => $P$Bxudi6gJMk2GRt2ed3xvZ06c1BPZXi/
[user_nicename] => admin
[user_email] => admin@host.com
[user_url] => http://localhost/
[user_registered] => 2010-06-29 07:08:55
[user_activation_key] =>
[user_status] => 0
[display_name] => Richard Branson
 */
 
 $current_user = wp_get_current_user();
if ( !($current_user instanceof WP_User) )
   return;
  
 
$user_ID = get_current_user_id();

           
             $args = array('role' => 'survey_editor',
                           'meta_key' => 'ownerID',
                           'meta_value' => $user_ID);

           
            
                           
             $blogusers = get_users($args);
                  foreach ($blogusers as $user) 
             
             {
    // Your code
      echo '<tr>';
                  echo  '<td>' . $user->user_login . '</td>';
                echo '<td>' . $user->user_email . '</td>';
                echo '<td>';

                $userMeta = get_userdata( $user->ID );
                global $wpdb;
                $capabilities = $userMeta->{$wpdb->prefix . 'capabilities'};

                if ( !isset( $wp_roles ) )
                    $wp_roles = new WP_Roles();

                foreach ( $wp_roles->role_names as $role => $name ) :
                    if ( array_key_exists( $role, $capabilities ) )
                        echo $role;
                endforeach;

                echo '</td>';

                echo '<td>' . $user->display_name . '</td>';
                echo '</tr>';
}
              
         
              
                
             
            
?>

        </tfoot>
      </tbody>
    </table>

    <button>
      <a class="addnewuser" style = "text-decoration:none;margin-top:10px;" href="../wordpress/wp-admin/user-new.php">Add New User</a>
    </button>
    <button>
      <a class="addnewassignment" style="text-decoration:none;margin-top:10px;" href=""
        <?php echo $NewUserAddress; ?>">Add New Assignment
      </a>
    </button>
  </div>


</div>
<?php }?>