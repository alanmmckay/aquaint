<?php
ini_set('display_errors',1);
//ensure "file_uploads = On" within php.ini
//ensure chmod 777 for upload folder
if(isset($_POST['submit'])){
    //$target_dir = getcwd().'/uploads/';
    $target_dir = 'uploads/';
    $target_file = $target_dir . basename($_FILES['fileToUpload']['name']);
    echo $target_file;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $check = getimagesize($_FILES['fileToUpload']['tmp_name']);
    if($check !== false){
        echo "File is an image - " . $check['mime'] . '.';
        $uploadOk = 1;
    }else{
        echo 'File is not an image.';
        $uploadOk = 0;
    }

    if($_FILES['fileToUpload']['size'] > 500000){
        echo 'Sorry, your file is too large.';
        $uploadOk = 0;
    }

    if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif'){
        echo 'Sorry, only jpg, jpeg, png, and gif files are allowed.';
        $uploadOk = 0;
    }

    if($uploadOk == 0){
        echo 'Sorry, your file was not uploaded.';
    }else{
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)){
            echo 'The file '.htmlspecialchars(basename($_FILES['fileToUpload']['name'])).' has been uploaded.';
        }else{
            echo 'Sorry, there was an error uploading your file.';
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <body>
        <form action='upload.php' method='post' enctype='multipart/form-data'>
            <input type='file' name='fileToUpload' id='fileToUpload'/>
            <input type='submit' value='Upload Image' name='submit'/>
        </form>
    </body>
</html>