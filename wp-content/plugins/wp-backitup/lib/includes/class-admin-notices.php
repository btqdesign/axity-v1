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

		//PROMO Constants
		private $BLACK_FRIDAY_2017_PROMO = 'black-friday-2017';
		private $SAFE_BETA_PROMO = 'safe-beta-december';
		private $INDEPENDENCE_DAY_PROMO = 'independence-day-promo';

		public function __construct(){

			$today = date("Y-m-d H:i");
			//$today = date("Y-m-d H:i", strtotime("04 July 2018 5:00 AM UTC")); //todo: comment for LIVE
			$this->today =$today;

			//IS there a promo to run
			$this->promo = $this->get_active_promo();
		}

		/**
		 * Get the active notice
		 *
		 */
		private function get_active_promo() {

			//Is the independence day sale active?
//			if ( false !== $this->get_indepence_day_promo_id()) {
//				return $this->INDEPENDENCE_DAY_PROMO;
//			}

			//Is the safe beta promo active
//			if ( false !== $this->get_safe_beta_notice_id()) {
//				return $this->SAFE_BETA_PROMO;
//			}
//
//
//			//Is the black friday active
//			if ( false !== $this->get_black_friday_day_id()) {
//				$wpbacktiup_license = new WPBackItUp_License();
//
//				//Don't show notice when premium is installed
//				if (!$wpbacktiup_license->is_premium_license() || ! $wpbacktiup_license->is_license_active() ) {
//					return  $this->BLACK_FRIDAY_2017_PROMO;
//				}
//			}

			return false; //no active promos

		}

		/**
		 * Run the active notice
		 * Only one notice should be displayed at a time
		 *
		 */
		public function run() {
			try {

				switch ( $this->promo ) {
//					case $this->SAFE_BETA_PROMO:
//						$promo =  sprintf("%s-%s",$this->promo,$this->get_safe_beta_notice_id());
//						$notice = $this->get_safe_beta_notice();
//						$this->show_notice($promo,$notice);
//						break;
//					case $this->BLACK_FRIDAY_2017_PROMO:
//						$promo =  sprintf("%s-%s",$this->promo,$this->get_black_friday_day_id());
//						$notice = $this->get_black_friday_notice();
//						$this->show_notice($promo,$notice);
//						break;
					case $this->INDEPENDENCE_DAY_PROMO:
						$promo =  sprintf("%s-%s",$this->promo,$this->get_indepence_day_promo_id());
						$notice = $this->get_independence_day_notice();
						$this->show_notice($promo,$notice);
						break;
					default:
						$this->wordpress_review();
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
		 * Show Notice
		 *
		 * @param $id
		 * @param $notice
		 */
		private function show_notice($id, $notice) {

			if (is_array($notice)) {
				$promo_notice = array(
					'id'                => $id,
					'days_after'        => $notice['days_after'],
					'temp_days_after'   => $notice['temp_days_after'],
					'type'              => 'updated',
					'message'           => $notice['message'],
					'link_1'            => $notice['link_1'],
					'link_label_1'      => $notice['link_label_1'],
					'link_label_2'      => $notice['link_label_2'],
					'link_label_3'      => $notice['link_label_3'],
				);

				new WPBackitup_Admin_Notice($promo_notice);
			}
		}

		/**
		 * Get SAFE promo ID
		 *
		 * @return bool|int false = no promo
		 *
		 */
//		private function get_safe_beta_notice_id() {
//			$id = false;
//
//			//12:00 AM EST = 5:00 AM UTC
//			//11:59 PM EST = 4:59 AM UTC
//
//			$promo_start = date( "Y-m-d H:i", strtotime( "01 December 2017 5:00 AM UTC" ) );
//			$promo_end   = date( "Y-m-d H:i", strtotime( "30 December 2017 4:59 AM UTC" ) );
//
//			if ( $this->today >= $promo_start && $this->today <= $promo_end ) {
//				$id = 0;
//			}
//
//			return $id;
//		}

		/**
		 * Get Independence Day promo ID
		 *
		 * @return bool|int false = no promo
		 *
		 */
		private function get_indepence_day_promo_id() {
			$id = false;

			//12:00 AM EST = 5:00 AM UTC
			//11:59 PM EST = 4:59 AM UTC

			$promo_start = date( "Y-m-d H:i", strtotime( "02 July 2018 5:00 AM UTC" ) );
			$promo_end   = date( "Y-m-d H:i", strtotime( "08 July 2018 4:59 AM UTC" ) );

			if ( $this->today >= $promo_start && $this->today <= $promo_end ) {
				$id = 0;
			}

			return $id;
		}


		/**
		 * Get Black Friday Promo ID
		 *
		 * @return bool|int false = no promo
		 *
		 */
//		private function get_black_friday_day_id() {
//			$id = false;
//
//			//12:00 AM EST = 5:00 AM UTC
//			//11:59 PM EST = 4:59 AM UTC
//
//			$pre_sale_start = date( "Y-m-d H:i", strtotime( "16 November 2017 5:00 AM UTC" ) ); // 16th  12:00 AM EST
//			$pre_sale_end   = date( "Y-m-d H:i", strtotime( "24 November 2017 4:59 AM UTC" ) ); // 23rd 11:59 PM EST
//
//			$sale_start     = date( "Y-m-d H:i", strtotime( "24 November 2017 5:00 AM UTC" ) );// 24th 12:00 AM EST
//			$sale_end       = date( "Y-m-d H:i", strtotime( "27 November 2017 4:59 AM UTC" ) );// 26th 11:59 PM EST
//
//			$lastday_start  = date( "Y-m-d H:i", strtotime( "27 November 2017 5:00 AM UTC" ) );// 27th 12:00 AM EST
//			$lastday_end    = date( "Y-m-d H:i", strtotime( "28 November 2017 4:59 AM UTC" ) );// 27th 11:59 PM EST
//
//
//			if ( $this->today >= $pre_sale_start && $this->today <= $pre_sale_end ) {
//				$id = 0;
//			} elseif ( $this->today >= $sale_start && $this->today <= $sale_end ) {
//				$id = 1;
//			} elseif ( $this->today >= $lastday_start && $this->today <= $lastday_end ) {
//				$id = 2;
//			}
//
//			return $id;
//		}

		/**
		 * Get Black Friday notice
		 *
		 * @return array|false on no notice
		 */
//		private function get_black_friday_notice() {
//			$message= array();
//			$link_1=array();
//			$link_label_1=array();
//			$link_label_2=array();
//			$link_label_3=array();
//			$days_after=array();
//			$temp_days_after=array();
//
//			$id = $this->get_black_friday_day_id();
//			if (false===$id) return false;
//
//			//PRESALE
//			$message[]=sprintf( "%s<p>%s<p>%s",
//				'<h2>' . esc_html__( "Black Friday/Cyber Monday Sale Starts Soon!", "wp-backitup") . ' </h2>',
//				__( "Save <b>30%</b> on WPBackItUp Premium for a limited time!", "wp-backitup" ),
//				__( "We just wanted to let you know that WPBackItUp will be participating in the Black Friday and Cyber Monday craziness next week.<br/><br/>If you purchase WPBackItUp Premium or upgrade an existing license between <b>Friday, November 24, 2017, 12 AM EST (5 AM UTC)</b> and <b>Monday, November 27, 2017, 11:59 PM EST ( 4:59 AM UTC )</b> you'll automatically get <b>30%</b> off our regular prices.", "wp-backitup" )
//			);
//
//			$days_after[]=0;
//			$temp_days_after[]=1;
//			$link_1[] =  "https://www.wpbackitup.com/pricing-purchase/?utm_medium=plugin&utm_source=wp-backitup&utm_campaign=plugin-black-friday-1";
//			$link_label_1[] =  esc_html__( 'Buy now, I don\'t need the discount!', 'wp-backitup' );
//			$link_label_2[] = esc_html__( 'Remind me later', 'wp-backitup' );
//			$link_label_3[] = esc_html__( 'I already purchased', 'wp-backitup' );
//
//			//SALE
//			$message[]=sprintf( "%s<p>%s<p>%s",
//				'<h2>' . esc_html__( "WPBackItUp Black Friday SALE! Save 30% on all purchases and upgrades!", "wp-backitup") . ' </h2>',
//				__( "<b>It’s SALE time!</b><br/><br/>The WPBackItUp Black Friday sale has started so if you have been thinking about safeguarding your WordPress site with WPBackItUp Premium then now is the time.", "wp-backitup" ),
//				__( "If you purchase WPBackItUp Premium or upgrade an existing license between <b>Friday, November 24, 2017, 12 AM EST (5 AM UTC)</b> and <b>Monday, November 27, 2017, 11:59 PM EST ( 4:59 AM UTC )</b> you'll automatically get 30% off our regular prices.", "wp-backitup" )
//			);
//			$days_after[]=0;
//			$temp_days_after[]=1;
//			$link_1[] =  "https://www.wpbackitup.com/pricing-purchase/?utm_medium=plugin&utm_source=wp-backitup&utm_campaign=plugin-black-friday-2";;
//			$link_label_1[] =  esc_html__( 'Buy now and save 30%', 'wp-backitup' );
//			$link_label_2[] = esc_html__( 'Remind me later', 'wp-backitup' );
//			$link_label_3[] = esc_html__( 'I already purchased', 'wp-backitup' );
//
//			//LAST DAY
//			$message[]=sprintf( "%s<p>%s<p>%s",
//				'<h2>' . esc_html__( "Less than 24 hours left to save 30%  on WPBackItUp Premium!", "wp-backitup") . ' </h2>',
//				__( "Happy Cyber Monday! Today is your last chance to save <b>30% </b> on WPBackItUp Premium.", "wp-backitup" ),
//				__( "If you purchase WPBackItUp Premium or upgrade an existing license between <b>Friday, November 24, 2017, 12 AM EST (5 AM UTC)</b> and <b>Monday, November 27, 2017, 11:59 PM EST ( 4:59 AM UTC )</b> you'll automatically get 30% off our regular prices.", "wp-backitup" )
//			);
//			$days_after[]=0;
//			$temp_days_after[]=1;
//			$link_1[] =  "https://www.wpbackitup.com/pricing-purchase/?utm_medium=plugin&utm_source=wp-backitup&utm_campaign=plugin-black-friday-3";;
//			$link_label_1[] =  esc_html__( 'Buy now and save 30%', 'wp-backitup' );
//			$link_label_2[] = esc_html__( 'Remind me later', 'wp-backitup' );
//			$link_label_3[] = esc_html__( 'I already purchased', 'wp-backitup' );
//
//			$rtn = array(
//				'message'=>$message[$id],
//				'days_after'=>$days_after[$id],
//				'temp_days_after'=>$temp_days_after[$id],
//				'link_1'=>$link_1[$id],
//				'link_label_1'=>$link_label_1[$id],
//				'link_label_2'=>$link_label_2[$id],
//				'link_label_3'=>$link_label_3[$id],
//				);
//
//			return $rtn;
//
//		}


		/**
		 * Get SAFE beta promo
		 *         		 *
		 * @return array|false false on no notice
		 */
//		private function get_safe_beta_notice() {
//			$message= array();
//			$link_1=array();
//			$link_label_1=array();
//			$link_label_2=array();
//			$link_label_3=array();
//			$days_after=array();
//			$temp_days_after=array();
//
//			$id = $this->get_safe_beta_notice_id();
//			if (false===$id) return false;
//
//			$message[]=sprintf( "%s<p>%s<p>%s",
//				'<h2>' . esc_html__( "SAFE BETA - Black Friday/Cyber Monday Sale Starts Soon!", "wp-backitup") . ' </h2>',
//				__( "Save <b>30%</b> on WPBackItUp Premium for a limited time!", "wp-backitup" ),
//				__( "We just wanted to let you know that WPBackItUp will be participating in the Black Friday and Cyber Monday craziness next week.<br/><br/>If you purchase WPBackItUp Premium or upgrade an existing license between <b>Friday, November 24, 2017, 12 AM EST (5 AM UTC)</b> and <b>Monday, November 27, 2017, 11:59 PM EST ( 4:59 AM UTC )</b> you'll automatically get <b>30%</b> off our regular prices.", "wp-backitup" )
//			);
//			$days_after[]=0;
//			$temp_days_after[]=1;
//			$link_1[] =  "https://www.wpbackitup.com/pricing-purchase/?utm_medium=plugin&utm_source=wp-backitup&utm_campaign=plugin-black-friday-1";
//			$link_label_1[] =  esc_html__( 'Buy now, I don\'t need the discount!', 'wp-backitup' );
//			$link_label_2[] = esc_html__( 'Remind me later', 'wp-backitup' );
//			$link_label_3[] = esc_html__( 'I already purchased', 'wp-backitup' );
//
//
//			return array(
//				'message'=>$message[$id],
//				'days_after'=>$days_after[$id],
//				'temp_days_after'=>$temp_days_after[$id],
//				'link_1'=>$link_1[$id],
//				'link_label_1'=>$link_label_1[$id],
//				'link_label_2'=>$link_label_2[$id],
//				'link_label_3'=>$link_label_3[$id],
//			);
//
//		}


		/**
		 * Get Independence day promo
		 *
		 * @return array|false false on no notice
		 */
		private function get_independence_day_notice() {
			$message= array();
			$link_1=array();
			$link_label_1=array();
			$link_label_2=array();
			$link_label_3=array();
			$days_after=array();
			$temp_days_after=array();

			$id = $this->get_indepence_day_promo_id();
			if (false===$id) return false;

			$message[]=sprintf( "%s<p>%s<p>%s",
				'<h2>' . esc_html__( "Celebrate Independence Day with WPBackItUp and Save 30%!", "wp-backitup") . ' </h2>',
				__( "WPBackItUp would like to wish a happy Independence Day to all Americans!", "wp-backitup" ),
				__( "This week only purchases and upgrades of WPBackItUp Premium will automatically receive <b>30%</b> off our regular prices.", "wp-backitup" )
			);
			$days_after[]=0;
			$temp_days_after[]=1;
			$link_1[] =  "https://www.wpbackitup.com/pricing-purchase/?utm_medium=plugin&utm_source=wp-backitup&utm_campaign=plugin-independence-day-promo";
			$link_label_1[] =  esc_html__( 'Buy now!', 'wp-backitup' );
			$link_label_2[] = esc_html__( 'Remind me later', 'wp-backitup' );
			$link_label_3[] = esc_html__( 'I already purchased', 'wp-backitup' );


			return array(
				'message'=>$message[$id],
				'days_after'=>$days_after[$id],
				'temp_days_after'=>$temp_days_after[$id],
				'link_1'=>$link_1[$id],
				'link_label_1'=>$link_label_1[$id],
				'link_label_2'=>$link_label_2[$id],
				'link_label_3'=>$link_label_3[$id],
			);

		}

	}
}