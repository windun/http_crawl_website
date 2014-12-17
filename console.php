<?php
	/*
	$INSTANCE = 1;

	ob_implicit_flush(true);
	set_time_limit(0);
	$file_name = "out".$INSTANCE.".txt";
	$file = file($file_name);

	echo "Output[".count($file)."]:\n\n";

	// Print out the file
	ob_start();

	for($i = 0; $i < count($file); $i++)
	{	
		echo $file[$i] . "\n";
	}	
	ob_end_flush();
	ob_flush();
*/
$INSTANCE = 1;
$cmd = "cat out".$INSTANCE.".txt";

$descriptorspec = array(
   0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
   1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
   2 => array("pipe", "w")    // stderr is a pipe that the child will write to
);
flush();
$process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());
echo "<pre>";
if (is_resource($process)) {
    while ($s = fgets($pipes[1])) {
        print $s;
        flush();
    }
}
echo "</pre>";
?>
