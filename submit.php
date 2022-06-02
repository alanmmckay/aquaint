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

                $fileName = pathinfo($target_file);
                $prefix = $fileName['filename'];
                $suffix = $fileName['extension'];
                $new_file = $target_dir.$prefix.'-aquatint.jpg';

                echo 'The file '.htmlspecialchars(basename($_FILES['uploadImage']['name'])).' has been uploaded.';

                $script = 'python3 aquatintScript.py "'.$target_file.'" ';
                $script = $script.$_POST['greycut'].' ';
                $script = $script.$_POST['temperature'].' ';
                $script = $script.$_POST['totalsweeps'];

                exec($script,$output,$result);

                if(count($output) == 0 and $result == 0){
?>
        <h3>Success! View image <a href="<?php echo $new_file; ?>">here</a></h2>
        <h4>New Image: </h4>
        <img src="<?php echo $new_file;?>" />
        <hr>
        <h3>Progression: </h3>
        <h4>Application of Greyscale: </h4>
        <img src="uploads/<?php echo $prefix; ?>-origin.jpg" />
        <h4>Application of Greycut: </h4>
        <img src="uploads/<?php echo $prefix; ?>-greycut.jpg">

        <div class="range" style="margin:25px;border-top:solid grey 1px;border-bottom:solid grey 1px;">
        <h4>Application of Sweeps: </h4>

<?php
                    $val = $_POST['totalsweeps'] - 1;
                    $functionCall = "displayImage('sweepSlider',".$val.")";
                    echo 'Min:1 <input type="range" min="0" max="'.$val.'" id="sweepSlider" value="0" oninput="'.$functionCall.';"/> Max:'.$_POST['totalsweeps'];
?>
                    <p>Sweep iteration: <span id="sweepSliderVal"></span></p>
                    <img src="uploads/<?php echo $prefix; ?>-sweep0.jpg" id="sweep0" />
<?php
                    for ($i = 1; $i < $_POST['totalsweeps']; $i++){
                        echo '<img src="uploads/'.$prefix.'-sweep'.$i.'.jpg" id="sweep'.$i.'" style="display:none;" />';
                    }
?>
        </div>
        <h4>Finished image: </h4>
        <img src="<?php echo $new_file; ?>" />
        <hr>
<?php
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
                <input type='range' min='0' max='1' value='.5' name='greycut' id='greycut' step='.01' oninput='setSliderVal("greycut",0);' />
                <p>Value: <span id='greycutVal'></span></p>
                <label for='temperature'>Temperature</label><br>
                <input type='range' min='0.1' max='10' value='5' name='temperature' id='temperature' step='0.1' oninput='setSliderVal("temperature",0);' />
                <p>Value: <span id='temperatureVal'></span></p>
                <label for='totalsweeps'>Total Sweeps</label><br>
                <input type='range' min='1' max='10' value='5' name='totalsweeps' id='totalsweeps' step='1' oninput='setSliderVal("totalsweeps",0);' />
                <p>Value: <span id='totalsweepsVal'></span></p>

            </fieldset>
            <input type='submit' value='Upload Image' name='submit' style='float:left;' onclick='document.getElementById("wait").innerHTML = "Please wait...";'/>
            <p id='wait' style='float:left;'></p>
        </form>
<?php
    }
?>
    <h5 style='clear:both'>
        --Note: This service is still in development.
    </h5>
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
    </main>
    </body>
</html>