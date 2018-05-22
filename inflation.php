<?php
/*
	Inflation

	A simple PHP function to either calculate inflation locally or to scrape the http://data.bls.gov/ inflation calculator for the 
	adjustment.

	Phillip Gooch <phillip.gooch@gmail.com>
*/

class inflation{

	/*
		This is the historic CPI information as pulled from the latest report from http://www.bls.gov/cpi/tables.htm in Table 24. The 
		data is stored by year, each one containing another array with the average followed by the monthly value. With this information 
		calculating the adjusted amount is easy. The Official Bureau of Labor Statistics websites calculator uses year averages for every 
		year except the current, where it uses the latest month. The last year will not be complete until the following year, so in order 
		to making finding the absolute latest data as easy as possible simply leave the remaining months omitted. In order to keep 
		month-to-month calculations simple set the year average as null until it can be updated, this keeps each months data in order. 
		This table and repository will probably not be updated for each months data, but if you do please feel free to make a pull 
		request with the updated information and I'll merge them. The simplest way to get the data is probably with the BLS Data Tool
		located at https://data.bls.gov/cgi-bin/dsrv?cu  you can set all and output large chunks at a time in a format you can copy in.
		Do not copy directly from the PDF of the table as it does not come accross in order.
	*/
	private $cpi_data = array(
		1913=>array(9.9		,9.8		,9.8		,9.8		,9.8		,9.7		,9.8		,9.9		,9.9		,10.0		,10.0		,10.1		,10.0		),
		1914=>array(10.0	,10.0		,9.9		,9.9		,9.8		,9.9		,9.9		,10.0		,10.2		,10.2		,10.1		,10.2		,10.1		),
		1915=>array(10.1	,10.1		,10.0		,9.9		,10.0		,10.1		,10.1		,10.1		,10.1		,10.1		,10.2		,10.3		,10.3		),
		1916=>array(10.9	,10.4		,10.4		,10.5		,10.6		,10.7		,10.8		,10.8		,10.9		,11.1		,11.3		,11.5		,11.6		),
		1917=>array(12.8	,11.7		,12.0		,12.0		,12.6		,12.8		,13.0		,12.8		,13.0		,13.3		,13.5		,13.5		,13.7		),
		1918=>array(15.1	,14.0		,14.1		,14.0		,14.2		,14.5		,14.7		,15.1		,15.4		,15.7		,16.0		,16.3		,16.5		),
		1919=>array(17.3	,16.5		,16.2		,16.4		,16.7		,16.9		,16.9		,17.4		,17.7		,17.8		,18.1		,18.5		,18.9		),
		1920=>array(20.0	,19.3		,19.5		,19.7		,20.3		,20.6		,20.9		,20.8		,20.3		,20.0		,19.9		,19.8		,19.4		),
		1921=>array(17.9	,19.0		,18.4		,18.3		,18.1		,17.7		,17.6		,17.7		,17.7		,17.5		,17.5		,17.4		,17.3		),
		1922=>array(16.8	,16.9		,16.9		,16.7		,16.7		,16.7		,16.7		,16.8		,16.6		,16.6		,16.7		,16.8		,16.9		),
		1923=>array(17.1	,16.8		,16.8		,16.8		,16.9		,16.9		,17.0		,17.2		,17.1		,17.2		,17.3		,17.3		,17.3		),
		1924=>array(17.1	,17.3		,17.2		,17.1		,17.0		,17.0		,17.0		,17.1		,17.0		,17.1		,17.2		,17.2		,17.3		),
		1925=>array(17.5	,17.3		,17.2		,17.3		,17.2		,17.3		,17.5		,17.7		,17.7		,17.7		,17.7		,18.0		,17.9		),
		1926=>array(17.7	,17.9		,17.9		,17.8		,17.9		,17.8		,17.7		,17.5		,17.4		,17.5		,17.6		,17.7		,17.7		),
		1927=>array(17.4	,17.5		,17.4		,17.3		,17.3		,17.4		,17.6		,17.3		,17.2		,17.3		,17.4		,17.3		,17.3		),
		1928=>array(17.1	,17.3		,17.1		,17.1		,17.1		,17.2		,17.1		,17.1		,17.1		,17.3		,17.2		,17.2		,17.1		),
		1929=>array(17.1	,17.1		,17.1		,17.0		,16.9		,17.0		,17.1		,17.3		,17.3		,17.3		,17.3		,17.3		,17.2		),
		1930=>array(16.7	,17.1		,17.0		,16.9		,17.0		,16.9		,16.8		,16.6		,16.5		,16.6		,16.5		,16.4		,16.1		),
		1931=>array(15.2	,15.9		,15.7		,15.6		,15.5		,15.3		,15.1		,15.1		,15.1		,15.0		,14.9		,14.7		,14.6		),
		1932=>array(13.7	,14.3		,14.1		,14.0		,13.9		,13.7		,13.6		,13.6		,13.5		,13.4		,13.3		,13.2		,13.1		),
		1933=>array(13.0	,12.9		,12.7		,12.6		,12.6		,12.6		,12.7		,13.1		,13.2		,13.2		,13.2		,13.2		,13.2		),
		1934=>array(13.4	,13.2		,13.3		,13.3		,13.3		,13.3		,13.4		,13.4		,13.4		,13.6		,13.5		,13.5		,13.4		),
		1935=>array(13.7	,13.6		,13.7		,13.7		,13.8		,13.8		,13.7		,13.7		,13.7		,13.7		,13.7		,13.8		,13.8		),
		1936=>array(13.9	,13.8		,13.8		,13.7		,13.7		,13.7		,13.8		,13.9		,14.0		,14.0		,14.0		,14.0		,14.0		),
		1937=>array(14.4	,14.1		,14.1		,14.2		,14.3		,14.4		,14.4		,14.5		,14.5		,14.6		,14.6		,14.5		,14.4		),
		1938=>array(14.1	,14.2		,14.1		,14.1		,14.2		,14.1		,14.1		,14.1		,14.1		,14.1		,14.0		,14.0		,14.0		),
		1939=>array(13.9	,14.0		,13.9		,13.9		,13.8		,13.8		,13.8		,13.8		,13.8		,14.1		,14.0		,14.0		,14.0		),
		1940=>array(14.0	,13.9		,14.0		,14.0		,14.0		,14.0		,14.1		,14.0		,14.0		,14.0		,14.0		,14.0		,14.1		),
		1941=>array(14.7	,14.1		,14.1		,14.2		,14.3		,14.4		,14.7		,14.7		,14.9		,15.1		,15.3		,15.4		,15.5		),
		1942=>array(16.3	,15.7		,15.8		,16.0		,16.1		,16.3		,16.3		,16.4		,16.5		,16.5		,16.7		,16.8		,16.9		),
		1943=>array(17.3	,16.9		,16.9		,17.2		,17.4		,17.5		,17.5		,17.4		,17.3		,17.4		,17.4		,17.4		,17.4		),
		1944=>array(17.6	,17.4		,17.4		,17.4		,17.5		,17.5		,17.6		,17.7		,17.7		,17.7		,17.7		,17.7		,17.8		),
		1945=>array(18.0	,17.8		,17.8		,17.8		,17.8		,17.9		,18.1		,18.1		,18.1		,18.1		,18.1		,18.1		,18.2		),
		1946=>array(19.5	,18.2		,18.1		,18.3		,18.4		,18.5		,18.7		,19.8		,20.2		,20.4		,20.8		,21.3		,21.5		),
		1947=>array(22.3	,21.5		,21.5		,21.9		,21.9		,21.9		,22.0		,22.2		,22.5		,23.0		,23.0		,23.1		,23.4		),
		1948=>array(24.1	,23.7		,23.5		,23.4		,23.8		,23.9		,24.1		,24.4		,24.5		,24.5		,24.4		,24.2		,24.1		),
		1949=>array(23.8	,24.0		,23.8		,23.8		,23.9		,23.8		,23.9		,23.7		,23.8		,23.9		,23.7		,23.8		,23.6		),
		1950=>array(24.1	,23.5		,23.5		,23.6		,23.6		,23.7		,23.8		,24.1		,24.3		,24.4		,24.6		,24.7		,25.0		),
		1951=>array(26.0	,25.4		,25.7		,25.8		,25.8		,25.9		,25.9		,25.9		,25.9		,26.1		,26.2		,26.4		,26.5		),
		1952=>array(26.5	,26.5		,26.3		,26.3		,26.4		,26.4		,26.5		,26.7		,26.7		,26.7		,26.7		,26.7		,26.7		),
		1953=>array(26.7	,26.6		,26.5		,26.6		,26.6		,26.7		,26.8		,26.8		,26.9		,26.9		,27.0		,26.9		,26.9		),
		1954=>array(26.9	,26.9		,26.9		,26.9		,26.8		,26.9		,26.9		,26.9		,26.9		,26.8		,26.8		,26.8		,26.7		),
		1955=>array(26.8	,26.7		,26.7		,26.7		,26.7		,26.7		,26.7		,26.8		,26.8		,26.9		,26.9		,26.9		,26.8		),
		1956=>array(27.2	,26.8		,26.8		,26.8		,26.9		,27.0		,27.2		,27.4		,27.3		,27.4		,27.5		,27.5		,27.6		),
		1957=>array(28.1	,27.6		,27.7		,27.8		,27.9		,28.0		,28.1		,28.3		,28.3		,28.3		,28.3		,28.4		,28.4		),
		1958=>array(28.9	,28.6		,28.6		,28.8		,28.9		,28.9		,28.9		,29.0		,28.9		,28.9		,28.9		,29.0		,28.9		),
		1959=>array(29.1	,29.0		,28.9		,28.9		,29.0		,29.0		,29.1		,29.2		,29.2		,29.3		,29.4		,29.4		,29.4		),
		1960=>array(29.6	,29.3		,29.4		,29.4		,29.5		,29.5		,29.6		,29.6		,29.6		,29.6		,29.8		,29.8		,29.8		),
		1961=>array(29.9	,29.8		,29.8		,29.8		,29.8		,29.8		,29.8		,30.0		,29.9		,30.0		,30.0		,30.0		,30.0		),
		1962=>array(30.2	,30.0		,30.1		,30.1		,30.2		,30.2		,30.2		,30.3		,30.3		,30.4		,30.4		,30.4		,30.4		),
		1963=>array(30.6	,30.4		,30.4		,30.5		,30.5		,30.5		,30.6		,30.7		,30.7		,30.7		,30.8		,30.8		,30.9		),
		1964=>array(31.0	,30.9		,30.9		,30.9		,30.9		,30.9		,31.0		,31.1		,31.0		,31.1		,31.1		,31.2		,31.2		),
		1965=>array(31.5	,31.2		,31.2		,31.3		,31.4		,31.4		,31.6		,31.6		,31.6		,31.6		,31.7		,31.7		,31.8		),
		1966=>array(32.4	,31.8		,32.0		,32.1		,32.3		,32.3		,32.4		,32.5		,32.7		,32.7		,32.9		,32.9		,32.9		),
		1967=>array(33.4	,32.9		,32.9		,33.0		,33.1		,33.2		,33.3		,33.4		,33.5		,33.6		,33.7		,33.8		,33.9		),
		1968=>array(34.8	,34.1		,34.2		,34.3		,34.4		,34.5		,34.7		,34.9		,35.0		,35.1		,35.3		,35.4		,35.5		),
		1969=>array(36.7	,35.6		,35.8		,36.1		,36.3		,36.4		,36.6		,36.8		,37.0		,37.1		,37.3		,37.5		,37.7		),
		1970=>array(38.8	,37.8		,38.0		,38.2		,38.5		,38.6		,38.8		,39.0		,39.0		,39.2		,39.4		,39.6		,39.8		),
		1971=>array(40.5	,39.8		,39.9		,40.0		,40.1		,40.3		,40.6		,40.7		,40.8		,40.8		,40.9		,40.9		,41.1		),
		1972=>array(41.8	,41.1		,41.3		,41.4		,41.5		,41.6		,41.7		,41.9		,42.0		,42.1		,42.3		,42.4		,42.5		),
		1973=>array(44.4	,42.6		,42.9		,43.3		,43.6		,43.9		,44.2		,44.3		,45.1		,45.2		,45.6		,45.9		,46.2		),
		1974=>array(49.3	,46.6		,47.2		,47.8		,48.0		,48.6		,49.0		,49.4		,50.0		,50.6		,51.1		,51.5		,51.9		),
		1975=>array(53.8	,52.1		,52.5		,52.7		,52.9		,53.2		,53.6		,54.2		,54.3		,54.6		,54.9		,55.3		,55.5		),
		1976=>array(56.9	,55.6		,55.8		,55.9		,56.1		,56.5		,56.8		,57.1		,57.4		,57.6		,57.9		,58.0		,58.2		),
		1977=>array(60.6	,58.5		,59.1		,59.5		,60.0		,60.3		,60.7		,61.0		,61.2		,61.4		,61.6		,61.9		,62.1		),
		1978=>array(65.2	,62.5		,62.9		,63.4		,63.9		,64.5		,65.2		,65.7		,66.0		,66.5		,67.1		,67.4		,67.7		),
		1979=>array(72.6	,68.3		,69.1		,69.8		,70.6		,71.5		,72.3		,73.1		,73.8		,74.6		,75.2		,75.9		,76.7		),
		1980=>array(82.4	,77.8		,78.9		,80.1		,81.0		,81.8		,82.7		,82.7		,83.3		,84.0		,84.8		,85.5		,86.3		),
		1981=>array(90.9	,87.0		,87.9		,88.5		,89.1		,89.8		,90.6		,91.6		,92.3		,93.2		,93.4		,93.7		,94.0		),
		1982=>array(96.5	,94.3		,94.6		,94.5		,94.9		,95.8		,97.0		,97.5		,97.7		,97.9		,98.2		,98.0		,97.6		),
		1983=>array(99.6	,97.8		,97.9		,97.9		,98.6		,99.2		,99.5		,99.9		,100.2		,100.7		,101.0		,101.2		,101.3		),
		1984=>array(103.9	,101.9		,102.4		,102.6		,103.1		,103.4		,103.7		,104.1		,104.5		,105.0		,105.3		,105.3		,105.3		),
		1985=>array(107.6	,105.5		,106.0		,106.4		,106.9		,107.3		,107.6		,107.8		,108.0		,108.3		,108.7		,109.0		,109.3		),
		1986=>array(109.6	,109.6		,109.3		,108.8		,108.6		,108.9		,109.5		,109.5		,109.7		,110.2		,110.3		,110.4		,110.5		),
		1987=>array(113.6	,111.2		,111.6		,112.1		,112.7		,113.1		,113.5		,113.8		,114.4		,115.0		,115.3		,115.4		,115.4		),
		1988=>array(118.3	,115.7		,116.0		,116.5		,117.1		,117.5		,118.0		,118.5		,119.0		,119.8		,120.2		,120.3		,120.5		),
		1989=>array(124.0	,121.1		,121.6		,122.3		,123.1		,123.8		,124.1		,124.4		,124.6		,125.0		,125.6		,125.9		,126.1		),
		1990=>array(130.7	,127.4		,128.0		,128.7		,128.9		,129.2		,129.9		,130.4		,131.6		,132.7		,133.5		,133.8		,133.8		),
		1991=>array(136.2	,134.6		,134.8		,135.0		,135.2		,135.6		,136.0		,136.2		,136.6		,137.2		,137.4		,137.8		,137.9		),
		1992=>array(140.3	,138.1		,138.6		,139.3		,139.5		,139.7		,140.2		,140.5		,140.9		,141.3		,141.8		,142.0		,141.9		),
		1993=>array(144.5	,142.6		,143.1		,143.6		,144.0		,144.2		,144.4		,144.4		,144.8		,145.1		,145.7		,145.8		,145.8		),
		1994=>array(148.2	,146.2		,146.7		,147.2		,147.4		,147.5		,148.0		,148.4		,149.0		,149.4		,149.5		,149.7		,149.7		),
		1995=>array(152.4	,150.3		,150.9		,151.4		,151.9		,152.2		,152.5		,152.5		,152.9		,153.2		,153.7		,153.6		,153.5		),
		1996=>array(156.9	,154.4		,154.9		,155.7		,156.3		,156.6		,156.7		,157.0		,157.3		,157.8		,158.3		,158.6		,158.6		),
		1997=>array(160.5	,159.1		,159.6		,160.0		,160.2		,160.1		,160.3		,160.5		,160.8		,161.2		,161.6		,161.5		,161.3		),
		1998=>array(163.0	,161.6		,161.9		,162.2		,162.5		,162.8		,163.0		,163.2		,163.4		,163.6		,164.0		,164.0		,163.9		),
		1999=>array(166.6	,164.3		,164.5		,165.0		,166.2		,166.2		,166.2		,166.7		,167.1		,167.9		,168.2		,168.3		,168.3		),
		2000=>array(172.2	,168.8		,169.8		,171.2		,171.3		,171.5		,172.4		,172.8		,172.8		,173.7		,174.0		,174.1		,174.0		),
		2001=>array(177.1	,175.1		,175.8		,176.2		,176.9		,177.7		,178.0		,177.5		,177.5		,178.3		,177.7		,177.4		,176.7		),
		2002=>array(179.9	,177.1		,177.8		,178.8		,179.8		,179.8		,179.9		,180.1		,180.7		,181.0		,181.3		,181.3		,180.9		),
		2003=>array(184.0	,181.7		,183.1		,184.2		,183.8		,183.5		,183.7		,183.9		,184.6		,185.2		,185.0		,184.5		,184.3		),
		2004=>array(188.9	,185.2		,186.2		,187.4		,188.0		,189.1		,189.7		,189.4		,189.5		,189.9		,190.9		,191.0		,190.3		),
		2005=>array(195.3	,190.7		,191.8		,193.3		,194.6		,194.4		,194.5		,195.4		,196.4		,198.8		,199.2		,197.6		,196.8		),
		2006=>array(201.6	,198.3		,198.7		,199.8		,201.5		,202.5		,202.9		,203.5		,203.9		,202.9		,201.8		,201.5		,201.8		),
		2007=>array(207.342	,202.416	,203.499	,205.352	,206.686	,207.949	,208.352	,208.299	,207.917	,208.490	,208.936	,210.177	,210.036	),
		2008=>array(215.303	,211.080	,211.693	,213.528	,214.823	,216.632	,218.815	,219.964	,219.086	,218.783	,216.573	,212.425	,210.228	),
		2009=>array(214.537	,211.143	,212.193	,212.709	,213.240	,213.856	,215.693	,215.351	,215.834	,215.969	,216.177	,216.330	,215.949	),
		2010=>array(218.056	,216.687	,216.741	,217.631	,218.009	,218.178	,217.965	,218.011	,218.312	,218.439	,218.711	,218.803	,219.179	),
		2011=>array(224.939	,220.223	,221.309	,223.467	,224.906	,225.964	,225.722	,225.922	,226.545	,226.889	,226.421	,226.230	,225.672	),
		2012=>array(229.594	,226.665	,227.663	,229.392	,230.085	,229.815	,229.478	,229.104	,230.379	,231.407	,231.317	,230.221	,229.601	),
		2013=>array(232.957	,230.280	,232.166	,232.773	,232.531	,232.945	,233.504	,233.596	,233.877	,234.149	,233.546	,233.069	,233.049	),
		2014=>array(236.736	,233.916	,234.781	,236.293	,237.072	,237.900	,238.343	,238.250	,237.852	,238.031	,237.433	,236.151	,234.812	),			
		2015=>array(237.017 ,233.707	,234.722	,236.119	,236.599	,237.805	,238.638	,238.654	,238.316	,237.945	,237.838	,237.336	,236.525	),
		2016=>array(240.007	,236.916	,237.111	,238.132	,239.261	,240.229	,241.018	,240.628	,240.849	,241.428	,241.729	,241.353	,241.432	),
		2017=>array(245.119	,242.839	,243.603	,243.801	,244.524	,244.733	,244.955	,244.786	,245.519	,246.819	,246.663	,246.669	,246.524 	),
		2018=>array(null 	,247.867	,248.991	,249.554	,250.546	 																								),
 	);
	/*
		This will use the big-ol multi-dimensional array above to determine the inflation or deflation about of a given value between two 
		dates.

		$amount - The amount you want to determine inflation/deflation for.
		$in_year - The year of the amount.
		$like_year - The year you want to determine inflation/deflation for. If "today" is passed it will calculate with the most current 
		             information
	*/
	public function adjust($amount,$in_year,$like_year='today',$round_to=2){

		// Convert the date into the cpi average value (in that big table above). We have a function for this.
		$in_year_cpi = $this->get_cpi_avg($in_year);
		$like_year_cpi = $this->get_cpi_avg($like_year);

		// Get the "zero" year amount
		$zero_year_amount  = $amount/$in_year_cpi;

		// Readjust it for the like year
		$amount = $zero_year_amount*$like_year_cpi;

		// Round it off to 2 digits (because thats how money works) and return it
		return round($amount,$round_to);

	}

	/*
		This will get the last date found in the cpi_data and return it in a user readable string.
	*/
	public function data_updated(){

		// Get the year
		$data = $this->cpi_data;
		end($data);
		$year = key($data);

		// Get the month
		$month = date('F',mktime(0,0,0,count(current($data))-1,1));

		// Return a string with the month year
		return $month.' '.$year;
	}

	/*
		This will take a year and attempt to determine what CPI data to grab down to the month, if 'today' is passed it simply grabs the 
		last possible month.

		$year - the year you looking for.
	*/
	private function get_cpi_avg($year){

		// This is much simpler if your looking for today
		if($year=='today'){
			$data = $this->cpi_data;
			$data = end($data);
			$val = end($data);
			return (float)$val;

		// If it's not then I guess we have to do some work.
		}else{

			// A regular expression can solve all the worlds problems.
			preg_match_all('~([0-9]{1,2})?.?([0-9]{4}).?([0-9]{1,2})?~',$year,$date);

			// First check the year, if we can't find it were going to throw an exception and return 0, guaranteeing an obviously faulty result
			if(!isset($date[2][0])){
				trigger_error('Unable to find year from "'.$year.'"');
				return 0.0;
			
			// If we did find the year we need to check if it's a valid one, if not throw an exception and return 0 like above.
			}else if(!isset($this->cpi_data[$date[2][0]])){
				trigger_error('The year "'.$date[2][0].'" is not valid.');
				// If it's really really old return 0
				if($date[2][0]<1913){
					return 0.0;
				// If it's really really new return the newest we do have
				}else{
					return $this->get_cpi_avg('today');
				}
			
			// Finally if all seems good rename the year var so it's easier to remember for later
			}else{
				$year = $date[2][0];
			}

			// Now we can look for the month, this is not as critical, so we'll keep any errors quiet
			if($date[1][0]!=''){
				$month = (int)$date[1][0];
			}else if($date[3][0]!=''){
				$month = (int)$date[3][0];
			}else{
				$month = 0;
			}

			// Now were going to check if the month is valid, if not then were going to use the last month we have that year (this should only happen if they pick a month in the future of the current year or fail to pick a month for the current year).
			if(!isset($this->cpi_data[$year][$month])){
				$month = count($this->cpi_data[$year])-1;
			}

			// Now we can get and return the CPI average value for the specified month/year
			return (float)$this->cpi_data[$year][$month];

		}

	}

	/*
		This function will manually pull the inflation value from the BLS.gov CPI calculator. Because it uses the calculator it cannot do 
		month level conversions and only takes years. This is dramatically slower than using the built in data. Since this one does not 
		rely on the $cpi_data it can be called statically. Using this method is not recommended. Will return a generic error message if 
		it can not find an answer on the cpi calculator page.

		$ammount - The value you want to adjust
		$in_year - The year the value was what it was worth
		$like_year - The year you want to know what the value would equate to in $in_year
	*/
	public static function grabAdjustment($ammount,$in_year,$like_year=''){
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
		if(isset($inflated[1][0])){
			$inflated = $inflated[1][0];
		}else{
			$inflated = 'There is an error retriving the information from the CPI calculator.<!-- URL:'.$url.' -->';
		}

		// Clean it up and return it
		$inflated = ltrim($inflated,'$');
		return $inflated;
	}

}