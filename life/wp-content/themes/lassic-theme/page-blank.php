<?php
/*
	Template Name: Blank Page
*/

get_template_part('include/theme-components/cs-header/header','blank');
?>
<section class="page-section">
    <!-- Container -->
    <div class="container">
        <!-- Row -->
        <div class="row">
            <!-- Col Md 12 -->
            <div class="col-md-12">
            	 <div class="col-md-12">
				<?php
                    if ( have_posts() ) {
                        while ( have_posts() ) {
                            the_post(); 
                            the_content();
							wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'lassic' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) );
                        } // end while
                    } // end if
                ?>
                </div>
        </div>
       </div>
     </div>
 </section>