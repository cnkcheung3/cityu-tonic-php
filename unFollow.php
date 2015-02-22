<?php

function unfollow($body, $conn){
    
    $obj = json_decode($body, true);
    $id = $obj['id'];
    $token = $obj['token'];
    // TODO: do the auth stuff
    
    $following_id = $obj['fid'];
    $sql = "DELETE FROM Follow where me_id = '$id' AND following_id = '$following_id'";
    $result = $conn->query($sql);
    $response = array();
    if($result){
        $response['msg'] = "success";
        $response['status'] = "200";
        $response['relation'] = "none";
    }else{
        $response['msg'] = "failure: some error during deletion";
        $response['status'] = "404";
    }
    echo json_encode($response);
    
}



?>