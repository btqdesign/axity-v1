<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 */
	global  $cs_theme_options;
	get_header();
?>
 	<!-- Col Md 12 -->
   	<section class="page-section">
        <div class="container">
            <div class="row">
                <div class="page-not-found">
                  <h2><?php _e('404','lassic');?></h2>
                  <h3><?php _e('Oops, This Page Could Not Be Found!','lassic'); ?></h3>
                  <div class="cs-content404">
                    <div class="desc">
                      <p><?php _e('Unfortunately the page you were looking for could not be found. It may be temporarily unavailable, moved or no longer exist. <br> Check the URL you entered for any mistakes and try again.','lassic'); ?></p> 
                    </div>
                    <a class="go-home csbg-color" href="<?php echo esc_url(site_url()); ?>"><?php _e('Go Home', 'lassic'); ?> <i class="icon-long-arrow-right"></i></a>
                  </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Col Md 12 --> 
<?php get_footer();?>