<?php
$time_start = microtime(true); //timestamp for logging
include('APIAeriesInclude.php');

//Archive filepath
$archiveFile = "logs/$schoolFile" . "." . date('D');

//creating API URL
$urlSchools = $apiUrl . $v3Url . $schoolsUrl;

if (!$schools = getReq($urlSchools, $headers)) {
	wlogDie("ERROR: Cannot connect to $urlSchools URL");
}
if (!$fp = fopen("$schoolFile", 'w')) {
    wlogDie("ERROR: Cannot open input file: $schoolFile");
}

$count = 0; //initialize count for logging

foreach($schools as $school){
    $data = array($school['SchoolCode'], $school['Name']);
    ++$count;
    writeLine($fp, $data); 
}

fclose($fp);

//create a copy for archive
if(!copy($schoolFile, $archiveFile)){
    wlog("Could not archive file $schoolFile");
}

$time_end = microtime(true); //time stamp for logging
$execution_time = round(($time_end - $time_start),2); //seconds rounded to the nearest hundredth 

wlog("Wrote $count school records ---");
wlog("Peak memory usage was " . round((memory_get_peak_usage(True)*.000001), 2) . " MB"); //log the highest memory used during script
wlog('Total execution time: '.$execution_time.' seconds ###'); //log the time it took for script to complete
?>