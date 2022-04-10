<?php   
// pipe_worker.php, all it does is to read instructions
// from STDIN, and write response to STDOUT
$line = fread(STDIN,4096);
fwrite(STDOUT, "$line world");

?>