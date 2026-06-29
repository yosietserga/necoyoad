<?php

    $return = [];

    $notNull = array(
        'code',
    );

    $canBeNull = array(
        'status',
        'discount',
        'type',
        'total',
        'logged',
        'shipping',
        'date_start',
        'date_end',
        'uses_total',
        'uses_customer',
    );

    $many = array(
        'properties',
        'descriptions',
        'products',
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