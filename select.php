<?php session_start();  
error_reporting(0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>GMM v2.1</title>
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

</script>

</head>
<body>
	<div id="page-background"><img src="dna3.jpg" width="100%" height="100%" alt=""></div>
	<div id="content">

<?php

$protein1begin = $_SESSION['protein1begin']; 
$protein1end = $_SESSION['protein1end']; 
$protein2begin = $_SESSION['protein2begin']; 
$protein2end = $_SESSION['protein2end']; 
$pgene = $_SESSION['pgene']; 
$gene = $_SESSION['gene'];

//print_r($gene);
$f1=0;
for($i=0;$i<sizeof($gene);$i++){

if($i==0){
$ngene[$f1] = $gene[$i];
$place[$f1] = $i;
$f1++;
}elseif($gene[$i] != $gene[$i-1]) {
		$ngene[$f1] = $gene[$i];
		$place[$f1] = $i;
		$f1++;
}


}
/*
print_r($ngene);
print "<br>";
print "<br>";
print_r($place);


/*
print_r($pnorm1begin);
print "<br>";
print "<br>";
print_r($pnorm1end);

print "<br>";
print "<br>";


*/



?>





<font size="5"  face="Georgia, Arial, Garamond"><div align="left">Please select your proteins of interest </font><BR /><form name="aform" action="edit.php" method="post" >
<br>

<table border="2">
<?php
$f2=0;
flush(); 
$n = sizeof($ngene);
?>
<?php
 for($c=0;$c<$n;$c++){ 
?>

<tr>
<td>
<?php
  echo $c+1;// number
  ?>
</td>


<td>
<?php 
echo $ngene[$c];// name of gene
?>
</td>







<td>
<?php 
for($pro=0;$pro<sizeof($gene) ; $pro++){// protein 
	if($gene[$pro]==$ngene[$c]) {
	?>

<input type="checkbox" name="genearray[]" value="<?php echo $pro;    ?>"  />



<br/>
<?php
}
}

?>


</td>

<td>
<?php 
for($pro=0;$pro<sizeof($gene) ; $pro++){// protein 
	if($gene[$pro]==$ngene[$c]) {
		echo $pgene[$pro];
	    echo "<br>";
		
		
	}
}
?>
</td>








</tr>




<?php
 }
 
?>


</table>
<br /><br />
<input type="button" onclick="SetAllCheckBoxes('aform', 'genearray[]', true);" value="all of them!">

<input type="button" onclick="SetAllCheckBoxes('aform', 'genearray[]', false);" value="uncheck all!">

<BR />
<BR />
<tr>
<td>
<input type="submit" name="SUBMIT" value="submit"/>

</td>
</tr>




</form>
