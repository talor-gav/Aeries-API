<?php
$time_start = microtime(true); //timestamp for logging
include('APIAeriesInclude.php');

//Archive filepath
$archiveFile = "logs/$attFile" . "." . date('D');


//Change this to the school code needing attendance
$schoolCodes = array(
4,
5,
6,
92
);

// Customize period and attaendance codes to be written to attendance record
$attArray = array(  // change array names
"Q", 
"U", 
" ",
"A", 
"O",
"T"
);

/* Outputs 4 digit year, two digit month with leading zero, two digit day 
with leading zero. Modify date if an older absence file is needed. */
$date = date('Ymd'); 

if (!$fp = fopen("$schoolFile", 'r')) {
    wlogDie("ERROR: Cannot open input file: $schoolFile");
}
if(!$output = fopen("$attFile", "w")){
    wlogDie("ERROR: Cannot open output file: $attFile");
}

$count = 0;
$periodCount = 0;

//TODO : All day attendance or period attendance only.

foreach($schoolCodes as $schoolCode){
    $url = "$apiUrl" . "$v3Url" . "$schoolsUrl" . $schoolCode . "$attendanceUrl" . "?StartDate=" . $date . "&EndDate=" . $date; // make school[0] into customizable array git rid of fgetcsv
    $student = getReq($url, $headers); //cURL request for school

    foreach($student as $key => $students){
        if(in_array($students['AttendanceDays'][0]['AllDayAttendanceCode'], $attArray)){
            $data[$students['PermanentID']][] = $students['PermanentID'];
            $data[$students['PermanentID']][] = $date;
            $data[$students['PermanentID']][] = $schoolCode;
            $data[$students['PermanentID']][] = $students['AttendanceDays'][0]['AllDayAttendanceCode'];
            writeLine($output, $data[$students['PermanentID']]);
            $count++;
        }
    }
}

fclose($fp);
fclose($output);

//create a copy for archive
if(!copy($attFile, $archiveFile)){
    wlog("Could not archive file $attFile");
}

$time_end = microtime(true); //time stamp for logging
$execution_time = round(($time_end - $time_start),2); //seconds rounded to the nearest hundredth 

wlog("Wrote $count student absence records ---");
wlog("Peak memory usage was " . round((memory_get_peak_usage(True)*.000001), 2) . " MB"); //log the highest memory used during script
wlog('Total execution time: '.$execution_time.' seconds ###'); //log the time it took for script to complete
?>