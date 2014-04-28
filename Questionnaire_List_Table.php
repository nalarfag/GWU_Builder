<?php

//Our class extends the WP_List_Table class, so we need to make sure that it's there
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Description of Questionnaire_List_Table
 *
 * @author Nada Alarfag
 * 
 * based on plugin by Matthew Van Andel  (email : matt@mattvanandel.com)
 */
class Questionnaire_List_Table extends WP_List_Table {

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */
    function __construct() {
        global $status, $page;
        parent::__construct(array(
            'singular' => 'wp_list_questionnaire', //Singular label
            'plural' => 'wp_list_questionnaires', //plural label, also this well be one of the table css class
            'ajax' => false //We won't support Ajax for this table
        ));
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'AllowMultiple':
            case 'AllowAnnonymous':
                if ($item->$column_name == true)
                    return 'Yes';
                else
                    return 'No';
                return;
            case 'Topic':
                return $item->$column_name;
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> 
     * ************************************************************************ */
    function column_Title($item) {
        $edit_link = add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
            'id' => 'edit', 'Qid' => $item->QuestionnaireID), admin_url('admin.php'));
        //Build row actions
        if ($item->PublishFlag != true)
            $actions = array(
                'edit' => sprintf('<a id="edit" href="%s">Edit</a>', $edit_link),
                'delete' => sprintf('<a id="delete" href="?page=%s&action=%s&qid=%s">Delete</a>', $_REQUEST['page'], 'delete', $item->QuestionnaireID),
                'duplicate' => sprintf('<a id="duplicate" href="?page=%s&action=%s&qid=%s">Duplicate</a>', $_REQUEST['page'], 'dublicate', $item->QuestionnaireID),
            );
        else {
            $actions = array(
                'delete' => sprintf('<a id="delete" href="?page=%s&action=%s&qid=%s">Delete</a>', $_REQUEST['page'], 'delete', $item->QuestionnaireID),
                'duplicate' => sprintf('<a id="duplicate" href="?page=%s&action=%s&qid=%s">Duplicate</a>', $_REQUEST['page'], 'dublicate', $item->QuestionnaireID),
            );
        }

        $datetime = strtotime($item->DateModified);
        $DateModified = date("m/d/y g:iA", $datetime);
        $datetime = strtotime($item->DateCreated);
        $DateCreated = date("m/d/y", $datetime);
        $output = ' <strong> <a class="row-title" href="' . add_query_arg(array('page' => 'GWU_add-Questionnaire-page',
                    'id' => 'view', 'Qid' => $item->QuestionnaireID), admin_url('admin.php')) . ' ">
                                        ' . $item->Title . ' </a> </strong><br/>
                                        <small> Created in ' . $DateCreated . ' <br/>
                                         Last edited in ' . $DateModified . '<br/></small>
                                         <span style="color:silver">Created by ' . $item->CreatorName . ' </span><br/>';

        $output.= $this->row_actions($actions);
        //Return the title contents
        return $output;
    }

    /**
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> 
     * ************************************************************************ */
    function column_Publish($item) {

        if ($item->PublishFlag != true) {
            $output = '<a id="publish" href=' . add_query_arg(array('page' => 'GWU_Questionnaire-mainMenu-page',
                        'action' => 'publish', 'qid' => $item->QuestionnaireID
                            ), admin_url('admin.php')) . ' ">Publish</a>';
        } else {


            //Build row actions
            if ($item->PostId == -1)
                $output = '<a id="reactivate" href=' . add_query_arg(array('page' => 'GWU_Questionnaire-mainMenu-page',
                            'action' => 'reactivate', 'qid' => $item->QuestionnaireID
                                ), admin_url('admin.php')) . ' ">Republish</a>';
            else {
                $Link = get_permalink($item->PostId);
                $output = '<a href="' . $Link . '">' . $Link . '</a>';
                $actions = array(
                    'deactivate' => sprintf('<a id="deactivate" href="?page=%s&action=%s&qid=%s">Deactivate link</a>', $_REQUEST['page'], 'deactivate', $item->QuestionnaireID),
                );
            }
        }
        $output.= '<br/><br/><br/>'. $this->row_actions($actions);


        //Return the title contents
        return $output;
    }

    /**
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     */
    function get_columns() {
        return $columns = array(
            'Title' => 'Title',
            'Topic' => 'Topic',
            'AllowAnnonymous' => 'Allow Anonymous',
            'AllowMultiple' => 'Allow Multiple',
            'Publish' => 'Publish'
        );
    }

    /**
     * Decide which columns to activate the sorting functionality on
     * @return array An associative array containing all
     *  the columns that should be sortable: 'slugs'=>array('data_values',bool)
     */
    public function get_sortable_columns() {
        return $sortable = array(
            'Title' => array('Title', false),
            'Topic' => array('Topic', false),
            'AllowAnnonymous' => array('AllowAnnonymous', false),
            'AllowMultiple' => array('AllowMultiple', false)
        );
    }

    /**     * ***********************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     * ************************************************************************ */
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $perpage = 10;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);



        /* -- Preparing your query -- */
        $query = "SELECT * FROM gwu_questionnaire";

        /* where cluase for editor or onwer*/
         if (current_user_can('edit_survey')) {
            $EditorID = get_current_user_id();
             $whereCondition= '`EditorID`='.$EditorID;
            } //user is owner or adminstrator
            elseif (current_user_can('own_survey')) {
             
                $OwnerID = get_current_user_id();
                $whereCondition= '`OwnerID`='.$OwnerID;
            }

         $query.=' WHERE ' . $whereCondition;
        
        /* -- Ordering parameters -- */
        //Parameters that are going to be used to order the result
        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
        if (!empty($orderby) & !empty($order)) {
            $query.=' ORDER BY ' . $orderby . ' ' . $order;
        }


        $totalitems = $wpdb->query($query);

        //Which page is this?
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        //Page Number
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems / $perpage);
        //adjust the query to take pagination into account
        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query.=' LIMIT ' . (int) $offset . ',' . (int) $perpage;
        }

        /* -- Register the pagination -- */
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));
        //The pagination links are automatically built according to those parameters


        /* -- Fetch the items -- */
        $this->items = $wpdb->get_results($query);
    }

}

?>
