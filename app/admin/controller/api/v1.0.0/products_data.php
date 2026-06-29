<?php

    $return = [];

    $notNull = array(
        'model',
        'date_available',
    );

    $canBeNull = array(
        'sku',
        'image',
        'price',
        'status',
        'quantity',
        'image',
        'cost',
        'minimum',
        'subtract',
        'height',
        'width',
        'length',
        'weight',
        'sc'
    );

    $many = array(
        'properties',
        'descriptions',
        'customer_groups',
        'stores',
        'tags',
        'options',
        'discounts',
        'specials',
        'downloads',
        'related',
        'images',
        'categories',
        'attributes',
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