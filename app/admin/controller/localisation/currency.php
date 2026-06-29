<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerLocalisationCurrency extends ControllerAdmin {
    protected string $object_type       = 'currency'; //this will be saved into related tables
    
    protected string $model_name        = 'modelCurrency';  //this will load main model class
    protected string $model_route       = 'localisation/currency'; //path to main model class
    protected string $model_object_type = 'currency'; // to set into mode

    protected string $controller_name   = 'currency'; //controller name
    protected string $controller_route  = 'localisation/currency'; //controller route

    protected string $controller_template_basename = 'currency'; //template controller name
    protected string $controller_template_route = 'localisation/currency'; //template controller path

    protected array $form_vars = [
        'currency_id' => [
            'name' => 'currency_id',
            'type' => 'number',
        ],
        'code' => [
            'name' => 'code',
            'type' => 'string',
            'required' => true,
        ],
        'symbol_left' => [
            'name' => 'symbol_left',
            'type' => 'string',
        ],
        'symbol_right' => [
            'name' => 'symbol_right',
            'type' => 'string',
        ],
        'value' => [
            'name' => 'value',
            'type' => 'float',
        ],
        'decimal_place' => [
            'name' => 'decimal_place',
            'type' => 'number',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'boolean',
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
        'title' => [
            'name' => 'title',
            'type' => 'string',
        ],
        'code' => [
            'name' => 'code',
            'type' => 'string',
        ],
        'sort' => [
            'name' => 'sort',
            'label' => 'Sort By',
            'type' => 'option',
            'options' => [
                'td.title' => 'Title',
                't.sort_order' => 'Sort Order',
                't.date_modified' => 'Date Modified',
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

    protected array $public_methods = ['insert', 'update', 'copy', 'delete', 'activate', 'grid'];

    public function init() {
        parent::init();
        $this->addFilter("grid:data", function ($data) {
            $data['batch_available'] = ['deleteAll'];

            $data['columns'] =
            [
                'title' => [
                    'name' => 'title',
                    'label' => 'Currency',
                    'isSortable' => true,
                    'formatter'=>function($column) {
                        $str = $column['title'];
                        if ($column['currency_id'] == $this->config->get('config_currency_id')) {
                            $str .= '<small><b>'.  $this->language->get('text_default') .'</b></small>';
                        }
                        return $str;
                    }
                ],
                'code' => [
                    'name' => 'code',
                    'label' => 'Code',
                ],
                'value' => [
                    'name' => 'value',
                    'label' => 'Value',
                ],
            ];

            return $data;
        });
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'localisation/country')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['selected'] as $currency_id) {
            if ($this->config->get('config_country_id') == $currency_id) {
                $this->error['warning'] = $this->language->get('error_default');
            }

            $currency_info = $this->model->getById($currency_id);

            if ($currency_info) {
                if ($this->config->get('config_currency') == $currency_info['code']) {
                    $this->error['warning'] = $this->language->get('error_default');
                }
            }
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}