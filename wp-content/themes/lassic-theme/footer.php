<?php
/**
 * The template for displaying Footer
 */
 global $wpdb,$cs_theme_options;
 ?>
		<!-- Main Section End -->
        </div>
	</main>
    <!-- Main Content Section -->
    <div class="clear"></div>
    <!-- Footer Start -->
    <?php
		$cs_sub_footer_social_icons = $cs_theme_options['cs_sub_footer_social_icons']; 
		$footer_twitter = '';
		$cs_footer_switch = isset($cs_theme_options['cs_footer_switch']) ? $cs_theme_options['cs_footer_switch'] : '';
		$cs_footer_widget = isset($cs_theme_options['cs_footer_widget']) ? $cs_theme_options['cs_footer_widget'] : '';
 		if(isset($cs_footer_switch) and $cs_footer_switch=='on'){
        ?>
           <footer id="footer-sec">
                <div class="container">
                    <div class="row">
                        <?php 
						if( $cs_footer_widget == 'on' ){
							if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer-widget-1') ) : endif; 
						}
                        ?>
                        <div class="footer-sepratore col-md-12">
                          <span id="backtop">
                          <a href="#"> 
                            <i class="icon-arrow-up8"></i> 
                          </a>
                          </span>
                        </div>
						<div class="footer-content">
                        	<?php if( $cs_sub_footer_social_icons == 'on' ){ ?>
                                <div class="cs-social-media">
                                    <?php cs_social_network(); ?>
                                </div>
                            <?php } ?>
                            <div class="cs-footer-menu">
	                            <div class="col-md-12">
	                                 <?php cs_footer_navigation(); ?>
	                            </div>
	                        </div>
	                        <?php
                            if(isset($cs_footer_widget) and $cs_footer_widget == 'on'){  
                            ?>
                            <div id="copyright">
                                <div class="col-md-12">
                                  <p><?php
                                      $cs_copy_right = $cs_theme_options['cs_copy_right'];
                                       if(isset($cs_copy_right) and $cs_copy_right<>''){ 
                                      echo do_shortcode(htmlspecialchars_decode($cs_copy_right)); 
                                    } else{
                                      echo '&copy;'.gmdate("Y")." ".get_option("blogname")." Wordpress All rights reserved.";  
                                    }
                                    ?> 
                                </p>
                                </div>
                             </div>
                        </div>
                		<?php }?>
                    </div>
                </div>
           </footer>
            <!-- Footer End -->
	    <!-- Bottom Section -->
    <?php } ?>
    </div>
    <!-- Wrapper End -->
    <?php
    if(isset($cs_theme_options['cs_google_analytics']) and $cs_theme_options['cs_google_analytics']<>''){
    echo '<script type="text/javascript">
                '. htmlspecialchars_decode($cs_theme_options['cs_google_analytics']) .'
          </script>';
    }
    wp_footer();
    ?>
	</body>
</html>