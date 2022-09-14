<?php 

$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');
//index editors with meta tag 'editor'

global $wpdb;

global $wp_query;

// foreach($_POST['contributors'] as $i => $id) {
        
//     update_user_meta( $id, 'title_role', 'Editor');
//   }
//add_filter( 'update_user_metadata', 120, 'title_role', 'Editor' );
//$meta_value = get_user_meta(120, 'title_role', false);
//$meta_value = 'Editor';
update_user_meta( 98, 'title_role', '');
// var_dump(get_user_meta(98, 'title_role', false));
echo "<br>";

// $users_with_editor_meta = $wpdb->get_results(
//   'SELECT user_nicename 
//   FROM `wp_users` as a
//   INNER JOIN wp_usermeta as b
//   ON a.ID = b.user_id
//   WHERE b.meta_value = "Editor" && b.meta_key= "title_role"'
//   , ARRAY_A
// );
// foreach ($users_with_editor_meta as $user){
//   $name = $user['user_nicename'];
//   $url = substr(get_site_url(),0,-2);
//   echo $url . 'author/' . $name;
//   echo "<br>";
// }


$post = get_post(46417)->ID;
var_dump($post);
var_dump(get_post_meta($post, 'clwp_post_contributors', true));
// function index_active_editor_page (){

// }

//add_filter( 'wpseo_robots', 'index_active_editor_page');
//in_array('Editor', get_userdata((int)$contributor)->roles)
if (in_array('editor', get_userdata(81)->roles)){
    //var_dump(get_userdata(120));
    echo"<br><br>yes<br><br>";
}else{
    echo "false <br>";
}


//$%%$$$$$$$$$$

$user_query = new WP_User_Query([
    'role__in' => ['editor'],
    'number' => '-1',
    'fields' => [
      'user_nicename',
      'ID',
    ]
  ]);

$users = $user_query->get_results();

//var_dump($users);
if( ! empty( $users) ):
    foreach($users as $user){
        //var_dump($user);
        $editor_id = $user->ID;
        //echo($editor_id);
        $id_len = strlen((string)$editor_id);
        $underscore = '_';

        $amount_edited = $wpdb -> get_results(
        "SELECT COUNT(post_id) AS amount FROM `wp_postmeta` WHERE 
        meta_key = 'clwp_post_contributors'
        && meta_value != 'NULL' 
        && meta_value LIKE '%s:$id_len:_$editor_id$underscore;s:17:contribution_type_;s:9:_Edited By_%'
        ", ARRAY_A);

        $amount = (int)($amount_edited[0]["amount"]);

        if ($amount > 0){
            $name = ($user->user_nicename);
            $url = substr(get_site_url(),0,-2);
            $author_url = $url . 'author/' . $name . '/';            
            echo"<br>";            
            echo($author_url);
            echo "<br>";
            echo (url_to_postid($author_url));

            // add_filter('wpseo_robots', function($robots){
            //     if (is_author( ' $name ')){
            //         return 'index,follow';
            //     }else{
            //     };
                
            // });
        }else{
            // echo"no";
    }
  }
endif;


$user_data = get_userdata(81);
echo"<br><br>" ; 
echo(get_author_template($user_data));

//META ROBOTSSSSSSS

add_filter('wpseo_robots', function($robots){

    $path = $_SERVER['REQUEST_URI'];
  
    if(strpos($path, 'clwp') !== false){
      return 'noindex, nofollow';
    }else{
      return $robots;
    }
  
  });


update_user_meta( 98, 'wpseo_noindex_author', 'on');


// add_filter( 'wpseo_sitemap_index', 'add_sitemap_custom_items' );

// function add_sitemap_custom_items( $sitemap_custom_items ) {
//    $sitemap_custom_items .= '
// <sitemap>
// <loc>http://www.example.com/external-sitemap-1.xml</loc>
// <lastmod>2017-05-22T23:12:27+00:00</lastmod>
// </sitemap>';
   
// /* Add Additional Sitemap
//  * This section can be removed or reused as needed
//  */
//   $sitemap_custom_items .= '
// <sitemap>
// <loc>http://www.example.com/external-sitemap-2.xml</loc>
// <lastmod>2017-05-22T23:12:27+00:00</lastmod>
// </sitemap>';

// /* DO NOT REMOVE ANYTHING BELOW THIS LINE
//  * Send the information to Yoast SEO
//  */
// return $sitemap_custom_items;
// }

// //remove sitemap cache
// add_filter( 'wpseo_enable_xml_sitemap_transient_caching', '__return_false');

//var_dump(is_single('author/asha-kennedy'));

//add_filter( 'wpseo_robots', 'yoast_seo_robots_remove_search' );

// function yoast_seo_robots_remove_search( $robots ) {
//     if ( is_single ( 123456 ) ) {
//         return false;
//     } else {
//         return $robots;
//     }

// }

echo"<br>";
echo"<br>";

$posts = \CLWP::get_author_posts(81);
var_dump($posts);

echo"<br>";
echo"<br>";
echo"<br>";
echo"<br>";


$author =  get_user_by( 'slug', 'brandon-hatch' );

var_dump($author);
echo"<br>";
echo"<br>";
echo"<br>";

$author =  get_user_by( 'slug', 'sarah-cimarusti' );
var_dump($author);


// $results = $wpdb ->get_results(
//     "SELECT * FROM `wp_yoast_indexable`
//     WHERE object_type = 'user'  
//     && object_id = '81' "
// );

// var_dump($results);
//["has_public_posts"]=> NULL

// $args = array(
//     'post_type' => 'post',
//     'meta_key' => 'clwp_post_contributors'
// );

// $post_query = new WP_Query($args);

//var_dump($post_query);

?>