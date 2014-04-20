
//add new option when click on add        
jQuery( document ).ready( function($) {   
    var scntDiv = $('#p_choices');
     var nameDiv = $('#p_flagsName');
      var valueDiv = $('#p_flagsValue');
    var i = $('#p_choices p').size() + 1;
        
    $('#addChoice').live('click', function() {
	$('<p class="p_choice_'+i+'"><label for="p_choices"><input type="text" id="p_choice_'+i+'" size="50" maxlength="255"  name="p_choice[]" value="" placeholder="Choice Value" /></label>').appendTo(scntDiv);
      	$('<p class="p_choice_'+i+'"><label for="p_choices"><input type="text" id="p_flagName_'+i+'"size="6" maxlength="20" name="p_flagName[]" value="" placeholder="flag name" /></label>').appendTo(nameDiv);

      	$('<p class="p_choice_'+i+'"><label for="p_choices"><input type="text" id="p_flagValue_'+i+'" size="6" maxlength="20" name="p_flagValue[]" value=""placeholder="flag value" ></label> <a href="#" id="remScnt">Remove</a></p>').appendTo(valueDiv);

      i++;
        return false;
    });
        
    $('#remScnt').live('click', function() { 
        if( i > 2 ) {
            console.debug($(this).parents('p'));
           var id= $(this).parents('p').attr('class');
           $('.'+id).remove();
           console.debug(id);
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
        }
        validation_holder = 0; // else return true
    /* validation end */
	
    }); // click end
        
    $("form#add_question input[name='save']").click(function() {
        var validation_holder= validateForm(this.form);
        if(validation_holder == 1) { // if have a field is blank, return false
            $("p.validate_msg").slideDown("fast");
            return false;
        }
        validation_holder = 0; // else return true
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




        /* validation question text */
        if(question == "") {
            $("span.val_qtext").html("You must enter a question").addClass('validate');
            validation_holder = 1;
        } else {
            $("span.val_qtext").html("");
        }
                        
        /* validation question text */
        if(questionEssay == "") {
            $("span.val_qtextA").html("You must enter a question").addClass('validate');
            validation_holder = 1;
        } else {
            $("span.val_qtextA").html("");
        }
        /* validation question number */
        if(questionNo == "") {
            $("span.val_qno").html("You must enter a question number").addClass('validate');
            validation_holder = 1;
        } else {
            $("span.val_qno").html("");
        }
		
		
        /* validation question choices */
        if(choice1 == "") {
            $("span.val_qchoice").html("You must enter at least the first two choices").addClass('validate');
            validation_holder = 1;
        } else {
            if(choice2=="")
            {
                $("span.val_qchoice").html("You must enter at least the first two choices").addClass('validate');
                validation_holder = 1;  
            }else
                $("span.val_qchoice").html("");
        }
	 /* validation question Detractor */	
        if(Detractor == "") {
            $("span.val_Detractor").html("You must enter a detractor").addClass('validate');
            validation_holder = 1;
        } else {
            $("span.val_Detractor").html("");
        }
        /* validation question Promoter */	
        if(Promoter == "") {
            $("span.val_Promoter").html("You must enter a promoter").addClass('validate');
            validation_holder = 1;
        } else {
            $("span.val_Promoter").html("");
        }
        return validation_holder;
    } 

}); // jQuery End    


