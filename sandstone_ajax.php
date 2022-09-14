<?php

$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');


    //$zip = $_GET['zip'];
    $zip = 84116;
    $redirect = $_GET['redirect'];

    $validateZipCode = '{
      validateZipCode(zipCode:"' . $zip . '"){
        cities{
          city
          state
        }
      }
    }';

    $data = wp_remote_post(SANDSTONE_GRAPHQL, [
      "headers" => SANDSTONE_HEADERS, 
      'body' => wp_json_encode([
        'query' => $validateZipCode,
      ])
    ]);

    $data = json_decode($data['body'], true);

    // if ($data['data']){
    //   $cities = $data['data']['validateZipCode']['cities'];

    //   // Get city name
    //   $userType = 'RESIDENTIAL';
    //   $city = $cities[0]['city'];
    //   $state = $cities[0]['state'];
    //   $getCityData = '{
    //     getCityOverview('  . 'userType: ' . $userType . ', citySlug:  "' . $city . '", stateAbbreviation: "' . $state . '"){
    //         name
    //         state
    //     }
    //   }'; 
      

    //   $city_data = wp_remote_post(SANDSTONE_GRAPHQL, [
    //     "headers" => SANDSTONE_HEADERS, 
    //     'body' => wp_json_encode([
    //       'query' => $getCityData,
    //     ])
    //   ]);

    //   $city_data = json_decode($city_data['body'], true);
    //   $city_name = $city_data['data']['getCityOverview']['name'];
    // }

    $cities = $data['data']['validateZipCode']['cities'][0];


    echo('<pre>');
      print_r($cities);
    echo('</pre>');
