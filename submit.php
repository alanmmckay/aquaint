<!DOCTYPE html>
<html>
    <head>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <!--style>
            main{
                width:75%;
                margin:auto;
                max-width:800px;
            }
            form{
                margin:auto;
            }
            img{
                width:95%;
                margin: 1%
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
        </style-->
    </head>
    <body>
    <div class='container' style='max-width:800px;margin-top:15px;'>
        <header class='page-header'>
            <h1>Aquatint Image Processor</h1>
        </header>

<?php
//git config --global --add safe.directory /var/www/html/aquatint
//ini_set('display_errors',1);

    if(isset($_POST['submit'])){
        $target_dir = 'uploads/';
        $target_file = $target_dir . basename($_FILES['uploadImage']['name']);
        $uploadOK = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $check = exif_imagetype($_FILES['uploadImage']['tmp_name']);
        $mimeType = image_type_to_mime_type($_FILES['uploadImage']['tmp_name']);

        if($check !== false){
            $uploadOk = 1;
            if($_FILES['uploadImage']['size'] > 1048576){
                echo '<div class="alert alert-danger"><strong>Warning!</strong> Sorry, your file is too large.</div>';
                $uploadOk = 0;
            }
        }else{
            echo '<div class="alert alert-danger"><strong>Warning!</strong> File is not an image.</div>';
            $uploadOk = 0;
        }

        if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif' && $mimeType == 'image/gif' && $mimeType == 'image/jpeg' && $mimeType == 'image/png'){
            echo '<div class="alert alert-danger"><strong>Warning!</strong> Only jpg, jpeg, png, and gif files are allowed.</div>';
            $uploadOk = 0;
        }

        if($uploadOk == 0){
            echo '<div class="alert alert-warning">Your file was not uploaded.</div>';
        }else{
            echo '<div class="alert alert-info"><a href="submit.php" class="alert-link">Process a new image</a></div>';
            if (move_uploaded_file($_FILES['uploadImage']['tmp_name'], $target_file)){

                $fileName = pathinfo($target_file);
                $prefix = $fileName['filename'];
                $suffix = $fileName['extension'];
                $new_file = $target_dir.$prefix.'-aquatint.jpg';

                //echo 'The file '.htmlspecialchars(basename($_FILES['uploadImage']['name'])).' has been uploaded.';

                $script = 'python3 aquatintScript.py "'.$target_file.'" ';
                $script = $script.$_POST['greycut'].' ';
                $script = $script.$_POST['temperature'].' ';
                $script = $script.$_POST['totalsweeps'];

                exec($script,$output,$result);

                if(count($output) == 0 and $result == 0){

?>
        <div class='alert alert-success'><strong>Success!</strong> The file <?php echo htmlspecialchars(basename($_FILES['uploadImage']['name'])) ?> has been uploaded. <a href="<?php echo $new_file; ?>" class='alert-link' target='_blank' rel='noopener noreferrer'>View Transformed Image</a></div>
        <h4>New Image: </h4>
        <img src="<?php echo $new_file;?>" class='img-fluid' />

        <hr>
        <h3>Progression:</h3>
        <h4>Application of Greyscale: </h4>
        <img src="uploads/<?php echo $prefix; ?>-origin.jpg" class='img-fluid'/>
        <h4>Application of Greycut: </h4>
        <img src="uploads/<?php echo $prefix; ?>-greycut.jpg" class='img-fluid'/>
        <div class="range" style="margin:25px;border-top:solid grey 1px;border-bottom:solid grey 1px;padding:5px;">
        <h4>Application of Sweeps: </h4>

<?php
                    $val = $_POST['totalsweeps'] - 1;
                    $functionCall = "displayImage('sweepSlider',".$val.")";
                    echo '<div class="row">';
                    echo '<div class="col-sm-1"> Min:1 </div>';
                    echo '<div class="col-sm-10"><input class="form-control" type="range" min="0" max="'.$val.'" id="sweepSlider" value="0" oninput="'.$functionCall.';"/></div>';
                    echo '<div class="col-sm-1"> Max:'.$_POST['totalsweeps'].' </div>';
                    echo '</div>';
?>
                    <p>Sweep iteration: <span id="sweepSliderVal"></span></p>
                    </form>
                    <img src="uploads/<?php echo $prefix; ?>-sweep0.jpg" id="sweep0" class='img-fluid'/>
<?php
                    for ($i = 1; $i < $_POST['totalsweeps']; $i++){
                        echo '<img src="uploads/'.$prefix.'-sweep'.$i.'.jpg" id="sweep'.$i.'" style="display:none;" class="img-fluid" />';
                    }
?>
        </div>
        <h4>Finished image: </h4>
        <img src="<?php echo $new_file; ?>" class='img-fluid' />
        <hr>
<?php
        }else{
            echo '<h3>There seems to have been a problem processing the file</h3>';
        }
            }else{
                echo '<h3>There was an error uploading your file.</h3>';
            }
        }
        echo '<div class="alert alert-info"><a href="submit.php" class="alert-link">Process a new image</a></div>';
    }else{
?>
        <form action='submit.php' method='post' enctype='multipart/form-data'>
            <div class='form-group'>
                <label for='uploadImage'>Select a file to process:</label>
                <input class='form-control' type='file' name='uploadImage' id='uploadImage' />
                <p for='uploadImage'><b>File size: Less than 1 megabyte</b></p>
            </div>

            <div class='form-group'>
                <label for='greycut'>Greycut:</label><br>
                <input class='form-control' type='range' min='0' max='1' value='0.50' name='greycut' id='greycut' step='.01' oninput='setSliderVal("greycut",0);' />
                <p>Value: <span id='greycutVal'></span></p>
            </div>

            <div class='form-group'>
                <label for='temperature'>Temperature:</label><br>
                <input class='form-control' type='range' min='0.1' max='10' value='5.0' name='temperature' id='temperature' step='0.1' oninput='setSliderVal("temperature",0);' />
                <p>Value: <span id='temperatureVal'></span></p>
            </div>

            <div class='form-group'>
                <label for='totalsweeps'>Total Sweeps:</label><br>
                <input class='form-control' type='range' min='1' max='10' value='0' name='totalsweeps' id='totalsweeps' step='1' oninput='setSliderVal("totalsweeps",0);' />
                <p>Value: <span id='totalsweepsVal'></span></p>
            </div>

            <div class='form-group' style='clear:both'>
                <input type='submit' value='Upload Image' name='submit' onclick='document.getElementById("wait").style.visibility = "visible";' style='float:left'/>
                <p id='wait' style='visibility:hidden;float:left;margin-left:10px;'><b>Please wait...</b></p>
            </div>
        </form>

<?php
    }
?>
    <br>
    <div class='alert alert-info' style='clear:both' >
        <strong>Note:</strong> This service is still in development.
    </div>
    <script>

        setSliderVal = function(sliderName,scew){
            slider = document.getElementById(sliderName);
            output = document.getElementById(sliderName+'Val');
            output.innerHTML =  Number(slider.value)+scew;
            return (Number(slider.value) + scew);
        }

        displayImage = function(sliderName,max){
            var val = setSliderVal(sliderName,1);
            val = val - 1;
            var adjacents = [];
            if(val > 0){
                adjacents.push(Number(val - 1));
            }
            if(val < max - 1){
                adjacents.push(Number(val + 1));
            }

            for(let i = 0; i <= max; i++){
                imageID = 'sweep'+i;
                image = document.getElementById(imageID);
                if(image){
                    image.style.display = 'none';
                }
            }

            imageID = 'sweep'+val;
            image = document.getElementById(imageID);
            if(image){
                image.style.display = 'block';
            }
        }
<?php
    if(isset($_POST['submit'])){
?>
        displayImage('sweepSlider',4);
<?php
    }else{
?>
        //window.onload = function(){
        setSliderVal('greycut',0);
        setSliderVal('temperature',0);
        setSliderVal('totalsweeps',0);
        //}
<?php
    }
?>
    </script>
    </div>
    </body>
</html>