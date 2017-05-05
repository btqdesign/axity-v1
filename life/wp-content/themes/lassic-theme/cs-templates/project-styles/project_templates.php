<?php 

// Project Templates
if ( !class_exists('ProjectTemplates') ) {
	
	class ProjectTemplates
	{
		
		function __construct()
		{
			// Constructor Code here..
		}
	
		//======================================================================
		// Project Grid View
		//======================================================================
		public function cs_grid_view( $atts ) {
			
		$defaults = array('column_size'=>'','cs_project_section_title'=>'','cs_project_view'=>'','cs_filterable'=>'','cs_text_align'=>'','cs_project_cat'=>'',
		'cs_project_num_post'=>'10','cs_project_pagination'=>'','cs_project_class' => '','cs_project_animation' => '');
		extract( shortcode_atts( $defaults, $atts ) );

		if (empty($_GET['page_id_all'])) $_GET['page_id_all'] = 1;
		ob_start();
		if(isset($atts['cs_project_num_post'])){
			$cs_project_per_page = $atts['cs_project_num_post'];
		}
		else{
			$cs_project_per_page = '-1';
		}
		
		$project_args_all = array('posts_per_page' => "-1", 'post_type' => 'project', 'post_status' => 'publish');
		
		if(isset($cs_project_cat) && $cs_project_cat <> '' &&  $cs_project_cat <> '0'){
			$cs_project_cat 			= $cs_project_cat;
			$project_category_array 	= array('project-category' => "$cs_project_cat");
			$project_args_all 			= array_merge($project_args_all, $project_category_array);
		}
		
		$query_all = new WP_Query($project_args_all);
		$count_post = $query_all->post_count;
		$args = array(
			'post_type' 		=> 'project',
			'paged'  			=> $_GET['page_id_all'],
			'posts_per_page' 	=> (int)"$cs_project_per_page",
			'order' 			=> "ID",
			'orderby'			=> "DESC",
		);
		if(isset($cs_project_cat) && $cs_project_cat <> '' &&  $cs_project_cat <> '0'){
			$project_category_array = array('project-category' => "$cs_project_cat");
			$args = array_merge($args, $project_category_array);
		}
		
		$cs_filter_id = '';
		$cs_filter_class = '';
		if( isset( $cs_filterable ) && $cs_filterable == 'yes' ) {
			cs_filterable();
			$randomId = cs_generate_random_string('10');
			$cs_filter_id	= 'id=list-'.$randomId;
			$cs_filter_class	= 'mix';
		}
		
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) { 	
			if((isset($cs_project_section_title) and $cs_project_section_title !='') || (isset($cs_filterable) and $cs_filterable =='yes')){
				echo '<div class="cs-section-title col-md-12">';
					if ( isset( $cs_project_section_title ) && $cs_project_section_title !='' ) {
						echo '<h2>'.$cs_project_section_title.'</h2>';
					}
					if( isset($cs_project_cat) && ($cs_project_cat <> "" && $cs_project_cat <> "0")){	
						$categories = get_categories( array('child_of' => "$cs_project_cat", 'taxonomy' => 'project-category', 'hierarchical' => false) );
					}else{
						$categories = get_categories( array('taxonomy' => 'project-category', 'hierarchical' => false) );
					}
					if( isset( $cs_filterable ) && $cs_filterable == 'yes' ) {?>
                    <div class="cs-filter">
                        <nav class="filter-nav">
                            <ul class="cs-filter-menu">
                                <li class="filter active" data-filter="all"><a href="javascript:;"><?php _e('All','lassic'); ?></a></li>
                                <?php
                                  foreach ($categories as $category) {
                                  ?>
                                  <li class="filter" data-filter="<?php echo intval($category->term_id); ?>">
                                    <a href="javascript:;">
                                        <?php echo esc_attr($category->cat_name); ?>
                                    </a>
                                  </li>
                                  <?php
                                  }
                                ?>
                            </ul>
                        </nav>
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {
                                jQuery('#list-<?php echo cs_allow_special_char($randomId);?>').mixitup({ effects :["blur","fade"]});
                            });
                        </script>
                    </div>
            <?php }
		       echo '</div>';
 			}?>
        	<div <?php echo esc_attr( $cs_filter_id );?> class="cs-portfoliolist">
	        	<?php 
					$cs_title_limit = 30;
					while ( $query->have_posts() ) : $query->the_post();
						global $post;
						$image_id = get_post_thumbnail_id($post->ID);
						if($image_id <> ''){
							$image_url = cs_attachment_image_src(get_post_thumbnail_id($post->ID), 382, 286);
						}else{
							$image_url 		= get_template_directory_uri().'/assets/images/no-image4x3.jpg';
						}
						$post_cats = wp_get_object_terms( $post->ID, 'project-category' );
						$p_cats = '';
						foreach($post_cats as $cats){
							$p_cats .= $cats->term_id.' ';
						}
						?>
                        <article class="col-md-3 <?php echo esc_html($cs_filter_class.' '.$p_cats); ?>" data-id="id-<?php echo intval($post->ID); ?>">
                            <figure>
                              <?php if($image_url <> ''){ ?>
                                  <a href="<?php esc_url(the_permalink()); ?>"><img src="<?php echo esc_url($image_url); ?>" alt=""></a>
                              <?php } ?>
                              <figcaption>
                                  <div class="caption-inner">
                                      <div class="text">
                                          <a href="<?php esc_url(the_permalink()); ?>">
                                              <i class="icon-plus8"></i>
                                          </a>
                                          <a href="<?php esc_url(the_permalink()); ?>"><?php echo esc_attr(__('View','lassic'),'lassic');?><br> <?php echo esc_attr(__('Project','lassic'),'lassic');?></a>
                                      </div>
                                  </div>
                              </figcaption>
                            </figure>
                             <div class="textinfo-sec">
                                <h3>
                                	<a href="<?php esc_url(the_permalink()); ?>">
										<?php cs_get_title($cs_title_limit); ?>
									</a>
                                </h3>
                                <?php $cs_term_list	= get_the_term_list ( $post->ID, 'project-category', '<span><i class="fa fa-plus"></i>', ', ', '</span>' ); ?>
                                <?php if ( isset( $cs_term_list ) && $cs_term_list!='' ) {?>
                                <ul class="post-options">
                                    <li><?php echo cs_allow_special_char($cs_term_list);?></li>
                                </ul>
                                <?php }?>
                            </div>
						</article>
 					<?php endwhile;?>
        		</div>
        	<?php
		 	if( isset( $cs_filterable ) && $cs_filterable == 'no' ) {
			 //==Pagination Start
				if ( $cs_project_pagination == "Show Pagination" && $count_post > $cs_project_num_post && $cs_project_num_post > 0 ) {
					$qrystr = '';
					if ( isset($_GET['page_id']) ) $qrystr .= "&amp;page_id=".$_GET['page_id'];
					echo cs_pagination($count_post, $cs_project_num_post, $qrystr, 'Show Pagination');
				}
			//==Pagination End	
		 	}
			wp_reset_postdata(); 
			 $projects_data = ob_get_clean();
         		echo cs_allow_special_char($projects_data);
        	}
	    }
		//======================================================================
		// Project Grid View
		//======================================================================
		public function cs_gutter_view( $atts ) {
			
		$defaults = array('column_size'=>'','cs_project_section_title'=>'','cs_project_view'=>'','cs_filterable'=>'','cs_text_align'=>'','cs_project_cat'=>'',
		'cs_project_num_post'=>'10','cs_project_pagination'=>'','cs_project_class' => '','cs_project_animation' => '');
		extract( shortcode_atts( $defaults, $atts ) );

		if (empty($_GET['page_id_all'])) $_GET['page_id_all'] = 1;
		ob_start();

		if(isset($atts['cs_project_num_post'])){
			$cs_project_per_page = $atts['cs_project_num_post'];
		}
		else{
			$cs_project_per_page = '-1';
		}
		
		$project_args_all = array('posts_per_page' => "-1", 'post_type' => 'project', 'post_status' => 'publish');
		
		if(isset($cs_project_cat) && $cs_project_cat <> '' &&  $cs_project_cat <> '0'){
			$cs_project_cat 			= $cs_project_cat;
			$project_category_array 	= array('project-category' => "$cs_project_cat");
			$project_args_all 			= array_merge($project_args_all, $project_category_array);
		}
		
		$query_all = new WP_Query($project_args_all);
		$count_post = $query_all->post_count;
		$args = array(
			'post_type' 		=> 'project',
			'paged'  			=> $_GET['page_id_all'],
			'posts_per_page' 	=> (int)"$cs_project_per_page",
			'order' 			=> "ID",
			'orderby'			=> "DESC",
		);
		if(isset($cs_project_cat) && $cs_project_cat <> '' &&  $cs_project_cat <> '0'){
			$project_category_array = array('project-category' => "$cs_project_cat");
			$args = array_merge($args, $project_category_array);
		}
		$cs_filter_id = '';
		$cs_filter_class = '';
		if( isset( $cs_filterable ) && $cs_filterable == 'yes' ) {
			cs_filterable();
			$randomId = cs_generate_random_string('10');
			$cs_filter_id	= 'id=list-'.$randomId;
			$cs_filter_class	= 'mix';
		}
		
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) { 
 			if ( isset( $cs_project_view ) && $cs_project_view == 'no-gutter' ){
				$cs_view	= 'cs-portfoliolist cs-gutterstyle cs-top-center';
			} elseif ( isset( $cs_project_view ) && $cs_project_view == 'gutter' ){
				$cs_view	= 'cs-portfoliolist cs-top-center';
			} else {
				$cs_view	= 'cs-portfoliolist cs-top-center';
			}
			if((isset($cs_project_section_title) and $cs_project_section_title !='') || (isset($cs_filterable) and $cs_filterable =='yes')){
				echo '<div class="cs-section-title col-md-12">';
				if ( isset( $cs_project_section_title ) && $cs_project_section_title !='' ) {
					echo '<h2>'.$cs_project_section_title.'</h2>';
				}
				if( isset($cs_project_cat) && ($cs_project_cat <> "" && $cs_project_cat <> "0")){	
					$categories = get_categories( array('child_of' => "$cs_project_cat", 'taxonomy' => 'project-category', 'hierarchical' => false) );
				
				}else{
					$categories = get_categories( array('taxonomy' => 'project-category', 'hierarchical' => false) );
				}
				if( isset( $cs_filterable ) && $cs_filterable == 'yes' ) {?>
                    <div class="cs-filter">
                        <nav class="filter-nav">
                            <ul class="cs-filter-menu">
                                <li class="filter active" data-filter="all"><a href="javascript:;"><?php _e('All','lassic'); ?></a></li>
                                <?php
                                  foreach ($categories as $category) {
                                  ?>
                                  <li class="filter" data-filter="<?php echo intval($category->term_id); ?>">
                                    <a href="javascript:;"><?php echo esc_attr($category->cat_name); ?></a>
                                  </li>
                                  <?php
                                  }
                                ?>
                            </ul>
                        </nav>
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {
                                jQuery('#list-<?php echo cs_allow_special_char($randomId);?>').mixitup({ effects :["blur","fade"]});
                            });
                        </script>
                    </div>
			<?php }
			echo '</div>';
		}
		?>
         <div <?php echo esc_attr( $cs_filter_id );?> class="<?php echo cs_allow_special_char($cs_view);?>">
          	<?php 
			$cs_title_limit = 30;
		 	while ( $query->have_posts() ) : $query->the_post();
				global $post;
				$image_id = get_post_thumbnail_id($post->ID);
				if($image_id <> ''){
					$image_url = cs_attachment_image_src(get_post_thumbnail_id($post->ID), 382, 286);
				}else{
					$image_url 		= get_template_directory_uri().'/assets/images/no-image4x3.jpg';
				}
				
				$post_cats = wp_get_object_terms( $post->ID, 'project-category' );
				$p_cats = '';
				foreach($post_cats as $cats){
					$p_cats .= $cats->term_id.' ';
				}
			?>
            <article class="col-md-4 <?php echo esc_html($cs_filter_class.' '.$p_cats); ?>" data-id="id-<?php echo intval($post->ID); ?>">
                 <figure>
                    <?php if($image_url <> ''){ ?>
                        <a href="<?php esc_url(the_permalink()); ?>"><img src="<?php echo esc_url($image_url); ?>" alt=""></a>
                    <?php } ?>
                    <h3>
    	                <a href="<?php esc_url(the_permalink()); ?>">
	        	            <?php cs_get_title($cs_title_limit); ?>
        	            </a>
                    </h3>
                    <figcaption>
                        <div class="caption-inner cs-large">
                            <div class="text">
                                <a href="<?php esc_url(the_permalink()); ?>">
                                    <i class="icon-plus8"></i>
                                </a>
                                <a href="<?php esc_url(the_permalink()); ?>"><?php echo esc_attr('View Project','lassic');?></a>
                                <?php $cs_term_list	= get_the_term_list ( $post->ID, 'project-category', '<span><i class="fa fa-plus"></i>', ', ', '</span>' ); ?>
                                <?php if ( isset( $cs_term_list ) && $cs_term_list!='' ) {?>
                                <ul class="post-options">
                                    <li><?php echo cs_allow_special_char($cs_term_list);?></li>
                                </ul>
                                <?php }?>
                            </div>
                        </div>
                    </figcaption>
                  </figure>
            </article>
		    <?php endwhile;?>
        </div>
        <?php
		 if( isset( $cs_filterable ) && $cs_filterable == 'no' ) {
			 //==Pagination Start
				if ( $cs_project_pagination == "Show Pagination" && $count_post > $cs_project_num_post && $cs_project_num_post > 0 ) {
					$qrystr = '';
					if ( isset($_GET['page_id']) ) $qrystr .= "&amp;page_id=".$_GET['page_id'];
					echo cs_pagination($count_post, $cs_project_num_post, $qrystr, 'Show Pagination');
				}
			//==Pagination End	
		 }
		 wp_reset_postdata(); 
		 $projects_data = ob_get_clean();
         	echo  cs_allow_special_char($projects_data);
        } 
     }
		//======================================================================
		// Project Grid View
		//======================================================================
		public function cs_modern_view( $atts ) {
			
		$defaults = array('column_size'=>'','cs_project_section_title'=>'','cs_project_view'=>'','cs_filterable'=>'','cs_text_align'=>'','cs_project_cat'=>'','cs_project_num_post'=>'10','cs_project_pagination'=>'','cs_project_class' => '','cs_project_animation' => '');
		extract( shortcode_atts( $defaults, $atts ) );

		if (empty($_GET['page_id_all'])) $_GET['page_id_all'] = 1;
		ob_start();

		
		if(isset($atts['cs_project_num_post'])){
			$cs_project_per_page = $atts['cs_project_num_post'];
		}
		else{
			$cs_project_per_page = '-1';
		}
		
		$project_args_all = array('posts_per_page' => "-1", 'post_type' => 'project', 'post_status' => 'publish');
		
		if(isset($cs_project_cat) && $cs_project_cat <> '' &&  $cs_project_cat <> '0'){
			$cs_project_cat 			= $cs_project_cat;
			$project_category_array 	= array('project-category' => "$cs_project_cat");
			$project_args_all 			= array_merge($project_args_all, $project_category_array);
		}
		
		$query_all = new WP_Query($project_args_all);
		$count_post = $query_all->post_count;
		$args = array(
			'post_type' 		=> 'project',
			'paged'  			=> $_GET['page_id_all'],
			'posts_per_page' 	=> (int)"$cs_project_per_page",
			'order' 			=> "ID",
			'orderby'			=> "DESC",
		);
		if(isset($cs_project_cat) && $cs_project_cat <> '' &&  $cs_project_cat <> '0'){
			$project_category_array = array('project-category' => "$cs_project_cat");
			$args = array_merge($args, $project_category_array);
		}
		$cs_filter_id = '';
		$cs_filter_class = '';
		if( isset( $cs_filterable ) && $cs_filterable == 'yes' ) {
			cs_filterable();
			$randomId = cs_generate_random_string('10');
			$cs_filter_id	= 'id=list-'.$randomId;
			$cs_filter_class	= 'mix';
		}
		
		if( isset( $cs_text_align ) && $cs_text_align =='center' ){
			$cs_text_align	= 'top-center';
		} else {
			$cs_text_align	= '';
		}
		
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) { 
			if((isset($cs_project_section_title) and $cs_project_section_title !='') || (isset($cs_filterable) and $cs_filterable =='yes')){
				echo '<div class="cs-section-title col-md-12">';
				if ( isset( $cs_project_section_title ) && $cs_project_section_title !='' ) {
					echo '<h2>'.$cs_project_section_title.'</h2>';
				}	
				if( isset($cs_project_cat) && ($cs_project_cat <> "" && $cs_project_cat <> "0")){	
					$categories = get_categories( array('child_of' => "$cs_project_cat", 'taxonomy' => 'project-category', 'hierarchical' => false) );
 				}else{
					$categories = get_categories( array('taxonomy' => 'project-category', 'hierarchical' => false) );
				}
           		if( isset( $cs_filterable ) && $cs_filterable == 'yes' ) {?>
                    <div class="cs-filter">
                        <nav class="filter-nav">
                            <ul class="cs-filter-menu">
                                <li class="filter active" data-filter="all"><a href="javascript:;">All</a></li>
                                <?php
                                  foreach ($categories as $category) {
                                  ?>
                                  <li class="filter" data-filter="<?php echo intval($category->term_id); ?>"><a href="javascript:;"><?php echo esc_attr($category->cat_name); ?></a></li>
                                  <?php
                                  }
                                ?>
                            </ul>
                        </nav>
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {
                                jQuery('#list-<?php echo cs_allow_special_char($randomId);?>').mixitup({ effects :["blur","fade"]});
                            });
                        </script>
                    </div>
            <?php }
			echo '</div>';
			}
			?>
         <div <?php echo esc_attr( $cs_filter_id );?> class="cs-portfoliolist <?php echo esc_attr( $cs_text_align );?>">
          	<?php 
				$cs_title_limit = 30;
				while ( $query->have_posts() ) : $query->the_post();
				global $post;
				$image_id = get_post_thumbnail_id($post->ID);
				if($image_id <> ''){
					$image_url = cs_attachment_image_src(get_post_thumbnail_id($post->ID), 382, 286);
				}else{
					$image_url 		= get_template_directory_uri().'/assets/images/no-image4x3.jpg';
				}
				
				$post_cats = wp_get_object_terms( $post->ID, 'project-category' );
				$p_cats = '';
				foreach($post_cats as $cats){
					$p_cats .= $cats->term_id.' ';
				}
			?>
            <article class="col-md-4 <?php echo esc_html($cs_filter_class.' '.$p_cats); ?>" data-id="id-<?php echo intval($post->ID); ?>">
             <figure>
				<?php if($image_url <> ''){ ?>
                    <a href="<?php esc_url(the_permalink()); ?>"><img src="<?php echo esc_url($image_url); ?>" alt=""></a>
                <?php } ?>
                <figcaption>
                    <div class="caption-inner">
                        <div class="text">
                            <a href="<?php esc_url(the_permalink()); ?>">
                                <i class="icon-plus8"></i>
                            </a>
                            <a href="<?php esc_url(the_permalink()); ?>"><?php echo esc_attr('View','lassic');?><br> <?php echo esc_attr('Project','lassic');?></a>
                        </div>
                    </div>
                </figcaption>
            </figure>
            <div class="textinfo-sec">
            	<h3>
	            	<a href="<?php esc_url(the_permalink()); ?>">
    	    	        <?php cs_get_title($cs_title_limit); ?>
        	        </a>
                </h3>
				<?php 
					$cs_term_list	= get_the_term_list ( $post->ID, 'project-category', '<span><i class="fa fa-plus"></i>', ', ', '</span>' ); 
					if ( isset( $cs_term_list ) && $cs_term_list!='' ) {?>
                        <ul class="post-options">
                            <li><?php echo cs_allow_special_char($cs_term_list);?></li>
                        </ul>
                <?php }?>
            </div>
         </article>
		<?php endwhile;?>
        </div>
        <?php
		
		 if( isset( $cs_filterable ) && $cs_filterable == 'no' ) {
			 //==Pagination Start
				if ( $cs_project_pagination == "Show Pagination" && $count_post > $cs_project_num_post && $cs_project_num_post > 0 ) {
					$qrystr = '';
					if ( isset($_GET['page_id']) ) $qrystr .= "&amp;page_id=".$_GET['page_id'];
					echo cs_pagination($count_post, $cs_project_num_post, $qrystr, 'Show Pagination');
				}
			//==Pagination End	
		 }
		 wp_reset_postdata();
		 $projects_data = ob_get_clean();
         echo  cs_allow_special_char($projects_data);
        }
    }
		//======================================================================
		// Project Mesonry View
		//======================================================================
		public function cs_mesonry_view( $atts ) {
			
		$defaults = array('column_size'=>'','cs_project_section_title'=>'','cs_project_view'=>'','cs_filterable'=>'','cs_text_align'=>'','cs_project_cat'=>'','cs_project_num_post'=>'10','cs_project_pagination'=>'','cs_project_class' => '','cs_project_animation' => '');
		extract( shortcode_atts( $defaults, $atts ) );

		if (empty($_GET['page_id_all'])) $_GET['page_id_all'] = 1;
		ob_start();

		
		if(isset($atts['cs_project_num_post'])){
			$cs_project_per_page = $atts['cs_project_num_post'];
		}
		else{
			$cs_project_per_page = '-1';
		}
		
		$project_args_all = array('posts_per_page' => "-1", 'post_type' => 'project', 'post_status' => 'publish');
		
		if(isset($cs_project_cat) && $cs_project_cat <> '' &&  $cs_project_cat <> '0'){
			$cs_project_cat 			= $cs_project_cat;
			$project_category_array 	= array('project-category' => "$cs_project_cat");
			$project_args_all 			= array_merge($project_args_all, $project_category_array);
		}
		
		$query_all = new WP_Query($project_args_all);
		$count_post = $query_all->post_count;
		$args = array(
			'post_type' 		=> 'project',
			'paged'  			=> $_GET['page_id_all'],
			'posts_per_page' 	=> (int)"$cs_project_per_page",
			'order' 			=> "ID",
			'orderby'			=> "DESC",
		);
		if(isset($cs_project_cat) && $cs_project_cat <> '' &&  $cs_project_cat <> '0'){
			$project_category_array = array('project-category' => "$cs_project_cat");
			$args = array_merge($args, $project_category_array);
		}
 		cs_mesonry();
 		$query = new WP_Query( $args );
		if ( $query->have_posts() ) { 	
				
            if ( isset( $cs_project_section_title ) && $cs_project_section_title !='' ) {
				echo '<div class="cs-section-title col-md-12">';
					echo '<h2>'.$cs_project_section_title.'</h2>';
				echo  '</div>';
			}
			if( isset($cs_project_cat) && ($cs_project_cat <> "" && $cs_project_cat <> "0")){	
				$categories = get_categories( array('child_of' => "$cs_project_cat", 'taxonomy' => 'project-category', 'hierarchical' => false) );
			}else{
				$categories = get_categories( array('taxonomy' => 'project-category', 'hierarchical' => false) );
			}
		 ?>

            <script type="text/javascript">
				 jQuery(document).ready(function($) {
					var container = jQuery(".mas-isotope").imagesLoaded(function() {
						container.isotope()
					});
					jQuery(window).resize(function() {
					  setTimeout(function() {
						jQuery(".mas-isotope").isotope()
					  }, 600)
					});
				
				 });
			</script>
        	<div class="cs-portfoliolist cs-top-center mas-isotope">
         	<?php 
				$cs_title_limit = 30;
		 		while ( $query->have_posts() ) : $query->the_post();
					global $post;
					$image_id = get_post_thumbnail_id($post->ID);
					if($image_id <> ''){
						$image_url = cs_attachment_image_src(get_post_thumbnail_id($post->ID), 0, 0);
					}else{
						$image_url 		= get_template_directory_uri().'/assets/images/no-image4x3.jpg';
					}
					
					$post_cats = wp_get_object_terms( $post->ID, 'project-category' );
					$p_cats = '';
					foreach($post_cats as $cats){
						$p_cats .= $cats->term_id.' ';
					}
					?>
                    <article class="col-md-4" data-id="id-<?php echo intval($post->ID); ?>">
                         <figure>
                            <?php if($image_url <> ''){ ?>
                                <a href="<?php esc_url(the_permalink()); ?>"><img src="<?php echo esc_url($image_url); ?>" alt=""></a>
                            <?php } ?>
                            <h3>
	                            <a href="<?php esc_url(the_permalink()); ?>">
    		                        <?php cs_get_title($cs_title_limit); ?>
            	                </a>
                            </h3>
                            <figcaption>
                                <div class="caption-inner cs-large">
                                    <div class="text">
                                        <a href="<?php esc_url(the_permalink()); ?>">
                                            <i class="icon-plus8"></i>
                                        </a>
                                        <a href="<?php esc_url(the_permalink()); ?>"><?php echo esc_attr('View Project','lassic');?></a>
                                        <?php 
											$cs_term_list	= get_the_term_list ( $post->ID, 'project-category', '<span><i class="fa fa-plus"></i>', ', ', '</span>' ); 										if ( isset( $cs_term_list ) && $cs_term_list!='' ) {?>
                                            <ul class="post-options">
                                                <li><?php echo cs_allow_special_char($cs_term_list);?></li>
                                            </ul>
                                        <?php }?>
                                    </div>
                                </div>
                            </figcaption>
                        </figure>
                  </article>
			<?php endwhile;?>
        </div>
        <?php
 		 if( isset( $cs_filterable ) && $cs_filterable == 'no' ) {
			//==Pagination Start
			if ( $cs_project_pagination == "Show Pagination" && $count_post > $cs_project_num_post && $cs_project_num_post > 0 ) {
				$qrystr = '';
				if ( isset($_GET['page_id']) ) $qrystr .= "&amp;page_id=".$_GET['page_id'];
				echo cs_pagination($count_post, $cs_project_num_post, $qrystr, 'Show Pagination');
			}
			//==Pagination End	
		}
 		 wp_reset_postdata(); 
		 $projects_data = ob_get_clean();
         echo  cs_allow_special_char($projects_data);
       	}
	}
}
}
