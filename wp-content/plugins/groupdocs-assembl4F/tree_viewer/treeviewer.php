<?php
// access wp functions externally
require_once('../bootstrap.php');

if( empty($_GET['private_key']) || empty($_GET['user_id']) ) {
	echo "ERROR: No private key and/or user id";
	exit();
}

	include_once(dirname(__FILE__) . '/lib/groupdocs-php/APIClient.php');
    include_once(dirname(__FILE__) . '/lib/groupdocs-php/StorageApi.php');
    include_once(dirname(__FILE__) . '/lib/groupdocs-php/GroupDocsRequestSigner.php');

    $path = $_POST['dir'];
    if ($path == NULL || $path == "/") {
        $path = "";
    } 

    $private_key = $_GET['private_key'];
    $user_id = $_GET['user_id'];
    

    $signer = new GroupDocsRequestSigner($private_key);
    $apiClient = new APIClient($signer);
    $api = new StorageApi($apiClient);
    
    $cur_path = substr($path, 0, strlen($path)-1);
    
  
    try {
        $result = $api->ListEntities($user_id, $cur_path);
        
        $files = $result->result->files;
        $folders = $result->result->folders;
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    print("<ul class=\"jqueryFileTree\" style=\"display: ;\">");
    if(!empty($folders)){
        foreach ($folders as $item) {
                print("<li class=\"directory collapsed\"><a href=\"#\" rel=\"" .
                          $path . $item->name . "/\">" . $item->name . "</a></li>");
        }
    }
    if(!empty($files)){
        foreach ($files as $item) {
                $href = $item->guid;
                print("<li class=\"file ext_" . strtolower($item->file_type) . "\"><a class='iframe' href='" . $href . "' rel=\"" .
                            $item->guid . "\">" . $item->name . "</a></li>");
        }
    }
    print("</ul>");
