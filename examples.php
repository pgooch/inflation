<html>
<head>
	<title>Inflation Class Examples</title>
	<style>
		p{
			max-width:589px;
			margin:1.5em auto;
		}
		td, th {
		    padding: .5em;
		}
		tr:nth-child(even) {
		    background: #eee;
		}
		table {
		    border-collapse: collapse;
		    margin: 1em auto;
		}
		body {}
		caption {
		    font-size: 1.5em;
		}
	</style>
</head>
<body>
<?php
date_default_timezone_set('America/Los_Angeles');
require_once('./inflation.php');
$inflation = new inflation();
?>

<p>
	The cost of a 1962 VW Beetle De Luxe (With White Wall Tires and Leatherette Interior) was $1,755.61, that would be $<?= number_format($inflation->adjust(1755.61,1962),2) ?> today.<br/>
</p>

<p>
	Despite the intended price point of $12,000 the DeLorean DMC-12 had an initial sticker price of $25,000, that would be like $<?= number_format($inflation->adjust(25000,1981,1985),0) ?> in 1985 when Doc Brown took his back to 1955 (where it would have been like $<?= number_format($inflation->adjust(25000,1981,1955),0) ?>).
</p>

<p>
	The cost of a Ferrari Testarossa in 1989 was $181,000, thats like $<?= number_format($inflation->adjust(181000,1989),0) ?> today.
</p>

<p>
	The cost of an original Apple 1 computer was $666.66 when it was released in 1976 that would be like $<?= number_format($inflation->adjust(666.66,1976,1998),2) ?> twenty-two years later when the first iMac was released.<br/>
</p>

<table>
	<caption>Nintendo Gaming Systems</caption>
	<tr>
		<th>System</th>
		<th>Release Date</th>
		<th>Original Price</th>
		<th>Adjusted Price</th>
	</tr>
	<?php
	foreach(array(
		'Nintendo Entertainment System' => array(
			'released' => '10/1985',
			'original_price' => 299.00,
		),
		'Game Boy' => array(
			'released' => '7/1989',
			'original_price' => 89.99,
		),
		'Super Nintendo Entertainment System' => array(
			'released' => '8/1991',
			'original_price' => 199.00,
		),
		'Virtual Boy' => array(
			'released' => '8/1995',
			'original_price' => 180.00,
		),
		'Nintendo 64' => array(
			'released' => '9/1996',
			'original_price' => 199.99,
		),
		'Game Boy Color' => array(
			'released' => '11/1998',
			'original_price' => 79.95,
		),
		'Game Boy Advance' => array(
			'released' => '06/2001',
			'original_price' => 149.99,
		),
		'Gamecube' => array(
			'released' => '11/2001',
			'original_price' => 199.00,
		),
		'Nintendo DS' => array(
			'released' => '11/2004',
			'original_price' => 149.99,
		),
		'Wii' => array(
			'released' => '12/2006',
			'original_price' => 249.99,
		),
		'Nintendo 3DS' => array(
			'released' => '2/2011',
			'original_price' => 249.99,
		),
		'WiiU (Basic Set)' => array(
			'released' => '11/2012',
			'original_price' => 299.99,
		),
		'WiiU (Delux Set)' => array(
			'released' => '11/2012',
			'original_price' => 349.99,
		),
	) as $system => $data){
		echo '<tr>
			<td>'.$system.'</td>
			<td>'.$data['released'].'</td>
			<td>'.$data['original_price'].'</td>
			<td>'.$inflation->adjust($data['original_price'],$data['released']).'</td>
		</tr>';
	}
	?>
</table>

<p>
Data last updated <?= $inflation->data_updated() ?>.
</p>

</body>
</html>