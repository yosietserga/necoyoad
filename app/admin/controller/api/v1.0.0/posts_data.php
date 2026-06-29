<?php

    $return = [];

	$notNull = array(
		'parent_id',
	);

	$canBeNull = array(
		'status',
		'image',
		'publish',
		'allow_reviews',
		'date_publish_start',
		'date_publish_end',
		'template',
		'sort_order',
	);

	$many = array(
		'properties',
		'descriptions',
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