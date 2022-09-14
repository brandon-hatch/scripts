<?php
$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');

global $wpdb;

//populate array on terms to keep
$terms_to_keep = [
"Best of - Service",
"Best of - Product",
"Review - Service",
"Review - Product",
"VS - Product",
"VS - Service",
"Outreach",
"Core Page",
"Landing Page",
"Testing - Service",
"Testing - Product",
"Guide",
"FAQ",
"Deals",
"Bundle",
"How to Watch",
"How-to",
"Dream Job",
"Giveaways",
"Not Working",
"video-post"];

// turn array to string, add last and first '
$terms_to_keep = implode("','",$terms_to_keep);
$terms_to_keep = "'" . $terms_to_keep;
$terms_to_keep = $terms_to_keep .= "'";

// QUERY 1
// Get the ids of terms we want to keep
$select_ids = $wpdb->get_results(
  "SELECT term_id
  FROM `wp_terms`
  WHERE name IN ($terms_to_keep)"
  , ARRAY_A
);

foreach($select_ids as $term_id) {
  $keep_ids = "'" . $term_id['term_id'] . "', " . $keep_ids;
}

$keep_ids = substr($keep_ids, 0, -2);

// QUERY 2
//Find IDs of terms we want to delete
$select_ids = $wpdb->get_results(
  "SELECT term_id
  FROM `wp_terms`
  WHERE term_id IN 
    (SELECT term_id
    FROM wp_term_taxonomy
    WHERE taxonomy = 'post_tag' AND term_id NOT IN ($keep_ids)
    )"
  , ARRAY_A
);

foreach($select_ids as $term_id) {
  $all_other_terms = $term_id['term_id'] . ', ' . $all_other_terms;
}
$all_other_terms = substr($all_other_terms, 0, -2);

echo "Refresh until there are no more IDs listed here:  ";
echo ($all_other_terms);

$integerIDs = array_map('intval', explode(' ', $all_other_terms));

//use wp_delete_term() function to remove from database
foreach($integerIDs as $id) {
    wp_delete_term( $id, 'post_tag' );
}

?>
