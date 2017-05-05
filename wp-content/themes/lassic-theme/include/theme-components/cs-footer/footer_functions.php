<?php

/** 
 * @ Default Footer Menu
 *
 *
 */
if ( ! function_exists( 'cs_footer_navigation' ) ) {
function cs_footer_navigation($nav='',$class=''){
	$id = rand(1,99);
	echo '<nav class="navigation'.$class.'">
			<!--<a class="cs-click-menu"><i class="icon-list8"></i></a>-->
			  ';
				cs_navigation('footer-menu','navbar-nav');
			echo '
          </nav>';	
	}
}