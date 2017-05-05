<?php

class ns_cloner_section_clone_over extends ns_cloner_section {
	
	public $modes_supported = array('clone_over');
	public $id = 'clone_over';
	public $ui_priority = 100;
	
	function init(){
		parent::init();		
		// register core clone and copy steps - this func will only be called if we're in clone_over mode so no need to check
		add_filter( 'ns_cloner_pipeline_steps', array($this,'register_tables_pipeline_step'), 200 );		
		add_filter( 'ns_cloner_pipeline_steps', array($this,'register_files_pipeline_step'), 300 );
		// add title update since core won't take care of that
		add_action( 'ns_cloner_after_clone_over_tables', array($this,'update_clone_over_title') );
	}
	
	function render(){
		$this->open_section_box( $this->id, __('Select Target Site(s) to Clone Over','ns-cloner') );
		?>
		
		<label for="clone_over_target_title"><?php _e('Give the new, cloned-over site(s) a title'); ?></label>
		<input type="text" name="clone_over_target_title" /><br/>
		<p class="description"><?php _e('To use the source site title, leave this blank.','ns-cloner'); ?></p>
		
		<label for="clone_over_target_ids"><?php _e('Pick an existing site or sites to clone over top of'); ?></label>
		<select name="clone_over_target_ids[]" multiple>
		  <?php $sites = function_exists('wp_get_sites')? wp_get_sites(array('limit'=>9999)) : get_blog_list(0,'all'); ?>
		  <?php foreach( $sites as $site ): ?>
			<option value="<?php echo $site['blog_id']; ?>">
				<?php $title = get_blog_details($site['blog_id'])->blogname; ?>
				<?php $url = is_subdomain_install()? "$site[domain]" : "$site[domain]$site[path]"; ?>
				<?php echo "$site[blog_id] - ".substr($title,0,30)." ($url)"; ?>
		  <?php endforeach; ?>
		</select>
		<p class="description"><?php _e( 'You can select multiple sites by pressing ctrl/&#8984; while clicking. Select all by clicking on one, then pressing ctrl/&#8984 + a.', 'ns-cloner' ); ?></p>
		
		<label><?php _e('Or, manually enter a site id to clone over top of'); ?></label>
		<input name="clone_over_target_ids[]" type="text" />
		<p class="description"><?php _e( 'Any id entered here will be targeted in addition to any selected above via the checkboxes.','ns-cloner' ); ?></p>

		<p class="description"><strong><?php _e('Please make sure you have backups of database and files as the sites you select will be permanently overwritten when you click the "Clone Over" button!'); ?></strong></p>
		
		<?php
		$this->close_section_box();
	}
	
	function validate( $errors ){
		$target_ids = isset($this->cloner->request['clone_over_target_ids'])? array_filter($this->cloner->request['clone_over_target_ids']) : array();
		if( empty($target_ids) ){
			$errors[] = array('message'=>__('Please select at least one target site.','ns-cloner'),'section'=>$this->id);
		}
		elseif( !get_blog_details(array_pop($this->cloner->request['clone_over_target_ids'])) ){
			$errors[] = array('message'=>__('It appears you entered an invalid target id.','ns-cloner'),'section'=>$this->id);
		}
		return $errors;
	}
	
	function register_tables_pipeline_step( $steps ){
		$steps['clone_over_tables'] = array($this,'clone_over_tables');
		return $steps; 
	}
	
	function register_files_pipeline_step( $steps ){
		if( $this->cloner->request['do_copy_files'] ){
			$steps['copy_over_files'] = array($this,'copy_over_files');	
		}
		return $steps;
	}	
	
	function clone_over_tables(){
		$source_id = $this->cloner->request['source_id'];
		foreach( $this->cloner->request['clone_over_target_ids'] as $target_id ){
			$this->cloner->dlog_break();
			$this->cloner->dlog( "Starting tables clone over for site $target_id" );
			$this->cloner->dlog_break();
			$this->cloner->set_up_vars( $source_id, $target_id );
			// enable title optional title replacement for cloned over sites (no replacement if new title left blank)
			if( !empty($this->cloner->request['clone_over_target_title']) ){
				$this->cloner->dlog( "Overwriting target title for clone over mode with user-provided title: ".$this->cloner->request['clone_over_target_title'] );
				$this->cloner->target_title = $this->cloner->request['clone_over_target_title'];
			}
			$this->cloner->clone_tables();
		}
	}
	
	function copy_over_files(){
		$source_id = $this->cloner->request['source_id'];
		foreach( $this->cloner->request['clone_over_target_ids'] as $target_id ){
			$this->cloner->dlog_break();
			$this->cloner->dlog( "Starting files clone over for site $target_id" );
			$this->cloner->dlog_break();
			$this->cloner->set_up_vars( $source_id, $target_id );
			$this->cloner->copy_files();
		}
	}
	
	function update_clone_over_title(){
		$title = $this->cloner->request['clone_over_target_title'];
		if( !empty($title) ){
			foreach( $this->cloner->request['clone_over_target_ids'] as $target_id ){
				update_blog_option( $target_id, 'blogname', $title );
			}
		}
	}
	
}
