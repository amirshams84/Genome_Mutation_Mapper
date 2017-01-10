<?php   
if (isset($_GET['file'])) { 
    $file = $_GET['file'] ;
    $flnm=$file;
        if (file_exists($flnm) && is_readable($flnm) && preg_match('/\.txt$/',$flnm))  { 
        $x=explode("/",$flnm);
        
            header('Content-type: application/text');  
            header("Content-Disposition: attachment; filename=\"".end($x)."\"");   
            readfile($flnm); 
        } 
    } else { 
    header("HTTP/1.0 404 Not Found"); 
    echo "<h1>Error 404: File Not Found: <br /><em>$file</em></h1>"; 
} 
?>