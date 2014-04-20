<?php
/**
 * Created by PhpStorm.
 * User: Ashley
 * Date: 4/18/14
 * Time: 1:56 PM
 */
$NewUserAddress = add_query_arg(array(
        'page' => 'GWU_view-Users-page',
        'id' => 'newUser'
        ),admin_url('admin.php')
);
?>

<div class = "wrapper">


    <h1>All Users</h1>
    <br><div class=table>
        <br>
        <table class="wp-list-table widefat fixed pages"  width="90%" border="1">
            <tbody>
            <thead>
            <tr>
                <th width="20%">Username</th>
                <th width="35%">Email</th>
                <th width="30%">role</th>
                <th width="15%">Name</th>
            </tr> </thead>
            <tfoot>

            <?php
            //foreach (as)
            ?>

            <tr>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
            </tr>
            </tfoot>
            </tbody>
        </table>
        <span><strong>Please go back to User Menu for adding new users</strong></span>
        <a class="addnewuser" href="<?php echo $NewUserAddress; ?>">Add New User</a>
    </div>








