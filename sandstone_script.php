<?php

$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');


  define("SANDSTONE_URL", "https://sandstone-backend-stage.herokuapp.com/graphql");
  $SANDSTONE_GRAPHQL = "https://sandstone-backend-stage.herokuapp.com/graphql";
  $SANDSTONE_HEADERS = [
    'Content-Type' => 'application/json',
    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOiJhYzE2NGY0MC1jYmU3LTRiNTUtOTVlMS01ZTY5ZjQ2YzdiZjgiLCJyZWFkIjp0cnVlLCJ3cml0ZSI6dHJ1ZSwiYWRtaW4iOnRydWUsImlhdCI6MTYyODg2NTcwOH0.bfiwVWHJo7-fhUuuwwjXuGwOxlPb2jm5hgFFgzDOuQY',
  ];
  $SANDSTONE_TOKEN = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOiJhYzE2NGY0MC1jYmU3LTRiNTUtOTVlMS01ZTY5ZjQ2YzdiZjgiLCJyZWFkIjp0cnVlLCJ3cml0ZSI6dHJ1ZSwiYWRtaW4iOnRydWUsImlhdCI6MTYyODg2NTcwOH0.bfiwVWHJo7-fhUuuwwjXuGwOxlPb2jm5hgFFgzDOuQY";
    
$validateZipCode = '{
      validateZipCode(zipCode:"9882"){
        cities{
          city
          state
          }
        }
        }';
  // $data = wp_remote_post(SANDSTONE_GRAPHQL, ["headers" => SANDSTONE_HEADERS, 'body' => wp_json_encode([
    $data = wp_remote_post($SANDSTONE_GRAPHQL, ["headers" => $SANDSTONE_HEADERS, 'body' => wp_json_encode([
        'query' => $validateZipCode,
      ])
    ]);

  // if( !is_wp_error($data) ) {
  $data = json_decode($data['body'], true);
  echo '<pre>';
  var_dump($data);
  echo'<br><br><br>';
  echo '</pre>';

  $zip = '98682';

  $results = array([
    'success' => false,
    'zip_code' => $zip,
    'locations' => [],
  ]);

  if ($data['data']){
    echo("yes");
    $cities = $data['data']['validateZipCode']['cities'];
    $results = array([
      'success' => true,
      'zip_code' => $zip,
      'locations' => $cities
    ]);
  }

  echo '<pre>';
  var_dump($results);
  echo'<br><br><br>';
  echo '</pre>';

  // }
  // else {
  //   return wp_send_json_error();
  // }


?>