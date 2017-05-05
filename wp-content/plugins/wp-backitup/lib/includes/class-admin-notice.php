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

		/**
		 * @var array Holds all our registered notices
		 * 
		 */
		private $notices;


		protected $initial_time_key;   //unique identifier key for an initial timestamp
		public $link_id;  //link unique id
		public $link_label; //Label of the link
		public $is_wpbackitup_page; //Check whether admin is in backitup pages.
		

		public function __construct($args){

			$args 			  = wp_parse_args( $args, $this->default_args() );
			$this->id 		  = $args['id'];
			$this->days 	  = $args['days_after'];
			$this->type       = $args['type'];
			$this->message    = $args['message'];
			$this->link_label = $args['link_label'];
			$this->rating 	  = $args['rating'];
			$this->slug       = $args['slug'];
			$this->cap        = $args['cap'];
			$this->scope      = $args['scope'];

			$this->initial_time_key = 'wp-backitup_notice_' . substr( md5( plugin_basename( __FILE__ ) ), 0, 20 );
			$this->link_id 	  = 'wpbackitup-review-link-' . $this->initial_time_key;

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

			$defaults = array(
				'id'         => 'wpbackitup_initial_id',
				'days_after' => 10,
				'type'       => '',
				'message'    => sprintf( esc_html__( 'Hey! You&#039;ve been using WPBackItUp for a little while now and we really hope it&#039;s helped you make backing up your site simple. You might not realize it, but user reviews are such a great help to us. We would be so grateful if you could take a minute to leave a review on WordPress.org. Many thanks in advance :)', 'wp-backitup' ) ),
				'link_label' => esc_html__( 'Click here to leave your review', 'wp-backitup' ),
				'rating'     => 5,
				'slug'	     => 'wp-backitup',

				// Parameters used in WP Dismissible Notices Handler
				'cap'        => 'administrator',
				'scope'      => 'global',
				'class'		 => '',
			);

			return apply_filters( 'wpbackitup_default_args', $defaults );

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
						/* translators: %s: required wordpress version */
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

			if(!$this->is_time()){
				return;
			}

			//Display messages on plugins AND wp-backitup only
			
			if( $this->is_notice_pages() ){

				foreach ( $this->notices as $id => $notice ) {

					$id = $this->get_id( $id );

					// Check if the notice was dismissed
					if ( $this->is_dismissed( $id ) ) {
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
				if ( false !== strpos($_GET['page'],'wp-backitup')){
					$this->is_wpbackitup_page = true;
				}
			}

			if( $pagenow == 'plugins.php' || $this->is_wpbackitup_page != false ){
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

			return apply_filters( 'wpbackitup_notice_types', $types );

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
						/* translators: %s: required php version */
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
		 * @return boolean
		 */
		public function is_time() {
			$installed = (int) get_option( $this->initial_time_key);
			if ( $installed == false || $installed == null ) {
				$this->setup_date();
				$installed = time();
			}
			if ( $installed + ( $this->days * 86400 ) >= time() ) {
				return false;
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
			$link    = $this->get_review_link_tag();
			$message = $message . ' ' . $link;
			return wp_kses_post( $message );
		}

		 
		/**
		 * Get the complete link tag
		 *
		 * @return string
		 */
		protected function get_review_link_tag() {
			$link = $this->get_review_link();
			return "<a href='$link' target='_blank' id='$this->link_id'>$this->link_label</a>";
		}

		/**
		 * Get the review link
		 *
		 * @return string
		 */
		 
		protected function get_review_link() {
			$link = 'https://wordpress.org/support/plugin/';
			$link .= $this->slug . '/reviews';
			$link = add_query_arg( 'rate', $this->rating, $link );
			$link = esc_url( $link . '#new-post' );
			return $link;
		}


		/**
		 * Notice dismissal triggered by Ajax
		 *
		 * @return void
		 */
		public function dismiss_notice_ajax() {

			if ( ! isset( $_POST['id'] ) ) {
				echo 0;
				exit;
			}

			if ( empty( $_POST['id'] ) || false === strpos( $_POST['id'], 'wpbackitup-' ) ) {
				echo 0;
				exit;
			}

			$id = $this->get_id( str_replace( 'wpbackitup-', '', $_POST['id'] ) );

			echo $this->dismiss_notice( $id );
			exit;

		}

		/**
		 * Dismiss a notice
		 *
		 * @param string $id ID of the notice to dismiss
		 *
		 * @return bool
		 */
		public function dismiss_notice( $id ) {

			$notice = $this->get_notice( $this->get_id( $id ) );

			if ( false === $notice ) {
				return false;
			}

			if ( $this->is_dismissed( $id ) ) {
				return false;
			}

			return 'user' === $notice['scope'] ? $this->dismiss_user( $id ) : $this->dismiss_global( $id );

		}

		/**
		 * Dismiss notice for the current user
		 *
		 * @param string $id Notice ID
		 *
		 * @return int|bool
		 */
		private function dismiss_user( $id ) {

			$dismissed = $this->dismissed_user();

			if ( in_array( $id, $dismissed ) ) {
				return false;
			}

			array_push( $dismissed, $id );

			return update_user_meta( get_current_user_id(), 'wpbackitup_dismissed_notices', $dismissed );

		}

		/**
		 * Dismiss notice globally on the site
		 *
		 * @param string $id Notice ID
		 *
		 * @return bool
		 */
		private function dismiss_global( $id ) {

			$dismissed = $this->dismissed_global();

			if ( in_array( $id, $dismissed ) ) {
				return false;
			}

			array_push( $dismissed, $id );

			return update_option( 'wpbackitup_dismissed_notices', $dismissed );

		}

		/**
		 * Get all dismissed notices
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

			$dismissed = get_user_meta( get_current_user_id(), 'wpbackitup_dismissed_notices', true );

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
			return get_option( 'wpbackitup_dismissed_notices', array() );
		}

		/**
		 * Check if a notice has been dismissed
		 *
		 * @param string $id Notice ID
		 *
		 * @return bool
		 */
		public function is_dismissed( $id ) {

			$dismissed = $this->dismissed_notices();

			if ( ! in_array( $this->get_id( $id ), $dismissed ) ) {
				return false;
			}

			return true;

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
		 * Return a specific notice
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


		//
		public function load_script(){
			?>

			<script>
			jQuery(document).ready(function($) {
			    $( '.wpbackitup-notice.notice.is-dismissible' ).on('click', '.notice-dismiss', function ( event ) {
			        event.preventDefault();
			        var $this = $(this);
			        if( 'undefined' == $this.parent().attr('id') ){
			            return;
			        }
			        $.post( ajaxurl, {
			            action: 'wpbackitup_dismiss_notice',
			            url: ajaxurl,
			            id: $this.parent().attr('id')
			        });

			    });
			});
			</script>

			<?php 
		}

	}

}
