<?php
$time_start = microtime(true); //timestamp for logging
include('APIAeriesInclude.php');

//Archive filepath
$archiveFile = "logs/$guarFile" . "." . date('D');

if (!$fp = fopen("$schoolFile", 'r')) {
    wlogDie("ERROR: Cannot open input file: $schoolFile");
}
if(!$output = fopen("$guarFile", "w")){
    wlogDie("ERROR: Cannot open output file: $guarFile");
}

$count = 0; //initialize count for logging
while($schools = fgetcsv($fp, ",")){
    $url = "$apiUrl" . "$v3Url" . "$schoolsUrl" . $schools[0] . "$guardiansUrl"; //Create API URL
    $guardians = getReq($url, $headers); //cURL request for each school
    if(isset($guardians)){ //In case of a cURL error
        foreach($guardians as $guardian){
            foreach($guardian as $key => $value){ 
                if($count === 0){
                    $heading[] = $key;
                } 
                $data[] = $value;
            }
            $data['GuardianID'] = "G" . ($count+1000); //create guardian unique ID
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
if(!copy($guarFile, $archiveFile)){
    wlog("Could not archive file $guarFile");
}

$time_end = microtime(true); //time stamp for logging
$execution_time = round(($time_end - $time_start),2); //seconds rounded to the nearest hundredth 

wlog("Wrote $count guardian records ---");
wlog("Peak memory usage was " . round((memory_get_peak_usage(True)*.000001), 2) . " MB"); //log the highest memory used during script
wlog('Total execution time: '.$execution_time.' seconds ###'); //log the time it took for script to complete
?>