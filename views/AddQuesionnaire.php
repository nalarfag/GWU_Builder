
       <script type='text/javascript'>
        function notEmpty(form){
            var title=form.questionnaire_title;
            if(title.value.length == 0){
                alert("Please enter a title for the Questionnaire");
                form.questionnaire_title.focus();
                return false;
            }
            return true;
        }
        


jQuery( document ).ready( function($) {   

   $('#anonymous_1').click(function () {
        $('#mutlipleRadio').hide();
    });
    $('#anonymous_2').click(function () {
        $('#mutlipleRadio').show();
    });
} );
    </script>
    <div id="Questionnaire-general" class="wrap">
        <h1>Add new questionnaire:</h1>
        <form id="Questionnaire-form" onsubmit="return notEmpty(this);"
              class="create-new" style="  padding-left: 20%;
              padding-top: 50px;" 
              method="post" action= "<?php echo admin_url('admin-post.php'); ?>
              ">
            <input type="hidden" name="action" value="add_new_Questionnaire" />
            <table>
                <tr>
                    <td style="width: 200px">Questionnaire Title</td>
                    <td class="style1">
                        <input type="text" id="questionnaire_title" name="questionnaire_title" size="30" />
                    </td>
                </tr>

                <tr>
                    <td>can this survey be taken by anonymous user?</td>
                    <td class="style1">
                        <span>
                            <input id="anonymous_1" name="anonymous" class="element radio" 
                                   type="radio" value="1" checked />
                            <label class="choice" for="anonymous_1">yes</label>
                            <input id="anonymous_2" name="anonymous" class="element radio" type="radio" value="0" />
                            <label class="choice" for="anonymous_2">no</label>

                        </span>

                    </td>
                </tr>

                <tr id="mutlipleRadio" style="display:none">
                    <td>can user take the survey multiple times</td>

                    <td class="style1">
                        <span>
                            <input id="multiple_1" name="multiple" class="element radio" 
                                   type="radio" value="1" checked />
                            <label class="choice" for="multiple_1">yes</label>
                            <input id="multiple_2" name="multiple" class="element radio" type="radio" value="0" />
                            <label class="choice" for="multiple_2">no</label>

                        </span> 

                    </td>
                </tr>

                <tr>
                    <td>questionnaire topic:</td>

                    <td class="style1">

                        <select class="element select medium" id="topic" name="topic"> 
                            <option value="" selected="selected"></option>
                            <option value="sports" >sports</option>
                            <option value="education" >education</option>
                            <option value="shopping" >shopping</option>
                            <option value="other" >other</option>

                        </select>
                    </td>
                </tr>

                <tr> 
                    <td></td>
                    <td align="right"> 	
                        <input type="submit" value="Save" 
                               class="button-primary"/>
                    </td> </tr>

            </table>

        </form>
    </div>


