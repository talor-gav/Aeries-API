<?php


//End-points for data
$apiUrl = 'Insert Aeries URL Here';
$v3Url = 'api/v3/';
$vUrl = 'api/';
$schoolsUrl = 'schools/';
$studentsUrl = "/students";
$guardiansUrl = "/contacts";
$attendanceUrl = "/attendance";
$rostersUrl = "/classes";
$teachersUrl = "/teachers";
$coursesUrl = "/courses";
$staffUrl = "/staff";

//File paths
$schoolFile = 'schools.csv';
$stuFile = 'students.csv';
$guarFile = 'guardians.csv';
$attFile = 'attendance.csv';
$periodAttFile = 'periodAtt.csv';
$enrollmentFile = 'enrollment.csv';
$teacherFile = 'teachers.csv';
$staffFile = 'staff.csv';

//Log paths
$logfilename = "logs/upload.log";

//Header array for API Authentication
$headers = array();
$headers[] = 'Accept: text/xml, text/html, application/xhtml+xml, */*';
$headers[] = 'Accept: application/json, text/html, application/xhtml+xml, */*';
$headers[] = 'AERIES-CERT: Insert Aeries Cert Here';


$logfp = fopen($logfilename,"a");
if (!$logfp) {
	exit(-1);
}

/**
 * Writes "$str" to global "$logfp" log file and kills script
 *
 * @param string "$str"
 */
function wlogDie ($str) {
	global $logfp;

	wlog($str);
	fclose($logfp);
	die(-1);
}
/**
 * Writes "$str" to global "$logfp" log file
 *
 * @param string "$str"
 */
function wlog ($str) {
	global $logfp;
	echo date("Y-m-d H:i:s") . " - $str\r\n";
    fwrite($logfp, date("Y-m-d H:i:s") . " - $str\r\n");
}

/**
 * Writes array "$row" as a comma-delimited, double-quoted text string to output file "$stufile"
 *
 * @param output file "$stufile"
 * @param array "$row"
 */
function writeLine ($stufile, $row) {
	$sisline = '"' . implode('","',$row) . '"' . "\r\n";
	if (!fwrite($stufile,$sisline)) {
		fclose($stufile);
		wlog("failed to write student line to import file: $sisline");
	}
}

/**
 * Takes objects in array "$row" and returns a formatted string "$line" Example:
 * $row = array(1,2,3); $x=andList($row);
 * Result: $x = "1, 2, and 3"
 * @param array "$row"
 * @return string "$line"
 */
function andList ($row) {
	$line = NULL;
	for ($x = 0; $x < count($row); $x++) {
		if ($line === NULL) {
			$line = $row[$x];
		} else if ( $x == (count($row) -1) ) {
			$line .= ", and " . $row[$x];
		} else {
			$line .= ", " . $row[$x];
		}
	}
	return $line;
}

//Creates an array of API Endpoint defined by $url
function getReq($url, $headers){
    $crl = curl_init();

    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($crl ,CURLOPT_FAILONERROR,true);
	curl_setopt($crl, CURLOPT_FOLLOWLOCATION, true);

    $execCrl = curl_exec($crl);
	$data = json_decode($execCrl, true);
	if(curl_error($crl)){
		$error_msg = curl_error($crl);
	}
	if(isset($error_msg)){
		unset($execCrl);
		wlog($url . " " . $error_msg . " ***");
	}
	else{
		unset($execCrl);
		return($data);
	}
}
?>