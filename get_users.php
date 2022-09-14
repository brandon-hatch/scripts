
<p>This script is used to determine what information is spit out by class-clwp.php's get_users function on 
line 373, it gives an array of all users (who are not subscribers), and the following attributes from the 
wp_users object it queries:
<li>'user_nicename' => $user['user_nicename'],</li>
<li>'roles' => $user_data->roles,</li>
<li>'user_email' => $user_data->user_email,</li>
<li>'display_name' => $user_data->display_name,</li>
<li>'no_index_user' => get_user_meta($user['ID'], 'wpseo_noindex_author', true),</li>
<br>
I used this information to see who is [no_index_user] = 'on' and what that actually means... 
So far no good information on that.

</p>

<p>keep scrolling to see who has [no_index_user] = 'on'</p>

<?php 
$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');

global $wpdb;

// all users that are not subscribers
$results = $wpdb->get_results("
SELECT ID, user_nicename
FROM wp_users
ORDER BY user_nicename ASC
", ARRAY_A);

$users = [];

if($results) {

    foreach($results as $user){
    print_r($user);
    $user_data = get_userdata($user['ID']);

    if(!in_array('subscriber', $user_data->roles)){

        
        $users[$user['ID']] = [
        'user_nicename' => $user['user_nicename'],
        'roles' => $user_data->roles,
        'user_email' => $user_data->user_email,
        'display_name' => $user_data->display_name,
        'no_index_user' => get_user_meta($user['ID'], 'wpseo_noindex_author', true),
        ];


    }
    }
}

    //print_r($users);

if($results){
    foreach($results as $user){

        $user_data = get_userdata($user['ID']);
    
        if(!in_array('subscriber', $user_data->roles)){
    
                echo($user_data->display_name);
                echo($user_data->roles[0]);
                echo" ";
                echo" wpseo_noindex_author: ";
                echo(get_user_meta($user['ID'], 'wpseo_noindex_author', true));
                echo"<br> "; 
    
        }
    }

}
?>