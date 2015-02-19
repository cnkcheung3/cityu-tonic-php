<?php
require("credential.php");
require("userInfo.php");
require("uploadImage.php");
require("getUserList.php");
require("getAudioFeed.php");
require("postAudioFeed.php");
require("unFollow.php");
require("follow.php");
require("like.php");

$hostname = "sql309.podserver.info";
$username = "podi_15847949";
$password = "z6ccn003";
$db = "podi_15847949_Tonic";

// Create connection
$conn = new mysqli($hostname, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//echo "Connected successfully";

$body = file_get_contents('php://input');
$type = $_GET['type'];

if($type == "login"){
    login($body, $conn);
}else if($type == "logout"){
    logout($body, $conn);
}else if($type == "createAc"){
    createAccount($body, $conn);
}else if($type == "editUser"){
    edit_user_info($body, $conn);
}else if($type == "uploadImage"){
    upload_image($_REQUEST['id'], $_REQUEST['token'], $_REQUEST['image'], $conn);
}else if($type == "getUserProfile"){
    get_user_profile($body, $conn);
}else if($type == "getFollower"){
    get_user_list($body, $conn, 0);
}else if($type == "getFollowing"){
    get_user_list($body, $conn, 1);
}else if($type == "getLikes"){
    get_user_list($body, $conn, 2);
}else if($type == "getAudioFeed"){
    get_audio_feed($body, $conn);
}else if($type == "postAudioFeed"){
    post_audio_feed($body, $conn);
}else if($type == "like"){
    like_feed($body, $conn);
}else if($type == "unlike"){
    unlike_feed($body, $conn);
}else if($type == "follow"){
    follow($body, $conn);
}else if($type == "unfollow"){
    unfollow($body, $conn);
}

?>