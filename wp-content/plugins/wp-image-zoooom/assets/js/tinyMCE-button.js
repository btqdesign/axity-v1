(function() {
    tinymce.PluginManager.add('image_zoom_button', function( editor, url ) {

    var this_js = tinyMCEPreInit.mceInit.content.external_plugins.image_zoom_button;
    var this_png_button = this_js.replace('js/tinyMCE-button.js', 'images/tinyMCE_button.png');

   function toggle_zoom() {
        var content = editor.selection.getContent();
        var zoom_class = 'zoooom';

        if ( content.indexOf('img ') < 0 ) {
            alert('First you have to select the image to which you want to add the zoom feature');
            return false;
        }

        if ( content.indexOf( zoom_class ) < 0 ) {
            if ( content.indexOf('size-full') > 0 ) {
                alert('You can add the zoom feature only to non full-size images');
                return false;
            }
            editor.dom.addClass( editor.selection.getNode(), zoom_class );
            this.active(true);
        } else {
            editor.dom.removeClass( editor.selection.getNode(), zoom_class );
            this.active(false);
        }
    }

    editor.addButton('image_zoom_button', {
            title: 'Image Zoooom',
            icon: 'icon image-zoom-icon',
//            image: '../wp-content/plugins/wp-image-zoooom/assets/images/tinyMCE_button.png',
            image: this_png_button,
            stateSelector: 'img.zoooom',
            onClick: toggle_zoom,
        });
    });
})();


