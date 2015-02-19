<?php

function follow($body, $conn){
    
    $obj = json_decode($body, true);
    $id = $obj['id'];
    $token = $obj['token'];
    // TODO: do the auth stuff
    
    $follow_id = $obj['fid'];
    
    $response = array();
    
    $sql = "SELECT * FROM Credential WHERE user_id = '$follow_id'";
    $result = $conn->query($sql);
    if($result->num_rows == 0){
        $response['msg'] = "failure: user doesn't exist";
        $response['status'] = "404";
        echo json_encode($response);
        return;
    }
    
    $sql = "SELECT Public FROM User_profile WHERE user_id = '$follow_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $isPublic = $row['Public'];
    
    if($id == $follow_id){
        $response['msg'] = "failure: cannot follow self";
        $response['status'] = "404";
    }else if($isPublic){
        //follow
        //echo "follow";
        $sql = "SELECT * FROM Follow WHERE me_id = '$id' AND following_id = '$follow_id'";
        $result = $conn->query($sql);
        if($result->num_rows == 0){
            $sql = "INSERT INTO Follow (me_id, following_id) VALUES ('$id', '$follow_id')";
            $result = $conn->query($sql);
            if($result === true){
                $response['msg'] = "success";
                $response['status'] = "200";
                $response['relation'] = "follow";
            }else{
                $response['msg'] = "failure: error during insertion";
                $response['status'] = "404";
            }
        }else{
            $response['msg'] = "failure: already follow";
            $response['status'] = "404";
        }
    }else{
        //request
        $sql = "SELECT * FROM Request WHERE me_id = '$id' AND request_id = '$follow_id'";
        $result = $conn->query($sql);
        if($result->num_rows == 0){
            $sql = "INSERT INTO Request (me_id, request_id) VALUES ('$id', '$follow_id')";
            $result = $conn->query($sql);
            if($result === true){
                $response['msg'] = "success";
                $response['status'] = "200";
                $response['relation'] = "request";
            }else{
                $response['msg'] = "failure: error during insertion";
                $response['status'] = "404";
            }
        }else{
            $response['msg'] = "failure: already request";
            $response['status'] = "404";
        }
    }
    
    echo json_encode($response);
    
    /*
    $sql = "DELETE FROM Follow where me_id = '$id' AND following_id = '$following_id'";
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
    */
}

?>