<?php
function authUser($id, $token, $conn){
     $md5_token = md5($token);
     $sql = "SELECT * FROM Credential_token WHERE user_id = '$id' AND token = '$md5_token'";
     $result = $conn->query($sql);
     if ($result->num_rows > 0){
        return true;
     }
     return false;
}
?>