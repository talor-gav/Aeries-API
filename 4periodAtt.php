<?php
$time_start = microtime(true); //timestamp for logging
include('APIAeriesInclude.php');

//Archive filepath
$archiveFile = "logs/$periodAttFile" . "." . date('D');

//Change this to the school code needing attendance
$schoolCodes = array(
4,
5,
6,
92
);

/* Customize period and attaendance codes to be written to attendance record
Link to confluence page with available fields xxx */
$periodCodes = array(
"O",
"I",
"A",
"Q", 
"U", 
"X", 
"T"
);

/* Outputs 4 digit year, two digit month with leading zero, two digit day 
with leading zero. Modify date if an older absence file is needed. */
$date = date('Ymd'); 


if (!$fp = fopen("$schoolFile", 'r')) {
    wlogDie("ERROR: Cannot open input file: $schoolFile");
}
if(!$output = fopen("$periodAttFile", "w")){
    wlogDie("ERROR: Cannot open output file: $periodAttFile");
}

$count = 0;
$periodCount = 0;

foreach($schoolCodes as $schoolCode){
    $url = "$apiUrl" . "$v3Url" . "$schoolsUrl" . $schoolCode . "$attendanceUrl" . "?StartDate=" . $date . "&EndDate=" . $date; // make school[0] into customizable array git rid of fgetcsv
    $student = getReq($url, $headers); //cURL request for each school
    foreach($student as $students){
        if(isset($students['AttendanceDays'][0]['Periods'])){
            foreach($students['AttendanceDays'][0]['Periods'] as $period){
                if(in_array($period['AttendanceCode'], $periodCodes)){
                    $periodList[$students['PermanentID']][] = $period['Period']; //creates an array of periods for each student
                    $periodCount++;
                }
            }
            if(isset($periodList[$students['PermanentID']])){
                $data[$students['PermanentID']][] = $students['PermanentID'];
                $data[$students['PermanentID']][] = $schoolCode;
                $data[$students['PermanentID']][] = $date;
                $p = andList($periodList[$students['PermanentID']]); //takes the array of periods and stores it in one variable
                $data[$students['PermanentID']][] = $p; 
            }
        }
        if(isset($data[$students['PermanentID']])){   //when looping through students with no period code "undefined offset"
            writeLine($output, $data[$students['PermanentID']]);
            $count++;
        }
    }
}


fclose($fp);
fclose($output);

//create a copy for archive
if(!copy($periodAttFile, $archiveFile)){
    wlog("Could not archive file $periodAttFile");
}

$time_end = microtime(true); //time stamp for logging
$execution_time = round(($time_end - $time_start),2); //seconds rounded to the nearest hundredth 

wlog("Wrote $count student absence records ---");
wlog("Wrote $periodCount periods to absence records");
wlog("Peak memory usage was " . round((memory_get_peak_usage(True)*.000001), 2) . " MB"); //log the highest memory used during script
wlog('Total execution time: '.$execution_time.' seconds ###'); //log the time it took for script to complete
?>