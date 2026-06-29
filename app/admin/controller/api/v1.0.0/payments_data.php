<?php

    $return = [];

	$notNull = array(
		'order_id',
		'customer_id',
		'order_payment_status_id',
		'transac_number',
		'transac_date',
		'payment_method',
		'amount',
	);

	$canBeNull = array(
		'store_id',
		'bank_account_id',
		'bank_from',
		'comment',
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