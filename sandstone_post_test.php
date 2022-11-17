<?php

$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');


$data1 = apply_filters('clwp/sandstone/tokens', '')['production_token'];
$data2 = apply_filters('clwp/sandstone/tokens', '')['staging_token'];

echo("Key Used (staging token): ");
echo('<br>');
echo('<pre>');
print_r($data2);
echo('</pre>');

echo("Sandstone URL: ");
echo('<br>');
echo(SANDSTONE_URL);
echo('<br>');
echo('<br>');


$ch = curl_init();
// we're updating a speed test with the upload speed
$_POST['upload_speed'] = floatval("2.8.13");
$_POST['testID'] = floatval(20869);
$_POST['date_submitted'] = date('c');
$_POST['site'] = strval(apply_filters('clwp/child-site/short-name', ''));
$_POST['download_speed'] = floatval("2.813");
$_POST['latency'] = floatval("52");
$_POST['jitter'] = floatval("406");

curl_setopt($ch, CURLOPT_URL, SANDSTONE_URL."speed-test/{$_POST['testID']}");


echo("CURL URL: ");
echo('<br>');
echo(SANDSTONE_URL."speed-test/{$_POST['testID']}");
echo('<br>');
echo('<br>');

$json = json_encode($_POST);

echo("CURL JSON Sent: ");
echo('<br>');
echo($json);
echo('<br>');
echo('<br>');

$headers = array();
$headers[] = "Content-Type: application/json";
$headers[] = 'Authorization: Bearer '.SANDSTONE_TOKEN;
$headers[] = 'accept: application/json';

echo("HEADERS used: ");
echo('<br>');
echo('<pre>');
print_r($headers);
echo('</pre>');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


$result = curl_exec($ch);
echo("RESPONSE: ");
echo('<br>');
echo($result);
echo('<br>');
echo('<br>');


curl_close($ch);


