<?php

$final='./fasta_files/final.csv';
//header('Content-Description: File Transfer');
header('Content-Type: application/CSV');
header('Content-Disposition: attachment; filename=' . $final);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
//header('Content-Length: ' . filesize($final));



?>