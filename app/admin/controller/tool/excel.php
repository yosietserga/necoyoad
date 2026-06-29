<?php

header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: filename=reporte_" . date('d') . date('m') . date('Y') . date('h') . date('i') . date('s') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $_POST['excel_data'];


