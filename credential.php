<?php

function login($body, $conn){
    
    $obj = json_decode($body, true);
    $ac = $obj['ac'];
    $pw = $obj['pw'];
    $md5_pw = md5($pw);
    
    $sql = "SELECT * FROM Credential where account = '$ac' AND password = '$md5_pw'";
    $result = $conn->query($sql);
    $response = array();
    
    // successful login
    if ($result->num_rows > 0) {
        
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        // generate a MD5 random token
        $token = md5(uniqid(mt_rand(), true));
        // we further encrypt the token and store it in db.
        $md5_token = md5($token);
        $insert = "INSERT INTO Credential_token (user_id, token) VALUES ('$user_id', '$md5_token')";
        
        if($conn->query($insert)){
            
            $response['user'] = array();
            $response['user']['id'] = $user_id;
            $response['user']['token'] = $token;   
            $response['msg'] = "success";
            $response['status'] = "200";
            
        }else{
            $response['msg'] = "failure: some problem when generate the token";
            $response['status'] = "404";
        }
        
    }else{
        
        $response['msg'] = "failure: account not exist or wrong password";
        $response['status'] = "404";
    }
    
    echo json_encode($response);
}

function logout($body, $conn){
    
    $obj = json_decode($body, true);
    $id = $obj['id'];
    $token = $obj['token'];
    $md5_token = md5($token);
    
    $sql = "SELECT * FROM Credential_token where user_id = '$id' AND token = '$md5_token'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $sql = "DELETE FROM Credential_token where user_id = '$id' AND token = '$md5_token'";
        if($conn->query($sql)){
            $response['msg'] = "success";
            $response['status'] = "200";
        }else{
            $response['msg'] = "failure: something wrong during clear the token";
            $response['status'] = "404";
        }
    }else{
        $response['msg'] = "failure: the id and token doesn't exist. Maybe expired. ";
        $response['status'] = "404";
    }
    
    echo json_encode($response);    
}

function createAccount($body, $conn){
    
    $obj = json_decode($body, true);
    $ac = $obj['ac'];
    $pw = $obj['pw'];
    $md5_pw = md5($pw);
    
    $sql = "SELECT * FROM Credential where account = '$ac'";
    $result = $conn->query($sql);
    $response = array();
    
    if ($result->num_rows > 0 || !isset($ac)){
        $response['msg'] = "failure: account name already exist";
        $response['status'] = "404";
    }else{
        $sql = "INSERT INTO Credential (account, password) VALUES ('$ac', '$md5_pw')";
        $result1 = $conn->query($sql);
        $sql = "SELECT user_id FROM Credential WHERE account = '$ac'";
        $result2 = $conn->query($sql);
        $row = $result2->fetch_assoc();
        $id = $row['user_id'];
        $sql = "INSERT INTO User_profile (user_id, about ,propic_url) VALUES ('$id', NULL, NULL)";
        $result3 = $conn->query($sql);
        
        $sql = "INSERT INTO Follow (me_id, following_id) VALUES ('$id', '$id')";
        $result4 = $conn->query($sql);
        
        if(($result1 === true) && ($result3 === true) && ($result4 === true)){
            $response['msg'] = "success";
            $response['status'] = "200";
        }else{
            $response['msg'] = "failure: error creating account";
            $response['status'] = "404";
        }
    }
    
    echo json_encode($response);
}


?>