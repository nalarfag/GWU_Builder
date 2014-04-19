<?php
$NewQuestionnaireAddress = add_query_arg(array(
    'page' => 'GWU_add-Questionnaire-page',
    'id' => 'newQuestionnaire'
        ), admin_url('admin.php'));

?>
<div class="wrap">

    <?php if ($Qestionnaires != false) {
        ?>
        <h1>Questionnaire Set</h1> 

        <br><div class=table>

            <br>
            <table class="wp-list-table widefat fixed pages"  width="90%" border="1">
                <tbody>
                <thead>
                    <tr>
                        <th width="30%">Questionnaire Name</th>
                        <th width="20%">Topic</th>
                        <th width="15%">Allow Anonymous</th>
                        <th width="15%">Allow Multiple</th>
                        <th width="10%"></th>
                        <th width="10%"></th>
                    </tr> </thead>
                <tfoot>

                    <?php
                    foreach ($Qestionnaires as $Qestionnaire) {

                        // $tableName=$table->Tables_in_builder;
                        $id = $Qestionnaire->get_QuestionnaireID();
                        $Name = $Qestionnaire->get_Title();
                        $Date = $Qestionnaire->get_DateCreated();
                        $DateModified = $Qestionnaire->get_DateModified();
                        $Anonymous = $Qestionnaire->get_AllowAnonymous();
                        $Multiple = $Qestionnaire->get_AllowMultiple();
                        $Topic = $Qestionnaire->get_Topic();
                        $CreatorName = $Qestionnaire->get_CreatorName();
                        $PostId = $Qestionnaire->get_PostId();
                        $PublishFlag=$Qestionnaire->get_PublishFlag();
                        $Link = get_permalink($PostId);
                        ?>

  

                    <tr>
                        <td align="left" nowrap="nowrap">
                          <strong> <a class="row-title" href="<?php echo add_query_arg( array('page' => 'GWU_add-Questionnaire-page',
                                         'id' => 'view', 'Qid' => $id), admin_url('admin.php')); ?> ">
                                         <?php echo $Name; ?> </a> </strong><br/>
                                         Created in  <?php echo $Date; ?> <br/>
                                         Last modified in  <?php echo $DateModified; ?> <br/>
                                         Created by  <?php echo $CreatorName; ?> <br/>
                        </td>
                        <td  align="center" nowrap="nowrap"><?php echo $Topic ?></td>
                        <td align="center" nowrap="nowrap"> <?php echo  ($Anonymous ? 'Yes' : 'No') ?></td>
                        <td align="center" nowrap="nowrap"><?php echo ($Multiple ? 'Yes' : 'No') ?></td>
                        <td align="center" nowrap="nowrap"><a class="row-title" href="<?php echo add_query_arg( array('page' => 'GWU_add-Questionnaire-page',
                                         'id' => 'duplicate', 'Qid' => $id), admin_url('admin.php')); ?> ">
                                         Duplicate </a></td>
                        

                        <td style="100px;" align="center" nowrap="nowrap">
                            <a class="View-Q" href= <?php echo'"';
                            if ($PublishFlag != true) 
                            {   echo  add_query_arg(  array('page' => 'GWU_Questionnaire-mainMenu-page',
                               'id' => 'publish', 'Qid' => $id
                               ), admin_url('admin.php')) . '">Publish</a>' ;
                            }
                              else   
                              {
                              echo  $Link . '">' . $Link . '</a>'; 
                              }
                              ?>
                           </tr>
                           
                             <?php } ?> 
               
                            </tfoot> 
                            </tbody>
            </table>
        </div> 
        </br>
        </br>
  
<?php } ?>

        <a class="add-new-h2"  href="<?php echo $NewQuestionnaireAddress; ?>">Add new questionnaire</a></div>


<?php echo $message; ?>