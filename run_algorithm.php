<html>
    <body>
        
<?php

    if( !isset($_POST['code']) ) { echo shell_exec('echo ERROR: No code passed to run_algorithm.php >> user_data/ERRORS/run_algorithm.log');}
    $code=$_POST["code"];
    if( !isset($_POST['nickname']) ) { echo shell_exec('echo ERROR: No nickname passed to run_algorithm.php >> user_data/$code/run_algorithm.log');}
    if( !isset($_POST['uniqlength']) ) { echo shell_exec('echo ERROR: No uniqlength passed to run_algorithm.php >> user_data/$code/run_algorithm.log');} 
    if( !isset($_POST['min_size']) ) { echo shell_exec('echo ERROR: No min_size passed to run_algorithm.php >> user_data/$code/run_algorithm.log');} 
    $nickname = $_POST["nickname"];
    $uniqlength = $_POST["uniqlength"];
    $min_size = $_POST["min_size"];
    $url="analysis.php?code=$code";
    $filename="user_uploads/$code";
    $oldmask = umask(0);
    mkdir("user_data/$code");
    umask($oldmask);
    
    echo shell_exec("./bin/web_pipeline $filename user_data/$code/$nickname $uniqlength $min_size &> user_data/$code/run_algorithm_errors.log &"); 

    header('Location: '.$url);
?>
    </body>
</html>

<!-- <form name="input_code_form" action="run.php" id="analysis_form" method="post"> -->
