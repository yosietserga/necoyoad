<?php
final class EmailTemplate {
	private $db;
    
    public function __construct($registry) {
        $this->db = $registry->get('db');
    }
    
	public function getPremadeTemplateList($dir,$recursive=false) {
        $tree = [];
		$directories = glob($dir.'*', GLOB_ONLYDIR);		
		foreach ($directories as $directory) {
			if ($recursive) {
			   if ($subdirectories = $this->getPremadeTemplateList($dir.basename($directory).'/')) {
			     foreach ($subdirectories as $subdirectory) {
				    $tree[basename($directory)][] = basename($subdirectory);
                 }
               } else {
                    $tree[] = basename($directory);
               }
			} else {
			    $tree[] = basename($directory);
			}
		}
		return $tree;
    }    
    
    function readPremadeTemplate($templatename='')	{
		if (!$templatename) {
			return false;
		}

		$templatedir = DIR_EMAIL_TEMPLATE . $templatename;
		if (!is_dir($templatedir)) {
			return false;
		}

		$templatefile = $templatedir . '/index.html';
		if (!is_file($templatefile)) {
			return false;
		}

		$contents = file_get_contents($templatefile);
		preg_match_all('%img(.*?)src=(["\']*[^"\' >]+["\'> ])%i', $contents, $imagematches);

		foreach ($imagematches[2] as $match) {
			if (substr($match, 0, 4) == 'http') {
				continue;
			}

			$newurl = $this->getNewImagePath($match, $templatename);

			$contents = str_replace('src=' . $match, 'src=' . $newurl, $contents);
		}
		unset($imagematches);

		preg_match_all('%background=(["\']*[^"\' >]+["\'> ])%i', $contents, $imagematches);

		foreach ($imagematches[1] as $match) {
			if (substr($match, 0, 4) == 'http') {
				continue;
			}

			$newurl = $this->getNewImagePath($match, $templatename);

			$contents = str_replace('background=' . $match, 'background=' . $newurl, $contents);
		}
		unset($imagematches);

		$stylematches = [];
		preg_match_all('%style=(["\']*[^"\'>]+["\'> ])%i', $contents, $stylematches);
		foreach ($stylematches[1] as $m => $match) {
			$imagematches = [];
			preg_match_all('%url\((.*?)\)%', $match, $imagematches);
			foreach ($imagematches[1] as $imagematch) {
				if (substr($imagematch, 0, 4) == 'http') {
					continue;
				}

				$newurl = $this->getNewImagePath($imagematch, $templatename);

				$newmatch = str_replace('url(' . $imagematch . ')', 'url(' . $newurl . ')', $match);

				$contents = str_replace($match, $newmatch, $contents);
			}
		}

		preg_match_all('%:background\((.*?)\)%i', $contents, $imagematches);

		foreach ($imagematches[1] as $match) {
			if (substr($match, 0, 4) == 'http') {
				continue;
			}

			$newurl = $this->getNewImagePath($match, $templatename);

			$contents = str_replace(':background(' . $match . ')', ':background(' . $newurl . ')', $contents);
		}
		unset($imagematches);

		return $contents;
	}
    
    function getNewImagePath($img=false, $templatename)	{
		if (!$img) {
			return '';
		}

		$addquotes = '';
		if (substr($img, 0, 1) == '"') {
			$img = str_replace('"', '', $img);
			$addquotes = '"';
		}
		if (substr($img, 0, 1) == "'") {
			$img = str_replace("'", '', $img);
			$addquotes = "'";
		}
        $templatename = rawurlencode($templatename);
	    $templatename = str_replace('%20',' ',$templatename);  
	    $templatename = str_replace('%2F','/',$templatename); 
	    $templatename = str_replace('&amp;','&',$templatename); 
	    $templatename = str_replace('%26','/',$templatename); 
        $newurl = HTTP_EMAIL_TPL_IMAGE . $templatename . '/' . $img;

		if ($addquotes) {
			$newurl = $addquotes . $newurl . $addquotes;
		}
		return $newurl;
	}
}
