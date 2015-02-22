<?php

function get_user_list($body, $conn, $type){
    
    $obj = json_decode($body, true);
    // todo: do the auth stuff
    
    $id  = $obj['id'];
    $token = $obj['token'];
    
    $sid = $obj['sid'];
    
    $json = array();
    
    if($type == 0) // get follower
        $sql = "SELECT C.user_id, C.account, D.propic_url, D.about from (SELECT B.user_id, B.account FROM (SELECT me_id FROM Follow WHERE following_id = '$sid') A JOIN (SELECT * FROM Credential) B ON A.me_id = B.user_id) C LEFT JOIN (SELECT * FROM User_profile) D on C.user_id = D.user_id";
    else if($type == 1) // get following
        $sql = "SELECT C.user_id, C.account, D.propic_url, D.about from (SELECT B.user_id, B.account FROM (SELECT following_id FROM Follow WHERE me_id = '$sid') A JOIN (SELECT * FROM Credential) B ON A.following_id = B.user_id) C LEFT JOIN (SELECT * FROM User_profile) D on C.user_id = D.user_id";
    else if($type == 2) // get Likes
        $sql = "SELECT C.user_id, C.account, D.propic_url, D.about from (SELECT B.user_id, B.account FROM (SELECT user_id FROM Likes WHERE feed_id = '$sid') A JOIN (SELECT * FROM Credential) B ON A.user_id = B.user_id) C LEFT JOIN (SELECT * FROM User_profile) D on C.user_id = D.user_id";
    
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $temp = array();
        $temp['uid'] = $row['user_id'];
        $temp['ac'] = $row['account'];
        $temp['pic'] = $row['propic_url'];
        $temp['des'] = $row['about'];
        array_push($json, $temp);
    }
    
    echo json_encode($json);
    
}

?>