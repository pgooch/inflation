# Inflation Class

A simple PHP function to either calculate inflation locally or to scrape the http://data.bls.gov/ inflation calculator for the adjustment.

### Requirements
This function requires cURL access if you wish to scrape the bls.gov calculator for your answer.

### Usage
After includeing the class you either main adjustment function:

- `adjust($amount,$in_year,[$like_year])` will adjust the `$ammount` as if was in `$in_year` like it was `$like_year`. With this function you do not need to call the `$like_year`, if you lave it absent it will automatically fill it for the latest possible date (the last month of the last year of data). This function uses the internal CPI data table and therefore can do inflation down to the month. Various month formats should be usable, but it only supports month and year combinations (like 10/1999 or 1987-4). This will return the adjusted value of today and throw a notice if there was an error.

- `grabAdjustment($ammount,$in_year,[$like_year])`  will adjust the `$ammount` as if was in `$in_year` like it was `$like_year`. Like with the above if you omit the `$like_year` it will pull for the current year. Since this one puuls from the CPI calculator is cannot do to-the-month adjustments. This will return the adjusted amount or an error message. This is a static function so it can be called directly, but it is also substanially slower beacuse it has to cURL the bls.gov website for the infomration.

Example useage can be found in examples.php. 

### About the data
The data used in internal calculations is gathered from http://www.bls.gov/cpi/cpid1406.pdf Table 24. It is stored in a multi-dimensional array in a `array($year => array($avg, $jan, $feb...),...` format. This class will most likely not be updated on a monthly basis as new information is added, but adding new months of data is simple. If you do go ahead and add new data submit a pull request and I'll incorperate it into the main repository.