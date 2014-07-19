<?php
/*
	Inflation

	A simple single-class function that will get an amount adjusted for inflation by scraping the http://data.bls.gov/ inflation calculator.

	Phillip Gooch
*/

class inflation{

	// This function can be pulled out of you don't want to keep everything class based (or, classy, har har har)
	public static function adjust($ammount,$in_year,$like_year=''){
		// $ammount in $in_year would be like _return_ in $like_year

		// First, if the $like_year is blank make it this year
		if($like_year==''){$like_year=date('Y');}

		// Now we scrape the CPI Calculator
		$url = 'http://data.bls.gov/cgi-bin/cpicalc.pl?cost1='.ltrim($ammount,'$').'&year1='.$in_year.'&year2='.$like_year;
		$curl=curl_init($url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		$page=curl_exec($curl);
		curl_close($curl);

		// Get the inflated amount from the scrapped page
		preg_match_all('~<span id="answer">(.+?)</span>~',$page,$inflated);
		$inflated = $inflated[1][0];

		// Clean it up and return it
		$inflated = ltrim($inflated,'$');
		return $inflated;
	}

}