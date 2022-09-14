# scripts
scripts I use for testing clwp and child tasks

https://www.DOMAIN.com/app/themes/coolwhip-child/scripts/one-time-script.php

$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');

if(!current_user_can('edit_posts')) {
  exit;
}
