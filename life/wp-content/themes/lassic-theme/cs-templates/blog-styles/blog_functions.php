<?php
/**
 * File Type: Blog Shortcode
 */
	

//======================================================================
// Adding Blog Posts Start
//======================================================================
if (!function_exists('cs_blog_shortcode')) {
	function cs_blog_shortcode( $atts ) {
		global $post,$wpdb,$cs_theme_options,$cs_counter_node,$cs_xmlObject,$column_attributes;
		$defaults = array('cs_blog_section_title'=>'','cs_blog_view'=>'','cs_blog_cat'=>'','cs_blog_orderby'=>'DESC','orderby'=>'ID','cs_blog_description'=>'yes','cs_blog_excerpt'=>'255','cs_blog_num_post'=>'10','blog_pagination'=>'','cs_blog_class' => '','cs_blog_animation' => '');
		extract( shortcode_atts( $defaults, $atts ) );
		
		// Check Section or page layout
		$cs_sidebarLayout = '';
		$section_cs_layout = '';
		$pageSidebar = false;
		$blog_col_class = 'col-md-4';
		
		if(isset($cs_xmlObject->sidebar_layout)) $cs_sidebarLayout = $cs_xmlObject->sidebar_layout->cs_page_layout;
		if(isset($column_attributes->cs_layout)){
			$section_cs_layout = $column_attributes->cs_layout;
			if ( $section_cs_layout == 'left' || $section_cs_layout == 'right' ) {
				$pageSidebar = true;
			}
		}
		if ( $cs_sidebarLayout == 'left' || $cs_sidebarLayout == 'right') {
			$pageSidebar = true;
		}
		if($pageSidebar == true) {
			$blog_col_class = 'col-md-6';
		}
		// Check Section or page layout ends
		$CustomId	= '';
		if ( isset( $cs_blog_class ) && $cs_blog_class ) {
			$CustomId	= 'id="'.$cs_blog_class.'"';
		}
		if ( trim($cs_blog_animation) !='' ) {
			$cs_custom_animation	= 'wow'.' '.$cs_blog_animation;
		} else {
			$cs_custom_animation	= '';
		}
		$owlcount = rand(40, 9999999);
		$cs_counter_node++;
		ob_start();
		if (isset($cs_xmlObject->sidebar_layout) && $cs_xmlObject->sidebar_layout->cs_page_layout <> '' and $cs_xmlObject->sidebar_layout->cs_page_layout <> "none"){				
				$cs_blog_layout = 'col-md-6';
		}else{
				$cs_blog_layout = 'col-md-4';	
		}
		//==Filters
		$filter_category = '';
		$filter_tag = '';
		$author_filter = '';
		if ( isset($_GET['filter_category']) && $_GET['filter_category'] <> '' && $_GET['filter_category'] <> '0' ) { 
			$filter_category = $_GET['filter_category'];
		}
		//==Sorting
		if(isset($_GET['sort']) and $_GET['sort']=='asc'){
			$cs_blog_orderby	= 'ASC';
		} else{
			$cs_blog_orderby	= $cs_blog_orderby;
		}
		if(isset($_GET['sort']) and $_GET['sort']=='alphabetical'){
			$orderby				= 'title';
			$cs_blog_orderby	    = 'ASC';
		} else{
			$orderby	= 'meta_value';
		}
		//==Sorting End 
		
		if (empty($_GET['page_id_all'])) $_GET['page_id_all'] = 1;

		$cs_blog_num_post	= $cs_blog_num_post ? $cs_blog_num_post : '-1';
		
		$cs_args = array('posts_per_page' => "-1", 'post_type' => 'post', 'order' => $cs_blog_orderby, 'orderby' => $orderby, 'post_status' => 'publish', 'ignore_sticky_posts' => 1);
		
		if(isset($cs_blog_cat) && $cs_blog_cat <> '' &&  $cs_blog_cat <> '0'){
			$blog_category_array = array('category_name' => "$cs_blog_cat");
			$cs_args = array_merge($cs_args, $blog_category_array);
		}
		
		if(isset($filter_category) && $filter_category <> '' && $filter_category <> '0'){
				
				if ( isset($_GET['filter-tag']) ) {$filter_tag = $_GET['filter-tag'];}
				if($filter_tag <> ''){
					$blog_category_array = array('category_name' => "$filter_category",'tag' => "$filter_tag");
				}else{
					$blog_category_array = array('category_name' => "$filter_category");
				}
				$cs_args = array_merge($cs_args, $blog_category_array);
			}
			
		if ( isset($_GET['filter-tag']) && $_GET['filter-tag'] <> '' && $_GET['filter-tag'] <> '0' ) {
			$filter_tag = $_GET['filter-tag'];
			if($filter_tag <> ''){
				$course_category_array = array('category_name' => "$filter_category",'tag' => "$filter_tag");
				$args = array_merge($args, $course_category_array);
			}
		}
		if ( isset($_GET['by_author']) && $_GET['by_author'] <> '' && $_GET['by_author'] <> '0' ) {
			$author_filter = $_GET['by_author'];
			if($author_filter <> ''){
				$authorArray = array('author' => "$author_filter");
				$cs_args = array_merge($cs_args, $authorArray);
			}
		}
		

		$query = new WP_Query( $cs_args );
		$count_post = $query->post_count;
		
		$cs_blog_num_post	= $cs_blog_num_post ? $cs_blog_num_post : '-1';
		$cs_args = array('posts_per_page' => "$cs_blog_num_post", 'post_type' => 'post', 'paged' => $_GET['page_id_all'], 'order' => $cs_blog_orderby, 'orderby' => $orderby, 'post_status' => 'publish', 'ignore_sticky_posts' => 1);
		
		if(isset($cs_blog_cat) && $cs_blog_cat <> '' &&  $cs_blog_cat <> '0'){
			$blog_category_array = array('category_name' => "$cs_blog_cat");
			$cs_args = array_merge($cs_args, $blog_category_array);
		}
		
		if(isset($filter_category) && $filter_category <> '' && $filter_category <> '0'){
				
				if ( isset($_GET['filter-tag']) ) {$filter_tag = $_GET['filter-tag'];}
				if($filter_tag <> ''){
					$blog_category_array = array('category_name' => "$filter_category",'tag' => "$filter_tag");
				}else{
					$blog_category_array = array('category_name' => "$filter_category");
				}
				$cs_args = array_merge($cs_args, $blog_category_array);
			}
			
		if ( isset($_GET['filter-tag']) && $_GET['filter-tag'] <> '' && $_GET['filter-tag'] <> '0' ) {
			$filter_tag = $_GET['filter-tag'];
			if($filter_tag <> ''){
				$course_category_array = array('category_name' => "$filter_category",'tag' => "$filter_tag");
				$cs_args = array_merge($cs_args, $course_category_array);
			}
		}
		if ( isset($_GET['by_author']) && $_GET['by_author'] <> '' && $_GET['by_author'] <> '0' ) {
			$author_filter = $_GET['by_author'];
			if($author_filter <> ''){
				$authorArray = array('author' => "$author_filter");
				$cs_args = array_merge($cs_args, $authorArray);
			}
		}
		
		if ( $cs_blog_cat !='' && $cs_blog_cat !='0'){ 
			$row_cat = $wpdb->get_row($wpdb->prepare("SELECT * from $wpdb->terms WHERE slug = %s", $cs_blog_cat ));
		}
		
		$outerDivStart	= '';
		$outerDivEnd	= '';
		$section_title  = '';
		
		if(isset($cs_blog_section_title) && trim($cs_blog_section_title) <> ''){
			$section_title = '<div class="main-title col-md-12"><div class="cs-section-title"><h2>'.$cs_blog_section_title.'</h2></div></div>';
		}
		
		$randomId = cs_generate_random_string('10');
		$outerDivStart	= '<div class="cs-blog cs-'.$cs_blog_view.'">';
		$outerDivEnd	= '</div>';
 		$cs_blogObject	= new cs_blog_templates();
		$query = new WP_Query( $cs_args );
		$post_count = $query->post_count;
		echo cs_allow_special_char( $section_title );
		echo cs_allow_special_char($outerDivStart);
		if ( $cs_blog_view == 'blog-large' ) {
			$cs_blogObject->cs_large_view( $cs_blog_description , $cs_blog_excerpt,$cs_blog_cat,$query);
		} else if ( $cs_blog_view == 'blog-medium' ) {
			$cs_blogObject->cs_medium_view( $cs_blog_description , $cs_blog_excerpt,$cs_blog_cat,$query);
		} else if ( $cs_blog_view == 'blog-grid' ) {
			$cs_blogObject->cs_grid_view( $cs_blog_description , $cs_blog_excerpt, $cs_blog_cat, $cs_blog_layout, $query, $blog_col_class );
		}else if ( $cs_blog_view == 'blog-crousel' ) {
			$cs_blogObject->cs_crousel_view( $cs_blog_description , $cs_blog_excerpt, $cs_blog_cat, $cs_blog_layout, $query, $blog_col_class );
		} else {
			$cs_blogObject->cs_grid_view( $cs_blog_description , $cs_blog_excerpt, $cs_blog_cat, $query );
		}
		echo cs_allow_special_char( $outerDivEnd );
		//==Pagination Start
 		 if ( $blog_pagination == "Show Pagination" && $count_post > $cs_blog_num_post && $cs_blog_num_post > 0 ) {
			$qrystr = '';
			 if ( isset($_GET['page_id']) ) $qrystr .= "&amp;page_id=".$_GET['page_id'];
			 if ( isset($_GET['by_author']) ) $qrystr .= "&amp;by_author=".$_GET['by_author'];
			 if ( isset($_GET['sort']) ) $qrystr .= "&amp;sort=".$_GET['sort'];
			 if ( isset($_GET['filter_category']) ) $qrystr .= "&amp;filter_category=".$_GET['filter_category'];
			 if ( isset($_GET['filter-tag']) ) $qrystr .= "&amp;filter-tag=".$_GET['filter-tag'];
				 
			echo cs_pagination($count_post, $cs_blog_num_post,$qrystr,'Show Pagination');
		 }
		//==Pagination End	
 	    wp_reset_postdata();	
            $post_data = ob_get_clean();
            return $post_data;
         }
	add_shortcode( 'cs_blog', 'cs_blog_shortcode' );
}
//=================================================
// @ show post title
//=================================================
if (!function_exists('cs_get_title')) {
	function cs_get_title($cs_title_limit = '') {
		global $post;
		$cs_title = substr(get_the_title(),0, $cs_title_limit);
		$cs_link = ''; 
		if(strlen(get_the_title())>$cs_title_limit){ $cs_title.='...';}
		echo cs_allow_special_char($cs_title);
	}
}
//=================================================
// @ show post title
//=================================================
if (!function_exists('cs_post_image')) {
	function cs_post_image($cs_thumbnail = '') {
		global $post;
		echo '<div class="cs-media">
			<figure>
				<a href="'.esc_url(get_permalink()).'"><img alt="blog-grid" src="'.esc_url( $cs_thumbnail ).'"></a>
				<figcaption>
					<a href="'.esc_url(get_permalink()).'"><span><img alt="plus-icon" src="'.get_template_directory_uri().'/assets/images/plus.png"></span></a>
				</figcaption>
			</figure>
		</div>';
	}
}