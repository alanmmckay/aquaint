<!DOCTYPE html>
<html>
    <head>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <style>
            span#one{
                color:white;
                animation-name:fadein1;
                animation-duration:3s;
                animation-iteration-count: infinite;
            }

            span#two{
                color:white;
                animation-name:fadein2;
                animation-duration:3s;
                animation-iteration-count: infinite;
            }

            span#three{
                color:white;
                animation-name:fadein3;
                animation-duration:3s;
                animation-iteration-count: infinite;
            }

            @keyframes fadein1 {
                0%{color:white}
                15%{color:black}
                75%{color:black}
                95%{color:white}
            }
            @keyframes fadein2 {
                0%{color:white}
                55%{color:black}
                75%{color:black}
                95%{color:white}
            }
            @keyframes fadein3 {
                0%{color:white}
                75%{color:black}
                95%{color:white}
            }
        </style>
    </head>
    <body>
    <div class='container' style='max-width:800px;margin-top:15px;'>
        <header class='page-header'>
            <h1>Aquatint Image Processor</h1>
        </header>

<?php
//git config --global --add safe.directory /var/www/html/aquatint
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);

    if(isset($_POST['submit'])){
        $target_dir = 'uploads/';
        $uploadOk = 1;

        //Validate string form controls:
        if(isset($_POST['file_name']) && isset($_FILES['uploadImage']['name'])){
            if(strlen($_FILES['uploadImage']['name']) <= 0 || strlen($_POST['file_name']) <= 0){
                $uploadOk = 0;
            }else{
                $preg_result = preg_match("/\A([a-z0-9]+)\z/",$_POST['file_name']);
                if($preg_result == 0){
                    echo '<div class="alert alert-danger"><strong>Warning!</strong> Please refrain from altering hidden form.</div>';
                    $uploadOk = 0;
                }
            }

            if($uploadOk == 1){
                $file_name = $_POST['file_name'];
                $origin_file = $target_dir . basename($_FILES['uploadImage']['name']);
                $imageFileType = strtolower(pathinfo($target_dir . $origin_file,PATHINFO_EXTENSION));
                $target_file = $target_dir . $file_name  . "." . $imageFileType;
                $check = exif_imagetype($_FILES['uploadImage']['tmp_name']);
                $mimeType = image_type_to_mime_type($check);
            }
        }else{
            $uploadOk = 0;
        }

        //Validate filetype:
        if($uploadOk == 1 ){
            if($check !== false){
                //$uploadOk = 1;
                if($_FILES['uploadImage']['size'] > 1048576){
                    echo '<div class="alert alert-danger"><strong>Warning!</strong> Sorry, your file is too large.</div>';
                    $uploadOk = 0;
                }
            }else{
                echo '<div class="alert alert-danger"><strong>Warning!</strong> File is not an image.</div>';
                $uploadOk = 0;
            }

            #If one of the following conditions are satisfied, then the imagefiletype is good:
            if( ($imageFileType != 'jpg') && ($imageFileType != 'png') && ($imageFileType != 'jpeg') && ($imageFileType != 'gif') ){
                echo '<div class="alert alert-danger"><strong>Warning!</strong> Only jpg, jpeg, png, and gif files are allowed.</div>';
                $uploadOk = 0;
            }else

            #If one of the following conditions are satisfied, then the mimetype is good:
            if( ($mimeType != 'image/gif') && ($mimeType != 'image/jpeg') && ($mimeType != 'image/png') ){
                echo '<div class="alert alert-danger"><strong>Warning!</strong> Only jpg, jpeg, png, and gif files are allowed.</div>';
                $uploadOk = 0;
            }

        }

        //Validate numeric form controls:
        if($uploadOk == 1){

            //Greycut:
            try{
                $greycut = (float) $_POST['greycut'];
                if($greycut < 0 || $greycut > 1){
                    echo '<div class="alert alert-danger"><strong>Warning!</strong> Please refrain from changing form values with the element inspector.</div>';
                    $uploadOk = 0;
                }else{
                    $greycut = (string) $greycut;
                }
            }catch (Exception $ex){
                echo '<div class="alert alert-danger"><strong>Warning!</strong> Please refrain from changing form values with the element inspector.</div>';
                $uploadOk = 0;
            }

            //Temperature:
            try{
                $temperature = (float) $_POST['temperature'];
                if($temperature < 0.1 || $temperature > 10){
                    echo '<div class="alert alert-danger"><strong>Warning!</strong> Please refrain from changing form values with the element inspector.</div>';
                    $uploadOk = 0;
                }else{
                    $temperature = (string) $temperature;
                }
            }catch (Exception $ex){
                echo '<div class="alert alert-danger"><strong>Warning!</strong> Please refrain from changing form values with the element inspector.</div>';
                $uploadOk = 0;
            }

            //Total Sweeps:
            try{
                $totalsweeps = (float) $_POST['totalsweeps'];
                if($totalsweeps < 1 || $totalsweeps > 10){
                    echo '<div class="alert alert-danger"><strong>Warning!</strong> Please refrain from changing form values with the element inspector.</div>';
                    $uploadOk = 0;
                }else{
                    $totalsweeps = (string) $totalsweeps;
                }
            }catch (Exception $ex){
                echo '<div class="alert alert-danger"><strong>Warning!</strong> Please refrain from changing form values with the element inspector.</div>';
                $uploadOk = 0;
            }

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

                $script = 'python3 aquatintScript.py "'.$target_file.'" ';
                $script = $script.$greycut.' ';
                $script = $script.$temperature.' ';
                $script = $script.$totalsweeps;

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

        <div class='alert alert-info' style='clear:both' >
            <strong>Note:</strong> This service is still in development.
        </div>
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

<?php

$file_name = '';
for($i = 0; $i <= rand(10,20); $i++){
    $new_ord = rand(87,122);
    if($new_ord >= 97){
        $file_name = $file_name . chr($new_ord);
    }else{
        $file_name = $file_name . $new_ord;
    }
}
$json = file_get_contents("map.json");
$json_data = json_decode($json,true);
$json_data[$file_name] = array("status" => 0, "time" => time());
file_put_contents("map.json",json_encode($json_data));

//Logic to trim old data within maps.json:
$currentTime = time();
$keepers = array();
foreach($json_data as $key => $value){
    if( ((int)$value["time"]) >= ((int)$currentTime - (60*30)) ){
        $keepers[$key] = $value;
    }
}
file_put_contents("map.json",json_encode($keepers));

?>

            <div class='form-group' style='clear:both;display:none;'>
                <input class='form-control' type='text' name='file_name' id='file_name' value=<?php echo $file_name; ?> >
                <br>
            </div>

            <div class='form-group' style='clear:both'>
                <input type='submit' value='Upload Image' name='submit' onclick="submit_process('<?php echo $file_name; ?>');" style='float:left'/>
                <p id='wait' style='visibility:hidden;float:left;margin-left:10px;'><b>Please wait<span id='one'>.</span><span id='two'>.</span><span id='three'>.</span></b></p>
            </div>
        </form>
        <div class="progress" style='clear:both'>
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 0%" >
            </div>
        </div>
        <div id='progress-text' class='alert alert-light' style='clear:both' >
        </div>
<?php
    }
?>
    <script>

        query = function(filestring){
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function(){
                if (this.readyState == 4 && this.status == 200){
                    result = this.responseText;
                    finished = JSON.parse(result)[0];
                    total = JSON.parse(result)[1];
                    ratio = (finished/total) * 100;
                    new_width = '' + ratio + "%";
                    document.getElementById('progress-bar').style.width = new_width;
                    progress_text_object = document.getElementById('progress-text');
                    if(finished == 0){
                        rewrite = JSON.parse(result)[2];
                        progress_text_object.innerHTML = 'Step 1/'+total+'; Resizing and applying greyscale to original image: ' + Math.ceil(rewrite * 100) + '% complete.';
                    }else if(finished == 1){
                        progress_text_object.innerHTML = 'Step 2/'+total+'; Original image resized - Applying greycut...';
                    }else if(finished == 2){
                        progress_text_object.innerHTML = 'Step 3/'+total+'; Greycut applied - Applying temperature...';
                    }else if(finished == 3){
                        rewrite = JSON.parse(result)[2];
                        progress_text_object.innerHTML = 'Step 4/'+total+'; Greycut and Temperature applied - Applying first sweep: ' + Math.ceil(rewrite * 100) + '% complete.';
                    }else if(finished >= 4){
                        rewrite = JSON.parse(result)[2];
                        progress_text_object.innerHTML = 'Step '+Math.min(total,(finished+1))+'/'+total+'; Applying sweep: ' + Math.ceil(rewrite * 100) + '% complete.';
                    }
                }
            };
            xmlhttp.open("GET","status.php?id="+filestring,true);
            xmlhttp.send();
        }

        submit_process = function(filestring){
            document.getElementById("wait").style.visibility = "visible";
            setInterval(function(){
                query("<?php echo $file_name;?>");
            },3000);
        }

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
