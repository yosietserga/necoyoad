<?php

    $return = [];

    $notNull = array(
        'customer_id',
        'currency_id',
        'currency_code',
        'currency_value',
        'currency_title',
        'amount',
        'amount_available',
        'amount_deferred',
        'amount_blocked',
        'amount_total',
        'description',
    );

    $canBeNull = array(
        'status',
        'type',
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