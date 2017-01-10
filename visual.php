<?php session_start(); 
//error_reporting(0);
$rand = $_SESSION['rand'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>GMM v2.0</title>
</head>


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
position:fixed; top:0; left:0; width:100%; height:100%;

}
/* Specify the position and layering for the content that needs to appear in front of the background image. Must have a higher z-index value than the background image. Also add some padding to compensate for removing the margin from the 'html' and 'body' tags. */
#content {position:relative; z-index:1; padding:10px;}
</style>



<script type="text/javascript">
<!--
//   this script is used to for chack all boxes or none of them
function SetAllCheckBoxes(FormName, FieldName, CheckValue)
{
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		objCheckBoxes.checked = CheckValue;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++)
			objCheckBoxes[i].checked = CheckValue;
}
// -->
</script>
 
<body></head>
<body>
	<div id="page-background"><img src="dna1.jpg" width="100%" height="100%" alt="Smile"></div>
	<div id="content">
<?php
$ref_name = $_POST['refname'];
//$order = $_POST['order'];
$seqname = $_POST['seqname'];
$protein = $_POST['protein'];
$_SESSION['seqname'] = $seqname;
$_SESSION['protein'] = $protein;
$_SESSION['ref_name'] = $ref_name;

/*
//// test of input

print $ref_name;
print "<br>";
//print_r($order);
print "<br>";
print sizeof($seqname);
print "<br>";
print_r($seqname);
print "<br>";
print_r($protein);
print "<br>";
///////////
*/
/*

$ac_list = $_SESSION['ac_list'];
$reference_index = $_SESSION['reference_index'];
$query_array_of_index = $_SESSION['queindex'];
/*
print $ac_list[$reference_index];
print "<br>";
print "<br>";
for($i=0;$i<sizeof($query_array_of_index);$i++){
print $ac_list[$query_array_of_index[$i]];
print "<br>";

print "<br>";
}
print_r($query_array_of_index);


*/


 $protein1begin = $_SESSION['protein1begin']; 
 $protein1end = $_SESSION['protein1end']; 
 $protein2begin = $_SESSION['protein2begin']; 
 $protein2end = $_SESSION['protein2end']; 
 
 /*
 print_r( $protein1begin);
 print "<br>";
  print_r( $protein1end);
  print "<br>";
   print_r( $protein2begin);
   print "<br>";
    print_r( $protein2end);
	print "<br>";
 

 
 */
 
 
 
 
$multi=0;
unset($multi_sub);
unset($multi_ins);
unset($multi_del);
//read file of mutations into an array 
for ($j=2;$j<=sizeof($seqname)+1;$j++){

$sub=$ins=$del=0;
unset($index_delete);
unset($index_insert);
unset($index_subs);
$file = './fasta_files/result'.'_'.$rand.'_'.$j.'.txt';
$f=0;
$handle = fopen($file, 'r') or die('Could not get handle');
if ($handle) {
    for ($i=0;(($buffer = fgets($handle, 4096)) !== false);$i++) {
	$x = preg_split("/[\s,]+/", $buffer);
	array_pop($x);
	if($x[1]=='-' && $x[2]!='-') {
	$index_delete[$del] = $x[0];
	$del++;
	}
	elseif($x[1]!='-' && $x[2]=='-') {
	$index_insert[$ins] = $x[0];
	$ins++;
	}
	else{
	$index_subs[$sub] = $x[0];
	$sub++;
	}
	
	
	}
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}
//############ filing multiple array of multiple file 
if(isset($index_subs))$multi_sub[$multi] =$index_subs;
else $multi_sub[$multi]=0;
if(isset($index_insert))$multi_ins[$multi] =$index_insert;
else $multi_ins[$multi]=0;
if(isset($index_delete))$multi_del[$multi] =$index_delete;
else $multi_del[$multi]=0;
$multi++;
}
/*
print "<br>";
print "<br>";
print_r($multi_sub);
 print "<br>";
 print "<br>";
 print "<br>";
print_r($multi_ins);
 print "<br>";
 print "<br>";
 print "<br>";
print_r($multi_del);
 print "<br>";
 print "<br>";
 print "<br>";
 */
 
//print_r($seqname);
$_SESSION['multi_sub'] = $multi_sub;
$_SESSION['multi_ins'] = $multi_ins;
$_SESSION['multi_del'] = $multi_del; 
 
 
 
 
 //#########################################
 $ac_list = $_SESSION['ac_list'];
 //print_r($ac_list);
 $reference_index = $_SESSION['reference_index'];
 //print($reference_index);
 if (array_key_exists($reference_index, $ac_list)) {
     $ac_ref = $ac_list[$reference_index];


 
$gblink = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=nuccore&id=$ac_ref&rettype=gbwithparts"; 


	$gblink = str_replace(" ", "%20", $gblink);
	$gbstring = file_get_contents($gblink);
	$x = preg_split("/[\s,]+/", $gbstring);
	$reference_size = $x[2];
}else{
$reference_size = 35001;	
}
//print $reference_size;
$_SESSION['reference_size'] = $reference_size ;
$num=$reference_index+1;	
$ref='./fasta_files/file'.'_'.$rand.'_'.$num.'.fasta';
$tool = file_get_contents($ref);
$tool = strlen($tool);
$_SESSION['tool'] = $tool;





?>
<br />
<font size="5"  face="Georgia, Arial, Garamond"><div align="center">THE IMAGE IS READY: </font><BR />
<br />
<br />
<br />



<form>
<input type="button" value="Back to Protein Selection" onclick="window.location.href='select.php'">


<input type="button" value="VISUAL MAP" onclick="window.location.href='aks.php'">
 
 </form>