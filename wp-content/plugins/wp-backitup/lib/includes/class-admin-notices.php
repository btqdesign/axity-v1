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

if ( ! class_exists( 'WPBackitup_Admin_Notices' ) ) {

	class WPBackitup_Admin_Notices {

		private $promo;
		private $today;

		public function __construct(){

			//$test_date = date("Y-m-d H:i", strtotime("16 November 2017 5:00 AM UTC")); //todo: comment for LIVE
			//$this->today =$test_date;//todo:remove this line for LIVE

			$this->today =date("Y-m-d H:i"); //todo:uncomment for LIVE

			//IS there a promo to run
			$this->promo = $this->get_active_notice();
		}

		/**
		 * Run the active notice
		 * Only one notice should be displayed at a time
		 *
		 */
		public function run() {
			try {
				//Which promo should be run
				switch ( $this->promo ) {
					case "black-friday":
						$this->black_friday();
						break;
					default:
						//Run the wordpress review promo if nothing else is going on
						$this->wordpress_review();
						break;
				}
			} catch ( Exception $e ) {

			}
		}

		/**
		 * WordPress Review Notice
		 *
		 */
		private function wordpress_review() {
			global $WPBackitup;

			//if they had more than 10 successful backups then show the message in 1 day
			$days_after = 10;//default to 10 days after install
			$successfull_backups =$WPBackitup->successful_backup_count();
			if ($successfull_backups>=10) $days_after = 1;

			new WPBackitup_Admin_Notice( array(
				'id' => 'wpbu-review-me',
				'days_after' => $days_after,
				'type' => 'updated'));
		}

		/**
		 * Black Friday Notice
		 *
		 */
		private function black_friday() {

			$black_friday_notice = $this->get_blackfriday_notice();

			//black friday sale notice
			$days_after=0;
			$black_friday_sale = array(
				'id'                => 'black-friday-sale',
				'days_after'        => 0,
				'temp_days_after'   => $black_friday_notice['temp_days_after'],
				'type'              => 'updated',
				'message'           => $black_friday_notice['message'],
				'link_1'            => $black_friday_notice['link_1'],
				'link_label_1'      => $black_friday_notice['link_label_1'],
				'link_label_2'      => $black_friday_notice['link_label_2'],
				'link_label_3'      => $black_friday_notice['link_label_3'],
			);

			new WPBackitup_Admin_Notice($black_friday_sale);
		}

		/**
		 * Get the active notice
		 */
		private function get_active_notice() {

			$wpbacktiup_license = new WPBackItUp_License();

			//Don't show notice when premium is installed
			if (!$wpbacktiup_license->is_premium_license() || ! $wpbacktiup_license->is_license_active() ){
				return 'black-friday';
			}

		}


		private function get_black_friday_day_id() {
			$id = false;

			//12:00 AM EST = 5:00 AM UTC
			//11:59 PM EST = 4:59 AM UTC

			$pre_sale_start = date( "Y-m-d H:i", strtotime( "16 November 2017 5:00 AM UTC" ) ); // 16th  12:00 AM EST
			$pre_sale_end   = date( "Y-m-d H:i", strtotime( "24 November 2017 4:59 AM UTC" ) ); // 23rd 11:59 PM EST

			$sale_start     = date( "Y-m-d H:i", strtotime( "24 November 2017 5:00 AM UTC" ) );// 24th 12:00 AM EST
			$sale_end       = date( "Y-m-d H:i", strtotime( "27 November 2017 4:59 AM UTC" ) );// 26th 11:59 PM EST

			$lastday_start  = date( "Y-m-d H:i", strtotime( "27 November 2017 5:00 AM UTC" ) );// 27th 12:00 AM EST
			$lastday_end    = date( "Y-m-d H:i", strtotime( "28 November 2017 4:59 AM UTC" ) );// 27th 11:59 PM EST


			if ( $this->today >= $pre_sale_start && $this->today <= $pre_sale_end ) {
				$id = 0;
			} elseif ( $this->today >= $sale_start && $this->today <= $sale_end ) {
				$id = 1;
			} elseif ( $this->today >= $lastday_start && $this->today <= $lastday_end ) {
				$id = 2;
			}

			return $id;
		}

		private function get_blackfriday_notice() {
			$message= array();
			$link_1=array();
			$link_label_1=array();
			$link_label_2=array();
			$link_label_3=array();
			$temp_days_after=array();

			$id = $this->get_black_friday_day_id();

			//PRESALE
			$message[]=sprintf( "%s<p>%s<p>%s",
				'<h2>' . esc_html__( "Black Friday/Cyber Monday Sale Starts Soon!", "wp-backitup") . ' </h2>',
				__( "Save <b>30%</b> on WPBackItUp Premium for a limited time!", "wp-backitup" ),
				__( "We just wanted to let you know that WPBackItUp will be participating in the Black Friday and Cyber Monday craziness next week.<br/><br/>If you purchase WPBackItUp Premium or upgrade an existing license between <b>Friday, November 24, 2017, 12 AM EST (5 AM UTC)</b> and <b>Monday, November 27, 2017, 11:59 PM EST ( 4:59 AM UTC )</b> you'll automatically get <b>30%</b> off our regular prices.", "wp-backitup" )
			);
			$temp_days_after[]=1;
			$link_1[] =  "https://www.wpbackitup.com/pricing-purchase/?utm_medium=plugin&utm_source=wp-backitup&utm_campaign=plugin-black-friday-1";
			$link_label_1[] =  esc_html__( 'Buy now, I don\'t need the discount!', 'wp-backitup' );
			$link_label_2[] = esc_html__( 'Remind me later', 'wp-backitup' );
			$link_label_3[] = esc_html__( 'I already purchased', 'wp-backitup' );

			//SALE
			$message[]=sprintf( "%s<p>%s<p>%s",
				'<h2>' . esc_html__( "WPBackItUp Black Friday SALE! Save 30% on all purchases and upgrades!", "wp-backitup") . ' </h2>',
				__( "<b>It’s SALE time!</b><br/><br/>The WPBackItUp Black Friday sale has started so if you have been thinking about safeguarding your WordPress site with WPBackItUp Premium then now is the time.", "wp-backitup" ),
				__( "If you purchase WPBackItUp Premium or upgrade an existing license between <b>Friday, November 24, 2017, 12 AM EST (5 AM UTC)</b> and <b>Monday, November 27, 2017, 11:59 PM EST ( 4:59 AM UTC )</b> you'll automatically get 30% off our regular prices.", "wp-backitup" )
			);
			$temp_days_after[]=1;
			$link_1[] =  "https://www.wpbackitup.com/pricing-purchase/?utm_medium=plugin&utm_source=wp-backitup&utm_campaign=plugin-black-friday-2";;
			$link_label_1[] =  esc_html__( 'Buy now and save 30%', 'wp-backitup' );
			$link_label_2[] = esc_html__( 'Remind me later', 'wp-backitup' );
			$link_label_3[] = esc_html__( 'I already purchased', 'wp-backitup' );

			//LAST DAY
			$message[]=sprintf( "%s<p>%s<p>%s",
				'<h2>' . esc_html__( "Less than 24 hours left to save 30%  on WPBackItUp Premium!", "wp-backitup") . ' </h2>',
				__( "Happy Cyber Monday! Today is your last chance to save <b>30% </b> on WPBackItUp Premium.", "wp-backitup" ),
				__( "If you purchase WPBackItUp Premium or upgrade an existing license between <b>Friday, November 24, 2017, 12 AM EST (5 AM UTC)</b> and <b>Monday, November 27, 2017, 11:59 PM EST ( 4:59 AM UTC )</b> you'll automatically get 30% off our regular prices.", "wp-backitup" )
			);
			$temp_days_after[]=1;
			$link_1[] =  "https://www.wpbackitup.com/pricing-purchase/?utm_medium=plugin&utm_source=wp-backitup&utm_campaign=plugin-black-friday-3";;
			$link_label_1[] =  esc_html__( 'Buy now and save 30%', 'wp-backitup' );
			$link_label_2[] = esc_html__( 'Remind me later', 'wp-backitup' );
			$link_label_3[] = esc_html__( 'I already purchased', 'wp-backitup' );

			$rtn = array(
				'message'=>$message[$id],
				'temp_days_after'=>$temp_days_after[$id],
				'link_1'=>$link_1[$id],
				'link_label_1'=>$link_label_1[$id],
				'link_label_2'=>$link_label_2[$id],
				'link_label_3'=>$link_label_3[$id],
				);

			return $rtn;

		}

	}
}