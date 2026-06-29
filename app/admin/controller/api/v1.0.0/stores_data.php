<?php

    $return = [];

	$notNull = array(
		'name',
		'folder',
	);

	$canBeNull = array(
		'status',
	);

	$many = array(
		'properties',

		'manufacturers',
		'categories',
		'products',
		'downloads',

		'posts',
		'post_categories',
		'pages',
		'banners',
		'menus',
		'objects',

		'coupons',
		'customers',
		'bank_accounts',

		'templates',
		'themes',

		'contacts',

		'users',
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