<?php
$time_start = microtime(true); //timestamp for logging
include('APIAeriesInclude.php');

//Archive filepath
$archiveFile = "logs/$stuFile" . "." . date('D');

if (!$fp = fopen("$schoolFile", 'r')) {
    wlogDie("ERROR: Cannot open input file: $schoolFile");
}
if(!$output = fopen("$stuFile", "w")){
    wlogDie("ERROR: Cannot open output file: $stuFile");
}

$count = 0; //initialize count for logging

while($schools = fgetcsv($fp, ",")){
    $url = $apiUrl . $v3Url . $schoolsUrl . $schools[0] . $studentsUrl; //Create API URL
    $student = getReq($url, $headers); //cURL request for each school
    if(isset($student)){ //In case of a cURL error
        foreach($student as $students){
            foreach($students as $key => $value){
                if($count === 0){ //creates header array during first iteration of loop
                    $heading[] = $key; 
                }  
                $data[] = $value;
            }
            $data[1] = $schools[1]; //Outputs school name instead of school code
            if(is_numeric($data[11]) && $data[11] > 0 && $data[11] < 10){ //if the grade is a number and less than 10
                $data[11] = "0" . $data[11]; //prepend a zero to the grade
            }
            if(isset($heading)){ //Writes header to output file
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
if(!copy($stuFile, $archiveFile)){
    wlog("Could not archive file $stuFile");
}

$time_end = microtime(true); //time stamp for logging
$execution_time = round(($time_end - $time_start),2); //seconds rounded to the nearest hundredth 

wlog("Wrote $count student records ---");
wlog("Peak memory usage was " . round((memory_get_peak_usage(True)*.000001), 2) . " MB"); //log the highest memory used during script
wlog('Total execution time: '.$execution_time.' seconds ###'); //log the time it took for script to complete
?>