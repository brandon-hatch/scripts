<?php

$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');

if(!current_user_can('edit_posts')) {
  exit;
}

$args = [
  'numberposts' => -1,
  'lang' => 'en, au',
  'post_status' => [
    'publish', 
    'draft', 
    'private', 
    'inherit', 
    'future', 
    'tao_sc_publish'
  ],
];

$posts_array = get_posts($args);

foreach ($posts_array as $key => $value) {
  $post_meta = get_post_meta($value->ID, 'schema_options', true);

  $new_meta_value = [
    'article-markup'
  ];

  $final_meta_data = array_merge($post_meta, $new_meta_value);

  update_post_meta($value->ID, 'schema_options', $final_meta_data);

}

echo("Done!");
