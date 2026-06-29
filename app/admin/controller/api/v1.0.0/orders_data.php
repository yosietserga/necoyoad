<?php

    $return = [];

	$notNull = array(
		'customer_id',
	);

	$canBeNull = array(
		'store_name',
		'store_url',
		'firstname',
		'lastname',
		'telephone',
		'email',

		'shipping_firstname',
		'shipping_lastname',
		'shipping_company',
		'shipping_address_1',
		'shipping_address_2',
		'shipping_city',
		'shipping_zone',
		'shipping_zone_id',
		'shipping_country',
		'shipping_country_id',

		'payment_firstname',
		'payment_lastname',
		'payment_company',
		'payment_address_1',
		'payment_address_2',
		'payment_city',
		'payment_zone',
		'payment_zone_id',
		'payment_country',
		'payment_country_id',

		'ip',
		'total',
	);

	$many = array(
		'products',
		'totals',
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