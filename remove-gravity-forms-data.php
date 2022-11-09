<?php

$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');

if(!current_user_can('edit_posts')) {
  exit;
}

echo '<body style="background-color:grey">';

// Run this on localhost:1337/app/themes/coolwhip-child/scripts/remove-gravity-forms-data.php
// Check localhost:1338 wordpress db and make sure all the gf tables are gone
// Add existing tables to $table_names below

global $wpdb;

$table_names = [
  "wp_gf_draft_submissions",
  "wp_gf_entry",
  "wp_gf_entry_meta",
  "wp_gf_entry_notes",
  "wp_gf_form",
  "wp_gf_form_meta",
  "wp_gf_form_revisions",
  "wp_gf_form_view"
];

foreach ($table_names as $table_name) {
  $sql = "DROP TABLE IF EXISTS $table_name;";
  $wpdb->query($sql);
}

//Let us know when it is finished 
echo "<br>
<h1 style='margin-top: 0px;
margin-bottom: 50px;
text-align: center;
font-family: sans-serif;
font-size: 3rem;
letter-spacing: 0.15rem;
text-transform: uppercase;
color: #fff;
text-shadow: -4px 4px #ef3550,
             -6px 6px #f48fb1,
             -12px 12px #7e57c2,
             -16px 16px #2196f3,
             -20px 20px #26c6da,
             -24px 24px #43a047,
             -28px 28px #eeff41,
             -32px 32px #f9a825,
             -36px 36px #ff5722;'
> SCRIPT COMPLETED </h1>";
