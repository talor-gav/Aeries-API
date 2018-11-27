<?php
$time_start = microtime(true); //timestamp for logging
include('APIAeriesInclude.php');

//Archive filepath
$archiveFile = "logs/$enrollmentFile" . "." . date('D');

if (!$fp = fopen("$schoolFile", 'r')) {
    wlogDie("ERROR: Cannot open input file: $schoolFile");
}
if(!$output = fopen("$enrollmentFile", "w")){
    wlogDie("ERROR: Cannot open output file: $enrollmentFile");
}


$count = 0; //initialize count for logging
$courseUrl = "$apiUrl" . "$vUrl" . "$coursesUrl";
$courses = getReq($courseUrl, $headers); 
foreach($courses as $course){
    $courseArray[$course['ID']] = $course['Title'];
}
while($schools = fgetcsv($fp, ",")){
    $url = "$apiUrl" . "$vUrl" . "$schoolsUrl" . $schools[0] . "$rostersUrl"; //Create API URL
    $rosters = getReq($url, $headers); //cURL request for each school
    if(isset($rosters)){ //In case of a cURL error
        foreach($rosters as $roster){
            if(array_key_exists($roster['CourseID'], $courseArray)){
                $roster['CourseName'] = $courseArray[$roster['CourseID']];
            } 
            foreach($roster as $key => $value){
                if($count === 0){
                    $heading[] = $key;
                } 
                $data[] = $value;
            }
            if(isset($heading)){ //Writes header to output file
                writeLine($output, $heading);
                unset($heading);
            }
            $data[10] = "Period " . $data[5] . " - " . $data [10] . " - " . $data[3]; //Creates display name for class
            $count++;
            writeLine($output, $data);
            unset($data);
        }
    }
}

fclose($fp);
fclose($output);

//create a copy for archive
if(!copy($enrollmentFile, $archiveFile)){
    wlog("Could not archive file $enrollmentFile");
}

$time_end = microtime(true); //time stamp for logging
$execution_time = round(($time_end - $time_start),2); //seconds rounded to the nearest hundredth 

wlog("Wrote $count enrollment records ---");
wlog("Peak memory usage was " . round((memory_get_peak_usage(True)*.000001), 2) . " MB"); //log the highest memory used during script
wlog('Total execution time: '.$execution_time.' seconds ###'); //log the time it took for script to complete
?>