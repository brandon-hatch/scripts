<?php

$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');
echo("YEET"); //test


use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;

// $filePath = (get_template_directory().'coolwhip/assets/images/brands/TEST-LOGO-PNG.png');
$filePath = ('./test-logo-svg.svg');

// Set the hostname for the AWS SFTP server
$hostname = '';

// Load the public key for the server
$key = PublicKeyLoader::load(file_get_contents('./ftp')); // get this file from clwp

$public_key = '' // get this from clwp

// Connect to the SFTP server using the public key
$sftp = new SFTP($public_key);

if (!$sftp->login('logoUploader', $key)) {
  echo('Login failed');
} else {
  echo('woohoo');

  //upload the images (uncomment to use it)
  try {
    $uploadIt = $sftp->put('/', './logo-test.svg', SFTP::SOURCE_LOCAL_FILE);
    $uploadIt = $sftp->put('/', './logo-test.jpg', SFTP::SOURCE_LOCAL_FILE);
  } catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    echo $sftp->getLastSFTPError();
  }
}
