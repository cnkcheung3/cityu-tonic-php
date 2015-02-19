<?php

require_once("auth.php");

//$base = $_REQUEST['image']
function upload_image($id, $token, $base, $conn){
    if(authUser($id, $token, $conn)){
        // Decode Image
        $binary=base64_decode($base);
        header('Content-Type: bitmap; charset=utf-8');
        // Images will be saved under 'www/imgupload/uplodedimages' folder
        $fileid = substr(md5(uniqid(rand(), true)),0,6);
        $file = fopen('user_pic/'.$fileid, 'wb');
        // Create File
        fwrite($file, $binary);
        fclose($file);
        
        //Delete the old photo
        $sql = "SELECT propic_url from User_profile WHERE user_id = '$id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $old_img_path = 'user_pic/'.$row['propic_url'];
        unlink($old_img_path);
        
        //Update db
        $sql = "UPDATE User_profile SET propic_url = '$fileid' WHERE user_id = '$id'";
        $conn->query($sql);
        
        //echo "Image upload complete, Please check your php file directory";
        $response = array();
        $response['msg'] = "success: Image upload complete, Please check your php file directory";
        $response['status'] = "200";
        $response['url'] = $fileid;        
        echo json_encode($response);
    }else{
        
        $response = array();
        $response['msg'] = "failure: Not auth.";
        $response['status'] = "404";
        echo json_encode($response);
        
    }
}
    
?>