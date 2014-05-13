<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use WordPress\ORM\Model\GWWrapper;

$adminURL = admin_url('admin-post.php');
$delbtnurl = WP_PLUGIN_URL . '/GWU_Builder/images/delete.png';

$Wrapper = new GWWrapper();
$QuestionnaireID = $_GET['Qid'];
$QuestionSeq = $_GET['qno'];

$Question = $Wrapper->getQuestion($QuestionSeq, $QuestionnaireID)[0];
$action = null;
$actions = array();

$actions = $Wrapper->listActions($QuestionnaireID, $QuestionSeq);

if (!is_array($actions)) {
    $actions = array();
}

$actionsCounter = 0; //array_count_values($actions);
wp_enqueue_media();
?>

<style>
    
    .deleteBtn {
        background: url('<?php echo $delbtnurl; ?>') no-repeat center;
        height: 16px;
        width: 16px;
        border: none;
        cursor: pointer;
    }
    
    
</style>
<script  type='text/javascript'>
    var noOfActions = <?php echo $actionsCounter; ?>;
    function removeAction(removeActionBtn) {


        var id = jQuery(removeActionBtn).attr('id').split('_')[1];
        var actionId = jQuery("#actionID_" + id).val();
        //alert('Action ID:' + actionId);
        var result = 'false';
        var data = {
            action: 'delete_gw_action',
            ActionID: actionId
        };

        jQuery.ajax({
            type: "post",
            url: ajaxurl,
            data: data,
            dataType: 'json',
            success: function(response) {
                location.reload();
                //  if (response.success) {
                //jQuery("#actionID_" + id).val(response.result);
                //    result = 'true';
                //  jQuery("#action_" + id).remove();
                /*var actionDiv = removeActionBtn.parentNode;
                 var parentDiv = actionDiv.parentNode;
                 parentDiv.removeChild(actionDiv);
                 noOfActions = noOfActions - 1;*/
                //} 
                /////alert('success');
                /*if(response.success) {
                 jQuery("#action_" + id).remove();
                 }*/

            },
            error: function(jqXHR, textStatus, errorThrown) {
                //alert('error');
            }

        });
        /*
         if(result == 'true'){
         var actionDiv = removeActionBtn.parentNode;
         var parentDiv = actionDiv.parentNode;
         parentDiv.removeChild(actionDiv);
         noOfActions = noOfActions - 1;
         }*/
    }

    jQuery(document).ready(function($) {


<?php
foreach ($actions as $c) {
    ?>
            addActionLine("web");
            $("#actionID_" + noOfActions).val('<?php echo $c->get_ActionID(); ?>');
            $("#file_" + noOfActions).val('<?php echo $c->get_LinkToAction(); ?>');
            $("#file_" + noOfActions).attr("readonly", "true");
            $("#seqNo_" + noOfActions).val('<?php echo $c->get_Sequence(); ?>');
            $("#seqNo_" + noOfActions).attr("readonly", "true");
            $('#upLoadMedia_' + noOfActions).attr("disabled", "true");
            $('#saveMedia_' + noOfActions).attr("disabled", "true");
            $('#deleteMedia_' + noOfActions).removeAttr("disabled");
            //$('#deleteMedia_' + noOfActions).attr("disabled", "false");
            //$('#deleteMedia_' + noOfActions).attr("enabled", "true");
    <?php
}
?>
        //uploadMedia();

        var _custom_media = true,
                _orig_send_attachment = wp.media.editor.send.attachment;


    });

    function uploadMedia(upButton) {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = upButton;
        var id = jQuery(upButton).attr('id').split('_')[1];
        _custom_media = true;
        wp.media.editor.send.attachment = function(props, attachment) {
            if (_custom_media) {
                jQuery('#file_' + id).val(attachment.url);
            } else {
                return _orig_send_attachment.apply(this, [props, attachment]);
            }
        };

        wp.media.editor.open(button);
        return false;
    }
    function saveMedia(saveButton) {
        var id = jQuery(saveButton).attr('id').split('_')[1];
        var url = "";
        var seqNo = ""
        url = jQuery('#file_' + id).val();
        seqNo = jQuery('#seqNo_' + id).val();
        jQuery('#span_' + id).text("");
        if (url == '') {
            jQuery('#span_' + id).text("Media Link cannot be empty");
            return;
        }
        if (seqNo == '') {
            jQuery('#span_' + id).text("Sequence Number cannot be blank");
            return;
        }
        if (isNaN(seqNo) != false || seqNo < '1' || seqNo.indexOf(".") != -1) {
            jQuery('#span_' + id).text("Invalid sequence number");
            return;
        }


        var isImage = false;
        var isVideo = false;
        var mediatype = '';
        var _validFileExtensions = [".jpg", ".jpeg", ".gif", ".png"];
        for (var j = 0; j < _validFileExtensions.length; j++) {
            var sCurExtension = _validFileExtensions[j];
            if (url.substr(url.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                isImage = true;
                mediatype = "Image";
                break;
            }
        }

        var _validFileExtensions = [".mov", ".mp4", ".avi", ".wmv", ".ogv", ".mpg", ".m4v", ".3gp", ".3g2"];
        for (var j = 0; j < _validFileExtensions.length; j++) {
            var sCurExtension = _validFileExtensions[j];
            if (url.substr(url.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                isImage = true;
                mediatype = "Video";
                break;
            }
        }
        if (mediatype == '') {
            jQuery('#span_' + id).text("In valid media type. Kindly upload file is supported format");
            return;
        }

        var data = {
            action: 'save_action',
            QuestionnaireID: <?php echo $QuestionnaireID; ?>,
            QuestionSeq: <?php echo $QuestionSeq; ?>,
            ActionType: mediatype,
            LinkToAction: url,
            //Duration: NULL,
            Sequence: seqNo
                    //Content: NULL
        };

        jQuery.ajax({
            type: "post",
            url: ajaxurl,
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    jQuery("#actionID_" + id).val(response.result.ActionID);
                }
                
                jQuery('#deleteMedia_' + id).removeAttr('disabled');
            }

        });

        saveButton.disabled = true;
        jQuery('#upLoadMedia_' + id).attr("disabled", "true");
        jQuery("#file_" + id).attr("readonly", "true");
        jQuery("#seqNo_" + id).attr("readonly", "true");
    }



    function cancelAndReturnToQuestionnaire() {

        window.location = '<?php echo admin_url("admin.php?page=GWU_add-Questionnaire-page&id=view&Qid=" . $QuestionnaireID); ?>';
    }
    function addActionLine(type) {
        noOfActions = noOfActions + 1;
        var fileChooser = " ";
        var removeAction = '<input type="image" id="removeAction_'
                + noOfActions + '" name="removeAction_' + noOfActions
                + '" value="" onClick="removeAction(this)" src="<?php echo $delbtnurl; ?>" style="position:absolute;"/>';

        fileChooser = '<input type ="hidden" name="actionID_' + noOfActions + '" id="actionID_' + noOfActions + '" />  <label for="file">File to upload:</label>   <input id="file_' + noOfActions + '" type="text" name="file_' + noOfActions + '"> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp';
        fileChooser += '<input type="text" name="seqNo_' + noOfActions + '" id="seqNo_' + noOfActions + '"" size="3"/>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
        fileChooser += '<input type="button" name="saveMedia_' + noOfActions + '" id="saveMedia_' + noOfActions + '" value="save" onClick="saveMedia(this)"/>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
        fileChooser += '<input type="button" name="upLoadMedia_' + noOfActions + '" id="upLoadMedia_' + noOfActions + '" value="Browse" onClick="uploadMedia(this)"/>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
        fileChooser += '<input type="button" name="deleteMedia_' + noOfActions + '" id="deleteMedia_' + noOfActions + '" value="" onClick="removeAction(this)" disabled="true" class="deleteBtn"/>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp';
        fileChooser += '<span id="span_' + noOfActions + '" name="span_' + noOfActions + '"style="color:red"></span>';

        jQuery("#actionsDiv").append('<div id="action_' + noOfActions + '">' + fileChooser + '</div>');


    }
</script>

<div id="form_container">


    <h3>Define Actions for question number <?php echo $Question->get_QuestionNumber(); ?></h3>
    <form id="actionlogicform" class="actionlogic"  method="post" action="<?php echo $adminURL; ?>">
        <input type="hidden" name="action" value="done_action" />
        <input type="hidden" name="QuestionnaireID" value="<?php echo $QuestionnaireID; ?>"/>

        <?php
        if ($action != null) {
            ?>
            <input type="hidden" name="ActionID" value="<?php echo $action->get_ActionID(); ?>" />
            <?php
        }
        ?>

        <input type="hidden" name="QuestionSeq" value="<?php echo $QuestionSeq; ?>"/>

        <div id="actionsDiv">

        </div>
        <!--<input type="button" id="uploadAction" name="uploadAction" value="Upload Media" />!-->
        <input type="button" id="addAction" name="addAction" value="Add Media" onClick="addActionLine('web')"/>
        <br/>
        <br/>

        <div>
            <input type="submit" id="done" name="done" value="Done"/>
            <input type="button" id="cancel" name="cancel" value="Cancel" onClick="cancelAndReturnToQuestionnaire()"/>
        </div>


    </form>	
</div>