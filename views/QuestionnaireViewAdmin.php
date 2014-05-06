<script type='text/javascript'>

        
    jQuery( document ).ready( function($) {  
        $(this).find(".button-primary").hide();
        $(document).on('mouseenter', '.divbutton', function () {
            $(this).find(".button-primary").show();
        }).on('mouseleave', '.divbutton', function () {
            $(this).find(".button-primary").hide();
        });
    });
            
    jQuery( document ).ready( function($) { 
        var id =1;
        var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
        $("#dialog-confirm-multiple").dialog({
            dialogClass   : "wp-dialog",
            autoOpen: false,
            resizable: false,
            width: 300,
            modal: true,
            buttons: {
                "Yes": function () { 
		   
                    $(this).dialog("close");
                    $.ajax({
                        type: "POST",
                        url: ajax_url,
                        action: 'delete_questionnaire',
                        data:
                            {
                            action: 'delete_questionnaire',
                            id:id
                        }
                        ,
                        success: function(data) {
                            //   window.location.reload(true);
                            // $("#"+data).remove();
                            $("#QuestionnaireView").load(location.href + " #QuestionnaireView");
                            $("#Notice").html('<div class="updated"><p>The questionnaire was successfully deleted</p></div>');

                        }

                    });

                },
                "No": function () {
                    $(this).dialog("close");
                }
            }
        });
        $(document).on("click","a#delete",function(e,ui){
            //   debugger;
            if(e.originalEvent) {
                e.preventDefault();
                var url = ($(this).attr('href'));
                id = getURLParameter(url, 'qid');
                //  console.debug(id);
                $("#dialog-confirm-multiple").dialog('open');
                return false;
            }
        });
        
        $(document).on("click","a#publish",function(e,ui){

            e.preventDefault();
            var url = ($(this).attr('href'));
            id = getURLParameter(url, 'qid');
            //   console.debug(id);
            //   debugger;
            $.ajax({
                type: "POST",
                url: ajax_url,
                action: 'publish_questionnaire',
                data:
                    {
                    action: 'publish_questionnaire',
                    id:id
                }
                ,
                success: function(data) {
                    //   window.location.reload(true);
                    // $("#"+data).remove();
		    var response=$(data);
		    var success=response.filter('#success').text();

                    $("#QuestionnaireView").load(location.href + " #QuestionnaireView");
		    if(success=='true')
                        $("#Notice").html('<div class="updated"><p>The questionnaire was successfully published</p></div>');
                    else
                        $("#Notice").html('<div class="updated"><p>Faild to publish the questionnaire</p></div>');

                }

            });
            
        });
        $(document).on("click","a#deactivate",function(e,ui){

            e.preventDefault();
            var url = ($(this).attr('href'));
            id = getURLParameter(url, 'qid');
            //   console.debug(id);
            //   debugger;
            $.ajax({
                type: "POST",
                url: ajax_url,
                action: 'deactivate_questionnaire',
                data:
                    {
                    action: 'deactivate_questionnaire',
                    id:id
                }
                ,
                success: function(data) {
                    //   window.location.reload(true);
                    // $("#"+data).remove();
		    var response=$(data);
		    var success=response.filter('#success').text();
		    //console.debug(success);
                    $("#QuestionnaireView").load(location.href + " #QuestionnaireView");
		    if(success=='true')
                        $("#Notice").html('<div class="updated"><p>The questionnaire link was successfully deactivated</p></div>');
                    else
                        $("#Notice").html('<div class="updated"><p>Faild to deactivate the questionnaire link</p></div>');

                }

            });
            
        });
        $(document).on("click","a#reactivate",function(e,ui){

            e.preventDefault();
            var url = ($(this).attr('href'));
            id = getURLParameter(url, 'qid');
            //   console.debug(id);
            //   debugger;
            $.ajax({
                type: "POST",
                url: ajax_url,
                action: 'reactivate_questionnaire',
                data:
                    {
                    action: 'reactivate_questionnaire',
                    id:id
                }
                ,
                success: function(data) {
                    //   window.location.reload(true);
                    // $("#"+data).remove();
		    var response=$(data);
		    var success=response.filter('#success').text();
		    //console.debug(success);
                    $("#QuestionnaireView").load(location.href + " #QuestionnaireView");
		    if(success=='true')
                        $("#Notice").html('<div class="updated"><p>The questionnaire was successfully republished</p></div>');
                    else
			$("#Notice").html('<div class="updated"><p>Failed to republished the questionnaire link</p></div>');

                }

            });
            
        });
        $(document).on("click","a#duplicate",function(e,ui){

            e.preventDefault();
            var url = ($(this).attr('href'));
            id = getURLParameter(url, 'qid');
	    // console.debug(id);
            debugger;
            $.ajax({
                type: "POST",
                url: ajax_url,
                action: 'duplicate_questionnaire',
                data:
                    {
                    action: 'duplicate_questionnaire',
                    id:id
                }
                ,
                success: function(data) {
                    //   window.location.reload(true);
                    // $("#"+data).remove();
                    $("#QuestionnaireView").load(location.href + " #QuestionnaireView");
                    $("#Notice").html('<div class="updated"><p>The questionnaire was successfully duplicated</p></div>');
                }

            });
            
        });
    });
    function getURLParameter(url, name) {
        return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
    }    
</script>
<?php
$NewQuestionnaireAddress = add_query_arg(array(
    'page' => 'GWU_add-Questionnaire-page',
    'id' => 'newQuestionnaire'
        ), admin_url('admin.php'));
?>          

<div  class="wrap">
    <h2>Questionnaire <a class="add-new-h2"  href="<?php echo $NewQuestionnaireAddress; ?>">Add new</a></h2>

    <div id="Notice"></div>

    <div id="QuestionnaireView">
        <?php
        //show the Table of Questionnaire
        $wp_list_table = new Questionnaire_List_Table();
        $wp_list_table->prepare_items();
        $wp_list_table->display();
        ?>
    </div> </div>



<div id="dialog-confirm-multiple" title="Confirmation Required">
    <p>This item will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

