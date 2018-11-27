<?php
$time_start = microtime(true); //timestamp for logging
include('APIAeriesInclude.php');

//Archive filepath
$archiveFile = "logs/$staffFile" . "." . date('D');

if (!$fp = fopen("$schoolFile", 'r')) {
    wlogDie("ERROR: Cannot open input file: $schoolFile");
}
if(!$output = fopen("$staffFile", "w")){
    wlogDie("ERROR: Cannot open output file: $staffFile");
}

$count = 0; //initialize count for logging
while($schools = fgetcsv($fp, ",")){
    $url = $apiUrl . $v3Url . $staffUrl; //Create API URL
    $staffs = getReq($url, $headers); //cURL request for each school
    if(isset($staffs)){ //In case of a cURL error
        foreach($staffs as $staff){
            if(isset($staff['SchoolAccessPermissions']) && isset($staff['ExtendedProperties'])){ //Removes weird unused array(throws error during writeLine)
                unset($staff['SchoolAccessPermissions']);
                unset($staff['ExtendedProperties']);
            }
            foreach($staff as $key => $value){
                if($count === 0){
                    $heading[] = $key;
                } 
                $data[] = $value;
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
if(!copy($staffFile, $archiveFile)){
    wlog("Could not archive file $staffFile");
}

$time_end = microtime(true); //time stamp for logging
$execution_time = round(($time_end - $time_start),2); //seconds rounded to the nearest hundredth 

wlog("Wrote $count staff records ---");
wlog("Peak memory usage was " . round((memory_get_peak_usage(True)*.000001), 2) . " MB"); //log the highest memory used during script
wlog('Total execution time: '.$execution_time.' seconds ###'); //log the time it took for script to complete
?>