<?php

function get_audio_feed($body, $conn){
    
    $obj = json_decode($body, true);
    // todo: do the auth stuff
    $id  = $obj['id'];
    if(isset($obj['time']) && $obj['time'] != "")
        $offset_time = $obj['time'];
    else
        $offset_time = date('Y-m-d hh:mm:ss');
    $updated = $obj['update'];
    $json = array();
    
    $sql = "SELECT * FROM (SELECT * FROM (SELECT A.following_id, B.account, B.propic_url FROM (SELECT following_id FROM Follow WHERE me_id = '$id') A JOIN (SELECT temp1.user_id, temp1.account, temp2.propic_url FROM Credential temp1 LEFT JOIN (SELECT * FROM User_profile) temp2 ON temp1.user_id = temp2.user_id) B on A.following_id = B.user_id) C JOIN Feed D ON C.following_id = D.user_id OR D.user_id = '$id' ORDER BY created_time DESC) E WHERE E.created_time < '$offset_time' LIMIT 0,10 ";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $temp = array();
        $temp['fid'] = $row['feed_id'];
        $updated_time = $row['updated_time'];
        $need_detail = true;
        $need_update = false;
        foreach ($updated as $u) {
            if($temp['fid'] == $u['fid'])
                $need_detail = false;
            if(strtotime($updated_time) >= strtotime($u['time']))
                $need_update = true;
        }
        
        if($need_detail){
            $temp['uid'] = $row['user_id'];
            $temp['ac'] = $row['account'];
            $temp['url'] = $row['audio_url'];
            $temp['tit'] = $row['title'];
            $temp['loc'] = $row['location'];
            $temp['ct'] = $row['created_time'];
            $temp['img'] = $row['propic_url'];
            $temp['ut'] = $updated_time;
            $temp['like'] = getListOfLike($temp['fid'], $conn);
        }else if($need_update){
            $temp['ut'] = $updated_time;
            $temp['like'] = getListOfLike($temp['fid'], $conn);
            $temp['img'] = $row['propic_url'];
        }
        array_push($json, $temp);
    }
    
    echo json_encode($json);
}

function getListOfLike($fid, $conn){
    $arr = array();
    $sql = "SELECT B.user_id, B.account FROM (SELECT * FROM Likes WHERE feed_id = '$fid') A JOIN (SELECT * FROM Credential) B ON A.user_id = B.user_id";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $temp = array();
        $temp['uid'] = $row['user_id'];
        $temp['ac'] = $row['account'];
        array_push($arr, $temp);
    }
    return $arr;
}


?>