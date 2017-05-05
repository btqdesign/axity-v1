<?php
/**
 * The template for Sending a Contact Message
 */

	require_once '../../../wp-load.php';
	define('WP_USE_THEMES', false);
	$subject = '';
	$cs_contact_error_msg = '';
	$subject_name = 'Subject';
	foreach ($_REQUEST as $keys=>$values) {
		$$keys = $values;
	}
	if(isset($phone) && $phone <> ''){
		$subject_name = 'Phone';
		 $subject = $phone;
	}
	
	$subjecteEmail = "(" . $bloginfo . ") Contact Form Received";
	$message = '
		<table width="100%" border="1">
		  <tr>
			<td width="100"><strong>Name:</strong></td>
			<td>'.$contact_name.'</td>
		  </tr>
		';
	if (isset($contact_lastname)) {
		$message .= '
		 <tr>
			<td width="100"><strong>Last Name:</strong></td>
			<td>'.$contact_lastname.'</td>
		  </tr>
		';
	}
	$message .= '
		  <tr>
			<td><strong>Email:</strong></td>
			<td>'.$contact_email.'</td>
		  </tr>
		  <tr>
			<td><strong>'.$subject_name.':</strong></td>
			<td>'.$subject.'</td>
		  </tr>
		  <tr>
			<td><strong>Message:</strong></td>
			<td>'.$contact_msg.'</td>
		  </tr>
		  <tr>
			<td><strong>IP Address:</strong></td>
			<td>'.$_SERVER["REMOTE_ADDR"].'</td>
		  </tr>
		</table>
		';
	$headers = "From: " . $contact_name . "\r\n";
	$headers .= "Reply-To: " . $contact_email . "\r\n";
	$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
	$headers .= "MIME-Version: 1.0" . "\r\n";
	$attachments = '';
	if(	wp_mail( sanitize_email($cs_contact_email), $subjecteEmail, $message, $headers, $attachments ) ) {
		$json	= array();
		$json['type']    = "success";
		$json['message'] = cs_textarea_filter($cs_contact_succ_msg);
	} else {
		$json['type']    = "error";
		$json['message'] = cs_textarea_filter($cs_contact_error_msg);
	};
	echo json_encode( $json );