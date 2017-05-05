<?php if (!defined ('ABSPATH')) die('No direct access allowed');
/**
 * Welcome Page Class
 *
 * @package     WPBackItUp
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2016, WPBackItUp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.13.1
 */


/**
 * WPBackItUp_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.13.1
 */
class WPBackItUp_Welcome {

	private $display_version;
	/**
	 * Get things started
	 *
	 * @since 1.13.1
	 */
	public function __construct() {
		global $WPBackitup;

		list( $this->display_version ) = explode( '-', $WPBackitup->formatted_version() );

		$this->render_header();

		$selected = isset( $_GET['tab'] ) ? $_GET['tab'] : 'getting-started';
		if ($selected == 'getting-started') $this->getting_started_screen();
		if ($selected == 'whats-new') $this->whats_new_screen();
		if ($selected == 'premium') $this->premium_screen();
		if ($selected == 'changelog') $this->changelog_screen();
		if ($selected == 'tools') $this->tools_screen();

		$this->render_footer();

	}

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 1.9
	 * @return void
	 */
	private function tabs() {
		$selected = isset( $_GET['tab'] ) ? $_GET['tab'] : 'getting-started';

		?>
		<h1 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wp-backitup-about' ), 'admin.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'wp-backitup' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'whats-new' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wp-backitup-about','tab'=>'whats-new'  ), 'admin.php' ) ) ); ?>">
				<?php _e( "What's New", 'wp-backitup'); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'premium' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wp-backitup-about','tab'=>'premium' ), 'admin.php' ) ) ); ?>">
				<?php _e( 'Premium Plugin', 'wp-backitup' ); ?>
			</a>
		</h1>
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
			<h1><?php printf( __( 'Welcome to WPBackItUp %s', 'wp-backitup' ), $this->display_version ); ?></h1>
			<p class="about-text">
				<?php printf( __( 'Thank you for updating to the latest version! WPBackItUp %s is ready to make sure your site is backed up quickly, securely, and completely!', 'wp-backitup' ), $this->display_version ); ?>
			</p>
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
	private function getting_started_screen() {
		global $WPBackitup;
		?>
			<p class="about-description"><?php _e( 'Use the tips below to get started using WPBackItUp and you will be <em>backing it up</em> in no time!', 'wp-backitup' ); ?></p>

			<div class="changelog">
				<h2><?php _e( 'Backups', 'wp-backitup' );?></h2>
				<h3><?php _e( 'Creating Your First Backup', 'wp-backitup' );?></h3>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/backup_inprogress.png'?>" />
					</div>
					<div class="feature-section-content">
						<h4><a href="<?php echo admin_url( 'admin.php?page=wp-backitup-backup' ) ?>"><?php printf( __( 'One Click &rarr; Backup', 'wp-backitup' )); ?></a></h4>
						<p><?php printf( __( 'The backup menu option is the starting point for all things related to backups. To create your first backup, simply click the <em>Backup</em> button and WPBackItUp will backup your entire site. This includes your database, plugins, themes, and even all your media files.', 'wp-backitup' )); ?></p>


						<h4><?php _e( 'Download Backups', 'wp-backitup' );?></h4>
						<p><?php _e( 'Downloading your backups is simple too. Click the backup you want to download from the <em>Available backups</em> listing. Then click the part of the backup you want to download (database, plugins, themes, media files).  Or download the entire backup in just one click.', 'wp-backitup' );?></p>

						<div class="feature-section-media-inline">
							<img src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/single-file-download.png'?>" />
						</div>

					</div>
				</div>
			</div>

			<div class="changelog">
				<h2><?php _e( 'Automatic  Backups', 'wp-backitup' );?></h2>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/backup_scheduler.png'; ?>"/>
					</div>
					<div class="feature-section-content">
						<h4><?php _e( 'Schedule your Backups (Premium Only)','wp-backitup' );?></h4>
						<p><?php _e( 'Schedule your backups to run any day of the week, or all of them. With our flexible backup scheduler just select the days of the week you want your backup to run and WPBackItUp will handle the rest.', 'wp-backitup' );?></p>

						<h4><?php _e( 'Backup Notifications', 'wp-backitup' );?></h4>
						<p><?php  _e( 'Want to get notified every time a backup finishes? Just add your email address to the notification section in backup settings. WPBackItUp even supports multiple email addresses so add as many as you like.', 'wp-backitup' );?></p>
						<div class="feature-section-media-inline">
							<img src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/email_notifications.png'?>" />
						</div>

						<h4><?php _e( 'Backup Retention', 'wp-backitup' ); ?></h4>
						<p><?php  _e( 'Concerned about the amount of storage space used for backups? Use the <em>backup retention</em> setting to tell WPBackItUp how many backups you want to keep. WPBackItUp will make sure only the newest backups are saved.', 'wp-backitup' ); ?></p>

					</div>

					<div class="feature-section-media">
						<img src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/backup_retention.png'?>" />
					</div>


				</div>
			</div>

			<div class="changelog">
				<h2><?php _e( 'Restore', 'wp-backitup' );?></h2>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/restore_success.png'; ?>"/>
					</div>
					<div class="feature-section-content">
						<h4><?php printf( __( 'One Click &rarr; Restore (Premium Only)', 'wp-backitup' )); ?></a></h4>
						<p><?php _e( 'We hope you never need to restore your site but if you do then WPBackItUp has you covered.  And with our one-click restore feature, restoring your site is just as easy as backing it up.', 'wp-backitup' );?></p>

						<h4><?php printf( __( 'Migration &amp; Cloning (Premium Only)', 'wp-backitup' )); ?></a></h4>
						<p><?php _e( 'Need to migrate your site to a new host or want to clone a copy to your staging server?  WPBackItUp does that too!  Just upload your backups to your new WordPress install and restore it using the one-click restore feature.', 'wp-backitup' );?></p>

						<div class="feature-section-media-inline">
							<img src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/upload_success.png'?>" />
						</div>
					</div>

				</div>
			</div>

			<div class="changelog">
				<h2><?php _e( 'Need Help?', 'wp-backitup' );?></h2>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/support_page.png'; ?>"/>
					</div>

					<div class="feature-section-content">
						<h4><?php _e( 'Phenomenal Support','wp-backitup' );?></h4>
						<?php echo( WPBackItUp_Utility::get_anchor_with_utm(__('Get support','wp-backitup'),'support/home' ,'getting+started','support',WPBACKITUP__SUPPORTSITE_URL))?>

						<p><?php printf(__( 'We do our best to provide the best product possible but if you run into trouble then support is just a few clicks away. To get help or if you have a question, simply open a ticket using our %s.', 'wp-backitup'),WPBackItUp_Utility::get_anchor_with_utm(__('support portal','wp-backitup'),'support/home' ,'getting+started','support',WPBACKITUP__SUPPORTSITE_URL));?></p>

						<h4><?php _e( 'Need Even Faster Support?', 'wp-backitup' );?></h4>
						<p><?php printf(__( 'Our %s system is there for customers that need faster or more in-depth assistance.', 'wp-backitup' ),WPBackItUp_Utility::get_anchor_with_utm(__('Priority Support','wp-backitup'),'priority-support' ,'getting+started','support',WPBACKITUP__SECURESITE_URL));?></p>

						<h4><?php _e( 'Need Help Migrating Your Site to a New Host?', 'wp-backitup' );?></h4>
						<p><?php printf(__( 'Our Site Migration experts have helped hundreds of WPBackItUp customers migrate their sites and now you can take advantage of that experience with our WordPress %s. Let our experts save you time and headaches by doing it for you!', 'wp-backitup' ),WPBackItUp_Utility::get_anchor_with_utm(__('Site Migration Service','wp-backitup'),'wordpress-site-migration' ,'getting+started','support',WPBACKITUP__SECURESITE_URL));?></p>

						<h4><?php _e( 'Tools Tools Tools...','wp-backitup' );?></h4>
						<p><?php printf(__( 'Great web sites and businesses are built with great tools and products that help you achieve your goals as efficiently as possible.  My team and I  have spent a tremendous amount of time and effort evaluating tools to help run our WordPress powered website and business. And now you can leverage all our hard work and my 20+ years experience in the software industry with a simple mouse click.  The %s is a growing list of tools and products we use at WPBackItUp to enhance our websites and businesses.  Please feel free to use our experience to empower, enhance and accelerate your sites and businesses.', 'wp-backitup' ),WPBackItUp_Utility::get_anchor_with_utm(__('WPBackItUp Tools List','wp-backitup'),'tools' ,'getting+started','tools',WPBACKITUP__SECURESITE_URL));?></p>


				</div>
			</div>

			<div class="changelog">
				<h2><?php _e( 'Stay Up to Date', 'wp-backitup' );
					//TODO: Add subscription page to wpbackitup site
					?></h2>
				<div class="feature-section two-col">
					<div class="col">
						<h4><?php _e( 'Get Notified of New Releases','wp-backitup' );?></h4>
						<p><?php printf(__( 'New features that make WPBackItUp even more powerful are released often. Subscribe to our newsletter to stay up to date with our latest releases. %s to ensure you do not miss a release!', 'wp-backitup' ),WPBackItUp_Utility::get_anchor_with_utm(__('Sign up now','wp-backitup'),'subscribe' ,'getting+started','subscribe',WPBACKITUP__SECURESITE_URL));?></p>
					</div>
					<div class="col">
						<h4><?php _e( 'Get Alerted About New Tutorials', 'wp-backitup' );?></h4>
						<p><?php printf(__( '%s to hear about the latest tutorials that explain how to take WPBackItUp further.', 'wp-backitup' ),WPBackItUp_Utility::get_anchor_with_utm(__('Sign up now','wp-backitup'),'subscribe' ,'getting+started','subscribe',WPBACKITUP__SECURESITE_URL));?></p>
					</div>

				</div>
			</div>


		<?php
	}

	/**
	 * Render Whats New Screen
	 *
	 * @access public
	 * @since 1.13.1
	 * @return void
	 */
	private function whats_new_screen() {
		?>
			<p class="about-description"><?php printf( __( 'Below are just a few highlights for version %s. ', 'wp-backitup' ), $this->display_version); ?></p>

			<div class="changelog">
				<h2><?php _e( 'New Features', 'wp-backitup' );?></h2>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo WPBACKITUP__PLUGIN_URL . 'images/active_plugins_1.14.png'; ?>"/>
					</div>
					<div class="feature-section-content">
						<h4><?php _e( 'WPBackItUp Community Edition', 'wp-backitup' );?></h4>
						<p><?php _e( 'In version 1.14 we decided to do some important housekeeping we have wanted to do for quite some time now.  This housekeeping does not include any new features but it does lay the necessary groundwork for major improvements in the future.', 'wp-backitup' );?></p>
						<p><?php _e( 'The most important and noticeable change to you is that we decided to split the WPBackItUp plugin into two plugins: WPBackItUp Community Edition(CE) and WPBackItUp Premium.   The Community Edition or CE plugin, is the version we offer for free via the WordPress.org repository.  The premium plugin is the one you must purchase via www.wpbackitup.com that provides additional features. If you are receiving this email, you have purchased the premium plugin.', 'wp-backitup' );?></p>
						<p><?php _e( 'We decided to make this split for a number of technical reasons but the most important to you is that we want to be able to release Premium features separate from the CE updates.  With the two versions bundled into a single plugin we were forced to release updates to both sets of customers on the same schedule.  With this split we are now able to release updates to the CE  plugin much more frequently and with less impact to our premium customers.', 'wp-backitup' );?></p>
					</div>

				</div>
			</div>

			<div class="changelog">
				<h2><?php _e( 'Previous Release Highlights', 'wp-backitup' );?></h2>
				<div class="feature-section three-col">
					<div class="col">
						<h4><?php _e( 'Support Center Enhancements', 'wp-backitup' );?></h4>
						<p><?php _e( 'We have made major enhancements to the support center that will allow us to make our world class support even better.  You are now able to <em>view, download</em> and <em>delete</em> the log files we use to help troubleshoot problems with your site. This is an important addition because sometimes customer sites are so crippled by hosting issues, hackers or bad plugins, that they are unable to send us their logs files.  Now if that ever happens to you, they can easily be downloaded and emailed to support. ', 'wp-backitup' );?></p>
					</div>
					<div class="col">
						<h4><?php _e( 'Cleanup Supporting Zip Files', 'wp-backitup' );?></h4>
						<p><?php _e( 'WPBackItUp now does an even better job of conserving space used on your host.  When you select the <em>Cleanup Supporting Zip Files</em>  setting, WPBackItUp will remove the supporting zip files that were used to create your backup.', 'wp-backitup' );?></p>
					</div>
					<div class="col">
						<h4><?php _e( 'Email  Notifications', 'wp-backitup' );?></h4>
						<p><?php _e( 'WPBackItUp now supports multiple email addresses for backup notifications. Using the WPBackItUp Settings page add multiple email addresses to the <em>Email Notifications</em> setting and all will receive notification emails when your backups complete.', 'wp-backitup' );?></p>
					</div>
					<div class="clear">
						<div class="col">
							<h4><?php _e( 'Maximum Zip File Size', 'wp-backitup' );?></h4>
							<p><?php _e( "Over the years we have found that some hosting providers enforce strict limitations on the size of your backup archives.  When this happens you might see backup errors, or more commonly backup jobs appear to never finish.  To combat this challenge we have added the <em>Maximum Zip File Size</em> setting.  This setting tells WPBackItUp when to stop adding files to a backup archive and create a new one.  Don't worry WPBackItUp won't miss any files, it will just add them to a new backup archive.", 'wp-backitup' );?></p>
						</div>
					</div>
				</div>

			<div class="return-to-dashboard">
				<?php _e( 'Want to see the entire changelog?', 'wp-backitup' ); ?> &mdash;
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wp-backitup-about','tab'=>'changelog' ), 'admin.php' ) )); ?>"><?php _e( 'View the Full Changelog', 'wp-backitup' ); ?></a>
			</div>
		<?php
	}

	/**
	 * Render Whats New Screen
	 *
	 * @access public
	 * @since 1.13.1
	 * @return void
	 */
	private function tools_screen() {
		?>

		<?php
	}


	/**
	 * Render Changelog Screen
	 *
	 * @access public
	 * @since 2.0.3
	 * @return void
	 */
	private function changelog_screen() {
		?>
			<div class="changelog">
				<h2><?php _e( 'Full Changelog', 'wp-backitup' );?></h2>
				<div class="feature-section">
					<?php echo $this->parse_readme(); ?>
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
	private function premium_screen() {
		global $WPBackitup;
		?>
			<h2><?php _e( 'Take a look at what you are missing!', 'wp-backitup' );?></h2>

			<!-- TABLE -->
			<section class="wpbackitup-features-table">
				<div class="wpbackitup-container">
					<table class="table">
						<thead>
						<tr>
							<td><?php _e( 'FEATURES', 'wp-backitup' ); ?></td>
							<td><?php _e('LITE', 'wp-backitup' ); ?></td>
							<td><?php _e('PREMIUM', 'wp-backitup' ); ?></td>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><?php _e('Complete Backup (Database, Themes, Plugins &amp; Media Files)', 'wp-backitup' ); ?> </td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Compressed Backups (Zip Format)', 'wp-backitup' ); ?></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Download Backups', 'wp-backitup' ); ?></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Directory Filters', 'wp-backitup' ); ?></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Database Table Filters', 'wp-backitup' ); ?></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Single File Database Export', 'wp-backitup' ); ?></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Purge Old Backups', 'wp-backitup' ); ?></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Cleanup Work Files &amp; Logs', 'wp-backitup' ); ?></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Backup Notifications via Email', 'wp-backitup' ); ?></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><strong><?php _e('One Click Restore</strong>', 'wp-backitup' ); ?></td>
							<td class="times"><i class="fa fa-times" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Single File Backup', 'wp-backitup' ); ?></td>
							<td class="times"><i class="fa fa-times" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Scheduled Backups', 'wp-backitup' ); ?></td>
							<td class="times"><i class="fa fa-times" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Import Backups', 'wp-backitup' ); ?></td>
							<td class="times"><i class="fa fa-times" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
<!--						<tr>-->
<!--							<td>--><?php //_e('Encrypted Backups', 'wp-backitup' ); ?><!--</td>-->
<!--							<td class="times"><i class="fa fa-times" aria-hidden="true"></i></td>-->
<!--							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>-->
<!--						</tr>-->
						<tr>
							<td><?php _e('Premium Support', 'wp-backitup' ); ?></td>
							<td class="times"><i class="fa fa-times" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><?php _e('Product Updates', 'wp-backitup' ); ?></td>
							<td class="times"><i class="fa fa-times" aria-hidden="true"></i></td>
							<td class="check"><i class="fa fa-check" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td class="link"><?php echo( WPBackItUp_Utility::get_anchor_with_utm(__('GET PREMIUM','wp-backitup'),'pricing-purchase' ,'premium+plugin','feature+compare',WPBACKITUP__SECURESITE_URL))?></td>
						</tr>
						</tbody>
					</table>
				</div>
			</section>

			<!-- #TABLE -->

<!--	</div>-->
		<?php
	}

	/**
	 * Parse the WPBackItUp readme.txt file
	 *
	 * @since 2.0.3
	 * @return string $readme HTML formatted readme file
	 */
	private function parse_readme() {
		$file = file_exists( WPBACKITUP__PLUGIN_PATH . 'readme.txt' ) ? WPBACKITUP__PLUGIN_PATH . 'readme.txt' : null;

		if ( ! $file ) {
			$readme = '<p>' . __( 'No valid changelog was found.', 'wp-backitup' ) . '</p>';
		} else {
			$readme = file_get_contents( $file );
			$readme = nl2br( esc_html( $readme ) );
			$readme = explode( '== Changelog ==', $readme );
			$readme = end( $readme );

			$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
			$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
			$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
			$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
			$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
		}

		return $readme;
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
			.wpbackitup-about-wrap  h2 { text-align:left; }
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
			/* responsive */
			@media all and ( max-width: 782px ) {
				.wpbackitup-about-wrap .feature-section-content,
				.wpbackitup-about-wrap .feature-section-media { float: none; padding-right: 0; width: 100%; text-align: left; }
				.wpbackitup-about-wrap .feature-section-media img { float: none; margin: 0 0 20px; }

				.wpbackitup-about-wrap .feature-section-media-inline { float: none; padding-left: 0; width: 100%; text-align: left; }
				.wpbackitup-about-wrap .feature-section-media-inline img { float: none; margin: 0 0 20px; }
			}
			/*]]>*/

			/*Feature comparison CSS*/
			.wpbackitup-container {
				width: 90%;
				margin: 0 auto;
				position: relative;
				float:left;
			}

			.wpbackitup-features-table {
				background-color: #fff;
				/*padding: 10px;*/

			}
			.wpbackitup-features-table .table {
				font-family: Verdana,Geneva,Kalimati,sans-serif;
				margin: 0 auto;
				width: 100%;
				max-width: 100%;
			}
			.wpbackitup-features-table .table tr td {
				height: 60px;
				text-align: center;
			}

			.wpbackitup-features-table .table tbody tr td:first-child {
				text-align: left;
				text-indent: 20px;
				border-top: 1px dotted #dfdfdf;
				border-bottom: 1px dotted #dfdfdf;
				color: #555;
				font-size: 15px;
				-webkit-transition: all 0.3s ease;
				-o-transition: all 0.3s ease;
				transition: all 0.3s ease;
			}
			.wpbackitup-features-table .table tbody tr:hover td:first-child {
				background-color: #fafafa;
			}
			.wpbackitup-features-table .table  tr td:first-child {
				width: 70%;
			}
			.wpbackitup-features-table .table  tr td:nth-child(2) {
				width: 15%;
			}
			.wpbackitup-features-table .table  tr td:nth-child(3) {
				width: 15%;
			}
			.wpbackitup-features-table .table tbody tr td {
				line-height: 60px;
				font-size: 15px;
			}
			.wpbackitup-features-table .table thead tr td {
				color: #fff;
				line-height: 60px;
				font-weight: 700;
			}
			.wpbackitup-features-table .table thead tr td:first-child {
				background-color: #555;
				text-align: left;
				text-indent: 20px;
			}
			.wpbackitup-features-table .table thead tr td:nth-child(2),
			.wpbackitup-features-table .table thead tr td:nth-child(3) {
				background-color: #ffa311;
				position: relative;
				border-right: 1px solid #ffedcf;
			}
			.wpbackitup-features-table .table thead tr td:nth-child(2):before,
			.wpbackitup-features-table .table thead tr td:nth-child(3):before {
				content: '';
				position: absolute;
				width: 0;
				height: 0;
				border-left: 11px solid transparent;
				border-right: 11px solid transparent;
				border-top: 10px solid #ffa311;
				bottom: -10px;
				left: 50%;
				margin-left: -11px;
			}

			.wpbackitup-features-table .table tbody tr td.check {
				background-color: #9ec408;
				border-top: 1px solid #b8de22;
				color: #fff;
				font-size: 30px;
				border-right: 1px solid #f1f9d1;
				-webkit-transition: all 0.3s ease;
				-o-transition: all 0.3s ease;
				transition: all 0.3s ease;
			}

			.wpbackitup-features-table .table tbody tr td.link {
				background-color: #ffa311;
				border-top: 1px solid #ffc937;
				cursor: pointer;
				-webkit-transition: all 0.3s ease;
				-o-transition: all 0.3s ease;
				transition: all 0.3s ease;
			}
			.wpbackitup-features-table .table tbody tr td.link:hover {
				background-color: #ffc937;
			}
			.wpbackitup-features-table .table tbody tr td.link a {
				line-height: 60px;
				text-decoration: none;
				font-weight: 700;
				color: #fff;
				display: block;
				height: 60px;
				width: 100%;
			}
			.wpbackitup-features-table .table tbody tr:last-child td:first-child {
				border: none;
				background: none !important;
			}
			.wpbackitup-features-table .table tbody tr td.times {
				background-color: #e54b00;
				color: #fff;
				font-size: 30px;
				border-right: 1px solid #f1f9d1;
				border-top: 1px solid #ff651a;
				-webkit-transition: all 0.3s ease;
				-o-transition: all 0.3s ease;
				transition: all 0.3s ease;
			}
			.wpbackitup-features-table .table tbody tr:hover  td.times{
				background-color: #f45f17;
			}
			.wpbackitup-features-table .table tbody tr:hover td.check {
				background-color: #b7df18;
			}


		</style>

		<?php
	}

}
new WPBackItUp_Welcome();
