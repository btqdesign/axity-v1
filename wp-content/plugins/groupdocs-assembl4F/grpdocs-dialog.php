<?php
// access wp functions externally
require_once('bootstrap.php');

ini_set('display_errors', '0');
error_reporting(E_ALL | E_STRICT);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>GroupDocs Assembly</title>
	<script type="text/javascript" src="../../../wp-includes/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="js/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/grpdocs-dialog.js"></script>
	
	<link href="css/grpdocs-dialog.css" type="text/css" rel="stylesheet" />

</head>
<body>
<form id='form' onsubmit="" method="post" action="" enctype="multipart/form-data">
		
<table>  
  <tr>
    <td align="right" class="gray dwl_gray"><strong>Height</strong></td>
    <td valign="top" style="width:200px;"><input name="height" type="text" class="opt dwl" id="height" size="6" style="text-align:right" value="700" />px</td>
  </tr>
  <tr>
    <td align="right" class="gray dwl_gray"><strong>Width</strong></td>
    <td valign="top"><input name="width" type="text" class="opt dwl" id="width" size="6" style="text-align:right" value="600" />px</td>
  </tr>
</table>


<div class="section">
	
<ul class="tabs">
	<li>Paste Form ID</li>
</ul>

<div class="box visible">
 
  <strong>Form ID</strong><br />
  <input name="url" type="text" class="opt dwl" id="url" style="width:200px;" /><br/>
  <span id="uri-note"></span>
  
</div>
</div><!-- .section -->
	
<fieldset>
   <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
    <td colspan="2">
    <br />
    Shortcode Preview
    <textarea name="shortcode" cols="72" rows="2" id="shortcode"></textarea>
    </td>
	</tr>
   </table>
</fieldset>
	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="insert" name="insert" value="Insert" onclick="GrpdocsInsertDialog.insert();" />
			
		</div>

		<div style="float: right">
			<input type="button"  id="cancel" name="cancel" value="Cancel" onclick="tinyMCEPopup.close();"/>
		</div>
	</div>
</form>

</body>
</html>
