<?php session_start(); 
error_reporting(0);
$rand = $_SESSION['rand'];
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
//########################################setting flag
	set_time_limit(480);
	unset($query_array_of_index);
	unset($reference_index);
	unset($ac_list);
	unset($ref_file_name);
	unset($ref);
	unset($ref_string);
	unset($query_file_name);
	$flagref=0;
	$flagque=0;
	$flagprotein=0;
	if(isset($_POST['reference'])){
	$reference_index = $_POST['reference'];

	if(!empty($reference_index))$flagref=1;
	}
	if(isset($_POST['queindex'])){
	$query_array_of_index = $_POST['queindex'];

	if(!empty($query_array_of_index))$flagque=1;
	}
	//////////////////////////
	$ac_list = $_SESSION['all'];
	//print_r($ac_list);

	$ref_file_name = $ac_list[$reference_index];
	//echo $ref_file_name;
	$_SESSION['reference_index'] = $reference_index;
	//#####################  CALLING PROTEIN FUNCTION

	protein($ref_file_name,$rand);

 

	$header_name_list = array();



##################################################opening fasta file for ref and query add them to align.txt and run kalign by soap
	$ref = './fasta_files/fasta'.'_'.$rand.'_'.$ref_file_name.'.fasta';
	$ref_string = file_get_contents($ref);
	$align = './fasta_files/align'.'_'.$rand.'.txt';
	file_put_contents($align,$ref_string);
	
	$first_line = fgets(fopen($ref, 'r'));
	fclose($ref);
	array_push($header_name_list, $first_line);
	for($i=0;$i<sizeof($query_array_of_index);$i++){
		if($query_array_of_index[$i]!=$reference_index){
			$query_file_name = $ac_list[$query_array_of_index[$i]];
			$que = './fasta_files/fasta'.'_'.$rand.'_'.$query_file_name.'.fasta';
			$first_line = fgets(fopen($que, 'r'));
			fclose($que);
			array_push($header_name_list, $first_line);
			$query_string = file_get_contents($que);
			file_put_contents($align,$query_string,FILE_APPEND);
		}
	}
	chmod($align, 0777);
	//initiate the alignment
	soap($align,$rand);
	//print_r($header_name_list);
############################################################## end of Soap


	$_SESSION['reference_index'] = $reference_index;
	$_SESSION['queindex'] = $query_array_of_index ;

	################## runing kalign by soap

	function soap($align,$rand){
	set_time_limit(4800);
	//shell_exec('/usr/bin/perl ./soap/kalign.pl --email ashamsad@gmu.edu --stype dna --outfile ./fasta_files/SOAP'.$rand.' --format fasta ./fasta_files/align'.'_'.$rand.'.txt');
	shell_exec('./soap/mafft.bat --auto --thread 5 --treeout ./fasta_files/align'.'_'.$rand.'.txt > ./fasta_files/mafft'.$rand.'.fasta 2> ./fasta_files/mafft_log'.$rand.'.txt');
	chmod('./fasta_files/mafft'.$rand.'.fasta', 0777);
	chmod('./fasta_files/mafft_log'.$rand.'.txt', 0777);
	chmod('./fasta_files/align'.'_'.$rand.'.txt.tree', 0777);
	rename('./fasta_files/align'.'_'.$rand.'.txt.tree','./fasta_files/GMM2_phylogram_'.$rand.'.tree');

	}
?>
<?php

//Compare region
######################################################################
##########################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################
//############################### UNSET REGION
//$index_of_file_name
//$f

//##############################

	ini_set('memory_limit', '-1');
	ini_set("auto_detect_line_endings", true);
	########################### Breaking the result of Soap into multiple file 
	$index_of_file_name=1;
	//$handle = fopen('./fasta_files/SOAP'.$rand.'..fasta', 'r') or die('alignment was not successfull');
	$handle = fopen('./fasta_files/mafft'.$rand.'.fasta', 'r') or die('alignment was not successfull');
	if ($handle) {
	    for ($i=0;(($buffer = fgets($handle, 4096)) !== false);$i++) {
	 	if(strpbrk($buffer,'>')){
	        $fname = './fasta_files/file'.'_'.$rand.'_'.$index_of_file_name.'.fasta';
			file_put_contents($fname,$buffer);
			$index_of_file_name++;
	    }else{
		    $buffer = preg_replace('/\s+/', '', $buffer);
		    file_put_contents($fname,$buffer,FILE_APPEND);
			}
		 
		}
	    if (!feof($handle)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($handle);
	}
	chmod($fname, 0777);
//##################################################


//##################################################
	$f=2;//each que file
	while($f<$index_of_file_name){
	$h_ref = fopen('./fasta_files/file'.'_'.$rand.'_'.'1.fasta', 'r') or die('Couldnot get handle');
	$h_que = fopen('./fasta_files/file'.'_'.$rand.'_'.$f.'.fasta', 'r') or die('Couldnot get handle');
	unset($a_dist);
	unset($a_ref_dist);
	unset($a_que_dist);

	$mute_number=0;
	$index=0;
	unset($a_ref);
	unset($b_ref);
	unset($a_que);
	unset($b_que);



	while ((($b_ref = fgets($h_ref, 4096)) !== false)&&(($b_que = fgets($h_que, 4096)) !== false)) {
		if(!strpbrk($b_ref,'>')&&!strpbrk($b_que,'>')){
				#print($index);
				#print "<br>";
		        if(similar_text($b_ref,$b_que)==0){
				$index+=sizeof($b_ref);
				}else{
			     $a_ref = str_split($b_ref);
				 #print(count($a_ref));
			     $a_que = str_split($b_que);
			     for($j=0;$j<sizeof($a_ref);$j++){
				 	if($a_ref[$j]==$a_que[$j]){
				 		$index++;
		     		}else{

				      $index++;
				      $a_ref_dist[$mute_number]=$a_ref[$j];
					  $a_que_dist[$mute_number]=$a_que[$j];
					  $a_dist[$mute_number]=$index;
					  $mute_number++;
						}
					}
				}		  
				 
				 
		}
	}		
				 
	fclose($h_ref);
	fclose($h_que);
	/*
	print_r($a_dist);
	print "<br>";
	print_r($a_ref_dist);
	print "<br>";
	print_r($a_que_dist);
	print "<br>";
	*/
//###########################################################
// we want to set a point for merging adjacent indels here

//$indels_ref
//$indels_que
//$indels_index
	$in_part=0;
	$del_part=0;
	$sub_part=0;
	unset($insert_ref);
	unset($insert_que);
	unset($insert_index);
	unset($del_ref);
	unset($del_que);
	unset($del_index);
	unset($sub_ref);
	unset($sub_que);
	unset($sub_index);




//#########################




	for($i=0;$i<count($a_dist);$i++){
	if( $a_ref_dist[$i]=='-' && $a_que_dist[$i]!='-' ){
	$insert_ref[$in_part] = $a_ref_dist[$i];
	$insert_que[$in_part]=$a_que_dist[$i];
	$insert_index[$in_part]=$a_dist[$i];
	$in_part++;
	}elseif( $a_que_dist[$i]=='-'&& $a_ref_dist[$i]!='-'){
	$del_ref[$del_part] = $a_ref_dist[$i];
	$del_que[$del_part]=$a_que_dist[$i];
	$del_index[$del_part]=$a_dist[$i];
	$del_part++;
	}elseif( $a_ref_dist[$i]!='-' && $a_que_dist[$i]!='-' ){
	$sub_ref[$sub_part] = $a_ref_dist[$i];
	$sub_que[$sub_part] = $a_que_dist[$i];
	$sub_index[$sub_part]=$a_dist[$i];
	$sub_part++;

	}

	}

/*
//###################   testing region
if(isset($insert_index)){
print_r($insert_index);
print "<br>";
print_r($insert_ref);
print "<br>";
print_r($insert_que);
print "<br>";
print "<br>";
}
if(isset($del_index)){
print_r($del_index);
print "<br>";
print_r($del_ref);
print "<br>";
print_r($del_que);
print "<br>";
print "<br>";

}
//###############################
*/



//############################# set up flag 
	$sub_flag =$ins_flag=$del_flag = 0;
	if(isset($sub_index))$sub_flag=1;
	if(isset($insert_index))$ins_flag=1;
	if(isset($del_index))$del_flag=1;
	//#############################   now merging 
	if($ins_flag==1){
	unset($nomre);
	unset($jens);
	unset($new1);
	unset($refs);
	unset($ins_ref);
	$new1=$insert_index;
	$ins=$insert_que;
	$ins_ref=$insert_ref;
	for($j=1;$j<sizeof($insert_index);$j++){
	  for($i=0;$i<sizeof($insert_index);$i++){
  		if(isset($new1[$i+$j])){
      		if($new1[$i]+$j == $new1[$i+$j]){
			   $t1 = $ins[$i];
			   $t2 = $ins[$i+$j];
			   $t = $t1.$t2;
			   $ins[$i] = $t;
			   $new1[$i+$j]="@";
			   $ins[$i+$j]="@";
			   $ins_ref[$i+$j] = "@";
   			}
			   }
	         }
	       }
		   /*
	print "insert";
	print ($f);
	print "<br>";
	print "<br>";
	print_r($new1);
	print "<br>";
	print_r($ins_ref);
	print "<br>";
	print_r($ins);
	print "<br>";
	print "<br>";
	*/
	$em=0;
	for($i=0;$i<sizeof($new1);$i++){
		      if($new1[$i]!="@"){
			  $nomre[$em]=$new1[$i];
			  }
			  if($ins[$i]!="@"){
			  $jens[$em]=$ins[$i];
			  $refs[$em]=$ins_ref[$i];
			  $em++;
			  }
			  
			  
	  }
  
/*  
  //#####################  testing region
  print "<br>";
print "<br>";
  print "insert result for genome number :";
  print ($f);
  print "<br>";
  print "<br>";
  
 print "index of insert:  ";
 print_r($nomre);
  print "<br>";
  print "query:     ";
  print_r($jens);
  print "<br>";
 print " refernec:   "; 
  print_r($refs);
  print"<br>";
  
  
  
 */ 
 //################################ 
$insert_index=$nomre;
$insert_que=$jens;
$insert_ref = $refs;
}
// ####################################   delete merge 
if($del_flag==1){
unset($nomre);
unset($jens);
unset($new2); 
unset($pjens);
unset($temps);
$new2=$del_index;
$del=$del_ref;
$temps=$del_que;
for($j=1;$j<sizeof($del_index);$j++){
  for($i=0;$i<sizeof($del_index);$i++){
         if(isset($new2[$i+$j])){
	      if($new2[$i]+$j == $new2[$i+$j]){
		   $t1 = $del[$i];
		   $t2 = $del[$i+$j];
		   $t = $t1.$t2;
		   $del[$i] = $t;
		   $new2[$i+$j]="@";
		   $del[$i+$j]="@";
		   $temps[$i+$j]="@";
	       
		   }
		   }
         }
       }
/*
print_r($new);
print "<br>";
print_r($del);
print "<br>";
print_r($temps);
print "<br>";
*/
$em=0;
  for($i=0;$i<sizeof($new2);$i++){
	      if($new2[$i]!="@"){
		  $nomre[$em]=$new2[$i];
		  $pjens[$em]=$temps[$i];
		  }
		  if($del[$i]!="@"){
		  $jens[$em]=$del[$i];
		  
		  $em++;
		  }
		  
		  
  }
  
 /* 
  //################# testing region 
 print "<br>";
 print "<br>";
  print "delete result"; 
  
 print "<br>";
 print "<br>";
 print "index of delete:   ";
 print_r($nomre);
  print "<br>";
  print " refernce:   ";
  
  print_r($jens);
  print "<br>";
  print " query:   ";
  print_r($pjens);
  print "<br>";
//###############################

*/
$del_index=$nomre;
$del_ref=$jens;
$del_que = $pjens;

}

//  now we want to merge back file to its origin part

unset($a_dist);
unset($a_ref_dist);
unset($a_que_dist);


if($sub_flag==1){$a_dist=$sub_index;
$a_ref_dist =$sub_ref;
$a_que_dist =$sub_que;
}
if($ins_flag==1){
$a_dist=array_merge($a_dist,$insert_index);
$a_ref_dist = array_merge($a_ref_dist,$insert_ref);
$a_que_dist = array_merge($a_que_dist,$insert_que);
}
if($del_flag==1){
$a_dist=array_merge($a_dist,$del_index);
$a_ref_dist = array_merge($a_ref_dist,$del_ref);
$a_que_dist = array_merge($a_que_dist,$del_que);
}

//###########################  BUBBLE SORT
/*
print_r($a_dist);
print "<br>";
print_r($a_ref_dist);
print "<br>";
print_r($a_que_dist);
print "<br>";
*/
for ($i=0;$i<count($a_dist);$i++){
for($j=0;$j<count($a_dist);$j++){
if($a_dist[$i]<$a_dist[$j]){
////////////
$t=$a_dist[$i];
$a_dist[$i]=$a_dist[$j];
$a_dist[$j]=$t;
///////////
$tr = $a_ref_dist[$i];
$a_ref_dist[$i] = $a_ref_dist[$j];
$a_ref_dist[$j]=$tr;
//////////////
$tq = $a_que_dist[$i];
$a_que_dist[$i]=$a_que_dist[$j];
$a_que_dist[$j]=$tq;


}
}
}

/*
print_r($a_dist);
print "<br>";
print_r($a_ref_dist);
print "<br>";
print_r($a_que_dist);
print "<br>";

*/

//############################  merging protein name 

$protein1begin = $_SESSION['protein1begin']; 
$protein1end = $_SESSION['protein1end']; 
$protein2begin = $_SESSION['protein2begin']; 
$protein2end = $_SESSION['protein2end']; 
$pgene = $_SESSION['pgene']; 
 $forward = $_SESSION['forward'] ;
 $reverse = $_SESSION['reverse'] ;
 $comp = $_SESSION['comp'] ;
unset($pstring);
for($i=0;$i<sizeof($a_dist);$i++){
$pstring[$i]='';
}

for($i=0;$i<sizeof($a_dist);$i++){
$p=0;
while($p<sizeof($pgene)){
if($comp[$p]==0){
//if(!isset($a_dist[$i]) || !isset($protein1begin[$p]) || isset($protein1end[$p]) )continue;
if($a_dist[$i]>=$protein1begin[$p] && $a_dist[$i]<=$protein1end[$p]){
//$pstring[$p]=str_replace('UTR','',$pstring[$p]);
$pstring[$i].=$pgene[$p];
$pstring[$i].='/';
}
}elseif($comp[$p]==1){
if($a_dist[$i]>=$protein1begin[$p] && $a_dist[$i]<=$protein1end[$p]){
//$pstring[$p]=str_replace('UTR','',$pstring[$p]);
$pstring[$i].=$pgene[$p];
$pstring[$i].='/';
}elseif($a_dist[$i]>=$protein2begin[$p] && $a_dist[$i]<=$protein2end[$p]){
//
$pstring[$i].=$pgene[$p];
$pstring[$i].='/';

}
}

$p++;
}


}
for($i=0;$i<sizeof($a_dist);$i++){
if(strstr($pstring[$i],'/')==false) $pstring[$i]='UTR';
}





$fp = fopen('./fasta_files/result'.'_'.$rand.'_'.$f.'.txt', 'w');

$k=0;
${'muta_dict'.$f}=array();
while($k<count($a_dist)){
	${'muta_dict'.$f}[$a_dist[$k]] = array($a_ref_dist[$k],$a_que_dist[$k]);
	fprintf($fp,"%d,%s,%s,%s\n",$a_dist[$k],$a_ref_dist[$k],$a_que_dist[$k],$pstring[$k]);


$k++;
}

fclose($fp);

chmod('./fasta_files/result'.'_'.$rand.'_'.$f.'.txt', 0777);

$f++;

unset($a_dist);
unset($a_ref_dist);
unset($a_que_dist);


}

//here we are going to create excel file
$max_dist_count = 0;
$number_of_dist = 2;
/*
while($number_of_dist< $f){
	if ($max_dist_count < ${'muta_dict'.$number_of_dist}[sizeof(${'muta_dict'.$number_of_dist})-1]){
			$max_dist_count = ${'muta_dict'.$number_of_dist}[sizeof(${'muta_dict'.$number_of_dist})-1]);
	}
	$number_of_dist++;
}*/
$string ="POSITION\tREFERENCE:".ltrim(trim($header_name_list[0]),'>')."\t";
$ref_string = '';
$que_string = '';
for($i=1; $i<sizeof($header_name_list); $i++){
	$string .= "QUERY_".strval($i).":".ltrim(trim($header_name_list[$i]),'>')."\t";
}
//$string .=$ref_string .PHP_EOL;
//$string .=$que_string .PHP_EOL;
$string .= PHP_EOL;
//$string = "hello\n";
$count = 2;
$added_string = '';
//echo " this is f";
//printf($f);
//$max_dist_count = 2;
//printf($max_dist_count);
for($i=1; $i<40000; $i++){
	$count=2;
	$flag = False;
	$first_string = '';
	$second_string = '';
	while($count < $f){
		//print_r(${'muta_dict'.$count});
		if(array_key_exists($i, ${'muta_dict'.$count})==True && $flag==False){
			$flag = True;
			$first_string = $i . "\t" . ${'muta_dict'.$count}[$i][0]. "\t".${'muta_dict'.$count}[$i][1]. "\t";
		}elseif(array_key_exists($i, ${'muta_dict'.$count})==True && $flag==True){
			$second_string .= ${'muta_dict'.$count}[$i][1]."\t";
		
		}
		elseif(array_key_exists($i, ${'muta_dict'.$count})==False){
			$second_string .="\t";
		}
		$count++;	

	}
	if($flag == True){
		$string .= strtoupper($first_string) . strtoupper($second_string)."\n";
	}
}
//printf($string);
//$test = fopen('./fasta_files/test'.'_'.$rand.'_'.$f.'.txt', 'w');
file_put_contents('./fasta_files/GMM2_Comparision_file_'.$rand.'.txt', $string);
chmod('./fasta_files/GMM2_Comparision_file_'.$rand.'.txt', 0777);
//##########################################  THE END OF COMPARE 



//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//###############################################

function protein($ref,$rand){
	$newfile='./fasta_files/fasta'.'_'.$rand.'_'.$ref.'.fasta';
	$farray = file($newfile);
	if(strpbrk("|",$farray[0])){
		$ar = explode("|",$farray[0]);
		$ref_name = $ar[3];
	}else{
		$ar = explode(">",$farray[0]);
		$ref_name=$ar[1];
	}
	$url = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=nuccore&id=$ref_name&rettype=fasta_cds_na";
	$blah=0;
	$protein1begin=array(); 
	$protein1end=array();  
	$protein2begin=array();
	$protein2end=array();
	$pgene=array(); 
	$forward=array();
	$reverse=array();
	$comp=array();
	$gene=array();
	if(url_exists($url)==1){
	//if($blah){	
		ini_set('memory_limit', '-1');
		$myarray = file($url);
		//print_r($myarray);
		//  search that array for ">" sign and put each gene protein and location into $name
		$s = sizeof($myarray);
		$k=0;
		for($i=0;$i<$s;$i++){
			if(strpbrk($myarray[$i],">") ){
				 $name[$k]=$myarray[$i];
				 $loop[$k] = $i;
				 $k++;
			}
		}
		$n= sizeof($name);
	//print_r($name);
///       GENE FINDING 
	$gflag=0;
	for($j=0;$j<$n;$j++){
		$pos = strpos($name[$j], "[gene=");
		if($pos){
			$temp11 = explode("[gene=",$name[$j]);
		    $temp21[$j] = $temp11[1];
		    $temp31 = explode("] [",$temp21[$j]);
		    $gene[$j] = $temp31[0];
			$gflag=1;
		}elseif($j>0 && $gflag==1){
			$gene[$j] =$gene[$j-1];
		}else{
			$gene[$j] = "-";
		}
	
	}
///                        PROTEIN FINDING
//    break the $name file to extract the genes name and their location

for($j=0;$j<$n;$j++){
	$pos = strpos($name[$j], "[protein=");
	if($pos){ 
		$temp1 = explode("[protein=",$name[$j]);
	    $temp2[$j] = $temp1[1];
	    $temp3 = explode("] [",$temp2[$j]);
	    $pgene[$j] = $temp3[0];
	    $temp4[$j] = $temp3[2];
	    $temp5 =  explode("location=",$temp4[$j]);
	    $pspatt[$j] = $temp5[1];
	}
	
}
	//print "<br>";
//    now we have to do this for protein location again exactly 
//   extracting exact location of each protein
////////////////////////////   refining each location 
//    we have four types of location 
//    1- normal  1234...1244
//    2- complement  1234...1244 and 1345..1380
//    3- reverse normal   1344...1244
//    4- reverse complement   1344..1244 and 1210...1190


//finding norm location of protein
	$d = sizeof($pspatt);
	//print_r($pspatt);
	for($j=0;$j<$d;$j++){
	    if(strstr($pspatt[$j],'complement(join')){
	    	$pcomp1j[$j] = $pspatt[$j];
	    }
		elseif(strstr($pspatt[$j],'join') && strstr($pspatt[$j],'t(j')==false){ 
			$pjoin1[$j] = $pspatt[$j];
		}
		elseif(strstr($pspatt[$j],'complement') && strstr($pspatt[$j],'t(j')==false){ 
			$pcomp1[$j] = $pspatt[$j];
		}
		else{ 
			$pnorm1[$j] = $pspatt[$j];
		}
	}

	/*
	print "<br>";
	print "<br>";
	print "hello";
	print_r($pnorm1);
	print "<br>";
	print "<br>";
	print_r($pjoin1);
	print "<br>";
	print "<br>";
	print_r($pcomp1);
	print "<br>";
	print "<br>";
	print_r($pcomp1j);
	print "<br>";
	print "<br>";

	*/
##   extraction exact location

//#####################norm
	for($i=0;$i<$d;$i++){
		if(isset($pnorm1[$i])){
			$temp = explode("..",$pnorm1[$i]);
			$pnorm1begin[$i] = $temp[0];
			$temp1 = $temp[1];
			$temp2 = explode("]",$temp1);
			$pnorm1end[$i] = $temp2[0];
		}
	}

//############## join
	for($i=0;$i<$d;$i++){
		if(isset($pjoin1[$i])){
			$temp1 = explode("(",$pjoin1[$i]);
			$temp2[$i] = $temp1[1];
			$temp3 = explode(")",$temp2[$i]);
			$temp4[$i] = $temp3[0];
			$temp5 = explode(",",$temp4[$i]);
			$temp6[$i] = $temp5[0];
			$temp7[$i] = $temp5[1];
			$temp8 = explode("..",$temp6[$i]);
			$temp1begin[$i] = $temp8[0];
			$temp1end[$i] = $temp8[1];
			$temp9 = explode("..",$temp7[$i]);
			$temp2begin[$i] = $temp9[0];
			$temp2end[$i] = $temp9[1];
			
			$pjoin1begin[$i] = $temp1begin[$i];
			$pjoin2begin[$i] = $temp2begin[$i];
			
			$pjoin1end[$i] = $temp1end[$i];
			$pjoin2end[$i] = $temp2end[$i];
			
		}
	}
	//print_r($pcomp1);

///############### ###########   complement join
	for($i=0;$i<$d;$i++){
		if(isset($pcomp1j[$i])){
			$temp0 = explode("complement(join(",$pcomp1j[$i]);
			$tempk[$i] = $temp0[1];
	        $temp3 = explode("))]",$tempk[$i]);
	        $temp4[$i] = $temp3[0];
	        $temp5 = explode(",",$temp4[$i]);
	        $temp6[$i] = $temp5[0];
	        $temp7[$i] = $temp5[1];
	        $temp8 = explode("..",$temp6[$i]);
	        $temp1begin[$i] = $temp8[0];
	        $temp1end[$i] = $temp8[1];
	        $temp9 = explode("..",$temp7[$i]);
	        $temp2begin[$i] = $temp9[0];
	        $temp2end[$i] = $temp9[1];
	   	    $pcomp1begin[$i] = $temp1begin[$i];
			$pcomp2begin[$i] = $temp2begin[$i];
		    $pcomp1end[$i] = $temp1end[$i];
			$pcomp2end[$i] = $temp2end[$i];
	       
		}
	}
		
//############################compnorm		
	for($i=0;$i<$d;$i++){
		if(isset($pcomp1[$i])){
			$temp = explode("complement(",$pcomp1[$i]);
		    $temp0[$i] = $temp[1];
			$temp1 = explode("..",$temp0[$i]);
		    $pcompnorm1begin[$i] = $temp1[0];
		    $temp2 = $temp1[1];
		    $temp3 = explode(")",$temp2);
		    $pcompnorm1end[$i] = $temp3[0];
		}
	}
		
		
	
	
//############################################################################
//how to truncate protein name

	$spgene = sizeof($pgene);
	for($i=0;$i<$spgene;$i++) $newname[$i] = str_replace("protein","",$pgene[$i]);
	unset($pgene);
	for($i=0;$i<$spgene;$i++) $pgene[$i] = str_replace(" kDa","",$newname[$i]);
	unset($newname);
	for($i=0;$i<$spgene;$i++) $newname[$i] = str_replace("associated","",$pgene[$i]);
	unset($pgene);
	for($i=0;$i<$spgene;$i++) $pgene[$i] = str_replace("precursor","",$newname[$i]);
	unset($newname);
	for($i=0;$i<$spgene;$i++) $newname[$i] = str_replace("hypothetical","",$pgene[$i]);
	unset($pgene);
	for($i=0;$i<$spgene;$i++) $pgene[$i] = str_replace("truncated","",$newname[$i]);
	unset($newname);
	for($i=0;$i<$spgene;$i++) $newname[$i] = str_replace("control","",$pgene[$i]);
	unset($pgene);
	for($i=0;$i<$spgene;$i++) $pgene[$i] = str_replace("single-stranded","",$newname[$i]);
	unset($newname);
	for($i=0;$i<$spgene;$i++) $newname[$i] = str_replace("hexon assembly","",$pgene[$i]);
	unset($pgene);
	for($i=0;$i<$spgene;$i++) $pgene[$i] = str_replace("putative","",$newname[$i]);
	unset($newname);
	for($i=0;$i<$spgene;$i++) $newname[$i] = str_replace("late ","",$pgene[$i]);
	unset($pgene);
	for($i=0;$i<$spgene;$i++) $pgene[$i] = str_replace("early","",$newname[$i]);
	unset($newname);
	for($i=0;$i<$spgene;$i++) $newname[$i] = str_replace("polypeptide ","p",$pgene[$i]);
	unset($pgene);
	$pgene = $newname;
	unset($newname);

//###################################################




	$_SESSION['pgene'] = $pgene;
	$_SESSION['pnorm1begin'] = $pnorm1begin;
	$_SESSION['pnorm1end'] = $pnorm1end;
	if(isset($pjoin1begin)){
		$_SESSION['pjoin1begin'] = $pjoin1begin;
		$_SESSION['pjoin1end'] = $pjoin1end;
		$_SESSION['pjoin2begin'] = $pjoin2begin;
		$_SESSION['pjoin2end'] = $pjoin2end;
	}
	if(isset($pcomp1begin)){
		$_SESSION['$pcomp1begin'] = $pcomp1begin;
		$_SESSION['$pcomp1end'] = $pcomp1end;
		$_SESSION['$pcomp2begin'] = $pcomp2begin;
		$_SESSION['$pcomp2end'] = $pcomp2end;
	}
	if(isset($pcompnorm1begin)){
		$_SESSION['$pcompnorm1begin'] = $pcompnorm1begin;
		$_SESSION['$pcompnorm1end']=$pcompnorm1end;
	}

//################   testing point
	/*
	print_r($gene);
	print "<br>";
	print "<br>";
	print_r($pgene);
	print "<br>";
	print "<br>";
	*/

//#################



	$f1=0;
	for($i=0;$i<sizeof($gene);$i++){
		if($i==0){
			$ngene[$f1] =$gene[$i];
			$place[$f1] = $i;
			$f1++;
		}elseif($gene[$i] != $gene[$i-1]){
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




//############################creating array of protein


	unset($forward);
	unset($reverse);
	unset($comp);
	for($i=0;$i<sizeof($pgene);$i++){
		$forward[$i]=0;
		$reverse[$i] = 0;
		$comp[$i] = 0;
	}
	$h=0;
	for ($i=0;$i<sizeof($pgene);$i++){
      //$c = $pgene[$i];
	  //print $pgene[$i];
	  //print "<br>";
	  
	  if(isset($pnorm1begin[$i])){
	      $protein1begin[$h]=$pnorm1begin[$i];
		  $protein1end[$h] = $pnorm1end[$i];
		  $protein2begin[$h]=$pnorm1begin[$i];
		  $protein2end[$h] = $pnorm1end[$i];
		  $forward[$h]=1;
		  
		  $h++;
	  	}
	  elseif(isset($pjoin1begin[$i])){
	      $protein1begin[$h]=$pjoin1begin[$i];
		  $protein1end[$h] = $pjoin1end[$i];
		  $protein2begin[$h]=$pjoin2begin[$i];
		  $protein2end[$h] = $pjoin2end[$i];
		  $forward[$h]=1;
		  $comp[$h]=1;
		  $h++;
	 	}
       elseif(isset($pcomp1begin[$i])){
	      $protein1begin[$h]=$pcomp1begin[$i];
		  $protein1end[$h] = $pcomp1end[$i];
		  $protein2begin[$h]=$pcomp2begin[$i];
		  $protein2end[$h] = $pcomp2end[$i];
		  $reverse[$h]=1;
		  $comp[$h]=1;
		  $h++;
       	}
	   elseif(isset($pcompnorm1begin[$i])){
	      $protein1begin[$h]=$pcompnorm1begin[$i];
		  $protein1end[$h] = $pcompnorm1end[$i];
		  $protein2begin[$h]=$pcompnorm1begin[$i];
		  $protein2end[$h] = $pcompnorm1end[$i];
		  $reverse[$h]=1;
		  $h++;
		}
			
	}
	/*
		print_r($gene);
		print "<br>";
		print "<br>";
		print_r($pgene);
		print "<br>";
		print "<br>";
		print_r($protein1begin);
		print "<br>";
		print "<br>";
		print_r($protein1end);
		print "<br>";
		print "<br>";
		print_r($protein2begin);
		print "<br>";
		print "<br>";
		print_r($protein2end);
		print "<br>";
		print "<br>";
		print_r($forward);
		print "<br>";
		print "<br>";
		print_r($reverse);
		print "<br>";
		print "<br>";
		print_r($comp);
		
*/

	
	
	}else{
		//the static flag is on

		$hexon_gene='L3';
		$hexon_protein='hexon';
		$hexon_1_begin=17810;
		$hexon_1_end=20635;
		$hexon_2_begin=17810;
		$hexon_2_end=20635;
		$hexon_forward=1;
		$hexon_reverse=0;
		$hexon_comp=0;
		array_push($gene, $hexon_gene);
		array_push($pgene, $hexon_protein);
		array_push($protein1begin, $hexon_1_begin);
		array_push($protein1end, $hexon_1_end);
		array_push($protein2begin, $hexon_2_begin);
		array_push($protein2end, $hexon_2_end);
		array_push($forward, $hexon_forward);
		array_push($reverse, $hexon_reverse);
		array_push($comp, $hexon_comp);
		$penton_gene='L2';
		$penton_protein='penton';
		$penton_1_begin=13532;
		$penton_1_end=15094;
		$penton_2_begin=13532;
		$penton_2_end=15094;
		$penton_forward=1;
		$penton_reverse=0;
		$penton_comp=0;
		array_push($gene, $penton_gene);
		array_push($pgene, $penton_protein);
		array_push($protein1begin, $penton_1_begin);
		array_push($protein1end, $penton_1_end);
		array_push($protein2begin, $penton_2_begin);
		array_push($protein2end, $penton_2_end);
		array_push($forward, $penton_forward);
		array_push($reverse, $penton_reverse);
		array_push($comp, $penton_comp);
		$fiber_gene='L5';
		$fiber_protein='fiber';
		$fiber_1_begin=30926;
		$fiber_1_end=32029;
		$fiber_2_begin=30926;
		$fiber_2_end=32029;
		$fiber_forward=1;
		$fiber_reverse=0;
		$fiber_comp=0;
		array_push($gene, $fiber_gene);
		array_push($pgene, $fiber_protein);
		array_push($protein1begin, $fiber_1_begin);
		array_push($protein1end, $fiber_1_end);
		array_push($protein2begin, $fiber_2_begin);
		array_push($protein2end, $fiber_2_end);
		array_push($forward, $fiber_forward);
		array_push($reverse, $fiber_reverse);
		array_push($comp, $fiber_comp);
		$e1a_gene='E1A';
		$e1a_protein='E1A';
		$e1a_1_begin=572;
		$e1a_1_end=1426;
		$e1a_2_begin=572;
		$e1a_2_end=1426;
		$e1a_forward=1;
		$e1a_reverse=0;
		$e1a_comp=0;
		array_push($gene, $e1a_gene);
		array_push($pgene, $e1a_protein);
		array_push($protein1begin, $e1a_1_begin);
		array_push($protein1end, $e1a_1_end);
		array_push($protein2begin, $e1a_2_begin);
		array_push($protein2end, $e1a_2_end);
		array_push($forward, $e1a_forward);
		array_push($reverse, $e1a_reverse);
		array_push($comp, $e1a_comp);
		$pIVa_gene='pIVa';
		$pIVa_protein='pIVa';
		$pIVa_1_begin=3907;
		$pIVa_1_end=5531;
		$pIVa_2_begin=3907;
		$pIVa_2_end=5531;
		$pIVa_forward=0;
		$pIVa_reverse=1;
		$pIVa_comp=1;
		array_push($gene, $pIVa_gene);
		array_push($pgene, $pIVa_protein);
		array_push($protein1begin, $pIVa_1_begin);
		array_push($protein1end, $pIVa_1_end);
		array_push($protein2begin, $pIVa_2_begin);
		array_push($protein2end, $pIVa_2_end);
		array_push($forward, $pIVa_forward);
		array_push($reverse, $pIVa_reverse);
		array_push($comp, $pIVa_comp);
		$e2b_gene='e2b';
		$e2b_protein='pTP';
		$e2b_1_begin=8330;
		$e2b_1_end=10183;
		$e2b_2_begin=8330;
		$e2b_2_end=10183;
		$e2b_forward=1;
		$e2b_reverse=0;
		$e2b_comp=0;
		array_push($gene, $e2b_gene);
		array_push($pgene, $e2b_protein);
		array_push($protein1begin, $e2b_1_begin);
		array_push($protein1end, $e2b_1_end);
		array_push($protein2begin, $e2b_2_begin);
		array_push($protein2end, $e2b_2_end);
		array_push($forward, $e2b_forward);
		array_push($reverse, $e2b_reverse);
		array_push($comp, $e2b_comp);
		$pIIIa_gene='L1';
		$pIIIa_protein='pIIIa';
		$pIIIa_1_begin=10786;
		$pIIIa_1_end=12477;
		$pIIIa_2_begin=10786;
		$pIIIa_2_end=12477;
		$pIIIa_forward=1;
		$pIIIa_reverse=0;
		$pIIIa_comp=0;
		array_push($gene, $pIIIa_gene);
		array_push($pgene, $pIIIa_protein);
		array_push($protein1begin, $pIIIa_1_begin);
		array_push($protein1end, $pIIIa_1_end);
		array_push($protein2begin, $pIIIa_2_begin);
		array_push($protein2end, $pIIIa_2_end);
		array_push($forward, $pIIIa_forward);
		array_push($reverse, $pIIIa_reverse);
		array_push($comp, $pIIIa_comp);
		$dna_gene='E2A';
		$dna_protein='DNA_binding';
		$dna_1_begin=21410;
		$dna_1_end=22782;
		$dna_2_begin=21310;
		$dna_2_end=22782;
		$dna_forward=0;
		$dna_reverse=1;
		$dna_comp=0;
		array_push($gene, $dna_gene);
		array_push($pgene, $dna_protein);
		array_push($protein1begin, $dna_1_begin);
		array_push($protein1end, $dna_1_end);
		array_push($protein2begin, $dna_2_begin);
		array_push($protein2end, $dna_2_end);
		array_push($forward, $dna_forward);
		array_push($reverse, $dna_reverse);
		array_push($comp, $dna_comp);
		$pVIII_gene='L4';
		$pVIII_protein='pVIII';
		$pVIII_1_begin=25522;
		$pVIII_1_end=26205;
		$pVIII_2_begin=25522;
		$pVIII_2_end=26205;
		$pVIII_forward=1;
		$pVIII_reverse=0;
		$pVIII_comp=0;
		array_push($gene, $pVIII_gene);
		array_push($pgene, $pVIII_protein);
		array_push($protein1begin, $pVIII_1_begin);
		array_push($protein1end, $pVIII_1_end);
		array_push($protein2begin, $pVIII_2_begin);
		array_push($protein2end, $pVIII_2_end);
		array_push($forward, $pVIII_forward);
		array_push($reverse, $pVIII_reverse);
		array_push($comp, $pVIII_comp);
		$e3_gene='E3';
		$e3_protein='30.7kDa';
		$e3_1_begin=28786;
		$e3_1_end=29625;
		$e3_2_begin=28786;
		$e3_2_end=29625;
		$e3_forward=1;
		$e3_reverse=0;
		$e3_comp=0;
		array_push($gene, $e3_gene);
		array_push($pgene, $e3_protein);
		array_push($protein1begin, $e3_1_begin);
		array_push($protein1end, $e3_1_end);
		array_push($protein2begin, $e3_2_begin);
		array_push($protein2end, $e3_2_end);
		array_push($forward, $e3_forward);
		array_push($reverse, $e3_reverse);
		array_push($comp, $e3_comp);
		$e4_gene='E4';
		$e4_protein='14.1kDa';
		$e4_1_begin=34263;
		$e4_1_end=34640;
		$e4_2_begin=34263;
		$e4_2_end=34640;
		$e4_forward=0;
		$e4_reverse=1;
		$e4_comp=0;
		array_push($gene, $e4_gene);
		array_push($pgene, $e4_protein);
		array_push($protein1begin, $e4_1_begin);
		array_push($protein1end, $e4_1_end);
		array_push($protein2begin, $e4_2_begin);
		array_push($protein2end, $e4_2_end);
		array_push($forward, $e4_forward);
		array_push($reverse, $e4_reverse);
		array_push($comp, $e4_comp);

		/*
		print_r($gene);
		print "<br>";
		print "<br>";
		print_r($pgene);
		print "<br>";
		print "<br>";
		print_r($protein1begin);
		print "<br>";
		print "<br>";
		print_r($protein1end);
		print "<br>";
		print "<br>";
		print_r($protein2begin);
		print "<br>";
		print "<br>";
		print_r($protein2end);
		print "<br>";
		print "<br>";
		print_r($forward);
		print "<br>";
		print "<br>";
		print_r($reverse);
		print "<br>";
		print "<br>";
		print_r($comp);
		*/
		//die("unregistered protein");
	}
	$_SESSION['protein1begin'] =$protein1begin; 
	$_SESSION['protein1end'] =$protein1end; 
	$_SESSION['protein2begin'] =$protein2begin; 
	$_SESSION['protein2end'] =$protein2end;
	$_SESSION['pgene'] = $pgene ; 
	$_SESSION['forward'] = $forward ;
	$_SESSION['reverse'] = $reverse ;
	$_SESSION['comp'] = $comp ;
	$_SESSION['gene'] = $gene;

}//end of protein function

//#################
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

?>



<br />
<font size="5"  face="Georgia, Arial, Garamond"><div align="center">THE RESULT IS AVAILABLE: </font><BR />
<BR />
<BR />
<div>
<input type="button" value="GRAPHICAL VIEW" onclick='window.location.href="select.php"'>
<!--input type="button" value="EXCEL VIEW" onclick='window.location.href="excel.php"'-->
<input type="button" value="EXCEL VIEW" onclick='window.location.href="download_excel.php?file=<?php echo './fasta_files/GMM2_Comparision_file_'.$rand.'.txt';?>"'>
<input type="button" value="PHYLOGRAM VIEW" onclick='window.location.href="download_tree.php?file=<?php echo './fasta_files/GMM2_phylogram_'.$rand.'.tree';?>"'>
<INPUT TYPE="button" VALUE="START FROM THE BEGINING" onClick="history.go(-2);return true;">
</div>




</body>
</html>


