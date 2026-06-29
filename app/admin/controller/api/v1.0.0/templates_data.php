<?php

    $return = [];

	$notNull = array(
		'name',
		'version',
		'for_nt_version',
	);

	$canBeNull = array(
		'status',
		'colors',
		'cols',
	);

	$many = array(
		'properties',
		'themes',
		'stores',
	);

	foreach ($notNull as $v) {
		if (empty($v)) continue;
    	$return[$v] = $this->request->hasPost($v) && !empty($this->request->getPost($v)) ? $this->request->getPost($v) : $data[$v];
	}

	foreach ($canBeNull as $v) {
		if (empty($v)) continue;
    	$return[$v] = $this->request->hasPost($v) ? $this->request->getPost($v) : $data[$v];
	}

	foreach ($many as $v) {
		if (empty($v)) continue;
	    if ($this->request->hasPost($v)) 
	    	$return[$v] = $this->request->getPost($v);
	}