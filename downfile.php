<?php
set_time_limit(48000000000); 
ini_set("memory_limit","-1");  
if (isset($_GET['file'])) { 
    $file = $_GET['file'] ;
    $flnm="/home/ashamsad/biomuta-rev/".$file;
        
            
            readfile($flnm); 
      
} 
?>