<body id="Questionnaire-body" >
    <div id="Questionnaire-general" class="wrap">
        <h1>Add new questionnaire:</h1>
        <form id="Questionnaire-form" class="create-new" style="  padding-left: 20%;
    padding-top: 50px;"  method="post" action= "<?php echo admin_url('admin-post.php');?>">
            <input type="hidden" name="action" value="add_new_Questionnaire" />
		  <table>
                      <tr>
                          <td style="width: 200px">Questionnaire Title</td>
                <td class="style1">
                    <input type="text" name="questionnaire_title" size="30" />
                     <script type="text/javascript">
                        Calendar.setup({
                            inputField	 : "element_2_3",
                            baseField    : "element_2",
                            displayArea  : "calendar_2",
                            button		 : "cal_img_2",
                            ifFormat	 : "%B %e, %Y",
                            onSelect	 : selectDate
                        });
                    </script>
                </td>
                </tr>
                
                       <tr>
                           <td>can this survey be taken by anonymous user?</td>
                <td class="style1">
                    <span>
                        <input id="element_3_1" name="anonymous" class="element radio" type="radio" value="1" />
                        <label class="choice" for="anonymous_1">yes</label>
                        <input id="element_3_2" name="anonymous" class="element radio" type="radio" value="0" />
                        <label class="choice" for="anonymous_2">no</label>

                    </span>
                     
                </td>
                </tr>
                
                          <tr>
                                                         <td>can user take the survey multiple times</td>

                <td class="style1">
                    <span>
                        <input id="element_4_1" name="multiple" class="element radio" type="radio" value="1" />
                        <label class="choice" for="multiple_1">yes</label>
                        <input id="element_4_2" name="multiple" class="element radio" type="radio" value="0" />
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
                          <input type="submit" value="Save" class="button-primary"/>
                </td> </tr>
                  
                  </table>
 
        </form>
    </div>
</body>
