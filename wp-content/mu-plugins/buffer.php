<?php

/**
 * Output Buffering
 *
 * Buffers the entire WP process, capturing the final output for manipulation.
 */

ob_start();

add_action(
	'shutdown', 
	function() {
	    $final = '';
	
	    // We'll need to get the number of ob levels we're in, so that we can iterate over each, collecting
	    // that buffer's output into the final output.
	    $levels = ob_get_level();
	
	    for ($i = 0; $i < $levels; $i++)
	    {
	        $final .= ob_get_clean();
	    }
	
	    // Apply any filters to the final output
		echo apply_filters('final_output', $final);
	}, 
	0
);

add_filter('final_output', function($output) {
    $patt = array(
        //'/((src|href|action|srcset|xmlns)=("|\'))(\s*)(https?)(:)(\/\/)/'
        //,'/(url\(("|\'))(https?)(:)(\/\/)/'
        //,'/(&quot;)(https?)(:)(\\\\\/\\\\\/)/'
        //,'/("|\')(https?)(:)(\\\\\/\\\\\/)/'
        //,'/(,\s*("|\')?)(https?)(:)(\/\/)/'
        '/(("|\')https?:\/\/((axity\.idevol\.net)|(www\.axity\.com))\/[a-zA-Z0-9\._\/-]*\.(css|js))(\?(ver|version)=[a-zA-Z0-9%_.-]{1,8})("|\')/'
    );
    $repl = array(
        //'${1}${7}'
        //,'${1}${5}'
        //,'${1}${4}'
        //,'${1}${4}'
        //,'${1}${5}'
        '${1}${9}'
    );
    $output = preg_replace($patt, $repl, $output);
    
    // Soporte HTTPS
    $output = str_replace('http:', 'https:', $output);
    $output = str_replace('https://schemas.xmlsoap.org', 'http://schemas.xmlsoap.org', $output);
    $output = str_replace('https://docs.oasisopen.org', 'http://docs.oasisopen.org', $output);
    $output = str_replace('https://www.sitemaps.org', 'http://www.sitemaps.org', $output);
    
    return $output;
});

add_filter('wp_redirect', function($output) {
	$output = str_replace('http://axity.idevol.net', 'https://axity.idevol.net', $output);
	$output = str_replace('http://www.axity.com', 'https://www.axity.com', $output);
	  
	return $output;
});
