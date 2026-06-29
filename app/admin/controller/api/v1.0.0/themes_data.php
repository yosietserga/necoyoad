<?php

    $return = [];

	$notNull = array(
		'template_id',
		'user_id',
		'template',
		'date_publish_start',
		'default',
	);

	$canBeNull = array(
		'status',
		'store_id',
		'name',
		'date_publish_end',
	);

	$many = array(
		'properties',
		'styles',
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