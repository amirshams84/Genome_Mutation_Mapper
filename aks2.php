<?php session_start(); 
$rand = $_SESSION['rand'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>GMM v2.0</title>
</head>

<body>
<img src="png2.php"  />


<form>
 <input type="button" value="START FROM BEGINING" onclick="window.location.href='index.html'">
 <input type="button" value="Protein Selection" onclick="window.location.href='select.php'">
 <input type="button" value="Normal" onclick="window.location.href='aks.php'">
 <input type="button" value="No-Substitution" onclick="window.location.href='aks2.php'">
 <input type="button" value="No-Insertion" onclick="window.location.href='aks3.php'">
 <input type="button" value="No-Deletion" onclick="window.location.href='aks4.php'">
 <input type="button" value="EXCEL VIEW" onclick='window.location.href="download_excel.php?file=<?php echo './fasta_files/GMM2_Comparision_file_'.$rand.'.txt';?>"'>
<input type="button" value="PHYLOGRAM VIEW" onclick='window.location.href="download_tree.php?file=<?php echo './fasta_files/GMM2_phylogram_'.$rand.'.tree';?>"'>
 <input type="button" value="SAVE IT WITH MAX QUALITY" onclick='window.location.href="png_save.php?file=<?php echo './fasta_files/GMM2_Comparision_map'.'_NOSUBS_'.$rand.'.png';?>"'>
  </form>


 </body>
 </html>