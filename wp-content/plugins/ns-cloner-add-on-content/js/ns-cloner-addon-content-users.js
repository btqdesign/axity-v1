(function($){
  $(function(){
  	
  	// hide the correct option in the clone over target selector so the same site can't be both source and target
  	$('.ns-cloner-form').on('ns_cloner_source_refresh',function(){
  		var $source_selector = $('select[name=source_id]');
  		var $target_selector = $('select[name^=clone_over_target_ids]');
  		var $target_item_same_as_source = $target_selector.find('option[value='+$source_selector.val()+']');
  		// add back in a hidden option if one had been previously removed since it's obviously no longer selected in the source
  		if( $target_selector.data('removed_option') ){
  			// make sure if the insert position is not greater than the length of the list (would happen if it was the last item before being removed)
  			var insert_position = Math.max( $target_selector.data('removed_option_index'), $target_selector.children().length-1 );
  			$target_selector.children().eq( insert_position ).after( $target_selector.data('removed_option') );
  		} 
  		// remove the option that is the same as source (doing display:none on <option> doesn't work cross browser)
  		$target_selector.data('removed_option',$target_item_same_as_source.clone());
  		$target_selector.data('removed_option_index',$target_item_same_as_source.index());
  		$target_item_same_as_source.remove();
  	});
  	
  	// show list of post types for the currently selected source site (refresh on changing source)
	$('.ns-cloner-form').on('ns_cloner_source_refresh',function(){
  		$source_selector = $('select[name=source_id]');
  		$.get(
  			ns_cloner.ajaxurl,
  			{
				action: 'nsc_content_get_post_types',
  				nonce: ns_cloner.nonce,
  				source_id: $source_selector.val()
  			},
  			function(result){
  				$checkbox_wrapper = $('.ns-cloner-select-posttypes-control');
  				$checkbox_wrapper.children().slideUp(function(){ $(this).remove(); });
  				// output all post types as options, checked by default
  				$.each( result.post_types, function(post_type,label){
  					if( post_type=='attachment') return; // skip attachments from this ui since they are controlled by a checkbox in the copy files section
  					$checkbox_wrapper.append( '<label><input type="checkbox" value="'+post_type+'" name="post_types_to_clone[]" checked />'+label+'</label>' );
  				});
  			}  			
  		);	
	});
	
	// show hide the 'do_copy_posts' controls so they for clone over mode
	$('.ns-cloner-form').on('ns_cloner_form_refresh',function(){
  		var $mode_selector = $('.ns-cloner-select-mode');
  		if( $mode_selector.val()=='clone_over' ){
  			$('.ns-cloner-select-posttypes-control').prevUntil(':not(label)').show();
  		}
  		else{
  			$('.ns-cloner-select-posttypes-control').prevUntil(':not(label)').hide();
  		}
	});
	
	// grey/disable the post type selection and posts/postmeta table selection when 'do_copy_posts' is turned off
	$('input[name=do_copy_posts]').on('change',function(){
		if( $(this).val()=='0' ){
			$('.ns-cloner-select-tables-control').find('input').filter( function(){return $(this).val().match(/(posts|postmeta|comments|commentmeta|term_relationships|term_taxonomy|terms)$/);} ).attr('disabled','').removeAttr('checked');
			$('.ns-cloner-select-posttypes-control').css({opacity:0.75});
			$('[name="post_types_to_clone[]"]').attr('disabled','').removeAttr('checked');
		}
		else {
			$('.ns-cloner-select-tables-control').find('input').filter( function(){return $(this).val().match(/(posts|postmeta|comments|commentmeta|term_relationships|term_taxonomy|terms)$/);} ).removeAttr('disabled').attr('checked','');
			$('.ns-cloner-select-posttypes-control').css({opacity:1});
			$('[name="post_types_to_clone[]"]').removeAttr('disabled').attr('checked','');
		}
	});
  	
  });
})(jQuery);
