<!DOCTYPE html>
<html>
    <head>
        <style>
            main{
                width:75%;
                margin:auto;
                max-width:800px;
            }
            form{
                margin:auto;
            }
            img{
                width:100%;
            }

            form p{
                margin:1%;
                margin-bottom:3%;
                margin-top:0%;
            }
            .range input{
                width:400px;
            }
            .range{
                /*text-align:center;*/
                padding-left:40px;
            }
        </style>
    </head>
    <body>
    <main>
    <h1>Aquatint Image Processor</h1>

<?php
//git config --global --add safe.directory /var/www/html/aquatint
//ini_set('display_errors',1);

    if(isset($_POST['submit'])){
        echo '<h2><a href="submit.php">Process a new image</a></h2>';
        $target_dir = 'uploads/';
        $target_file = $target_dir . basename($_FILES['uploadImage']['name']);
        $uploadOK = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $check = getimagesize($_FILES['uploadImage']['tmp_name']);

        if($check !== false){
            //echo "File is an image - " . $check['mime'] . '.';
            $uploadOk = 1;

            if($_FILES['uploadImage']['size'] > 1048576){
                echo '<h2>Warning! Sorry, your file is too large.</h2>';
                $uploadOk = 0;
            }

        }else{
            echo '<h2>Warning! File is not an image.</h2>';
            $uploadOk = 0;
        }


        if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif'){
            echo '<h2>Warning! Only jpg, jpeg, png, and gif files are allowed.</h2>';
            $uploadOk = 0;
        }

        if($uploadOk == 0){
            echo '<h3>Your file was not uploaded.<h3>';
        }else{
            if (move_uploaded_file($_FILES['uploadImage']['tmp_name'], $target_file)){
                echo 'The file '.htmlspecialchars(basename($_FILES['uploadImage']['name'])).' has been uploaded.';

                $script = 'python3 aquatintScript.py uploads/';
                $script = $script.htmlspecialchars(basename($_FILES['uploadImage']['name'])).' ';
                $script = $script.$_POST['greycut'].' ';
                $script = $script.$_POST['temperature'].' ';
                $script = $script.$_POST['totalsweeps'];
                $runScript = exec($script);
                $fileName = pathinfo($target_file);
                $prefix = $fileName['filename'];
                $suffix = $fileName['extension'];
                if(file_exists($target_dir.$prefix.'-acq.jpg')){
                    echo '<h3>Success! View image <a href="'.$target_dir.$prefix.'-acq.jpg">here</a></h2>';
                    echo '<h4>New Image: </h4>';
                    echo '<img src="'.$target_dir.$prefix.'-acq.jpg"/>';
                    echo '<h4>Original Image: </h4>';
                    echo '<img src="'.$target_dir.$prefix.'.'.$suffix.'"/>';
                }else{
                    echo '<h3>There seems to have been a problem processing the file</h3>';
                }

            }else{
                echo '<h3>There was an error uploading your file.</h3>';
            }
        }
        echo '<h2><a href="submit.php">Process a new image</a></h2>';
    }else{
?>
        <h2>Select a file to process:</h2>
        <form action='submit.php' method='post' enctype='multipart/form-data'>
            <fieldset style='min-width:432px;'>
            <input type='file' name='uploadImage' id='uploadImage' />
            <label for='uploadImage' style='font-size:12px;float:right;'><b>File size: Less than 1 megabyte</b></label>
            </fieldset>
            <fieldset class='range'>
                <label for='greycut'>Greycut</label><br>
                <input type='range' min='0' max='1' value='.5' name='greycut' id='greycut' step='.01'/>
                <p>Value: <span id='greycutVal'></span></p>
                <label for='temperature'>Temperature</label><br>
                <input type='range' min='0.1' max='10' value='5' name='temperature' id='temperature' step='0.1'/>
                <p>Value: <span id='temperatureVal'></span></p>
                <label for='totalsweeps'>Total Sweeps</label><br>
                <input type='range' min='1' max='10' value='5' name='totalsweeps' id='totalsweeps' step='1'/>
                <p>Value: <span id='totalsweepsVal'></span></p>
                <script>

                    var greySlider = document.getElementById('greycut');
                    var greyOutput = document.getElementById('greycutVal');
                    greyOutput.innerHTML = greySlider.value;
                    var tempSlider = document.getElementById('temperature');
                    var tempOutput = document.getElementById('temperatureVal');
                    tempOutput.innerHTML = tempSlider.value;
                    var sweepSlider = document.getElementById('totalsweeps');
                    var sweepOutput = document.getElementById('totalsweepsVal');
                    sweepOutput.innerHTML = sweepSlider.value;

                    greySlider.oninput = function(){
                        greyOutput.innerHTML = greySlider.value;
                    }
                    tempSlider.oninput = function(){
                        tempOutput.innerHTML = tempSlider.value;
                    }
                    sweepSlider.oninput = function(){
                        sweepOutput.innerHTML = sweepSlider.value;
                    }

                </script>
            </fieldset>
            <input type='submit' value='Upload Image' name='submit' style='float:left;' onclick='document.getElementById("wait").innerHTML = "Please wait...";'/>
            <p id='wait' style='float:left;'></p>
        </form>
    <h5 style='clear:both'>
        --Note: This service is still in development.
    </h5>
    </main>
    </body>
</html>
<?php
    }
?>