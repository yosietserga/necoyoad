<?php

    $return = [];

    $notNull = array(
        'newsletter_id',
        'name',
        'subject',
        'from_name',
        'from_email',
        'replyto_email',
    );

    $canBeNull = array(
        'trace_email',
        'trace_click',
        'embed_image',
        'repeat',
        'date_start',
        'date_end',
    );

    $many = array(
        'properties',
        'links',
        'contacts',
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