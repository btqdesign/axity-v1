<?php
/**

 * @Import XML File For Theme Demo



 */

if ( ! function_exists( 'cs_demo_importer' ) ) {

	function cs_demo_importer(){
	global $page_option,$page;
	$import = get_option('cs_import_demo');
  	if(isset($_REQUEST['demo']) && $_REQUEST['demo']=='demo-data'){
		if(isset($_POST['demo-theme-data']) and $page_option['theme_options'][$_POST['demo-theme-data']]['page_slug']<>''){
				$page = $page_option['theme_options'][$_POST['demo-theme-data']]['page_slug'];
				$theme_option = $page_option['theme_options'][$_POST['demo-theme-data']]['theme_option'];
		}else{
				$page = get_page_by_path('home');
				$theme_option = $page_option['theme_options']['home1']['theme_option'];	
		}
 		require_once ABSPATH . 'wp-admin/includes/import.php';
 		if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);
			$cs_demoimport_error = false;

 			if ( !class_exists( 'WP_Importer' ) ) {

				$cs_import_class = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

				if ( file_exists( $cs_import_class ) ){

					require_once $cs_import_class;

				}

				else{

					$cs_demoimport_error = true;

				}

			}
  
		if ( !class_exists( 'WP_Import' ) ) {

			$cs_import_class = get_template_directory() . '/include/theme-components/cs-importer/wordpress-importer.php';

			if ( file_exists( $cs_import_class ) )

				require_once $cs_import_class;

			else

				$cs_demoimport_error = true;

		}
 		if($cs_demoimport_error){

 			echo __( 'Error.', 'lassic' ) . '</p>';

			die();

		}else{

 			if(!is_file( get_template_directory() . '/include/theme-components/cs-importer/demo.xml')){

				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'lassic' ) . '</strong><br />';

				echo __( 'The file does not exist, please try again.', 'lassic' ) . '</p>';

			}
 			else{

				if(!get_option('cs_import_demo')){

					update_option('cs_import_demo','success');

					global $wpdb,$page;
					
					/*
					code remove
					*/
					$theme_mod_val = array();

					$term_exists = term_exists('main-menu', 'nav_menu');
					if ( !$term_exists ) {
						$wpdb->insert(
							$wpdb->terms, 
							array(
							  'name'          => 'Main Menu',
							  'slug'       => 'main-menu',
							  'term_group' => 0
							),
							array(
							  '%s',
							  '%s',
							  '%d'
							) 
						  ); 
						$insert_id = $wpdb->insert_id;
						$theme_mod_val['main-menu'] = $insert_id;
						$wpdb->insert(
							$wpdb->term_taxonomy, 
							array(
							  'term_id'          => $insert_id,
							  'taxonomy'       => 'nav_menu',
							  'description' => '',
							  'parent' => 0,
							  'count' => 0
							),
							array(
							  '%d',
							  '%s',
							  '%s',
							  '%d',
							  '%d'
							) 
						  ); 

					}

					else $theme_mod_val['main-menu'] = $term_exists['term_id'];

					set_theme_mod( 'nav_menu_locations', $theme_mod_val );

					$cs_demo_import = new WP_Import();

					$cs_demo_import->fetch_attachments = true;

					$cs_demo_import->import(get_template_directory() . '/include/theme-components/cs-importer/demo.xml');
					
					$page = get_page_by_path($page);

  					cs_update_themeoptions($page,$_POST,$theme_option);

  		 }else{
				$page = get_page_by_path($page);
			 	cs_update_themeoptions($page,$_POST,$theme_option);

			}

		}

	}

  }

?>

   <div class="cs-demo-data">

        <h2>Import Demo Data</h2>

        

        <div class="inn-text">

            <p>Importing demo data helps to build site like the demo site by all means. It is the quickest way to setup theme. Following things happen when dummy data is imported;</p>

            <ul class="import-data">

                <li>&#8226; All wordpress settings will remain same and intact.</li>

                <li>&#8226; Posts, pages and dummy images shown in demo will be imported.</li>

                <li>&#8226; Only dummy images will be imported as all demo images have copy right restriction.</li>

                <li>&#8226; No existing posts, pages, categories, custom post types or any other data will be deleted or modified.</li>

                <li>&#8226; To proceed, please click "Import Demo Data" and wait for a while.</li>

            </ul>

        </div>

        <form method="post">

        	<ul class="form-elements noborder" id="other_sliders" style=" padding: 20px 0px 0px 0px; ">

            	<li class="to-field importdeta">

                <div class="meta-input pattern">

                <?php

 					$demo_data = get_option('demo-theme-data');

					foreach($page_option['theme_options']['select'] as $key=>$value){

						$checked=($demo_data==$key)?'checked':'';

						$custom_class=($demo_data==$key)?'check-list':'';

							

						echo '<div class="radio-image-wrapper">

								  <input name="demo-theme-data" class="radio" type="radio" 

								  onclick=select_bg("demo-theme-data","","","") value="'.$key.'" '.$checked.'

								  />

								  <label for="radio_option_1"> 
									  <span class="ss"><img src="'.get_template_directory_uri().'/include/assets/images/import/'.$page_option['theme_options'][$key]['thumb'].'.png" alt=""/></span> 
									  <span class="'.sanitize_html_class($custom_class).'" id="check-list">&nbsp;</span>
								  </label>
								  <span class="title-theme">'.$value.'</span>			
							</div>';
					}

				 ?>
              </div>
           		</li>
          	</ul>
        	<input name="reset"  type="submit" value="Import Demo Data" id="submit_btn" class="import-btn" />
            <input type="hidden" name="demo" value="demo-data" />
        </form>
	</div>
  <?php
	}
}

/**

 * @set home page with theme options
 */
function cs_update_themeoptions($page ='',$formdata = '',$theme_option = ''){
	if(isset($page->ID)){
		if(isset($formdata['demo-theme-data'])){
			update_option('demo-theme-data',$formdata['demo-theme-data']);
		}
		update_option( 'page_on_front',$page->ID );
		update_option( 'show_on_front', 'page' );
		update_option( 'front_page_settings', '1' );
		$cs_theme_skin = unserialize(base64_decode($theme_option));
	  	update_option( "cs_theme_options", $cs_theme_skin );
	}else{
		echo '<div class="updated"> <h4>Page not exist</h4></div>';	
	}
}