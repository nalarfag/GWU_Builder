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
                                    'Qid' => $_GET['Qid'],
                                    'type' => 'multipleS'),
                                admin_url('admin.php')); 
                        ?>>Multiple choice (single answers) </a></li>
     <li>
        <a <?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'mutlipleM' )
            { 
        ?>  class="select_q2" <?php } ?> href="<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new', 
                                    'Qid' => $_GET['Qid'],
                                    'type' => 'multipleM'),
                                admin_url('admin.php')); 
                        ?>">Multiple choice (multiple answers) </a></li>
    <li><a <?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'essay' )
            { 
        ?>  class="select_q2" <?php } ?> 
        href=<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new', 
                                    'Qid' => $_GET['Qid'],
                                    'type' => 'essay'),
                                admin_url('admin.php')); 
                        ?>>Essay/Comment </a></li>
    <li><a <?php if(isset( $_GET['type'] ) &&  $_GET['type'] == 'NPS' )
            { 
        ?>  class="select_q2" <?php } ?> 
        href=<?php echo add_query_arg( 
                                array ( 'page' => 'GWU_add-Questionnaire-page',
                                    'id' => 'new', 
                                    'Qid' => $_GET['Qid'],
                                    'type' => 'NPS'),
                                admin_url('admin.php')); 
                        ?>>NPS </a></li>
  </ul>
</div>
        
  
<script type='text/javascript'>

        
    jQuery( document ).ready( function($) {   
        var scntDiv = $('#p_choices');
        var i = $('#p_choices p').size() + 1;
        
        $('#addChoice').live('click', function() {
            $('<p><label for="p_choices"><input type="text" id="p_choice_'+i+'" size="50" name="p_choice[]" value="" placeholder="Choice Value" /></label> <a href="#" id="remScnt">Remove</a></p>').appendTo(scntDiv);
            i++;
            return false;
        });
        
        $('#remScnt').live('click', function() { 
            if( i > 2 ) {
                $(this).parents('p').remove();
                i--;
            }
            return false;
        });
    });

 jQuery( document ).ready( function($) { 
	var validation_holder;

	$("form#add_question input[name='saveAdd']").click(function() {
           var validation_holder= validateForm(this.form);
           if(validation_holder == 1) { // if have a field is blank, return false
			$("p.validate_msg").slideDown("fast");
			return false;
		}  validation_holder = 0; // else return true
		/* validation end */
	
	}); // click end
        
        $("form#add_question input[name='save']").click(function() {
           var validation_holder= validateForm(this.form);
           if(validation_holder == 1) { // if have a field is blank, return false
			$("p.validate_msg").slideDown("fast");
			return false;
		}  validation_holder = 0; // else return true
		/* validation end */
	
	}); // click end
        
        function validateForm(form) { 
            var validation_holder=0;
            var question = $("form#add_question input[name='question_text']").val();
              var questionEssay = $("form#add_question textarea[name='question_text']").val();

            var questionNo = $("form#add_question input[name='question_Number']").val();
            var choice1 = $("form#add_question input[id='p_choice_1']").val();
             var choice2 = $("form#add_question input[id='p_choice_2']").val();
             var Detractor = $("form#add_question input[id='Detractor']").val();
             var Promoter = $("form#add_question input[id='Promoter']").val();




		/* validation start */
		if(question == "") {
			$("span.val_qtext").html("You must enter a question").addClass('validate');
			validation_holder = 1;
		} else {
			$("span.val_qtext").html("");
			}
                        
                        /* validation start */
		if(questionEssay == "") {
			$("span.val_qtextA").html("You must enter a question").addClass('validate');
			validation_holder = 1;
		} else {
			$("span.val_qtextA").html("");
			}
                        /* validation start */
		if(questionNo == "") {
			$("span.val_qno").html("You must enter a question number").addClass('validate');
			validation_holder = 1;
		} else {
			$("span.val_qno").html("");
			}
		
		
                /* validation start */
		if(choice1 == "") {
			$("span.val_qchoice").html("You must enter at least two choices").addClass('validate');
			validation_holder = 1;
		} else {
                    if(choice2=="")
                        {
                          $("span.val_qchoice").html("You must enter at least two choices").addClass('validate');
			validation_holder = 1;  
                        }else
			$("span.val_qchoice").html("");
			}
		
                if(Detractor == "") {
			$("span.val_Detractor").html("You must enter a detractor").addClass('validate');
			validation_holder = 1;
		} else {
			$("span.val_Detractor").html("");
			}
                        if(Promoter == "") {
			$("span.val_Promoter").html("You must enter a promoter").addClass('validate');
			validation_holder = 1;
		} else {
			$("span.val_Promoter").html("");
			}
		return validation_holder;
} 

}); // jQuery End    
</script>      