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

<?php  add_filter('cron_schedules', 'crony_schedules', 10, 2);

function crony_schedules ($schedules) {
    if (!isset($schedules['twicehourly']))
        $schedules['twicehourly'] = array( 'interval' => 1800, 'display' => __('Twice Hourly') );
    if (!isset($schedules['weekly']))
        $schedules['weekly'] = array( 'interval' => 604800, 'display' => __('Once Weekly') );
    if (!isset($schedules['twiceweekly']))
        $schedules['twiceweekly'] = array( 'interval' => 302400, 'display' => __('Twice Weekly') );
    if (!isset($schedules['monthly']))
        $schedules['monthly'] = array( 'interval' => 2628002, 'display' => __('Once Monthly') );
    if (!isset($schedules['twicemonthly']))
        $schedules['twicemonthly'] = array( 'interval' => 1314001, 'display' => __('Twice Monthly') );
    if (!isset($schedules['yearly']))
        $schedules['yearly'] = array( 'interval' => 31536000, 'display' => __('Once Yearly') );
    if (!isset($schedules['twiceyearly']))
        $schedules['twiceyearly'] = array( 'interval' => 15768012, 'display' => __('Twice Yearly') );
    if (!isset($schedules['fouryearly']))
        $schedules['fouryearly'] = array( 'interval' => 7884006, 'display' => __('Four Times Yearly') );
    if (!isset($schedules['sixyearly']))
        $schedules['sixyearly'] = array( 'interval' => 5256004, 'display' => __('Six Times Yearly') );
    return apply_filters('crony_schedules',$schedules);
}

 include_once dirname(__FILE__) . '/models/GWQuestionnaire.php';
      include_once dirname(__FILE__) . '/models/GWWrapper.php';
     
       
           if (!defined('GWU_BUILDER_DIR'))
               define('GWU_BUILDER_DIR', WP_PLUGIN_DIR . '\\' . GWU_Builder);
           
           
           
           use WordPress\ORM\Model\GWWrapper;


?>

<div class="wrapper">

    <h1>Manage Cronjob</h1>


    <div class="table">

        <table class="wp-list-table widefat fixed pages" border="1">

            <thead>
            <tr>
                <th width="44%">CronJob</th>
                <th width="39%">Schedule</th>
                <th width="22%">Update</th>
            </tr>
            </thead>
            <tbody>


            <?php


            $schedules = wp_get_schedules();

            $interval = array();

            foreach ($schedules as $key => $value)
            {

                $interval[$key]  = $value['interval'];
            }

            array_multisort($interval,SORT_NUMERIC,$schedules);

            //'everyminute' => int 60

            global $wpdb;
            $query = "SELECT * FROM wp_crony_job";
            $cronjobs = $wpdb->get_results($query);
            foreach ($cronjobs as $cronjob) {
                $ID = $cronjob->cronjob_id;
                $Name = $cronjob->cronjob_name;
                $Time = $cronjob->cronjob_interval;

                ?>




                <tr>
                    <td  align="center" nowrap="nowrap"><?php echo $Name;?></td>
                    <td align="center" nowrap="nowrap">
                        <select class="element select medium buildDropDown"  id ="schedule" name="schedule">
                            <option value="">—— Select an schedule ——</option>

                            <?php
                            foreach($interval as $key=>$value)
                            {
                                echo '<option value="' . $key . '" '
                                    . ( $Time == $value['interval'] ? 'selected="selected"' : '' ) . '>'
                                    . $key
                                    . '</option>';



                            }

                            ?>




                        </select></td>
                    <td align="center" nowrap="nowrap"><input class="updateAssignment" style="display: none;" type="button" value="Update"/></td>
                </tr>
            <?php

            }

            global $wpdb;

            $wpdb->update('wp_crony_job', array(

                'cronjob_interval'=>$value['interval']
            ), array('cronjob_id' => $ID));


            ?>

            </tbody>
        </table>
    </div>
</div>