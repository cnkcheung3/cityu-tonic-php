<?php

function like_feed($body, $conn){
    
    $obj = json_decode($body, true);
    $id = $obj['id'];
    $token = $obj['token'];
    // TODO: do the auth stuff
    
    $fid = $obj['fid'];
    $sql = "SELECT * FROM Likes WHERE feed_id = '$fid' AND user_id = '$id'";
    $result = $conn->query($sql);
    
    $response = array();
    if($result->num_rows > 0){
        $response['msg'] = "failure: already like the feed";
        $response['status'] = "404";
    }else{
        $sql = "INSERT INTO Likes (feed_id, user_id) VALUES ('$fid', '$id')";
        $result = $conn->query($sql);
        if($result){
            $response['msg'] = "success";
            $response['status'] = "200";
        }else{
            $response['msg'] = "failure: some error during insertion";
            $response['status'] = "404";
        }
    }
    echo json_encode($response);
    
}

function unlike_feed($body, $conn){
    
    $obj = json_decode($body, true);
    $id = $obj['id'];
    $token = $obj['token'];
    // TODO: do the auth stuff
    
    $fid = $obj['fid'];
    $sql = "DELETE FROM Likes where feed_id = '$fid' AND user_id = '$id'";
    $result = $conn->query($sql);
    $response = array();
    if($result){
        $response['msg'] = "success";
        $response['status'] = "200";
    }else{
        $response['msg'] = "failure: some error during deletion";
        $response['status'] = "404";
    }
    echo json_encode($response);
    
}


?>