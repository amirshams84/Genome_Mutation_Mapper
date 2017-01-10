<?php session_start(); 
$rand = $_SESSION['rand'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>GMM V2.1</title>
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

</head>
<body>
	<div id="page-background"><img src="dna3.jpg" width="100%" height="100%" alt="Smile"></div>
	<div id="content">
	
 
 
<?php

//##############refine selected protein
unset($genearray); 
if(isset($_POST['genearray']))$genearray = $_POST['genearray'];
if(isset($_SESSION['pgene']))$pgene = $_SESSION['pgene'];
if(isset($_SESSION['protein1begin']))$protein1begin = $_SESSION['protein1begin']; 
if(isset($_SESSION['protein1end'])) $protein1end = $_SESSION['protein1end']; 
if(isset($_SESSION['protein2begin'])) $protein2begin = $_SESSION['protein2begin']; 
if(isset($_SESSION['protein2end'])) $protein2end = $_SESSION['protein2end']; 
if(isset($_SESSION['protein'])) $protein = $_SESSION['protein'];
if(isset($_SESSION['forward'])) $forward = $_SESSION['forward'];
if(isset($_SESSION['reverse'])) $reverse = $_SESSION['reverse'];
if(isset($_SESSION['comp'])) $comp = $_SESSION['comp'];
unset($p1b);
unset($p1e);
unset($p2b);
unset($p2e);
unset($forw);
unset($rever);
unset($com);
$h=0;
for ($i=0;$i<sizeof($genearray);$i++){
      if(isset($genearray[$i])){
      $c = $genearray[$i];
	  }
	  //print $pgene[$c];
	  //print "<br>";
	  
	  if(isset($protein1begin[$c]))$p1b[$h]=$protein1begin[$c];
	  if(isset($protein1end[$c]))$p1e[$h]=$protein1end[$c];
	  if(isset($protein2begin[$c]))$p2b[$h]=$protein2begin[$c];
	  if(isset($protein2end[$c]))$p2e[$h]=$protein1end[$c];
	  if(isset($forward[$c]))$forw[$h]=$forward[$c];
	  if(isset($reverse[$c]))$rever[$h]=$reverse[$c];
	  if(isset($comp[$c]))$com[$h]=$comp[$c];
	  $h++;
	 }


 $_SESSION['p1b'] = $p1b ;
 $_SESSION['p1e'] = $p1e ;
 $_SESSION['p2b'] = $p2b ;
 $_SESSION['p2e'] = $p2e ;
 $_SESSION['forw'] = $forw ;
 $_SESSION['rever'] = $rever;
$_SESSION['com'] = $com ;
$align = './fasta_files/align'.'_'.$rand.'.txt';
$f=0;
$handle = fopen($align, 'r') or die('Could not get handle');
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

$ref_name = array_shift($list_of_name);
$query_list=$list_of_name;
#######################    show all information to user for edit them 
?>


<form name="aform" action="visual.php" method="post" >

<?php #######################   editing name of sequences     ?>
<br>
<font size="5"  face="Georgia, Arial, Garamond">please edit your sequence name</font><br>

<br>
<table border="2">

<tr>
<td>
<?php
echo "Reference :";
?>
</td>
<td>
<input type="text" name="refname" size="153" value="<?php print $ref_name; ?>">

</td>
</tr>
</table>
<br>
<br>
<table border="2">

<?php
for ($i=0;$i<sizeof($query_list);$i++){

?>
<tr>
<td>
<?php
  echo "Query";
  echo $i+1;// number
  
  ?>
</td>
<td>
<select name="order[]" form="aform">
<?php for ($j=0;$j<sizeof($list_of_name);$j++){?>
<option value="<?php echo $j+1;?>"  ><?php echo $j+1;?></option>
<?php }?>
</select>
</td>
<td>
<input type="text" name="seqname[]" size="150" value="<?php  print $list_of_name[$i];    ?>">


<?php
}
?>

</td>
</tr>
</table>



<?php //#############  editing proteins name ?>
<br>
<font size="5"  face="Georgia, Arial, Garamond">please edit your protein name</font><br>


<br>

<table border="2">

<?php
flush(); 
?>
<?php
 for ($i=0;$i<sizeof($genearray);$i++){
 $c = $genearray[$i];
?>

<tr>
<td>
<?php
  echo $i+1;// number
  
  ?>
</td>

<td>
<input type="text" name="protein[]" size="50" value="<?php print $pgene[$c];    ?>">


<?php
}
?>
</td>
</tr>
</table>
<BR />
<BR />
<tr>
<td>
<input type="submit" name="Submit" value="SUBMIT"/>
<INPUT TYPE="button" VALUE="BACK" onClick="history.go(-1);return true;">
</td>
</tr>

</form>




















 