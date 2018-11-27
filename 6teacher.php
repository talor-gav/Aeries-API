<?php
$time_start = microtime(true); //timestamp for logging
include('APIAeriesInclude.php');

//Archive filepath
$archiveFile = "logs/$teacherFile" . "." . date('D');


if (!$fp = fopen("$schoolFile", 'r')) {
    wlogDie("ERROR: Cannot open input file: $schoolFile");
}
if(!$output = fopen("$teacherFile", "w")){
    wlogDie("ERROR: Cannot open output file: $teacherFile");
}


$count = 0; //initialize count for logging
while($schools = fgetcsv($fp, ",")){
    $url = "$apiUrl" . "$vUrl" . "$schoolsUrl" . $schools[0] . "$teachersUrl"; //Create API URL
    $teachers = getReq($url, $headers); //cURL request for each school
    if(isset($teachers)){ //In case of a cURL error
        foreach($teachers as $teacher){ 
            foreach($teacher as $key => $value){ 
                if($count === 0){
                    $heading[] = $key;
                } 
                $data[] = $value;
            }
            if(isset($heading)){
                writeLine($output, $heading);
                unset($heading);
            }
            $count++;
            writeLine($output, $data);
            unset($data);
        }
    }
}

fclose($fp);
fclose($output);

//create a copy for archive
if(!copy($teacherFile, $archiveFile)){
    wlog("Could not archive file $teacherFile");
}

$time_end = microtime(true); //time stamp for logging
$execution_time = round(($time_end - $time_start),2); //seconds rounded to the nearest hundredth 

wlog("Wrote $count teacher records ---");
wlog("Peak memory usage was " . round((memory_get_peak_usage(True)*.000001), 2) . " MB"); //log the highest memory used during script
wlog('Total execution time: '.$execution_time.' seconds ###'); //log the time it took for script to complete
?>