<?php

    $return = [];

	$notNull = array(
		'name',
	);

	$canBeNull = array(
		'status',
		'jquery_plugin',
		'params',
		'publish_date_start',
		'publish_date_end',
	);

	$many = array(
		'properties',
		'items',
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