<?php

$message = __('Only available in <a href="%1$s" target="_blank">PRO version</a>', 'wp-image-zoooom');
$message = wp_kses( $message, array('a' => array('href' => array(), 'target'=> array())));
$message = sprintf( $message, 'https://www.silkypress.com/wp-image-zoom-plugin/?utm_source=wordpress&utm_campaign=iz_free&utm_medium=banner');


?>

    <div id="wpfc-premium-tooltip" style="display:none;width: 230px; height: 60px; position: absolute; margin-left: 354px; margin-top: 112px; color: white;">
        <div style="float:left;width:13px;">
            <div style="width: 0px; height: 0px; border-top: 6px solid transparent; border-right: 6px solid #333333; border-bottom: 6px solid transparent; float: right; margin-right: 0px; margin-top: 16px;"></div>
        </div>
        <div style="font-family:sans-serif;font-size:13px;text-align: center; border-radius: 5px; float: left; background-color: rgb(51, 51, 51); color: white; width: 210px; padding: 10px 0px;">
            <label><?php echo $message; ?></label>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){
            $(".form-group.disabled-short").click(function(e){
                if(typeof window.tooltip != "undefined"){
                    clearTimeout(window.tooltip);
                }

                var inputCon = $(e.currentTarget).find(".input-group");
                var left = 30;


                $(e.currentTarget).children().each(function(i, child){
                    left += $(child).width(); 
                });

                $("#wpfc-premium-tooltip").css({"margin-left" : left + "px", "margin-top" : ($(e.currentTarget).offset().top - 38) + "px"});
                $("#wpfc-premium-tooltip").fadeIn( "slow", function() {
                    window.tooltip = setTimeout(function(){ $("#wpfc-premium-tooltip").hide(); }, 1000);
                });
                return false;
            });

            $(".form-group.disabled").click(function(e){
                 if(typeof window.tooltip != "undefined"){
                    clearTimeout(window.tooltip);
                }

                var left = $(e.currentTarget).width();

                $("#wpfc-premium-tooltip").css({"margin-left" : left + "px", "margin-top" : ($(e.currentTarget).offset().top - 38) + "px"});
                $("#wpfc-premium-tooltip").fadeIn( "slow", function() {
                    window.tooltip = setTimeout(function(){ $("#wpfc-premium-tooltip").hide(); }, 1000);
                });
                return false;


            });
        });
    </script>
    
