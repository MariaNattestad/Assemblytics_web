<html>
    <body>
        
<?php

    if( !isset($_POST['code']) ) { echo shell_exec('echo ERROR: No code passed to run_algorithm.php >> user_data/ERRORS/run_algorithm.log');}
    $code=$_POST["code"];
    if( !isset($_POST['nickname']) ) { echo shell_exec('echo ERROR: No nickname passed to run_algorithm.php >> user_data/$code/run_algorithm.log');}
    // if( !isset($_POST['read_length']) ) { echo shell_exec('echo ERROR: No read_length passed to run_now.php >> user_data/$code/run.log');}
    $nickname = $_POST["nickname"];
    // $read_length = $_POST["read_length"];
    $url="analysis.php?code=$code";
    $filename="user_uploads/$code";
    $oldmask = umask(0);
    mkdir("user_data/$code");
    umask($oldmask);
    
    echo shell_exec("./bin/web_ABVC $filename user_data/$code/$nickname &> user_data/$code/run_algorithm_errors.log &"); 

    header('Location: '.$url);
?>
    </body>
</html>

<!-- <form name="input_code_form" action="run.php" id="analysis_form" method="post"> -->
