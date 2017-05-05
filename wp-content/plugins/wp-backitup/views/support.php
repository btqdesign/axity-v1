<?php if (!defined ('ABSPATH')) die('No direct access allowed');

if( !class_exists( 'WPBackitup_Download_Logs' ) ) {
    require_once( WPBACKITUP__PLUGIN_PATH .'/lib/includes/class-download-log.php' );
}

/**
 * WP BackItUp  - Support View
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

/**
 * WPBackItUp_Support Class
 *
 * A general class for Support page.
 *
 * @since 1.13.1
 */
class WPBackItUp_Support {

	/**
	 * Get things started
	 *
	 * @since 1.13.1
	 */
	public function __construct() {

		$this->render_header();

		$selected = isset( $_GET['tab'] ) ? $_GET['tab'] : 'support';
		if(isset($_GET['delete_log']) && !empty($_GET['delete_log'])){
			$this->delete_action();
		}
		if ($selected == 'support') $this->support_screen();
		if ($selected == 'send-logs') $this->send_logs_screen();
		if ($selected == 'download-logs') $this->download_logs_screen();
		if ($selected == 'advanced') $this->advanced_screen();

		$this->render_footer();

	}

	public function admin_notices() {

		?>
		<div class="updated">
			<p><?php esc_html_e( 'YOUR MESSAGE', 'text-domain' ); ?></p>
		</div>
		<?php
	}


	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since  1.9
	 * @return void
	 */
	private function tabs() {
		$selected = isset( $_GET['tab'] ) ? $_GET['tab'] : 'support';

		?>
		<h1 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'support' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wp-backitup-support' ), 'admin.php' ) ) ); ?>">
				<?php _e( 'Support', 'wp-backitup' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'send-logs' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array('page' => 'wp-backitup-support','tab'  => 'send-logs'), 'admin.php' ) ) ); ?>">
				<?php _e( "Send Logs", 'wp-backitup' ); ?>
			</a>

			<a class="nav-tab <?php echo $selected == 'download-logs' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array('page' => 'wp-backitup-support','tab'  => 'download-logs'), 'admin.php' ) ) ); ?>">
				<?php _e( "Download Logs", 'wp-backitup' ); ?>
			</a>

			<?php //hide tab unless navigate directly
			if ( $selected == 'advanced') : ?>
				<a class="nav-tab <?php echo $selected == 'advanced' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array('page' => 'wp-backitup-support','tab'  => 'advanced'), 'admin.php' ) ) ); ?>">
					<?php _e( "Advanced", 'wp-backitup' ); ?>
				</a>
			<?php endif; ?>


		</h1>
		<?php
	}

	/**
	 * Welcome message
	 *
	 * @access public
	 * @since 2.5
	 * @return void
	 */
	private function welcome_message() {

		?>

		<div id="wpbackitup-header">
			<img class="wpbackitup-badge" src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/wpbackitup-logo.png'; ?>" alt="<?php _e( 'WPBackItUp', 'wp-backitup' ); ?>" / >
			<h1><?php  _e( 'Support Center', 'wp-backitup' ); ?></h1>
			<p class="about-text">
				<?php printf( __("Welcome to the WPBackItUp support center. If you have any questions or run into any trouble with WPBackItUp then you've come to the right place.", 'wp-backitup' ) ); ?>
			</p>
		</div>
		<?php
	}


	/**
	 * Render Support Screen
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	private function support_screen() {
	?>

		<p class="about-description"><?php _e( 'To streamline support requests and better serve you, we utilize a support ticket system. Every support request is assigned a unique ticket number which you can use to track progress and responses via our support portal. For your convenience we provide a complete archive and history of all your support requests. All correspondence is via email so a valid email address is required to submit a ticket. ', 'wp-backitup' ); ?></p>
		<div class="changelog">
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/support_portal.png'?>" />
					</div>

					<div class="feature-section-content">
						<h4><a href="http://support.wpbackitup.com/support/home" target="_blank"><?php printf( __( 'Search &rarr; Knowledge base', 'wp-backitup' )); ?></a></h4>
						<p><?php printf( __( "Want to search our entire documentation library, all our how to articles and even our faq's in one shot?  Just type your question into the knowledge base search bar and we'll show you everything we have for that topic.", 'wp-backitup' )); ?></p>

						<h4><a href="http://support.wpbackitup.com/support/tickets/new" target="_blank"><?php printf( __( 'New &rarr; Ticket', 'wp-backitup' )); ?></a></h4>
						<p><?php printf( __( 'Need to open a new support ticket? Just click the link above.  Please provide as much detail as possible so we can best assist you.', 'wp-backitup' )); ?></p>

						<h4><a href="http://support.wpbackitup.com/support/tickets" target="_blank"><?php printf( __( 'Check &rarr; Status', 'wp-backitup' )); ?></a></h4>
						<p><?php _e( 'To check ticket status or update a previously submitted ticket you will first need to login. Our support portal provides a history of your current and past support requests complete with responses.', 'wp-backitup' );?></p>

						<h4><a href="<?php echo esc_url( admin_url( add_query_arg( array('page' => 'wp-backitup-support','tab'  => 'send-logs'), 'admin.php' ))) ?>"><?php printf( __( 'Send &rarr; Logs', 'wp-backitup' )); ?></a></h4>
						<p><?php _e( 'Sometimes it may be necessary for you to send your log files to support. If that ever happens, just click the link above and we will show you what to do.', 'wp-backitup' );?></p>

					</div>
				</div>
		</div>

		<?php
	}

	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	private function send_logs_screen() {
	global $WPBackitup;
	?>
		<?php

			$namespace = $WPBackitup->namespace;

			$support_email =$WPBackitup->support_email();
			if (empty($support_email)){
				$wpbackitup_license = new WPBackItUp_License();
				$support_email =$wpbackitup_license->get_customer_email();
			}

			//Force registration for support
			$disabled='';

		?>

		<p class="about-description"><?php _e( 'If you have been asked by support to send your log files then you are in the right spot.', 'wp-backitup' ); ?></p>

		<div class="changelog">

			<div class="feature-section">
				<div class="feature-section-content">
						<!-- Display Settings widget -->

							<div class="widget">
								<form action="<?php echo get_admin_url(),"admin-post.php"; ?>" method="post" id="<?php echo $namespace; ?>-support-form">
									<?php wp_nonce_field($namespace . "-support-form"); ?>

									<h3 class="promo"><i class="fa fa-envelope"></i> <?php _e('Send Logs to Support', 'wp-backitup') ?></h3>
									<p><b><?php _e('This form should only be used when working with support.', 'wp-backitup') ?></b></p>
									<p><?php printf(__('Please make sure to open a support ticket via WPBackItUp <a href="%s" target="_blank"> support portal.</a> before using this form.', 'wp-backitup'), esc_url('http://support.wpbackitup.com/support/tickets/new')); ?></p>
									<p><em><?php _e('The ticket id you receive from your support request should be entered in the ticket id field below.', 'wp-backitup'); ?></em></p>
									<p><input <?php echo($disabled) ; ?> type="text" name="support_email" class="wpbiu-form-input" value="<?php echo $support_email; ?>" size="30" placeholder="<?php _e('your email address','wp-backitup')?>">
										<?php
										if ( false !== ( $msg = get_transient('error-support-email') ) && $msg)
										{
											echo '<span class="error">'.$msg.'</span>';
											delete_transient('error-support-email');
										}
										?>
									</p>

									<p>
										<input <?php echo($disabled) ; ?> type="text" name="support_ticket_id" class="wpbiu-form-input" value="<?php echo get_transient('support_ticket_id'); ?>" size="30" placeholder="<?php _e('support ticket id','wp-backitup')?>">
										<?php
										if ( false !== ( $msg = get_transient('error-support-ticket') ) && $msg)
										{
											echo '<span class="error">'.$msg.'</span>';
											delete_transient('error-support-ticket');
										}
										?>
									</p>

									<div>
									<textarea <?php echo($disabled); ?> name="support_body" class="wpbiu-support-textarea"  placeholder="<?php _e('problem description or additional information','wp-backitup')?>"><?php echo get_transient('support_body'); ?></textarea>
										<?php
										if ( false !== ( $msg = get_transient('error-support-body') ) && $msg)
										{
											echo '<span class="error">'.$msg.'</span>';
											delete_transient('error-support-body');
										}
										?>
									</div>

									<div style="clear:both;" />

									<p>
									<div class="submit">
										<input <?php echo($disabled) ; ?> type="submit" name="send_ticket" class="button-primary" value="<?php _e("Send Logs", 'wp-backitup') ?>" />

										<?php
										echo apply_filters( 'wpbackitup_show_active',
											'<div><em>*' . sprintf(__('Premium customers receive priority support.', 'wp-backitup')) . '</em></div>'
											,false
										);
										?>
									</div>
									</p>

									<?php //Successful email
									if (!empty($_GET["s"]) && '2' == $_GET["s"]) : ?>
										<div class="isa_error">
											<?php _e( 'Support email could not be sent!', 'wp-backitup' ); ?>
										</div>
									<?php endif; ?>

									<?php //Successful email
									if (!empty($_GET["s"]) && '1' == $_GET["s"]) : ?>
									<div class="isa_success">
										<?php _e( 'Support email sent successfully!', 'wp-backitup' ); ?>
									</div>
									<?php endif; ?>

								</form>
							</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}


	/**
	 * Render Download logs screen
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	private function download_logs_screen() {
		?>
        <form id = "download_backup" name="download_backup" action="<?php echo get_admin_url(),"admin-post.php"; ?>" method="post">
	    	<input type="hidden" name="action" value="download_backup">
	    	<input type="hidden" id="backup_file" name="backup_file" value="">
            <input type="hidden" id="download_logs" name="download_logs" value="1">
	   	    <?php wp_nonce_field('wp-backitup'. "-download_backup"); ?>
    	</form>
        <?php
        $WPBackitupListTable = new WPBackitup_Download_Logs();
  		echo '</pre><div class="wrap"><p class="about-description">'.__( 'Download logs file manually.', 'wp-backitup' ).'</p>';
		
  		$WPBackitupListTable->prepare_items(); 
		echo '<form method="post"><input type="hidden" name="page" value="download_logs_screen">';
  		$WPBackitupListTable->display(); 
		echo '</form></div>';  
	}

	/* For delete data data on delete action */
	private function delete_action(){
		
		$filename = WPBACKITUP__LOGS_PATH.'/'.$_GET['delete_log'];
		if (file_exists($filename)) {
			unlink($filename);
		}
	}

	/**
	 * Render Advanced Screen
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	private function advanced_screen() {
	global $WPBackitup;
	?>

		<p class="about-description"><?php _e( 'Content goes here', 'wp-backitup' ); ?></p>

		<div class="advanced-content">
			<div class="feature-section">
				<div class="feature-section-content">
<!--START-->


<!--END-->
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Render header
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	private function render_header() {
	?>
	<div class="wrap about-wrap wpbackitup-about-wrap">
		<?php

			$this->admin_head();
			$this->welcome_message();
			$this->tabs();
		?>
		<?php
	}

	/**
	 * Render footer
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	private function render_footer() {
	?>
		</div>

		<?php
	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since 1.13.1
	 * @return void
	 */
	private function admin_head() {
		?>
		<style type="text/css" media="screen">
			/*<![CDATA[*/
			.wpbackitup-about-wrap .wpbackitup-badge { float: right; border-radius: 4px; margin: 0 0 15px 15px; max-width: 100px; }
			.wpbackitup-about-wrap #wpbackitup-header { margin-bottom: 15px; }
			.wpbackitup-about-wrap #wpbackitup-header h1 { margin-bottom: 15px !important; }
			.wpbackitup-about-wrap .about-text { margin: 0 0 15px; max-width: 670px; }

			.wpbackitup-about-wrap .feature-section { margin-top: 20px; }
			.wpbackitup-about-wrap .feature-section-content,
			.wpbackitup-about-wrap .feature-section-media { width: 50%; box-sizing: border-box; }
			.wpbackitup-about-wrap .feature-section-content { float: left; padding-right: 50px; }
			.wpbackitup-about-wrap .feature-section-content h4 { margin: 0 0 1em; }
			.wpbackitup-about-wrap .feature-section-media { float: right; text-align: right; margin-bottom: 20px; }
			.wpbackitup-about-wrap .feature-section-media img { border: 1px solid #ddd; }

			.wpbackitup-about-wrap .feature-section-media-inline { width: 100%; box-sizing: border-box; }
			.wpbackitup-about-wrap .feature-section-media-inline { float: left; text-align: left; margin-bottom: 20px; }
			.wpbackitup-about-wrap .feature-section-media-inline img { border: 1px solid #ddd; }

			.wpbackitup-about-wrap .feature-section:not(.under-the-hood) .col { margin-top: 0; }
			.wpbiu-download-logs .feature-section-content{ width: 60%; }
			/* responsive */
			@media all and ( max-width: 782px ) {
				.wpbackitup-about-wrap .feature-section-content,
				.wpbackitup-about-wrap .feature-section-media { float: none; padding-right: 0; width: 100%; text-align: left; }
				.wpbackitup-about-wrap .feature-section-media img { float: none; margin: 0 0 20px; }

				.wpbackitup-about-wrap .feature-section-media-inline { float: none; padding-left: 0; width: 100%; text-align: left; }
				.wpbackitup-about-wrap .feature-section-media-inline img { float: none; margin: 0 0 20px; }
			}

			/*]]>*/


		</style>

		<?php
	}
}
new WPBackItUp_Support();