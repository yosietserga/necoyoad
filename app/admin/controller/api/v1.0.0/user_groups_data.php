<?php

    $return = [];

    $return['name'] = $this->request->hasPost('name') && !empty($this->request->getPost('name')) ? $this->request->getPost('name') : $data['name'];
    $return['image'] = $this->request->hasPost('image') ? $this->request->getPost('code') : $data['code'];

    if ($this->request->hasPost('stores')) $return['stores'] = $this->request->getPost('stores');

	$notNull = array(
		'name',
		'permission',
	);

	$canBeNull = array(

	);

	$many = array(
		'properties',
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