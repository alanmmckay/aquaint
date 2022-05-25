<!DOCTYPE html>
<html>
    <head>
        <style>
            main{
                width:75%;
                margin:auto;
            }
            form{
                margin:auto;
            }
            img{
                width:100%;
            }
        </style>
    </head>
    <body>
    <main>
    <h1>Aquatint Image Processor</h1>

<?php

ini_set('display_errors',1);

    if(isset($_POST['submit'])){
        echo '<h2><a href="submit.php">Create a new image</a></h2>';
        $target_dir = 'uploads/';
        $target_file = $target_dir . basename($_FILES['uploadImage']['name']);
        $uploadOK = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $check = getimagesize($_FILES['uploadImage']['tmp_name']);

        if($check !== false){
            //echo "File is an image - " . $check['mime'] . '.';
            $uploadOk = 1;
        }else{
            echo '<h2>Warning! File is not an image.</h2>';
            $uploadOk = 0;
        }

        if($_FILES['uploadImage']['size'] > 500000){
            echo '<h2>Warning! Sorry, your file is too large.</h2>';
            $uploadOk = 0;
        }

        if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif'){
            echo '<h2>Warning! Only jpg, jpeg, png, and gif files are allowed.</h2>';
            $uploadOk = 0;
        }

        if($uploadOk == 0){
            echo '<h3>Sorry, your file was not uploaded.<h3>';
        }else{
            if (move_uploaded_file($_FILES['uploadImage']['tmp_name'], $target_file)){
                echo 'The file '.htmlspecialchars(basename($_FILES['uploadImage']['name'])).' has been uploaded.';


                $runScript = exec('python3 aquatintScript.py uploads/'.htmlspecialchars(basename($_FILES['uploadImage']['name'])));
                if($runScript == true){
                    $fileName = pathinfo($target_file);
                    $prefix = strtolower($fileName['filename']);
                    $suffix = strtolower($fileName['extension']);
                    //echo $prefix;
                    //echo $suffix;
                    echo '<h3>Success! View image <a href="'.$target_dir.$prefix.'-acq.jpg">here</a></h2>';
                    echo '<h4>New Image: </h4>';
                    echo '<img src="'.$target_dir.$prefix.'-acq.jpg"/>';
                    echo '<h4>Original Image: </h4>';
                    echo '<img src="'.$target_dir.$prefix.'.'.$suffix.'"/>';
                }else{
                    echo 'nope';
                }

            }else{
                echo '<h3>Sorry, there was an error uploading your file.</h3>';
            }
        }
        echo '<h2><a href="submit.php">Create a new image</a></h2>';
    }else{
?>
        <h2>Select a file to process:</h2>
        <form action='submit.php' method='post' enctype='multipart/form-data'>
            <input type='file' name='uploadImage' id='uploadImage'/>
            <input type='submit' value='Upload Image' name='submit'/>
        </form>
    </main>
    </body>
</html>
<?php
    }
?>