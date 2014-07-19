# Inflation Class

A class that will get an amount, adjusted for inflation _or_ deflation, by using the US Bureau of Labor Statistics calculator.

### Requirements
This function requires cURL access.

### Usage
After including the class file you can all the static function with `inflation::adjust($amount,$in_year,$like_year)` where; `$amount` is the value you want to adjust, `$in_year` is the year that amount was in, and `$like_year` is the year you to get the adjustment for. You can leave `like_year` off to always pull the adjustment for the current year. For example:

	// The cost of the Apple 1, 10 years later.
	$wozers = inflation::adjust(666.66,1976,1986);

In this case th variable `$wozers` would contain `1,284.11`.

	// The cost of a 1962 VW Beetle De Luxe (With White Wall Tires and Leatherette Interior) today
	$putput = inflation::adjust(1755.61,1962);

And in this one the varible `$putput` would contain `13,829.79`.