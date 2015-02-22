<?php

require_once("auth.php");
    
function edit_user_info($body, $conn){
    $arr = json_decode($body, true);
    
    $id = $arr['id'];
    $token = $arr['token'];
    
    if(authUser($id, $token, $conn)){
        
        $about_me = $arr['des'];
        $update = "UPDATE User_profile SET about = '$about_me' WHERE user_id = '$id'";
         
        if($conn->query($update)){
            $arr = retrieve_user_profile($id, $conn);
            echo json_encode($arr);
        }else{
            $response['msg'] = "failure: some problem during edited";
            $response['status'] = "404";
            echo json_encode($response);
        }
        
    }else{
        
        $response['msg'] = "failure: unauthorized action";
        $response['status'] = "404";
        echo json_encode($response);
        
    }
    
}

function get_user_profile($body, $conn){
    
    $obj = json_decode($body, true);
    
    $me_id = $obj['mid'];
    $user_id = $obj['uid'];
    
    if(isset($me_id) && isset($user_id)){
        $arr = retrieve_user_profile($me_id,$user_id,$conn);
        echo json_encode($arr);
    }
    
   // }else{
    //    $response['msg'] = "failure: unauthorized action";
    //    $response['status'] = "404";
     //   echo json_encode($response);
    //}
}


function retrieve_user_profile($me_id, $user_id, $conn){
    
    $sql = "SELECT * FROM (Select * FROM User_profile WHERE user_id = '$user_id') temp1 JOIN Credential temp2 ON temp1.user_id = temp2.user_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    $arr['id'] = $user_id;
    $arr['des'] = $row['about'];
    $arr['pic_url'] = $row['propic_url'];
    $arr['name'] = $row['account'];
    
    $arr['follower'] = getFollowerCount($user_id, $conn);
    $arr['following'] = getFollowingCount($user_id, $conn);
    $arr['audio'] = getAudioCount($user_id,$conn);
    $arr['relation'] = getUserRelation($me_id, $user_id, $conn);
    
    return $arr;

}

function getAudioCount($id, $conn){
    $sql = "SELECT count(*) as num FROM Feed WHERE user_id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['num'];
}

function getFollowerCount($id, $conn){
    $sql = "SELECT count(*) as num FROM Follow WHERE following_id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['num'];
}

function getFollowingCount($id, $conn){
    $sql = "SELECT count(*) as num FROM Follow WHERE me_id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['num'];
}

function getUserRelation($me_id, $user_id, $conn){
    if($me_id == $user_id){
        return "me";
    }
    $sql = "SELECT * FROM Follow WHERE me_id = '$me_id' AND following_id = '$user_id'";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        return "following";
    }else{
        $sql = "SELECT * FROM Request WHERE me_id = '$me_id' AND request_id = '$user_id'";
        $result = $conn->query($sql);
        if($result->num_rows > 0)
            return "request";
        else
            return "none";
    }
}



?>