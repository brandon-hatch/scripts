<?php
$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');

Global $wpdb;

if (isset($_POST['submitID'])) {

  checkMeta($_POST['id']);
  isEditor($_POST['id']);
}

$who_has_editor_status = $wpdb->get_results(
  'SELECT * 
  FROM `wp_users` as a
  INNER JOIN wp_usermeta as b
  ON a.ID = b.user_id
  WHERE b.meta_value = "Editor" && b.meta_key= "title_role"'
  , ARRAY_A
);

//DETERMINE ROLE - DONT DO IT THIS WAY AS SOME HAVE EDITOR ROLE BUT NO POSTS
//BUT YOU CAN USE IT TO DETERMINE IF THEY HAVE THE EDITOR ROLE?


function isEditor($id){
  Global $wpdb;

  echo"Running check on user ID: $id... ";

  $id = (int)$id;
  
  $user_data = get_userdata($id);
  
  echo" ... user_data->roles = ";
  print_r($user_data->roles);

  // foreach ($user_data->roles as $roles){
  //   print_r($roles);
  //     if ($roles = 'editor'){
  //       echo "yeehaw";
  //      }
  //   }

  echo"... ... get_user_meta for wpseo_noindex_author = ";
  var_dump(get_user_meta($id, 'wpseo_noindex_author', true));


  //https://wordpress.stackexchange.com/questions/44964/how-do-i-update-a-specific-object-in-an-array-in-user-meta
  //$meta_value = (get_user_meta($id, 'wpseo_noindex_author', true));
  //$meta_value['wpseo_noindex_author'] = '';
  //var_dump(get_user_meta($id, 'wpseo_noindex_author', true));

  }

function checkMeta($id){
  Global $wpdb;

  $editor_status = $wpdb->get_results(
    "SELECT *
    FROM `wp_users` as a
    INNER JOIN wp_usermeta as b
    ON a.ID = b.user_id
    WHERE (b.meta_value = 'Editor' && b.meta_key = 'title_role' && a.ID = $id)"
    
  );

  if (!empty($editor_status)){
    echo "based on wp_usermeta table, user #$id is an editor ... ... ";
    return true;
  } else{
    echo "based on wp_usermeta table, user #$id is not an editor ... ... ";
    return false;
  }
}


?>
<form method="post" action="isEditorTEST.php">
  <input type="number" min="0" name="id"></input>
  <input type="submit" value="Submit" name="submitID"></input>
</form>

<hr>
<p>Determining who has the 'editor' meta_value</p>
<p>So far, only Sarah Cimarusti has this... see below:</p>

<p>
<?php 
    foreach($who_has_editor_status as $editor){
      echo $editor['display_name'];
      echo ", User ID: ";
      echo $editor['user_id'];
    }
  ?>
</p>


