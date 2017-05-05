<?php

class ns_cloner_section_copy_files extends ns_cloner_section {
	
	public $modes_supported = array('core','clone_over');
	public $id = 'copy_files';
	public $ui_priority = 500;
	
	function init(){
		parent::init();
		add_filter( 'ns_cloner_pipeline_steps', array($this,'remove_files_pipeline_step'), 1000 );
		add_filter( 'ns_cloner_search_items_after_sequence', array($this,'search_filter') );
		add_filter( 'ns_cloner_replace_items_after_sequence', array($this,'replace_filter') );
	}
	
	function render(){
		$this->open_section_box( $this->id, __('Copy Media Files','ns-cloner'), '', __('Copy Media Files','ns-cloner') );
		?>

		<label>
			<input type="checkbox" name="do_copy_files" checked /> <?php _e('Copy all uploads/media files from the source site to the target site\'s uploads directory','ns-cloner'); ?>
		</label>
		<label>
			<input type="checkbox" name="post_types_to_clone[]" value="attachment" checked />
			<span class="disabled-description">[DISABLED BY CONTENT CONTROLS ABOVE]</span>
			<?php _e('Copy all media library posts and details to the target site\'s database','ns-cloner'); ?>
		</label>
		<label>
			<input type="checkbox" name="do_replace_file_urls" checked /> <?php _e('Replace links to uploads/media in site content with new site url','ns-cloner'); ?>
		</label>
		<p class="description">
			<?php _e('Generally, you should either have all of these checked (if you want new copies of all files to be set up for target site) or all unchecked (if you want upload/media links on the target site to point back to the source site files).','ns-cloner'); ?>
			<?php _e('These options are here to give you more flexibily; for example to avoid a timeout on a site with many uploads you could uncheck "Copy all files" above but leave the other two checked and just manually copy the files via FTP.'); ?>
		</p>
		
		<?php
		$this->close_section_box();
	}

	function remove_files_pipeline_step( $steps ){
		if( !isset($this->cloner->request['do_copy_files']) ){
			unset( $steps["copy_files"] );
		}
		return $steps;
	}
	
	function search_filter( $search ){
		if( !isset($this->cloner->request['do_replace_file_urls']) ){
			unset( $search[ array_search($this->cloner->source_upload_dir_relative,$search) ]);
			unset( $search[ array_search($this->cloner->source_upload_url,$search) ]);
		}
		return $search;
	}

	function replace_filter( $replace ){
		if( !isset($this->cloner->request['do_replace_file_urls']) ){
			unset( $replace[ array_search($this->cloner->target_upload_dir_relative,$replace) ]);
			unset( $replace[ array_search($this->cloner->target_upload_url,$replace) ]);
		}
		return $replace;
		
	}
	
}
