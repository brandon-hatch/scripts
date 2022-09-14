<?php
$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');

global $wpdb;

// $posttaxonomies = get_object_taxonomies(157721);
$posttaxonomies = get_object_taxonomies(73225);

//var_dump($posttaxonomies);

$postmeta = get_post_meta(157721);
//print_r($postmeta);

//var_dump();

$query = new WP_Query(array(
  'post_id' => 157721)
);

//$search = (new SearchExclude)-> searchFilter($query);

//var_dump($search);


// $options = (new SearchExclude) -> getExcluded();

// var_dump($options);

// echo("<br><br><br><br>");

$exclude = get_option('sep_exclude');

var_dump($exclude);

//SearchExclude::getExcluded();
echo("<br><br>");

if (in_array(128137,$exclude)){
  echo("in array");

}else{
  echo("not in");
}

echo("<br><br>");

$posts = \CLWP::get_author_posts(465);

// $diff = array_diff($posts, $exclude);

// var_dump($posts);

foreach ($posts as $post){
  echo($post->ID);
  if (in_array($post->ID,$exclude)){
    echo("in array");
  
  }else{
    echo("not in");
  }
  echo("<br><br>");
}

// $foo = $posts.array_filter($posts, )


?>