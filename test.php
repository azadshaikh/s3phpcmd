<?php   
// pipe_client.php, uses proc_open() function to create the
// worker process, and send instructions to it
$descriptorspec = array(
    0 => array("pipe", "r"), // stdin for worker
    1 => array("pipe", "w"), // stdout for worker
    );
    $worker = proc_open("php pipe_worker.php", $descriptorspec, $pipes);
    if ($worker) {
    fwrite($pipes[0], "hello");
    
    while (!feof($pipes[1])) {
    echo fgets($pipes[1]). "\n";
    }
    proc_close($worker);
    }
?>