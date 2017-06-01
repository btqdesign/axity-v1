<?php 
/**
 * File Type: Team Templates Class
 */
 

if ( !class_exists('MemberTemplates') ) {
	
	class MemberTemplates
	{
		
		function __construct()
		{
			// Constructor Code here..
		}
	
		//======================================================================
		// Team View
		//======================================================================
		public function cs_member_view($cs_member_excerpt_length = '',$cs_query ='') {
			
			global $post;
			$width  = '0';
			$height = '0';
			$cs_title_limit = 1000;
			echo '<div class="cs-team team-grid">';
			if ( $cs_query->have_posts() ) {
				while ( $cs_query->have_posts() )  : $cs_query->the_post();
				$cs_member = get_post_meta(get_the_ID(), "member", true);
				if ( $cs_member <> "" ) {
					$cs_xmlObject = new SimpleXMLElement($cs_member);
					$cs_team_designation = $cs_xmlObject->cs_team_designation;
					
					
				} else {
					$cs_team_designation = '';
					if(!isset($cs_xmlObject))
						$cs_xmlObject = new stdClass();
				}

				$cs_image_id = get_post_thumbnail_id( $post->ID );
				$cs_image_url = cs_attachment_image_src( $cs_image_id, $width, $height );
				
				if($cs_image_url == ''){
					$cs_image_url = get_template_directory_uri().'/assets/images/no-image.jpg';
				}
				
			?>
            <article class="col-md-3">
                <figure>
                    <a href="<?php the_permalink();?>"><img src="<?php echo esc_url($cs_image_url); ?>" alt="<?php the_title();?>"></a>
                    <figcaption>
                        <div class="cs-social-media">
                            <ul>
                                <?php 
                                foreach ( $cs_xmlObject as $social ){
                                    if ( $social->getName() == "social_media" ) {
                                    $cs_social_title 		= $social->social_title;
                                    $cs_social_icon 		= $social->social_icon;
                                    $cs_url 				= $social->target_url;
                                    ?>
                                        <li><a data-original-title="<?php echo esc_attr( $cs_social_title );?>" href="<?php echo esc_url( $cs_url );?>"><i class="<?php echo esc_attr( $cs_social_icon );?>"></i></a></li>
                                    <?php 
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </figcaption>
                </figure>
                <div class="cs-text">
                    <h2 class="cs-post-title"><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
                    <?php 
                    if ( isset( $cs_team_designation ) && $cs_team_designation !='' ) {
                        echo '<span>'.$cs_team_designation.'</span>';
                    }
                    ?>
                </div>
            </article>
			<?php 
				endwhile;
			}
			echo ' </div>';
		}
		
		// View End
	}
}