<?php
//Turn php error reporting on. (Not recommended for live environments)
//error_reporting(E_ALL);
ini_set("display_errors", 0);

require_once('DynECTEmailRestClient.php');  //Include the DynECTEmailRestClient class
$config = parse_ini_file("suppression_config.ini");  //Include the Configuration File

//Set up the client and provide an apikey.
$apikey = $config['apikey'];
if (empty($apikey)) die('You need to enter an API Key!');

$DynECT = new DynECTEmailRestClient($apikey, 'json', 'http://emailapi.dynect.net/rest/');

//////////////////////////////////
// 1.) Select timeframe depending on day of week

switch (date('D')) {
    case 'Sun':
        exit;
        break;
    case 'Mon':
	$time_amount    = 3; 	 // Number of time units back
	$time_unit      = 'day';
        break;
    case 'Tue':
        exit;
        break;
    case 'Wed':
        exit;
        break;
    case 'Thu':
        exit;
        break;
    case 'Fri':
	$time_amount    = 4; // Number of time units back
	$time_unit      = 'day';
        break;
    case 'Sat':
        exit;
        break;
    default:
	exit;
}

//////////////////////////////////
// 2.) Defaults Initialization
$EndDate = date('Y-m-d');
$StartDate = date('Y-m-d', strtotime("-$time_amount $time_unit", strtotime($EndDate)));

echo "Reading suppressions from $StartDate to $EndDate.\n";
$screen_output = "Reading suppressions from $StartDate to $EndDate.\n";

//////////////////////////////////
// 3.) Get all of today's suppressions
$start_index = 0;
$suppressions = Array();
$results = $DynECT->GetSuppressionsCount($StartDate, $EndDate);
$count = $results->response->data->count;
echo "Reading $count suppressions.";
$screen_output .= "Reading $count suppressions.";

$csv_name = "Suppressions_$StartDate-$EndDate.csv";
$csv_output = "Email Address,Reason\n";
do {
    $results = json_decode($DynECT->GetSuppressions($StartDate, $EndDate, $start_index));
    if ($results) {
        foreach ($results->response->data->suppressions as $suppression) {
            array_push($suppressions, array('emailaddress' => $suppression->emailaddress, 'reason' => $suppression->reasontype));
            $csv_output .= "{$suppression->emailaddress},{$suppression->reasontype}\n";
            echo ".";
            $screen_output .= ".";
        }
    }
    $start_index += 200;
} while (count($results->response->data->suppressions));

echo "\nDone reading ".count($suppressions)." suppressions.\n";
$screen_output .= "\nDone reading ".count($suppressions)." suppressions.\n";

//////////////////////////////////
// 4.) Now send Email
$to = implode(',',$config['to_address']);
$from = $config['from_address'];
$subject = "Suppressions export from $StartDate to $EndDate.";
$content = chunk_split(base64_encode($csv_output));

$message = $screen_output;

$uid = md5(uniqid(time()));

$header = "From: Mike Veilleux <".$from.">\r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
$header .= "This is a multi-part message in MIME format.\r\n";
$header .= "--".$uid."\r\n";
$header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$header .= $message."\r\n\r\n";
$header .= "--".$uid."\r\n";
$header .= "Content-Type: text/csv; name=\"".$csv_name."\"\r\n"; // use diff. tyoes here
$header .= "Content-Transfer-Encoding: base64\r\n";
$header .= "Content-Disposition: attachment; filename=\"".$csv_name."\"\r\n\r\n";
$header .= $content."\r\n\r\n";
$header .= "--".$uid."--";

mail($to, $subject, $message, $header);

?>
