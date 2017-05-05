/*jQuery(document).ready( function() {

    jQuery('#upload_image_button').click(function() {

        formfield = jQuery('#upload_image').attr('name');
        tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
        return false;
    });

    window.send_to_editor = function(html) {

        imgurl = jQuery('img',html).attr('src');
        jQuery('#upload_image').val(imgurl);
        tb_remove();
    }

});*/
jQuery(document).ready(function(){
jQuery('#upload_image_button').click(function(){
wp.media.editor.send.attachment = function(props, attachment){
jQuery('#upload_image').val(attachment.url);
}

wp.media.editor.open(this);

return false;
});
});