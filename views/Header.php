<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href=<?php echo WP_PLUGIN_URL . '/GWU_Builder/images/Menustyle.css' ?> media="screen" />
   
    </head>
    <body>
        <div> <h1> Adding New Question</h1> </div>
        <div class="menu_q">
  <ul>
    <li><a <?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'mutlipleS' )
            { 
        ?>  class="select_q2" <?php } ?> 
        href=<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new',
                                    'Qid' => $_GET['Qid'], 'qno' => $_GET['qno'],
                                    'type' => 'multipleS'),
                                admin_url('admin.php')); 
                        ?>>Multiple choice (single answers) </a></li>
     <li>
        <a <?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'mutlipleM' )
            { 
        ?>  class="select_q2" <?php } ?> href="<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new', 
                                    'Qid' => $_GET['Qid'],'qno' => $_GET['qno'],
                                    'type' => 'multipleM'),
                                admin_url('admin.php')); 
                        ?>">Multiple choice (multiple answers) </a></li>
    <li><a <?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'essay' )
            { 
        ?>  class="select_q2" <?php } ?> 
        href=<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new', 
                                    'Qid' => $_GET['Qid'],'qno' => $_GET['qno'],
                                    'type' => 'essay'),
                                admin_url('admin.php')); 
                        ?>>Essay/Comment </a></li>
    <li><a <?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'NPS' )
            { 
        ?>  class="select_q2" <?php } ?> 
        href=<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new', 
                                    'Qid' => $_GET['Qid'],'qno' => $_GET['qno'],
                                    'type' => 'NPS'),
                                admin_url('admin.php')); 
                        ?>>NPS </a></li>
  </ul>
</div>
        
  
      