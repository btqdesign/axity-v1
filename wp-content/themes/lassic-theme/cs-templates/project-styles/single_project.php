<?php
    get_header();
	global $post, $cs_xmlObject;
?>
<div class="page-section" style="padding: 0;">
	<!-- Container -->
    <?php
	$cs_single_template = new cs_single_templates();
	if ( have_posts() ) while ( have_posts() ) : the_post();
		$cs_project = get_post_meta($post->ID, "csprojects", true);
		if ( $cs_project <> "" ) {
			$cs_xmlObject = new SimpleXMLElement($cs_project);
			$cs_detail_view =$cs_xmlObject->project_detail_view;
			$cs_related_post = $cs_xmlObject->cs_related_post;
			$cs_post_tags_show = $cs_xmlObject->post_tags_show;
			$cs_share_post = $cs_xmlObject->post_social_sharing;
			$post_pagination_show = $cs_xmlObject->post_pagination_show;
			$cs_thumb_view = $cs_xmlObject->project_thumbnail_view;
						
		} else {
			$cs_detail_view = 'style_1';
		}
		
		$cs_wide_area	=  isset( $cs_detail_view ) && ( $cs_detail_view == 'style_4' || $cs_detail_view == 'style_5' )  ?  'wide' : '';
	?>
	<div class="container <?php echo esc_attr( $cs_wide_area );?>">
		<!-- Row -->
		<div class="row">
 		<?php 
				if($cs_detail_view == 'style_1'){
					$cs_single_template->cs_project_view1($cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view);
				}elseif($cs_detail_view == 'style_2'){
					$cs_single_template->cs_project_view2($cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view);
				}elseif($cs_detail_view == 'style_3'){
					$cs_single_template->cs_project_view3($cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view);
				}elseif($cs_detail_view == 'style_4'){
					$cs_single_template->cs_project_view4($cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view);
				}elseif($cs_detail_view == 'style_5'){
					$cs_single_template->cs_project_view5($cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view);
				}else{
					$cs_single_template->cs_project_view1($cs_post_tags_show,$cs_share_post,$post_pagination_show,$cs_thumb_view);
				}
		?>
		</div>
	</div>
    <?php endwhile;?>
</div>
 <?php get_footer(); ?>