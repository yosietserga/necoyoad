<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerLocalisationGeoZone extends ControllerAdmin {
    protected string $object_type       = 'geozone'; //this will be saved into related tables
    
    protected string $model_name        = 'modelGeozone';  //this will load main model class
    protected string $model_route       = 'localisation/geozone'; //path to main model class
    protected string $model_object_type = 'geozone'; // to set into mode

    protected string $controller_name   = 'geo_zone'; //controller name
    protected string $controller_route  = 'localisation/geo_zone'; //controller route

    protected string $controller_template_basename = 'geo_zone'; //template controller name
    protected string $controller_template_route = 'localisation/geo_zone'; //template controller path

    protected array $form_vars = [
        'geo_zone_id' => [
            'name' => 'geo_zone_id',
            'type' => 'number',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
            'required' => true,
        ],
        'description' => [
            'name' => 'description',
            'type' => 'string',
        ],
    ];

    protected array $filters = [
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
                'name' => [
                    'name' => 'name',
                    'label' => 'Name',
                    'isSortable' => true,
                ],
            ];

            return $data;
        });

        $this->addFilter("getForm:data", function ($data) {
            if (!isset($this->modelCountry)) $this->load->model('localisation/country');
            $data['countries'] = $this->modelCountry->getAll(['language_id' => $this->config->get('config_language_id')]);

            if ($this->request->hasPost('zone_to_geo_zone')) {
                $data['zone_to_geo_zones'] = $this->request->getPost('zone_to_geo_zone');
            } elseif (isset($data['geo_zone_id'])) {
                $data['zone_to_geo_zones'] = $this->model->getZoneToGeoZones($data['geo_zone_id']);
            } else {
                $data['zone_to_geo_zones'] = [];
            }

            return $data;
        });
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'localisation/country')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('localisation/taxclass');

        foreach ($this->request->post['selected'] as $geo_zone_id) {
            $tax_rate_total = $this->modelTaxclass->getAllTotalByGeoZoneId($geo_zone_id);

            if ($tax_rate_total) {
                $this->error['warning'] = sprintf($this->language->get('error_tax_rate'), $tax_rate_total);
            }
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    
    public function zone() {
        $output = '<option value="0">' . $this->language->get('text_all_zones') . '</option>';

        $this->load->model('localisation/zone');

        $results = $this->modelZone->getAll([
            'country_id' => $this->request->getQuery('country_id'),
            'language_id' => $this->config->get('config_language_id'),

        ]);
        foreach ($results as $result) {
            $output .= '<option value="' . $result['zone_id'] . '"';

            if ($this->request->get['zone_id'] == $result['zone_id']) {
                $output .= ' selected="selected"';
            }

            $output .= '>' . $result['title'] . '</option>';
        }

        $this->response->setOutput($output, $this->config->get('config_compression'));
    }
}