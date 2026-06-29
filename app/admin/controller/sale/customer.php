<?php

/**
 * ControllerSaleCustomer
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */


require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerSaleCustomer extends ControllerAdmin
{

    protected string $post_type         = 'customer'; //this will be saved into main table 
    protected string $object_type       = 'customer'; //this will be saved into related tables

    protected string $model_name        = 'modelCustomer';  //this will load main model class
    protected string $model_route       = 'sale/customer'; //path to main model class
    protected string $model_object_type = 'customer'; // to set into mode

    protected string $controller_name   = 'customer'; //controller name
    protected string $controller_route  = 'sale/customer'; //controller route

    protected string $controller_template_basename = 'customer'; //template controller name
    protected string $controller_template_route = 'sale/customer'; //template controller path

    protected array $form_vars = [
        'customer_id' => [
            'name' => 'customer_id',
            'type' => 'number',
        ],
        'store_id' => [
            'name' => 'store_id',
            'type' => 'number',
        ],
        'address_id' => [
            'name' => 'address_id',
            'type' => 'number',
        ],
        'customer_group_id' => [
            'name' => 'customer_group_id',
            'type' => 'number',
        ],
        'firstname' => [
            'name' => 'firstname',
            'type' => 'string',
        ],
        'lastname' => [
            'name' => 'lastname',
            'type' => 'string',
        ],
        'email' => [
            'name' => 'email',
            'type' => 'string',
            'required' => true,
        ],
        'password' => [
            'name' => 'password',
            'type' => 'string',
        ],
        'telephone' => [
            'name' => 'telephone',
            'type' => 'string',
        ],
        'sex' => [
            'name' => 'sex',
            'type' => 'string',
        ],
        'rif' => [
            'name' => 'rif',
            'type' => 'string',
        ],
        'company' => [
            'name' => 'company',
            'type' => 'string',
        ],
        'activation_code' => [
            'name' => 'activation_code',
            'type' => 'string',
        ],
        'photo' => [
            'name' => 'photo',
            'type' => 'string',
        ],
        'congrats' => [
            'name' => 'congrats',
            'type' => 'number',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'number',
        ],
        'banned' => [
            'name' => 'banned',
            'type' => 'number',
        ],
        'approved' => [
            'name' => 'approved',
            'type' => 'number',
        ],
        'complete' => [
            'name' => 'complete',
            'type' => 'number',
        ],
        'visits' => [
            'name' => 'visits',
            'type' => 'number',
        ],
        'ip' => [
            'name' => 'ip',
            'type' => 'string',
        ],
        'birthday' => [
            'name' => 'birthday',
            'type' => 'date',
        ],
        'stores' => [
            'name' => 'stores',
            'type' => 'array',
        ],
        /*
        'customer_groups' => [
            'name' => 'customer_groups',
            'type' => 'array',
            'isProperty' => true,
            'group' => 'customer_groups',
            'key'  => 'customer_groups',
            'default' => []
        ],
        'internal_name' => [
            'name' => 'internal_name',
            'type' => 'string',
            'isProperty' => true,
            'group' => 'data',
            'key'  => 'internal_name',
        ],
        'layout' => [
            'name' => 'layout',
            'type' => 'string',
            'isProperty' => true,
            'group' => 'style',
            'key'  => 'view',
        ],
        */
    ];

    protected array $filters = [
        'email' => [
            'name' => 'email',
            'type' => 'string',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
        ],
        'customer_group_id' => [
            'name' => 'customer_group_id',
            'type' => 'number',
        ],
        'telephone' => [
            'name' => 'telephone',
            'type' => 'number',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'number',
        ],
        'approved' => [
            'name' => 'approved',
            'type' => 'number',
        ],
        'date_start' => [
            'name' => 'date_start',
            'type' => 'date',
        ],
        'date_end' => [
            'name' => 'date_end',
            'type' => 'date',
        ],
    ];

    protected array $public_methods = ['insert', 'update', 'delete', 'activate', 'grid'];

    public function init() {
        parent::init();
        $this->addFilter("grid:data", function ($data) {
            $data['batch_available'] = ['deleteAll'];

            $data['columns'] =
            [
                'photo' => [
                    'name' => 'photo',
                    'label' => 'Photo',
                    'formatter' => function ($row) {
                        return '<img src="' . $row['photo'] . '" alt="' . $row['firstname'] ." ". $row['lastname'] . '" />';
                    }
                ],
                'fullname' => [
                    'name' => 'fullname',
                    'label' => 'Fullname',
                    'formatter' => function ($result) {
                        $str = $result['firstname'] ." ". $result['lastname'];
                        if (isset($result['company']) && $result['company']) {
                            $str .= '<br /><small>Company: ' . $result['company'] . '</small>';
                        }
                        return $str;
                    }
                ],
                'email' => [
                    'name' => 'email',
                    'label' => 'Email',
                    'isSortable' => true,
                ],
                'telephone' => [
                    'name' => 'telephone',
                    'label' => 'Telephone',
                    'isSortable' => true,
                ],
                'status' => [
                    'name' => 'status',
                    'label' => 'Status',
                    'isSortable' => true,
                    'formatter' => function($result) {
                        return ($result['status']) ? $this->language->get('Active') : $this->language->get('Deactive');
                    }
                ],
                'approved' => [
                    'name' => 'approved',
                    'label' => 'Approved',
                    'isSortable' => true,
                    'formatter' => function ($result) {
                        return ($result['approved']) ? $this->language->get('Yes') : $this->language->get('No');
                    }
                ],
                'birthday' => [
                    'name' => 'birthday',
                    'label' => 'Birthday',
                    'isSortable' => true,
                    'formatter' => function($result) {
                        return "0000-00-00 00:00:00" != $result['birthday'] ? date('d-m-Y', strtotime($result['birthday'])) : "--";
                    }
                ],
                'date_added' => [
                    'name' => 'date_added',
                    'label' => 'Date Added',
                    'isSortable' => true,
                    'formatter' => function ($result) {
                        return "0000-00-00 00:00:00" != $result['date_added'] ? date('d-m-Y h:i A', strtotime($result['date_added'])) : "--";
                    }
                ],
            ];

            return $data;
        });


    }
}



