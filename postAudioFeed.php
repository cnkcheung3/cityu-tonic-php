<?php

function post_audio_feed($body, $conn){
    
    $obj = json_decode($body, true);
    $id = $obj['id'];
    $token = $obj['token'];
    // TODO: do the auth
    $title = $obj['tit'];
    $location = $obj['loc'];
    $audio_url = $obj['url'];
    
    $response = array();
    if(isset($audio_url)) {
        $sql = "INSERT INTO Feed (user_id, audio_url, title, location) VALUES ('$id', '$audio_url', '$title', '$location')";
        $result = $conn->query($sql);
    
        if($result === true){
            $response['msg'] = "success: post feed";
            $response['status'] = "200";
        }else{
            $response['msg'] = "failure: some problem during db insertion";
            $response['status'] = "404";
        }
    }else{
        $response['msg'] = "failure: audio url cannot be empty";
        $response['status'] = "404";
    }
    echo json_encode($response);
    
}


?>