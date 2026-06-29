<?php
header("Content-type: application/csv");  
header("Content-disposition: attachment; filename=csv_".date('d').date('m').date('Y').date('h').date('i').date('s').".csv");  
header("Pragma: no-cache");  
header("Expires: 0");  
echo $_POST['csv_data'];  

  
