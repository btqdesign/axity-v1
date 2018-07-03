<?php  if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * WP BackItUp -  Admin Review Nag Class
 *
 * @since   1.14.2
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

if ( ! class_exists( 'WPBackitup_Admin_Notice' ) ) {
//TODO: Refactor class and file name
	class WPBackitup_Admin_Notice {


		/**
		 * Minimum required version of PHP.
		 *
		 */
		public $php_version_required = '5.2';

		/**
		 * Minimum version of WordPress required to use the library
		 *
		 */
		public $wordpress_version_required = '3.8';

		public $is_wpbackitup_page; //Check whether admin is in backitup pages.

		/**
		 * @var array Holds all our registered notices
		 * 
		 */
		private $notices;

		private $id;
		private $days;				// days after the notice should display
		private $temp_days;			// days after the temporary dismissed notice should display
		private $notice_type;
		private $type;
		private $message;

		private $link_1;
		private $link_2;
		private $link_3;

		private $link_label_1;
		private $link_label_2;
		private $link_label_3;
		private $rating;
		private $slug;
		private $cap;
		private $scope;
		private $initial_time_key;
		private $link_id_1;
		private $link_id_2;
		private $link_id_3;

		
		public function __construct($args){

			$args 			  = wp_parse_args( $args, $this->default_args() );

			$this->id 		  = $args['id'];
			$this->days 	  = $args['days_after'];         //Show the notice after these days
			$this->temp_days  = $args['temp_days_after'];    //Show the notice again after these days after temporary dismiss
			$this->notice_type= $args['notice_type'];       //review,promo
			$this->type       = $args['type'];
			$this->message    = $args['message'];

			$this->link_1 = $args['link_1'];
			$this->link_2 = $args['link_2'];
			$this->link_3 = $args['link_3'];

			$this->link_label_1 = $args['link_label_1'];
			$this->link_label_2 = $args['link_label_2'];
			$this->link_label_3 = $args['link_label_3'];

			$this->rating 	  = $args['rating'];
			$this->slug       = $args['slug'];
			$this->cap        = $args['cap'];
			$this->scope      = $args['scope'];

			$this->initial_time_key = 'wp-backitup_notice_' . substr( md5( plugin_basename( __FILE__ ) ), 0, 20 );
			$this->link_id_1        = 'wp-backitup-notice-link1-' . $this->initial_time_key;
			$this->link_id_2        = 'wp-backitup-notice-link2-' . $this->initial_time_key;
			$this->link_id_3        = 'wp-backitup-notice-link3-' . $this->initial_time_key;

			//Register a new notice on instantiate
			$this->register_notice($args);

			$this->init();
		}



		/**
		 * Get the default arguments for a notice
		 *
		 * @since 1.0
		 * @return array
		 */
		private function default_args() {

			$slug = 'wp-backitup';
			$rating =5;

			$link = 'https://wordpress.org/support/plugin/';
			$link .= $slug . '/reviews/';
			$link = add_query_arg( '?filter', $rating, $link );
			$link = esc_url( $link . '#new-post' );

			$defaults = array(
				'id'                => 'wpbackitup_initial_id',
				'days_after'        => 10,
				'temp_days_after'   => 3,
				'notice_type'       => 'review',
				'type'              => '',
				'message'           => sprintf( "%s<p>%s<p>%s",
										esc_html__( "You've been using WPBackItUp for some time now and we truly hope it's made backing up your WordPress site simple.", "wp-backitup"),
										esc_html__( "You might not realize this, but user reviews are an essential part of the WordPress community.  They provide a tremendous benefit to both plugin developers and the WordPress community but unfortunately less than 1 percent of people take the time to leave reviews.  And with more than 50,000 plugins in the WordPress directory, reviews are the only way great people like you can find high quality, supported plugins.", "wp-backitup" ),
										esc_html__( "We would be extremely grateful if you would take just a few minutes to leave a review on WordPress.org. It really does help the entire community.  Many thanks in advance :)", "wp-backitup" )
									),

				'link_1'            => $link,
				'link_2'            => '#',
				'link_3'            => '#',

				'link_label_1'        => esc_html__( 'Ok, you deserve it', 'wp-backitup' ),
				'link_label_2'        => esc_html__( 'Nope, maybe later', 'wp-backitup' ),
				'link_label_3'        => esc_html__( 'I already did', 'wp-backitup' ),

				'rating'            => $rating,
				'slug'	            => $slug,

				// Parameters used in WP Dismissible Notices Handler
				'cap'               => 'administrator',
				'scope'             => 'global',
				'class'		        => '',
			);

			return apply_filters( 'wp-backitup_default_args', $defaults );
		}


		/**
		 * Initialize the library
		 *
		 * @return void
		 */
		private function init() {

			// Make sure WordPress is compatible
			if ( ! $this->is_wp_compatible() ) {
				$this->spit_error(
					sprintf(
						/* translators: %s: required WordPress version */
						esc_html__( 'WPBackItUp can not be used on your site because it requires WordPress Version %s or later.', 'wp-backitup' ),
						$this->wordpress_version_required
					)
				);

				return;
			}

			// Make sure PHP is compatible
			if ( ! $this->is_php_compatible() ) {
				$this->spit_error(
					sprintf(
						/* translators: %s: required php version */
						esc_html__( 'WPBackItUp can not be used on your site because it requires PHP Version %s or later.', 'wp-backitup' ),
						$this->php_version_required
					)
				);

				return;
			}

			add_action( 'admin_notices', array( $this, 'display' ) );
			add_action( 'admin_footer', array( $this, 'load_script' ) );
			add_action( 'wp_ajax_wpbackitup_dismiss_notice', array( $this, 'dismiss_notice_ajax' ) );

		}

		/**
		 * Check if the current WordPress version fits the requirements
		 *
		 * @return boolean
		 */
		private function is_wp_compatible() {

			if ( version_compare( get_bloginfo( 'version' ), $this->wordpress_version_required, '<' ) ) {
				return false;
			}

			return true;

		}

		/**
		 * Check if the version of PHP is compatible with this class
		 *
		 * @return boolean
		 */
		private function is_php_compatible() {

			if ( version_compare( phpversion(), $this->php_version_required, '<' ) ) {
				return false;
			}

			return true;

		}

		/**
		 * Display all the registered notices
		 *
		 * @return void
		 */
		public function display() {

			if ( is_null( $this->notices ) || empty( $this->notices ) ) {
				return;
			}

			//Display messages on plugins AND wp-backitup only
			
			if( $this->is_notice_pages() ){

				foreach ( $this->notices as $id => $notice ) {

					$id = $this->get_id( $id );

					//Check if the time is right for  new or temporary or permanently dimissed notice
					if(!$this->is_time( $id )){
						continue;
					}

					// Check if the current user has required capability
					if ( ! empty( $notice['cap'] ) && ! current_user_can( $notice['cap'] ) ) {
						continue;
					}

					$class = array(
						'wpbackitup-notice',
						'notice',
						$notice['type'],
						'is-dismissible',
						$notice['class'],
					);

					WPBackItUp_Admin_Bar::set_notices_on();
					printf( '<div id="%3$s" class="%1$s"><p>%2$s</p></div>', trim( implode( ' ', $class ) ), $notice['content'], "wpbackitup-$id" );

				}
			}

		}

		/**
		 * Spits an error message at the top of the admin screen
		 *
		 * @param string $error Error message to spit
		 *
		 * @return void
		 */
		protected function spit_error( $error ) {

			if( $this->is_notice_pages() ){
				printf(
					'<div class="notice notice-error is-dismissible"><p><strong>%1$s</strong> %2$s</pre></p></div>',
					esc_html__( 'WPBackitUp Error:', 'wp-backitup' ),
					wp_kses_post( $error )
				);
			}
		}

		/**
		*  Checks whether this is plugins page or wpbackitup pages 
		* 
		* @return boolean
		*
		*/
		public function is_notice_pages(){
			global $pagenow;

			if(isset($_GET['page'])){
				if ( false !== strpos($_GET['page'],'wp-backitup-backup')){
					$this->is_wpbackitup_page = true;
				}
			}

			//if( $pagenow == 'plugins.php' || $this->is_wpbackitup_page != false ){
			if( $this->is_wpbackitup_page != false ){
				return true;
			}

			return false;
		}

		/**
		 * Sanitize a notice ID and return it
		 *
		 * @param string $id
		 *
		 * @return string
		 */
		public function get_id( $id ) {
			return sanitize_key( $id );
		}

		/**
		 * Get available notice types
		 *
		 * @return array
		 */
		public function get_types() {

			$types = array(
				'error',
				'updated',
			);

			return apply_filters( 'wp-backitup_notice_types', $types );

		}


		/**
		 * Register a new notice
		 *
		 * @param string $id      Notice ID, used to identify it
		 * @param string $type    Type of notice to display
		 * @param string $content Notice content
		 * @param array  $args    Additional parameters
		 *
		 * @return bool
		 */
		public function register_notice( $args = array() ) {

			if ( is_null( $this->notices ) ) {
				$this->notices = array();
			}

			$id      = $this->get_id( $args['id'] );
			$type    = in_array( $t = sanitize_text_field( $args['type'] ), $this->get_types() ) ? $t : 'updated';
			$content = $this->get_message();
			$args    = wp_parse_args( $args, $this->default_args() );

			if ( array_key_exists( $id, $this->notices ) ) {

				$this->spit_error(
					sprintf(
						/* translators: %s: notice id */
						esc_html__( 'A notice with the ID %s has already been registered.', 'wp-backitup' ),
						"<code>$id</code>"
					)
				);

				return false;
			}

			$notice = array(
				'type'    => $type,
				'content' => $content,
			);

			$notice = array_merge( $notice, $args );

			$this->notices[ $id ] = $notice;

			return true;

		}

		/**
		 * Check if it is time to ask for a review
		 *	
		 * @param $id of the Notice ID 
		 *
		 * @return boolean
		 */
		public function is_time( $id ) {
			$installed = (int) get_option( $this->initial_time_key);
			if ( $installed == false || $installed == null ) {
				$this->setup_date();
				$installed = time();
			}

			//Check if the notice is permanently dismissed already
			if( $this->is_permanent_dismissed( $id ) ){
				return false;
			}

			//Check if the notice is temporarily dismissed
			if( $this->is_temp_dismissed( $id )){
				$notice = $this->get_dismissed_notice( $id );
				if( $notice['updated_at'] + ( $this->temp_days * 86400 ) >= time() ){
					return false;
				}

			}

			//installed datetime + N days >= now
			if ( $installed + ( $this->days * 86400 ) >= time() ) {
				return false;//dont display
			}

			return true;
		}


		/**
		 * Save the current date as the installation date
		 *
		 */
		
		protected function setup_date() {
			update_option( $this->initial_time_key, time() );
		}

 
		/**
		 * Get the review prompt message
		 *
		 * @return string
		 */
		protected function get_message() {
			$message = $this->message;
			$link    = $this->get_link_tag();
			$message = $message . ' ' . $link;
			return wp_kses_post( $message );
		}

		 
		/**
		 * Get the complete link tag
		 *
		 * @return string
		 */
		protected function get_link_tag() {

			$review_link_tag_1 = "<div><a href='$this->link_1' target='_blank' id='$this->link_id_1'>$this->link_label_1</a></div>";
			$review_link_tag_2 = "<div><a href='$this->link_2' id='wpb-temporary-hide'>$this->link_label_2</a></div>";
			$review_link_tag_3 = "<div><a href='$this->link_3' id='wpb-permanent-hide'>$this->link_label_3</a></div>";

			//$later_review_link_tag = "<a href='#' id='wpb-temporary-hide'>" . esc_html__('Nope, maybe later','wp-backitup'). "</a>";
			//$completed_review_link_tag = "<p><a href='#' id='wpb-permanent-hide'>" . esc_html__('I already did','wp-backitup') . "</a></p>";

			//need to check for values here

			$review_links = '<p>' .$review_link_tag_1 . ' ' . $review_link_tag_2 . ' ' . $review_link_tag_3 . '</p>';

			return $review_links;
		}

		/**
		 * Get the review link
		 *
		 * @return string
		 */
		 
//		protected function get_review_link_1() {
//			$link = 'https://wordpress.org/support/plugin/';
//			//$link .= $this->slug . '/reviews/';
//
//
//			$link .= $this->slug . '/reviews/';
//			$link = add_query_arg( '?filter', 5, $link );
//			$link = esc_url( $link . '#new-post' );
//			return $link;
//		}

		/**
		 * Notice dismissal triggered by Ajax
		 *
		 * @return void
		 */
		public function dismiss_notice_ajax() {

			//anytime dismiss is fired just set to 0 - IF any are visible it will get reset to 1
			WPBackItUp_Admin_Bar::set_notices_off();

			if ( ! isset( $_POST['id'] ) || !isset( $_POST['temp_dismiss'] ) || !isset( $_POST['updated_at'])) {
				echo 0;
				exit;
			}

			if ( empty( $_POST['id'] ) || false === strpos( $_POST['id'], 'wpbackitup-' ) ) {
				echo 0;
				exit;
			}

			$id = $this->get_id( str_replace( 'wpbackitup-', '', $_POST['id'] ) );
			$temp_dismiss = $_POST['temp_dismiss'];
			$updated_at = $_POST['updated_at'];

			echo $this->dismiss_notice( $id , $temp_dismiss , $updated_at );
			exit;

		}



		/**
		 * Dismiss a notice
		 *
		 * @param string $id ID of the notice to dismiss
		 *
		 * @return bool
		 */
		public function dismiss_notice( $id , $temp_dismiss , $updated_at ) {

			$notice = $this->get_notice( $this->get_id( $id ) );

			if ( false === $notice ) {
				return false;
			}

			if ( $this->is_permanent_dismissed( $id )) {
				return false;
			}

			return 'user' === $notice['scope'] ? $this->dismiss_user( $id , $temp_dismiss , $updated_at ) : $this->dismiss_global( $id , $temp_dismiss , $updated_at );

		}


		/**
		 * Dismiss notice for the current user
		 *
		 * @param string $id Notice ID
		 *
		 * @return int|bool
		 */
		private function dismiss_user( $id , $temp_dismiss , $updated_at ) {

			$dismissed = $this->dismissed_user();

			if ( in_array( $id, $dismissed ) ) {
				return false;
			}

			$dismiss_param = array(
				'id' => $id,
				'temp_dismiss' => $temp_dismiss,
				'updated_at' => $updated_at
				);
			array_push( $dismissed, $dismiss_param );

			return update_user_meta( get_current_user_id(), 'wp-backitup_dismissed_notices', $dismissed );

		}


		/**
		 * Dismiss notice globally on the site
		 *
		 * @param string $id Notice ID
		 *
		 * @return bool
		 */
		private function dismiss_global( $id , $temp_dismiss , $updated_at) {

			$dismissed = $this->dismissed_global();

			if ( in_array( $id, $dismissed ) ) {
				return false;
			}

			$dismiss_param = array(
				'id' => $id,
				'temp_dismiss' => $temp_dismiss,
				'updated_at' => $updated_at
				);
			array_push( $dismissed, $dismiss_param );

			return update_option( 'wp-backitup_dismissed_notices', $dismissed );

		}


		/**
		 * Get all permanent dismissed notices
		 *
		 * This includes notices dismissed globally or per user.
		 *
		 * @return array
		 */
		public function dismissed_notices() {

			$user   = $this->dismissed_user();
			$global = $this->dismissed_global();

			return array_merge( $user, $global );

		}


		/**
		 * Get user dismissed notices
		 *
		 * @return array
		 */
		private function dismissed_user() {

			$dismissed = get_user_meta( get_current_user_id(), 'wp-backitup_dismissed_notices', true );

			if ( '' === $dismissed ) {
				$dismissed = array();
			}

			return $dismissed;

		}


		/**
		 * Get globally dismissed notices
		 *
		 * @return array
		 */
		private function dismissed_global() {
			return get_option( 'wp-backitup_dismissed_notices', array() );
		}

		/**
		 * Check if a notice has been dismissed
		 *
		 * @param string $id Notice ID
		 *
		 * @return bool
		 */
		public function is_permanent_dismissed( $id ) {

			$dismissed = $this->dismissed_notices();

			foreach( $dismissed as $array ){

				if( $array['id'] == $id && $array['temp_dismiss'] == 0 ){
 					return true;
				}else{
					continue;
				}
			}

			return false;

		}

		/**
		* Check if a notice has been temporarily dismissed.
		* 
		* @param string $id of the Notice ID
		*
		* @return bool
		**/
		public function is_temp_dismissed( $id ){
			$dismissed = $this->dismissed_notices();

			foreach( $dismissed as $array ){

				if( $array['id'] == $id && $array['temp_dismiss'] == 1 ){
 					return true;
				}else{
					continue;
				}
			}

			return false;
		}


		/**
		 * Get all the registered notices
		 *
		 * @return array|null
		 */
		public function get_notices() {
			return $this->notices;
		}

		/**
		 * Return a specific notice from notice instance
		 *
		 * @param string $id Notice ID
		 *
		 * @return array|false
		 */
		public function get_notice( $id ) {

			$id = $this->get_id( $id );

			if ( ! is_array( $this->notices ) || ! array_key_exists( $id, $this->notices ) ) {
				return false;
			}

			return $this->notices[ $id ];

		}

		/**
		* Return a specific dismissed notice instant from database
		* 
		* @param string $id Notice ID
		* @return array|false
		*/
		public function get_dismissed_notice( $id ){
			$notices = $this->dismissed_notices();
			if(!empty($notices)){
				foreach ( array_reverse($notices) as $notice ) {
					if($notice['id'] == $id ){
						return $notice;
					}
				}
			}
		}


		//
		public function load_script(){
			?>

			<script>
			jQuery(document).ready(function($) {
				var now = function time(){
			            	return Math.floor(new Date().getTime() / 1000);
			            };

				//Temporary hide on clicking the cross icon
			    $( '.wpbackitup-notice.notice.is-dismissible' ).on('click', '.notice-dismiss', function ( event ) {
			        event.preventDefault();
			        var $this = $(this);
			        if( 'undefined' == $this.parent().attr('id') ){
			            return;
			        }
			        $.post( ajaxurl, {
			            action: 'wpbackitup_dismiss_notice',
			            url: ajaxurl,
			            id: $this.parent().attr('id'),
			            temp_dismiss: 1,
			            updated_at: now
			        });
			        $('.wpbackitup-notice').remove();

			    });

			    //Permanent hide on clicking 'I already did'
			    $( '.wpbackitup-notice.notice.is-dismissible' ).on('click', '#wpb-permanent-hide', function ( event ) {
			        event.preventDefault();
			        var $this = $(this);
			        if( 'undefined' == $this.parent().attr('id') ){
			            return;
			        }
			        $.post( ajaxurl, {
			            action: 'wpbackitup_dismiss_notice',
			            url: ajaxurl,
			            id: $this.parent().parent().attr('id'),
			            temp_dismiss: 0,
			            updated_at: now
			        });
			        $('.wpbackitup-notice').remove();

			    });

			    //Temporary hide ajax
			    $( '.wpbackitup-notice.notice.is-dismissible' ).on('click', '#wpb-temporary-hide', function ( event ) {
			        event.preventDefault();
			        var $this = $(this);
			        if( 'undefined' == $this.parent().attr('id') ){
			            return;
			        }
			        $.post( ajaxurl, {
			            action: 'wpbackitup_dismiss_notice',
			            url: ajaxurl,
			            id: $this.parent().parent().attr('id'),
			            temp_dismiss: 1,
			            updated_at: now
			        });

			        $('.wpbackitup-notice').remove();

			    });
			});
			</script>

			<?php 
		}

	}

}

