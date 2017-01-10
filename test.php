<?php
$index =4;

$table=array();
$result=array();
for($j=2;$j<=$index;$j++){
$fname='/Users/amirhosseinshamsaddinishahrbabak/gmm/file/result'.$j.'.txt';
unset($dump);
$dump=file($fname);
for($i=0;$i<sizeof($dump);$i++){
$dump[$i] = preg_replace('/\s+/', ',', $dump[$i]);
}
if($j==2){
$result=$dump;
}
else{
for($i=0;$i<sizeof($dump);$i++){
$temp='';
for($l=2;$l<$j;$l++){
$temp.=' , , , ,';
}
$temp.=$dump[$i];
$result[$i].=$dump[$i];
}

}

}
$string='';
for($i=0;$i<sizeof($result);$i++){
$string.=$result[$i];
$string.=',';
$string.=$taxarray[$i];
$string.=PHP_EOL;
}

$ftable='/Users/amirhosseinshamsaddinishahrbabak/gmm/file/finaltable.txt';
file_put_contents($ftable,$string);
$template='/Users/amirhosseinshamsaddinishahrbabak/gmm/file/header.txt';
$header='GI NUMBER ,';

for($i=2;$i<=$index;$i++){
$header.='NUMBER OF HITS IN ITERATION #'.$i.',';
}
$header.='TAXONOMY';
file_put_contents($template,$header);
$template='/Users/amirhosseinshamsaddinishahrbabak/gmm/file/header.txt';
$fname='/Users/amirhosseinshamsaddinishahrbabak/gmm/file/finaltable.txt';
$ftable='/Users/amirhosseinshamsaddinishahrbabak/gmm/file/finaltable.csv';
$h=fopen($template,'r');
$handle=fopen($fname,'r');
$fp = fopen($ftable, 'w+');
$d = fgetcsv($h); 
fputcsv($fp,$d);
while ( ($data = fgetcsv($handle) ) !== FALSE ) {
fputcsv($fp,$data);
}
fclose($fp);
fclose($handle);

?>