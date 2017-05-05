<?php
/**
 * 
 * @file utility.php Utility information about the plugin
 * 
 * */

// Security Check
if( !defined( 'ABSPATH' ) ) die();
global $FileManager;
?>
<?php

?>
<table>
	
	<tr>
		<td>PHP version</td>
		<td><?= phpversion(); ?></td>
	</tr>
	
	<tr>
		<td>Maximum file upload size</td>
		<td><?= ini_get('upload_max_filesize'); ?></td>
	</tr>

	<tr>
		<td>Post maximum file upload size</td>
		<td><?= ini_get('post_max_size'); ?></td>
	</tr>
	
	<tr>
		<td>Memory Limit</td>
		<td><?= ini_get('memory_limit'); ?></td>
	</tr>
	
	<tr>
		<td>Timeout</td>
		<td><?= ini_get('max_execution_time'); ?></td>
	</tr>
	
	<tr>
		<td>Browser and OS</td>
		<td><?= $_SERVER['HTTP_USER_AGENT']; ?></td>
	</tr>
	
</table>
