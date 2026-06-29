<?php

/**
 * @author Inversiones Necoyoad, C.A.
 * @copyright 2010
 */

final class General {

	function EasySize($size=0, $decimals=2, $decimal_separator = ',', $thousand_separator = '.') {
		if ($size < 1024) {
			return number_format($size) . ' Bytes'; 
		}

		if ($size >= 1024 && $size < (1024*1024)) {
			return number_format((float)($size/1024), $decimals,$decimal_separator,$thousand_separator) . ' KB';
		}

		if ($size >= (1024*1024) && $size < (1024*1024*1024)) {
			return number_format((float)($size/1024/1024), $decimals,$decimal_separator,$thousand_separator) . ' MB';
		}

		if ($size >= (1024*1024*1024)) {
			return number_format((float)($size/1024/1024/1024), $decimals,$decimal_separator,$thousand_separator) . ' GB';
		}
	}
    
    function listFiles($dir='', $skip_files = null, $recursive=false, $only_directories=false) {
    	if (empty($dir) || !is_dir($dir)) {
    		return false;
    	}
    	$file_list = [];
    	if (!$handle = @opendir($dir)) {
    		return false;
    	}
    	while (($file = readdir($handle)) !== false) {
    		if ($file == '.' || $file == '..') {
    			continue;
    		}
    		if (is_file($dir.'/'.$file)) {
    			if ($only_directories) {
    				continue;
    			}
    			if (empty($skip_files)) {
    				$file_list[] = $file;
    				continue;
    			}
    			if (!empty($skip_files)) {
    				if (is_array($skip_files) && !in_array($file, $skip_files)) {
    					$file_list[] = $file;
    				}
    				if (!is_array($skip_files) && $file != $skip_files) {
    					$file_list[] = $file;
    				}
    			}
    			continue;
    		}
    
    		if (is_dir($dir.'/'.$file) && !isset($file_list[$file])) {
    			if ($recursive) {
    				$file_list[$file] = listFiles($dir.'/'.$file, $skip_files, $recursive, $only_directories);
    			}
    		}
    	}
    	closedir($handle);
    	if (!$recursive) {
    		natcasesort($file_list);
    	}
    
    	return $file_list;
    }
}

