<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerLocalisationOrderPaymentStatus extends ControllerAdmin
{
    protected string $object_type       = 'order_payment_status'; //this will be saved into related tables

    protected string $model_name        = 'modelOrderpaymentstatus';  //this will load main model class
    protected string $model_route       = 'localisation/orderpaymentstatus'; //path to main model class
    protected string $model_object_type = 'order_payment_status'; // to set into mode

    protected string $controller_name   = 'order_payment_status'; //controller name
    protected string $controller_route  = 'localisation/order_payment_status'; //controller route

    protected string $controller_template_basename = 'order_payment_status'; //template controller name
    protected string $controller_template_route = 'localisation/order_payment_status'; //template controller path

    protected array $form_vars = [
        'ref' => [
            'name' => 'ref',
            'type' => 'string',
            'required' => true,
        ],
        'status' => [
            'name' => 'status',
            'type' => 'boolean',
            'default' => 1
        ],
        'descriptions' => [
            'name' => 'descriptions',
            'type' => 'array',
            'fields' => [
                'title' => [
                    'name' => 'title',
                    'type' => 'string',
                ],
            ]
        ],
    ];

    protected array $filters = [
        'ref' => [
            'name' => 'ref',
            'type' => 'string',
        ],
        'title' => [
            'name' => 'title',
            'type' => 'string',
        ],
        'sort' => [
            'name' => 'sort',
            'label' => 'Sort By',
            'type' => 'option',
            'options' => [
                'td.title' => 'Title',
                't.ref' => 'REF',
                't.date_added' => 'Date Added',
            ]
        ],
        'limit' => [
            'name' => 'limit',
            'label' => 'Items Per Page',
            'type' => 'option',
            'options' => [
                '10' => '10 Items per page',
                '25' => '25 Items per page',
                '50' => '50 Items per page',
                '100' => '100 Items per page',
                '250' => '250 Items per page',
            ]
        ],
    ];

    protected array $public_methods = ['insert', 'update', 'copy', 'delete', 'activate', 'grid', 'sortable'];

    public function init()
    {
        parent::init();
        $this->addFilter("grid:data", function ($data) {
            $data['batch_available'] = ['deleteAll'];

            $data['columns'] =
                [
                    'title' => [
                        'name' => 'title',
                        'label' => 'Title',
                        'isSortable' => true,
                        'formatter' => function ($column) {
                            $str = $column['title'];
                            if ($column['status_id'] == $this->config->get('config_order_payment_status_id')) {
                                $str .= '<small><b>' .  $this->language->get('text_default') . '</b></small>';
                            }
                            return $str;
                        }
                    ],
                    'ref' => [
                        'name' => 'ref',
                        'label' => 'REF',
                        'isSortable' => true,
                    ],
                    'status' => [
                        'name' => 'status',
                        'label' => 'Status',
                        'isSortable' => true,
                        'formatter' => function ($result) {
                            return ($result['status']) ? $this->language->get('Active') : $this->language->get('Deactive');
                        }
                    ],
                ];

            return $data;
        });
    }
}