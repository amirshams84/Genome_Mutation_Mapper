<?php

$x = "./fasta_files/picture.png";
$outputFile = "./fasta_files/picture.jpg";
/*
imagejpeg(imagecreatefromstring(file_get_contents($x)), "picture.jpg",99);
/*$path="./fasta_files/picture.jpg";
$image = file_get_contents($path);
$image = substr_replace($image, pack("cnn", 1, 600, 600), 13, 5);

header("Content-type: image/jpeg");
//header('Content-Disposition: attachment; filename="'.basename($path).'"');

//imagejpeg(file_get_contents($x));
*/
	$image = imagecreatefrompng($x);
    imagejpeg($image, $outputFile, 100);
    imagedestroy($image);
?>