<?php

class ns_cloner_section_copy_users extends ns_cloner_section {
	
	public $modes_supported = array('core','clone_over');
	public $id = 'copy_users';
	public $ui_priority = 400;
	
	function init(){
		parent::init();
		add_filter( 'ns_cloner_pipeline_steps', array($this,'register_users_pipeline_step'), 210 );
	}
	
	function render(){
		$this->open_section_box( $this->id, __('Copy Users','ns-cloner'), '', __('Copy Users','ns-cloner') );
		?>
		
		<h5><?php _e('Create New Admin(s)','ns-cloner'); ?></h5>
		<ul class="ns-repeater">
			<li>
			<input type="text" name="new_users[usernames][]" placeholder="username"/>
			<input type="text" name="new_users[emails][]" placeholder="email@email.com"/>
			<span class="ns-repeater-remove" title="remove">-</span>
			
			</li>
		</ul>		
		<input type="button" class="button ns-repeater-add" value="<?php _e('Add Another','ns-cloner'); ?>" />
		
		<h5><?php _e('Notify New Users'); ?></h5>
		<label>
			<input type="checkbox" name="do_user_notify" checked/> <?php _e('Send welcome email (configured in <a href="/wp-admin/network/settings.php" target="_blank">Network Settings</a>) to new users with their username and password.','ns-cloner'); ?>
		</label>
		
		<h5><?php _e('Copy Existing Users'); ?></h5>
		<label>
			<input type="checkbox" name="do_copy_users" /> <?php _e('Add all users of source site to target site as well','ns-cloner'); ?>
		</label>
		
		<?php
		$this->close_section_box();
	}
	
	function validate( $errors ){
		$new_users = $this->cloner->request['new_users'];
		foreach( $new_users['usernames'] as $index=>$username ){
			$email = $new_users['emails'][$index];
			// skip any double blanks
			if( empty($username) && empty($email) ) continue;
			// use wp's built in wpmu_validate_user_signup validation for all new site vars (reverse so when prepended they'll be in the right order)
			$wp_validation = wpmu_validate_user_signup( $username, $email );
			$user_errors = array_reverse( $wp_validation['errors']->get_error_messages() );
			if( !empty($user_errors )){
				$errors[] = array(
					'message' => sprintf(__('Username "%s" and Email "%s":  ','ns-cloner'),$username,$email) . join(' ',$user_errors),
					'section' => $this->id
				);
			}
		}
		return $errors;
	}

	function register_users_pipeline_step( $steps ){
		$steps["set_up_users"] = array($this,"set_up_users");
		return $steps;
	}
	
	function set_up_users(){
		$count_users_added = 1;
		// Add existing users
		if( isset($this->cloner->request['do_copy_users']) ){
			$users = get_users( array( 'blog_id'=>$this->cloner->source_id, 'fields'=>'all_with_meta' ) );
			foreach( $users as $user ){
				$target_ids = $this->cloner->current_clone_mode == 'clone_over'? $this->cloner->request['clone_over_target_ids'] : array($this->cloner->target_id);
				foreach( $target_ids as $target_id ){
					ns_wp_add_user( $target_id, $user->user_email, $user->user_login, $user->user_pass, $user->roles[0], NS_CLONER_LOG_FILE_DETAILED );
				}
				$count_users_added++;
			}
		}
		// Create any new admins specified
		if( !empty($this->cloner->request['new_users']['usernames']) ){
			$admins_to_add = array_combine( $this->cloner->request['new_users']['usernames'], $this->cloner->request['new_users']['emails'] );
			foreach( $admins_to_add as $username=>$email ){
				if( empty($username) && empty($email) ) continue;
				if( true === ns_wp_add_user( $this->cloner->target_id, sanitize_email($email), $username, '', 'administrator', NS_CLONER_LOG_FILE_DETAILED ) ){
					$count_users_added++;
				}
			}
		}
		$this->cloner->dlog( "Cloned or added $count_users_added users (including admin running the clone operation)" );
		$this->cloner->report[ __('Users cloned/added','ns-cloner') ] = $count_users_added;
	}
	
}
