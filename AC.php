<?php session_start();
	$count=0;
	$rand=0;
	while ($count<100000){
		$rand = rand(1,100000);
		$first_file='fasta_'.$rand.'_0.fasta';
		if (!file_exists($first_file))break;
		$count+=1;

	}

/*
$files = glob('./fasta_files/*'); 
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}
*/
	$_SESSION['rand']=$rand;
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>GMM v2.1</title>

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
		width:100%; 
		height:100%;

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
	<div id="page-background"><img src="dna3.jpg" width="100%" height="100%" alt="GMM"></div>
	<div id="content">
	

<?php
	set_time_limit(4800);
	#link for retrieving genbank file from NCBI
#http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=nuccore&id=AY803294.1&rettype=gbwithparts
#for genbank file
# get all ac numbers 

//###################################################   setting flag
	unset($fasta);
	unset($ac_num);
	unset($array_of_name);
	unset($ac_list);
	unset($farray);
	unset($ar);
	unset($fasta_file);
	unset($fa);
	unset($fastarray);
	unset($output);
	unset($ch);
	unset($all);
	$ac_list_size=0;
	$fasta_count=0;
	$total=0;
	$flagfa=0;
	$flagac=0;
	//#####################################################setting flag

	if(isset($_POST['AC-NUM'])){
		$ac_num = $_POST['AC-NUM'];
		if(!empty($ac_num))$flagac=1;
	}


	$fasta =$_POST['fasta'];
	if(!empty($fasta)&&strpbrk(">",$fasta))$flagfa=1;
	//else die("wrong fasta file");

	if($flagfa==0 && $flagac==0) die("nothing is inserted or your sequence is not Standard");
	//##################################################  recieving accession number & checking for correctness
	if($flagac==1){
		$ac_list = explode("\n",$ac_num);
		$ac_list = array_filter(array_map('trim', $ac_list));
		$ac_list_size = sizeof($ac_list);
		$_SESSION['ac_list'] = $ac_list;
		//print_r($ac_list);
	//print($ac_list_size);
	//print "<br>";
	//#### calling the function to save fasta file of ac numbers
	//if($ac_list_size <2 && $flagfa==0 ) die("at least two sequence should be inserted");
	for($i=0;$i<$ac_list_size;$i++){
		#calling genbak to generate file 
		genbank($ac_list[$i],$i,$rand);
		}
	}
//##################################################  recieving accession number

//################################################### receiving fasta file 


	if($flagfa==1){
	//print($fasta);
		$fasta_count=0;
		$fasta_count = substr_count($fasta,'>');

		fasta($fasta,$fasta_count,$ac_list_size,$rand);
	}
	$total=$fasta_count+$ac_list_size;
	if($total<2)die("at least two sequence should be inserted");
	//#####################################  now name extraction
	if($total!=0){
		$h=0;
		#reading file and extract the name of them store them into an array 
		for($i=0;$i<$total;$i++){
			$newfile='./fasta_files/fasta_'.$rand.'_'.$i.'.fasta';
			$farray = file($newfile);
			if(strpbrk("|",$farray[0])){
				$ar = explode("|",$farray[0]);
				$array_of_name[$i] = $ar[4];
			}else{
				$array_of_name[$i]=$farray[0];
			}
		}
	}

	//###################creating ALL
	for ($i=0;$i<$total;$i++){
		$all[$i]=$i;
	}




	$_SESSION['all']=$all;


	 // FUNCTION SECTIONS

	#####################################################genbank file creation
	function genbank($ac,$i,$rand){
		

		$LINK = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=nuccore&id=$ac&rettype=fasta";
		if (!$fp = curl_init($LINK)){
			print("you have entered a wrong ac number");
			print "<br>";
		}else{
			
			$fasta_string = file_get_contents($LINK);
			$newfile='./fasta_files/fasta_'.$rand.'_'.$i.'.fasta';
			file_put_contents($newfile, $fasta_string);
			chmod($newfile, 0777);
			
		}

		/*

		$gblink ="http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=nuccore&id=$ac&rettype=gbwithparts&retmode=text";
		if(url_exists($gblink)==1){
			//fasta link file
			$fasta_file = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=nuccore&id=$ac&rettype=fasta";
			$fasta_file = str_replace(" ", "%20", $fasta_file);
			$fasta_file = file_get_contents($fasta_file);
			$newfile='./fasta_files/fasta_'.$rand.'_'.$i.'.fasta';
			file_put_contents($newfile, $fasta_file);
			chmod($newfile, 0777);
		}elseif(url_exists($gblink)==0) {
			print("you have entered a wrong ac number");
			print "<br>";

		}
		*/
	}
	//####################################  fasta file 
	function fasta($fa,$fc,$ac_list_size,$rand){
		$fastarray = str_replace(">","@>",$fa);
		for($i=0;$i<$fc;$i++){
			$fa = explode("@",$fastarray);
		}
		array_shift($fa);
		$h=0;
		$h=$ac_list_size;
		for($k=0;$k<$fc;$k++){
			$temp = './fasta_files/fasta_'.$rand.'_'.$h.'.fasta';
			file_put_contents($temp,$fa[$k]);
			chmod($temp, 0777);
			$h++;
		}
	}
	//#####################################


	//########################check the existense

	//FUNCTION #
	function url_exists($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$output = curl_exec($ch); 
		//curl_close($ch);
		if(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) { 
			curl_close($ch);
			return 0;
		}else{
			curl_close($ch);
			return 1;
		}
	}
	############   CREATING HTML FORM TO SHOW ALL NAMES FOR THEM TO SELECT




	//###############################################SESSION

?>

<script type="text/javascript">
	var ray={
	ajax:function(st)
		{
			this.show('load');
		},
	show:function(el)
		{
			this.getID(el).style.display='';
		},
	getID:function(el)
		{
			return document.getElementById(el);
		}
	}
</script>

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

<body>
<font size="4"  face="Georgia, Arial, Garamond">STEP 2:  NOW YOU NEED TO SPECIFY YOUR "REFERENCE SEQUENCE" </font> 


<h2>

<em><strong>please select your reference sequence</strong></em><br>
</h2>
<style type="text/css">
	#load{
		position:absolute;
		z-index:1;
		border:3px double #999;
		width:300px;
		height:300px;
		margin-top:-150px;
		margin-left:-150px;
		background:#EEE;
		top:50%;
		left:50%;
		text-align:center;
		line-height:300px;
		font-family:"Trebuchet MS", verdana, arial,tahoma;
		font-size:18pt;
	}
</style>
<div id="load" style="display:none;">   Working!! Please Wait!! </div>
<form name="aform" action="align.php" method="post" onsubmit="return ray.ajax()">
<table border="2">
<?php
	flush(); 
	$n = sizeof($array_of_name);
?>
<?php
 for($c=0;$c<$n;$c++){ 
?>
	<tr>
	<td>
<?php
 echo $c+1;
?>
	</td>
	<td>
<input type="radio" name="reference" value="<?php
//this section merge name of sequence and its sequence with "@" delimiter and send them to the page 3 as refernce target  
 
 #echo $name[$c];
 #echo "@";
 #echo $marr[$c];
 #echo "@";
 echo "$c";
 ?>"><br>



	</td>
	<td>
<?php 
	echo $array_of_name[$c];
?>
	</td>
	</tr>




<?php
	}
?>
	</table>








<table>
<br><br>

<br>
<font size="4"  face="Georgia, Arial, Garamond">STEP 3:  NOW YOU NEED TO SPECIFY YOUR "QUERY SEQUENCES" </font> 
<h2>



<em><strong>please select your query sequences</strong></em><br>
</h2>



<br>

	<table border="2">
<?php
	$f2=0;
	flush(); 
	$n = sizeof($array_of_name);
?>
<?php
	for($c=0;$c<$n;$c++){ 
?>

	<tr>
	<td>
<?php
  echo $c+1;
?>
	</td>

	<td>
<input type="checkbox" name="queindex[]" value="<?php
	 #echo $name[$c];
	 #echo "@";
	 echo "$c";
	 ?>" 
	 />
 

	</td>
	<td>
<?php 
	echo $array_of_name[$c];
?>
	</td>
	</tr>




<?php
 }
 
?>


</table>
<br /><br />
<input type="button" onclick="SetAllCheckBoxes('aform', 'queindex[]', true);" value="SELECT ALL">

<input type="button" onclick="SetAllCheckBoxes('aform', 'queindex[]', false);" value="NONE OF THEM">

<BR />
<BR />

</table>

<tr>
<td>

<input type="submit" name="Submit" value="SUBMIT"/>
</td>
</tr>
<INPUT TYPE="button" VALUE="Back" onClick="history.go(-1);return true;">
</form>



</body>
</html>