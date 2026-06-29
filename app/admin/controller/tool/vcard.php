<?php

header("Content-type: text/x-vcard");
header("Content-Disposition: filename=vcard_" . date('d') . date('m') . date('Y') . date('h') . date('i') . date('s') . ".vcf");
header("Pragma: no-cache");
header("Expires: 0");
echo $_POST['vcard_data'];


