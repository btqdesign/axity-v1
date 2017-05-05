<?php
/**
 * The template for displaying Search Result
 */
	get_header();
	global  $cs_theme_option; 
 	if(isset($cs_theme_options['cs_excerpt_length']) && $cs_theme_options['cs_excerpt_length'] <> ''){ $default_excerpt_length = $cs_theme_options['cs_excerpt_length']; }else{ $default_excerpt_length = '255';}; 
			
	$cs_layout 	=  $cs_theme_options['cs_default_page_layout'];
	if ( isset( $cs_layout ) && $cs_layout == "sidebar_left") {
		$cs_layout = "content-right col-md-9";
	} else if ( isset( $cs_layout ) && $cs_layout == "sidebar_right" ) {
		$cs_layout = "content-left col-md-9";
	} else {
		$cs_layout = "col-md-12";
	}
	$cs_sidebar	= $cs_theme_options['cs_default_layout_sidebar'];
			
	$cs_tags_name = 'post_tag';
	$cs_categories_name = 'category';
	if(!isset($GET['page_id'])) $GET['page_id_all']=1;
	
	global $wp_query;
	?>
    <section class="page-section" style=" padding:0;">
        <!-- Container -->
        <div class="container">
            <!-- Row -->
            <div class="row">        
			<?php if ($cs_layout == 'content-right col-md-9'){ ?>
                <div class="content-lt col-md-3"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar) ) : ?><?php endif; ?></div>
            <?php } ?>
            	
   			<div class="<?php echo esc_attr($cs_layout); ?>">
				<?php
				//print_r($wp_query);
                if ( have_posts() ) : 
				  echo '<div class="relevant-search">';
                  echo '<div class="cs-section-title"><h3>'.__('Showing result for "'.get_search_query().'"','lassic').'</h3></div>';
                  echo '<div class="cs-search-results"><ul>';
                   while ( have_posts() ) : the_post();
                 ?>	
                    <li>
                    <?php 
                        if ( is_sticky() ){  echo '<span>'.__('Featured : ', 'lassic').'</span>';}
                         echo '<h5>'.date_i18n(get_option( 'date_format' ),strtotime(get_the_date('d/m/Y',$post->ID))); ?>, <?php  echo cs_get_the_excerpt('50',false);?></h5>
                        <a href="<?php esc_url(the_permalink()); ?>"><?php esc_url(the_permalink()); ?></a>
                    </li>
                <?php  
                endwhile;
                echo '</ul></div>';
				echo '</div>';
                else:
                    cs_no_result_found(); 
                endif;
				
                $qrystr = '';
				if ($wp_query->found_posts > get_option('posts_per_page')) {
    
					if ( isset($_GET['s']) ) $qrystr = "&amp;s=".$_GET['s'];
					if ( isset($_GET['page_id']) ) $qrystr .= "&amp;page_id=".$_GET['page_id'];
					echo cs_pagination($wp_query->found_posts,get_option('posts_per_page'), $qrystr);
                }
            ?>
           </div>                  
			
			<?php if ( $cs_layout  == 'content-left col-md-9'){ ?>
               <div class="content-rt col-md-3"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar) ) : ?><?php endif; ?></div>
            <?php } ?> 
        </div>
      </div>
   </section>
<?php 

get_footer();
?>
<!-- Columns End -->