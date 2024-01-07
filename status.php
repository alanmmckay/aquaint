<?php

$not_ready = json_encode(array(0,0));

if(isset($_GET['id'])){
    $result = preg_match("/\A([a-z0-9]+)\z/",$_GET['id']);
    $valid = 0;
    if($result == 1){
        $json = file_get_contents("map.json");
        $json_data = json_decode($json,true);
        if(isset($json_data[$_GET['id']])){
            $valid = 1;
        }else{
            echo $not_ready;
        }
    }else{
        echo $not_ready;
    }
}else{
    echo $not_ready;
}

if($valid == 1){
    try{
        $json = file_get_contents("uploads/".$_GET['id']."-status.json");
        $json_data = json_decode($json,true);
        $return = array($json_data["finished"],$json_data["total"],$json_data['rewrite']);
        echo json_encode($return);
    }catch(Exception $ex){
        echo $not_ready;
    }
}
?>
