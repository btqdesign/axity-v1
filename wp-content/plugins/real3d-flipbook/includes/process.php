
<!DOCTYPE html>
<html>
<body>

<?php

if (isset($_POST["imgbase"]) && !empty($_POST["imgbase"])) {

    // get the image data
    $data = $_POST['imgbase'];
    $bookName = $_POST['bookName'];

    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);

	$booksFolder = "../books/";
	if (!file_exists($booksFolder)) {
		mkdir($booksFolder, 0777, true);
	}
	
	$bookFolder = "../books/".$bookName;
	if (!file_exists($bookFolder)) {
		mkdir($bookFolder, 0777, true);
	}
	
	
    $filename = $bookFolder."/".$_POST['pageName'].".jpg";
    // $thumbName = $bookFolder."/".$_POST['pageName']."_thumb.jpg";

    $file = $filename;

    // decode the image data and save it to file
	if(!file_put_contents($file, $data)){
		echo "failed";
	}else{
		echo "success";
	}
    // echo $file;
	
	// $image = wp_get_image_editor( $file  ); // Return an implementation that extends <tt>WP_Image_Editor</tt>

	// echo("is_wp_error( $image )");
	// echo(is_wp_error( $image ));
// if ( ! is_wp_error( $image ) ) {
    // $image->rotate( 90 );
    // $image->resize( 300, 300, true );
    // $image->save( $thumbName );
// }

}

if (isset($_POST["deleteBook"]) && !empty($_POST["deleteBook"])) {
	$bookName = $_POST['deleteBook'];
	$dirPath = "../books/".$bookName;
	
	if (! is_dir($dirPath)) {
		throw new InvalidArgumentException("$dirPath must be a directory");
	}
	if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
		$dirPath .= '/';
	}
	$files = glob($dirPath . '*', GLOB_MARK);
	foreach ($files as $file) {
		if (is_dir($file)) {
			self::deleteDir($file);
		} else {
			unlink($file);
		}
	}
	rmdir($dirPath);
}


?>

</body>
</html>