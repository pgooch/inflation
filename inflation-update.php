<?php
/*
	Inflation Update

	This will use the BLS API to update inflation.php with the latest data set. This script requires a few things to run:
	- Write permission on inflation.php
	- A BLS API key. You can register for one at https://www.bls.gov/data/#api, once you get it you will need to place it in a plain text file 
	  with the name "blskey" The file must contain your key and noting else.
	- To be run from the command line.

	Be security aware when you deploy this. Without a blskey file the script will die without changing the inflation.php file. The same will
	also happen if there is a problem loading the data from the API. The data is sanitized before writing so all it should be able to do is 
	break inflation.php. I do not suggest even uploading this file into production, and only use it to update inflation.php locally. This is 
	mostly so this has more use as I rarely have need to update it.

	Phillip Gooch <phillip.gooch@gmail.com>
*/
define('YEARS_PER_GRAB',10); // The number of years to grab data for at a time, I've seen both 20 and 10 as the limit, no clue why it changes.

// Start with a few quick stops
if(php_sapi_name()!='cli'){
    echo 'This script can only be run from the command line.';
    exit;
}
if(!file_exists('blskey')){
    echo 'Unable to find blskey.';
    exit;
}
if(!file_exists('inflation.php')){
    echo 'Unable to find inflation.php.';
    exit;
}

// Grab that key we know where gonna need.
$blskey = file_get_contents('blskey');

// Load the data, this needs to be done in 20 year chunks as thats the limit of the API (but it's not 20 years inclusive), first year is 1913.
$yearly_data = array();
for($year_start=1913; $year_start<=date('Y'); $year_start+=(YEARS_PER_GRAB-1) ){
	$grab_url = 'https://api.bls.gov/publicAPI/v2/timeseries/data/CUUR0000SA0?'.http_build_query(array(
		'registrationkey' => $blskey,
		'catalog' => 'false',
		'startyear' => $year_start,
		'endyear' => $year_start+YEARS_PER_GRAB,
		'calculations' => 'false',
		'annualaverage' => 'true',
	));
	echo "\n".'Grabbing data from '.$year_start.' to '.($year_start+YEARS_PER_GRAB).' from '.$grab_url.'.';
	// Make the post request to the API
	$c = curl_init();
	curl_setopt_array($c,array(
		CURLOPT_URL => $grab_url,
		CURLOPT_POST => true,
		CURLOPT_RETURNTRANSFER => true,
	));
	try{
		$data = curl_exec($c);
		curl_close($c);
	}catch(Exception $e){
		throw new Exception($e);
		exit;
	}

	// This is a json string that can be used if you comment out the above in order to work on the system without making API calls.
	// $data = '{"status":"REQUEST_SUCCEEDED","responseTime":199,"message":["No Data Available for Series CUUR0000SA0 Year: 2021","No Data Available for Series CUUR0000SA0 Year: 2022","No Data Available for Series CUUR0000SA0 Year: 2023","No Data Available for Series CUUR0000SA0 Year: 2024","No Data Available for Series CUUR0000SA0 Year: 2025"],"Results":{"series":[{"seriesID":"CUUR0000SA0","data":[{"year":"2020","period":"M04","periodName":"April","latest":"true","value":"256.389","footnotes":[{}]},{"year":"2020","period":"M03","periodName":"March","value":"258.115","footnotes":[{}]},{"year":"2020","period":"M02","periodName":"February","value":"258.678","footnotes":[{}]},{"year":"2020","period":"M01","periodName":"January","value":"257.971","footnotes":[{}]},{"year":"2019","period":"M13","periodName":"Annual","value":"255.657","footnotes":[{}]},{"year":"2019","period":"M12","periodName":"December","value":"256.974","footnotes":[{}]},{"year":"2019","period":"M11","periodName":"November","value":"257.208","footnotes":[{}]},{"year":"2019","period":"M10","periodName":"October","value":"257.346","footnotes":[{}]},{"year":"2019","period":"M09","periodName":"September","value":"256.759","footnotes":[{}]},{"year":"2019","period":"M08","periodName":"August","value":"256.558","footnotes":[{}]},{"year":"2019","period":"M07","periodName":"July","value":"256.571","footnotes":[{}]},{"year":"2019","period":"M06","periodName":"June","value":"256.143","footnotes":[{}]},{"year":"2019","period":"M05","periodName":"May","value":"256.092","footnotes":[{}]},{"year":"2019","period":"M04","periodName":"April","value":"255.548","footnotes":[{}]},{"year":"2019","period":"M03","periodName":"March","value":"254.202","footnotes":[{}]},{"year":"2019","period":"M02","periodName":"February","value":"252.776","footnotes":[{}]},{"year":"2019","period":"M01","periodName":"January","value":"251.712","footnotes":[{}]},{"year":"2018","period":"M13","periodName":"Annual","value":"251.107","footnotes":[{}]},{"year":"2018","period":"M12","periodName":"December","value":"251.233","footnotes":[{}]},{"year":"2018","period":"M11","periodName":"November","value":"252.038","footnotes":[{}]},{"year":"2018","period":"M10","periodName":"October","value":"252.885","footnotes":[{}]},{"year":"2018","period":"M09","periodName":"September","value":"252.439","footnotes":[{}]},{"year":"2018","period":"M08","periodName":"August","value":"252.146","footnotes":[{}]},{"year":"2018","period":"M07","periodName":"July","value":"252.006","footnotes":[{}]},{"year":"2018","period":"M06","periodName":"June","value":"251.989","footnotes":[{}]},{"year":"2018","period":"M05","periodName":"May","value":"251.588","footnotes":[{}]},{"year":"2018","period":"M04","periodName":"April","value":"250.546","footnotes":[{}]},{"year":"2018","period":"M03","periodName":"March","value":"249.554","footnotes":[{}]},{"year":"2018","period":"M02","periodName":"February","value":"248.991","footnotes":[{}]},{"year":"2018","period":"M01","periodName":"January","value":"247.867","footnotes":[{}]},{"year":"2017","period":"M13","periodName":"Annual","value":"245.120","footnotes":[{}]},{"year":"2017","period":"M12","periodName":"December","value":"246.524","footnotes":[{}]},{"year":"2017","period":"M11","periodName":"November","value":"246.669","footnotes":[{}]},{"year":"2017","period":"M10","periodName":"October","value":"246.663","footnotes":[{}]},{"year":"2017","period":"M09","periodName":"September","value":"246.819","footnotes":[{}]},{"year":"2017","period":"M08","periodName":"August","value":"245.519","footnotes":[{}]},{"year":"2017","period":"M07","periodName":"July","value":"244.786","footnotes":[{}]},{"year":"2017","period":"M06","periodName":"June","value":"244.955","footnotes":[{}]},{"year":"2017","period":"M05","periodName":"May","value":"244.733","footnotes":[{}]},{"year":"2017","period":"M04","periodName":"April","value":"244.524","footnotes":[{}]},{"year":"2017","period":"M03","periodName":"March","value":"243.801","footnotes":[{}]},{"year":"2017","period":"M02","periodName":"February","value":"243.603","footnotes":[{}]},{"year":"2017","period":"M01","periodName":"January","value":"242.839","footnotes":[{}]},{"year":"2016","period":"M13","periodName":"Annual","value":"240.007","footnotes":[{}]},{"year":"2016","period":"M12","periodName":"December","value":"241.432","footnotes":[{}]},{"year":"2016","period":"M11","periodName":"November","value":"241.353","footnotes":[{}]},{"year":"2016","period":"M10","periodName":"October","value":"241.729","footnotes":[{}]},{"year":"2016","period":"M09","periodName":"September","value":"241.428","footnotes":[{}]},{"year":"2016","period":"M08","periodName":"August","value":"240.849","footnotes":[{}]},{"year":"2016","period":"M07","periodName":"July","value":"240.628","footnotes":[{}]},{"year":"2016","period":"M06","periodName":"June","value":"241.018","footnotes":[{}]},{"year":"2016","period":"M05","periodName":"May","value":"240.229","footnotes":[{}]},{"year":"2016","period":"M04","periodName":"April","value":"239.261","footnotes":[{}]},{"year":"2016","period":"M03","periodName":"March","value":"238.132","footnotes":[{}]},{"year":"2016","period":"M02","periodName":"February","value":"237.111","footnotes":[{}]},{"year":"2016","period":"M01","periodName":"January","value":"236.916","footnotes":[{}]},{"year":"2015","period":"M13","periodName":"Annual","value":"237.017","footnotes":[{}]},{"year":"2015","period":"M12","periodName":"December","value":"236.525","footnotes":[{}]},{"year":"2015","period":"M11","periodName":"November","value":"237.336","footnotes":[{}]},{"year":"2015","period":"M10","periodName":"October","value":"237.838","footnotes":[{}]},{"year":"2015","period":"M09","periodName":"September","value":"237.945","footnotes":[{}]},{"year":"2015","period":"M08","periodName":"August","value":"238.316","footnotes":[{}]},{"year":"2015","period":"M07","periodName":"July","value":"238.654","footnotes":[{}]},{"year":"2015","period":"M06","periodName":"June","value":"238.638","footnotes":[{}]},{"year":"2015","period":"M05","periodName":"May","value":"237.805","footnotes":[{}]},{"year":"2015","period":"M04","periodName":"April","value":"236.599","footnotes":[{}]},{"year":"2015","period":"M03","periodName":"March","value":"236.119","footnotes":[{}]},{"year":"2015","period":"M02","periodName":"February","value":"234.722","footnotes":[{}]},{"year":"2015","period":"M01","periodName":"January","value":"233.707","footnotes":[{}]}]}]}}';


	// Sometimes the data comes back not properly typed, so you get a string instead of a json object. That is usually a sign things didn't work.
	if(gettype($data)=='string'){
		$data = json_decode($data);
	}

	// If the request failed then stop and output why, probably the stingy API limits. 
	if($data->status=="REQUEST_NOT_PROCESSED"){
		throw new Exception(implode(', ',$data->message));
		exit;
	}

	// Output so we can load it in without having to make an API call because they are stingy with those.
	// echo "Printing JSON data for testing without API limits";
	// echo json_encode($data,null,JSON_PRETTY_PRINT);

	// Process the data and build a PHP array of it all that we can convert into PHP array code.
	foreach($data->Results->series[0]->data as $index => $data){
		if(!isset($yearly_data[$data->year])){
			$yearly_data[$data->year] = array(0=>'null');
		}
		// convert the number to the month eqiv, 13th month is annual and we store that as 0
		$int = intval(substr($data->period,1));
		if($int===13){
			$int = 0;
		}
		// Add it to it's appropriate location and sort
		$yearly_data[$data->year][$int] = floatval($data->value);
		ksort($yearly_data[$data->year]);
	}
}
ksort($yearly_data);

// echo 'Prepared data for insert';
// print_r($yearly_data);

// Make an array out of the array
$insert_string = "private \$cpi_data = array(
";
foreach($yearly_data as $year => $data) {
	$insert_string .= "		".$year."=>array(".implode(", ",$data)."),
";
}
$insert_string .= " 	);
	// CPI DATA END";

// in case things go oddly wrong this should ensure that it at works breaks inflation.php
$insert_string = preg_replace('~[^acdeilnprtuvy /_>;$=,\r\n\t\(\)\.\d]~i','',$insert_string);

// The following data is what will write to the inflation.php file.
// echo 'The following string will be inserted into inflation.php';
// echo "\n".$insert_string;

// Finally replace the data in the file
file_put_contents('inflation.php',preg_replace('~private \$cpi_data = .+// CPI DATA END~s',$insert_string,file_get_contents('inflation.php')));