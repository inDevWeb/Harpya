<?php
       $img = str_replace('data:image/png;base64,','',$_POST['data']);
       
                   $filename = $getMercID.'_'.date('dmYHi').'_'.rand(1111,9999).'.jpg'; // Set a filename
        file_put_contents("tmp/$filename",base64_decode($img)); // Save photo to folder 
        echo   'tmp/'.$filename;
