<?php
    $code=$_POST['code'];
    
    $filename="user_data/" . $code . "/progress.log";
    
    if (file_exists($filename)) {
        $progress_stats = file($filename);
        echo json_encode($progress_stats);
    }
    else {
        $progress_stats = "file " + $filename + " doesn't exist";
        echo json_encode($progress_stats);
    }

?>
