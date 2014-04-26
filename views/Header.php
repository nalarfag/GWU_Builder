        <div> <h1> Adding New Question</h1> </div>
 

        <div class="menu_q">
        <div id="menu" class="pure-skin-custom">
        <div class="pure-menu pure-menu-open">
            <a class="pure-menu-heading" href="#">Question Types</a>  <ul>
    <li <?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'multipleS' )
            { 
        ?>  class="menu-item-divided pure-menu-selected" <?php } ?> ><a
        href=<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new',
                                    'Qid' => $_GET['Qid'], 'qno' => $_GET['qno'],
                                    'type' => 'multipleS'),
                                admin_url('admin.php')); 
                        ?>>Multiple choice (single answers) </a></li>
     <li <?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'multipleM' )
            { 
        ?>  class="menu-item-divided pure-menu-selected" <?php } ?>>
        <a  href="<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new', 
                                    'Qid' => $_GET['Qid'],'qno' => $_GET['qno'],
                                    'type' => 'multipleM'),
                                admin_url('admin.php')); 
                        ?>">Multiple choice (multiple answers) </a></li>
    <li <?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'essay' )
            { 
        ?>  class="menu-item-divided pure-menu-selected" <?php } ?> >
        <a href=<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new', 
                                    'Qid' => $_GET['Qid'],'qno' => $_GET['qno'],
                                    'type' => 'essay'),
                                admin_url('admin.php')); 
                        ?>>Open-ended </a></li>
    <li<?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'NPS' )
            { 
        ?>  class="menu-item-divided pure-menu-selected" <?php } ?> >
        <a href=<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new', 
                                    'Qid' => $_GET['Qid'],'qno' => $_GET['qno'],
                                    'type' => 'NPS'),
                                admin_url('admin.php')); 
                        ?>>Net Promoter Score (NPS) </a></li>
        </ul>
        </div>
    </div>
        </div>
        
  
      