<?php session_start(); 
$rand = $_SESSION['rand'];
?>
<?php
 //###################################################################################
      //                                               drawing 
 
 //###################################################################################
 //###################################################################################
 //###################################################################################
 //###################################################################################
 //###################################################################################
 //###################################################################################

 
 // step1 what is the size of your refernce sequence 
 //print $reference_index;
 
 //######################  proteins
 

//##################	
 $ref_name = $_SESSION['ref_name'];
$reference_index = $_SESSION['reference_index'];
$reference_size = $_SESSION['reference_size'];
$toolbar = $_SESSION['tool'];
$seqname = $_SESSION['seqname'];
#########################	
 $adad=sizeof($seqname);
 $adad += 2;
 $tool=($adad*90)+200;// size of the whole image

 $im = imagecreate(1300,$tool);
 $white = imagecolorallocate($im,255,255,255);
 $black = imagecolorallocate($im,0,0,0);
 $blue = imagecolorallocate($im,0,191,255);
 
 /////////////////////////////////////////////////
  
 //##########  calculating the scale
 $scale = $toolbar/1000;
 ///////////////////////
 
 
 
#    first part we have to draw protein lines
 
 
 //##################  drawing proteins
 

 $protein1begin = $_SESSION['p1b']; 
 $protein1end = $_SESSION['p1e']; 
 $protein2begin =  $_SESSION['p2b'] ; 
 $protein2end =  $_SESSION['p2e']; 
 $protein = $_SESSION['protein'];
 $forward = $_SESSION['forw'];
 $reverse = $_SESSION['rever'];
 $comp = $_SESSION['com'];
 
 

for($i=0;$i<sizeof($protein1begin);$i++){
$protein1begin[$i] = $protein1begin[$i]/$scale;
$protein1end[$i] = $protein1end[$i]/$scale;
$protein2begin[$i] = $protein2begin[$i]/$scale;
$protein2end[$i] = $protein2end[$i]/$scale;
}	



$off=10;
$lastright=0;
for($i=0;$i<sizeof($protein1begin);$i++){

$left1 = $protein1begin[$i];
$right1 = $protein1end[$i];
$left2 = $protein2begin[$i];
$right2 = $protein2end[$i];

//lowring protein while overlap occured
if($left1<($lastright+10)&& $off<=90)$off+=30;
else $off=10;
//$off =50;
if($forward[$i]==1 && $reverse[$i]==0 && $comp[$i]==0){

//this is forward normal protein
 
imagefilledrectangle($im, $left1+100,45+$off,$right1+100,$off+55, $blue);
$locus = ($left1+$right1)/2;
imagestring($im,3,$locus+100-15,30+$off,$protein[$i],$black);
$head = array($right1+100,43+$off,$right1+100,57+$off,$right1+100+10,50+$off);
imagefilledpolygon($im,$head,3,$blue);
$lastright = $right1;

}
elseif($forward[$i]==1 && $reverse[$i]==0 && $comp[$i]==1){
//########################## this is forward join protein
imagefilledrectangle($im,$left1+100, 45+$off,$right2+100, 55+$off, $blue);
//imagefilledrectangle($im,$left2+100, 45+$off,$right2+100, 55+$off, $blue);
$locus = ($left1+$right2)/2;
imagestring($im,3,$locus+100-10,30+$off,$protein[$i],$black);
$head = array($right2+100,43+$off,$right2+100,57+$off,$right2+100+10,50+$off);
imagefilledpolygon($im,$head,3,$blue);
$lastright = $right1;
}


elseif($forward[$i]==0 && $reverse[$i]==1 && $comp[$i]==0){
//####################### this is reverse normal protein
imagefilledrectangle($im, $left1+100,45+$off,$right1+100,55+$off, $blue);
$locus = ($left1+$right1)/2;
imagestring($im,3,$locus+100-10, 30+$off,$protein[$i],$black);
$head = array($left1+100,43+$off,$left1+100,57+$off,$left1+100-10,50+$off);
imagefilledpolygon($im,$head,3,$blue);


$lastright = $right1;
}

elseif($forward[$i]==0 && $reverse[$i]==1 && $comp[$i]==1){

// this is reverse join protein
imagefilledrectangle($im,$left1+100, 45+$off,$right1+100,55+$off, $blue);
imagefilledrectangle($im,$left2+100, 45+$off,$right2+100, 55+$off, $blue);
$locus = ($left1+$right2)/2;
imagestring($im,3,$locus+100-10,30+$off,$protein[$i],$black);
$head = array($left1+100,43+$off,$left1+100,57+$off,$left1+100-10,50+$off);
imagefilledpolygon($im,$head,3,$blue);

$lastright = $right2;
}


}

 /////////////////////////     MUTE DRAW
 
 
//$off=($adad*50);//defining the offset
$off = 150; 

$element=1; 	  
 for($i=0;$i<sizeof($seqname);$i++){
 //if(in_array($i,$queindex)){
 for($k=0;$k<10;$k++){
	 imageline($im,100,$k+50+$off,1100,$k+50+$off,$black);
	 //imageline($im,50,$i+150 ,1300,$i+150,$black);
      }
  imagestring($im,3,10,$k+40+$off,$seqname[$i],$black);// i replaced Query $element with seq_name[$i]
	$off+=50;
	$element++;
 //}
 }
  
$offset=$off; 
 $multi_sub = $_SESSION['multi_sub'];
 $multi_ins = $_SESSION['multi_ins'];
 $multi_del = $_SESSION['multi_del']; 
 
 
 //////////  DRWAING SUBSTITUTION
 
 //$off=($adad*50);
 
$off=150;
 for($i=0;$i<sizeof($multi_sub);$i++){
if($multi_sub[$i]!=0)
 for($p=0;$p<sizeof($multi_sub[$i]);$p++){
	 $multi_sub[$i][$p]=($multi_sub[$i][$p]/$scale);
}
}

 


for($o=0;$o<sizeof($multi_sub);$o++){
 if($multi_sub[$o]!=0)
 for($p=0;$p<sizeof($multi_sub[$o]);$p++){

	 imageline($im,$multi_sub[$o][$p]+100,40+$off,$multi_sub[$o][$p]+100,50+$off,$blue);
	 }
 $off+=50; 
 //}
 }
 
 //$off=($adad*50);
 $off = 150;
  //////////  DRWAING Insertion
  
  for($i=0;$i<sizeof($multi_ins);$i++){
  if($multi_ins[$i]!=0)
 for($p=0;$p<sizeof($multi_ins[$i]);$p++){
	 $multi_ins[$i][$p]=($multi_ins[$i][$p]/$scale);
}
}
  
for($o=0;$o<sizeof($multi_ins);$o++){
 if($multi_ins[$o]!=0)
 for($p=0;$p<sizeof($multi_ins[$o]);$p++){
 
 $instri = array($multi_ins[$o][$p]+100-2,30+$off,$multi_ins[$o][$p]+100+2,30+$off,$multi_ins[$o][$p]+100,35+$off);
 imagefilledpolygon($im,$instri,3,$black);
 
 
 }
 
 $off+=50; 
 }
 
 //$off=($adad*50);
$off=150; 
//////////  DRWAING Deletion
 
 for($i=0;$i<sizeof($multi_del);$i++){
 if($multi_del[$i]!=0)
 for($p=0;$p<sizeof($multi_del[$i]);$p++){
	 $multi_del[$i][$p]=($multi_del[$i][$p]/$scale);
}
}
 
 for($o=0;$o<sizeof($multi_del);$o++){
  if($multi_del[$o]!=0)
 for($p=0;$p<sizeof($multi_del[$o]);$p++){
 
 $deltri = array($multi_del[$o][$p]+100-2,30+$off,$multi_del[$o][$p]+100+2,30+$off,$multi_del[$o][$p]+100,25+$off);
       imagefilledpolygon($im,$deltri,3,$black);
 
 }
 $off+=50;
 }
 
 
 
 
 
 
 
 //###################  drwaing refernce line with scale
 //$off=($adad*50)+200;
 $off=$offset;
 for($i=0;$i<10;$i++){
	 imageline($im,100,$i+50+$off,1100,$i+50+$off,$black);
	 //imageline($im,50,$i+150 ,1300,$i+150,$black);
      }
	  imagestring($im,3,10,$i+40+$off,$ref_name,$black);//i replaced 'Refernce' with $ref_name
  $j =0;
 for($i=0 ;$i<$reference_size+5000 ;$i+=5000){
	 if($i>=$reference_size){
	 //$i=$reference_size;
	 //$j = 1000;
	 //$e = $scale;
	 continue;
	 }
	 else{
	 $j = $i/$scale;
	 $e = round($i/1000);
	 }
	 imageline($im,$j+100,60+$off,$j+100,70+$off,$black);
	 imagestring($im,2,$j+100-10,75+$off,"$e kb",$black);
	 //$j+=125;
	 
 }
 

 ////////////////////////////////   
 // step 3    info table
 
 //$off=($adad*60)+150;
$off = $offset + 50;
$element=1;
for($i=0;$i<sizeof($seqname);$i++){
 imagestring($im,3,10,100+$off,"Query $element:    $seqname[$i]",$black);	 
 $off+=13;	 
 $element++;

}

imagestring($im,3,10,100+$off,"Reference:  $ref_name",$black);
$off+=13;

 
header("Content-type: image/png");
imagepng($im,NULL,0);
//////////////////////////////////////// 
//header('Content-Type: image/png');
imagepng($im,'./fasta_files/GMM2_Comparision_map'.'_'.$rand.'.png',PNG_NO_FILTER,0);
imagedestroy($im);

?>

