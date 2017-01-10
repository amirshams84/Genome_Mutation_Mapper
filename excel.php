<?php session_start(); 

$rand = $_SESSION['rand'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

<title>GMM v2.0</title>

<style type="text/css">

	body {

		margin:0;

		padding:40px;

		background:#00ffff;

		font:80% Arial, Helvetica, sans-serif;

		color:#303030;

		line-height:180%;

	}



	h1{

		font-size:180%;

		font-weight:normal;

		color:#555;

	}

	a{

		text-decoration:none;

		color:#f30;	

	}

	p{

		clear:both;

		margin:0;

		padding:.5em 0;

	}

	pre{

		display:block;

		font:100% "Courier New", Courier, monospace;

		padding:10px;

		border:1px solid #bae2f0;

		background:#e3f4f9;	

		margin:.5em 0;

		overflow:auto;

		width:800px;

	}





	/*  */



	#tooltip{

		position:absolute;

		border:1px solid #333;

		background:#f7f5d1;

		padding:2px 5px;

		color:#333;

		display:none;

		}	



	/*  */

	#page-background {

		position:fixed; 

		top:0; 

		left:0; 

		width:110%; 

		height:110%;



	}

	/* Specify the position and layering for the content that needs to appear in front of the background image. Must have a higher z-index value than the background image. Also add some padding to compensate for removing the margin from the 'html' and 'body' tags. */

	#content {

		position:relative; 

		z-index:1; 

		padding:10px;

	}

</style>



</head>

<body>

	<div id="page-background"><img src="dna1.jpg" width="100%" height="100%" alt="Smile"></div>

	<div id="content">

<?php 

	$align = './fasta_files/align'.'_'.$rand.'.txt';

	$f=0;

	$handle = fopen($align, 'r') or die('Could not get access to align'.'_'.$rand.'.txt');

	if ($handle) {

	    for ($i=0;(($buffer = fgets($handle, 4096)) !== false);$i++) {

		 if(strpbrk($buffer,'>')){

		     $buffer=str_replace(">", "", $buffer);

	        $list_of_name[$f] = $buffer;

			$f++;

	    }

		}

	    if (!feof($handle)) {

	        echo "Error: unexpected fgets() fail\n";

	    }

	    fclose($handle);

	}



//print_r($list_of_name);

unset($query_list);

/*

	for($i=0;$i<sizeof($list_of_name);$i++){

	$temp=explode('|',$list_of_name[$i]);

	$x=explode(',',$temp[4]);

	$list_of_name[$i]=$x[0];

	}*/

	$ref_name = array_shift($list_of_name);

	$query_list=$list_of_name;

	$index=sizeof($list_of_name);

	$k=2;

	for($j=0;$j<sizeof($query_list);$j++){

		$template='./fasta_files/header'.'_'.$rand.'_'.$k.'.txt';

		$header='';

		$header.=PHP_EOL;

		$header.='REFERENCE: '.$ref_name.PHP_EOL;

		$header.='QUERY: '.$query_list[$j].PHP_EOL;

		$header.=PHP_EOL;

		file_put_contents($template,$header);

		$k++;

	}

	$k=2;

	$final='./fasta_files/Details'.'_'.$rand.'.csv';

	$fp=fopen($final,'w+');

	for($j=0;$j<sizeof($query_list);$j++){

		$fname='./fasta_files/result'.'_'.$rand.'_'.$k.'.txt';

		$template='./fasta_files/header'.'_'.$rand.'_'.$k.'.txt';

		$handle=fopen($fname,'r');



		$header=fopen($template,'r');

	while ( ($data = fgetcsv($header,$delimiter = ',') ) !== FALSE ) {

		fputcsv($fp,$data,$delimiter = ',');

	}

	while ( ($data = fgetcsv($handle,$delimiter = ',') ) !== FALSE ) {

		fputcsv($fp,$data,$delimiter = ',');

	}

	fclose($header);

	fclose($handle);

	$k++;

	}



	fclose($fp);

/*

$k=2;

for($j=0;$j<sizeof($query_list);$j++){

$fname='/Users/amirhosseinshamsaddinishahrbabak/gmm/file/result'.$k.'.txt';

$template='/Users/amirhosseinshamsaddinishahrbabak/gmm/file/header'.$k.'.txt';

unlink($fname);

unlink($template);

$k++;

}

*/







?>

<font size="5"  face="Georgia, Arial, Garamond"><div align="center">THE EXCEL FILE IS READY: </font><BR />

<BR />







<br />

<br />

<BR />



<br />

<br />

<br />

<input type="button" value="BACK TO THE BEGINING" onclick="window.location.href='index.html'">

<input type="button" value="Download IT" onclick='window.location.href="download.php?file=<?php echo './fasta_files/Details'.'_'.$rand.'.csv';?>"'>

<input type="button" value="VISUAL MAP" onclick="window.location.href='select.php'">



