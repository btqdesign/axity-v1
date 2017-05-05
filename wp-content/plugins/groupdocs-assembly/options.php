<?php

    	//  If the user does not have the required permissions...
    if (!current_user_can('manage_options')) {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    	// Get GroupDocs plug-in options from database.
    $userId     = get_option('userId');
    $privateKey = get_option('privateKey');
if (isset ($_POST['login']) && ($_POST['password'])) {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    include_once(dirname(__FILE__) . '/tree_viewer/lib/groupdocs-php/APIClient.php');
    include_once(dirname(__FILE__) . '/tree_viewer/lib/groupdocs-php/StorageApi.php');
    include_once(dirname(__FILE__) . '/tree_viewer/lib/groupdocs-php/GroupDocsRequestSigner.php');
    include_once(dirname(__FILE__) . '/tree_viewer/lib/groupdocs-php/FileStream.php');
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //Create signer object
    $signer = new GroupDocsRequestSigner("123");
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Creaet Shared object
    $shared = new SharedApi($apiClient);
    //Set base path
    $shared->setBasePath($basePath);
    //Set empty variable for result
    $result = "";
    //Login and get user data
    $userData = $shared->LoginUser($login, $password);
    //Check status
    if ($userData->status == "Ok") {
        //If status Ok get all user data
        $result = $userData->result->user;
        $privateKey = $result->pkey;
        $userId = $result->guid;
    } else {
        ?>
        <div class="updated"><p><strong><?php _e('Enter the correct Login and Password!', 'menu-test' ); ?></strong></p></div>
    <?php
    }

}

//  If data was posted to the page...
    if( isset($_POST['grpdocs_assembly_submit_hidden']) && $_POST['grpdocs_assembly_submit_hidden'] == 1) {
        //  Save the API key to the Options table.
		$userId     = trim($_POST['userId']);
		$privateKey = trim($_POST['privateKey']);

		update_option( 'userId', $userId);
		update_option( 'privateKey', $privateKey);

        // Display an 'updated' message.
		?>
		<div class="updated"><p><strong><?php _e('Settings saved!', 'menu-test' ); ?></strong></p></div>
		<?php
    }
?>

<div>

	<h2>GroupDocs Options</h2>

    <form name="form_assembly" method="post" action="">

        <h3>Login and Password</h3>
        <table>
            <tr><td>Login:</td>
                <td><input type="text" name="login" value="<?php echo $login; ?>"></td></tr>
            <tr><td>Password:</td>
                <td><input type="password" name="password" value="<?php echo $password; ?>"></td></tr>
        </table>

        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="Get User Id and Private Key" />
        </p>

    </form>

	<form name="form" method="post" action="">

		<input type="hidden" name="grpdocs_assembly_submit_hidden" value="1">

		<h3>API Settings</h3>
		<table>
		<tr><td>User Id:</td>
		<td><input type="text" name="userId" value="<?php echo $userId; ?>"></td></tr>
		<tr><td>Private Key:</td>
		<td><input type="text" name="privateKey" value="<?php echo $privateKey; ?>"></td></tr>
		</table>

		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>

	</form>

</div>
