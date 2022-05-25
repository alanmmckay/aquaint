<?php
ini_set('display_errors',1);
?>
<!DOCTYPE html>
<html>
    <body>
        <?php

        // passthru - Execute an external program and display raw output

        // passthru() is used when the output from a Unix command
        // is binary data which needs to be passed directly back
        // to the browser.

        passthru('whoami');

        passthru('python3 pythonScript.py');


        $file_data = fopen('test.txt','r') or die('Unable to open file!');

        // look into fgets(), fread(), fwrite(), file(), file_exists(),
        // is_readable(), popen(), etc. Could be useful for gradual read/writes

        echo fread($file_data,filesize('test.txt'));

        fclose($file_data);

        ?>
        <p>HTML output</p>
    </body>
</html>