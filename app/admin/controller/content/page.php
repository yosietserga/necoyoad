<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerContentPage extends ControllerAdmin
{

    protected string $post_type         = 'page'; //this will be saved into main table 
    protected string $object_type       = 'page'; //this will be saved into related tables

    protected string $model_name        = 'modelPost';  //this will load main model class
    protected string $model_route       = 'content/post'; //path to main model class
    protected string $model_object_type = 'page'; // to set into mode

    protected string $controller_name   = 'page'; //controller name
    protected string $controller_route  = 'content/page'; //controller route

    protected string $controller_template_basename = 'page'; //template controller name
    protected string $controller_template_route = 'content/page'; //template controller path

    protected array $form_vars = [
        'post_id' => [
            'name' => 'post_id',
            'type' => 'number',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'number',
        ],
        'post_type' => [
            'name' => 'post_type',
            'type' => 'string',
            'default' => 'page',
            'required' => true,
        ],
        'allow_reviews' => [
            'name' => 'allow_reviews',
            'type' => 'boolean',
        ],
        'publish' => [
            'name' => 'publish',
            'type' => 'boolean',
        ],
        'image' => [
            'name' => 'image',
            'type' => 'text',
        ],
        'date_publish_start' => [
            'name' => 'date_publish_start',
            'type' => 'date',
        ],
        'date_publish_end' => [
            'name' => 'date_publish_end',
            'type' => 'date',
        ],
        'descriptions' => [
            'name' => 'descriptions',
            'type' => 'array',
            'fields' => [
                'title' => [
                    'name' => 'title',
                    'type' => 'string',
                ],
                'meta_description' => [
                    'name' => 'meta_description',
                    'type' => 'string',
                ],
                'description' => [
                    'name' => 'description',
                    'type' => 'string',
                ],
            ]
        ],
        'categories' => [
            'name' => 'categories',
            'type' => 'array',
            'object_type' => 'post_category'
        ],
        'stores' => [
            'name' => 'stores',
            'type' => 'array',
        ],
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
    ];

    protected array $filters = [
        'title' => [
            'name' => 'title',
            'type' => 'string',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'number',
        ],
        'date_publish_start' => [
            'name' => 'date_publish_start',
            'type' => 'date',
        ],
        'date_publish_end' => [
            'name' => 'date_publish_end',
            'type' => 'date',
        ],
    ];

    protected array $public_methods = ['insert', 'update', 'copy', 'delete', 'activate', 'sortable', 'grid'];

    public function init() {
        parent::init();
        $this->addFilter("grid:data", function ($data) {
            $data['pages'] = $this->model->getAll(['language_id' => $this->config->get('config_language_id')]);
            
            $data['batch_available'] = ['copyAll', 'deleteAll'];

            $data['columns'] =
            [
                'title' => [
                    'name' => 'title',
                    'label' => 'Title',
                    'isSortable' => true,
                    'formatter' => function ($result) {
                        $str = $result['title'];
                        $str .= '&nbsp;<a href="'. Url::createUrl("content/page",array('page_id'=>$result['post_id']),'NONSSL',HTTP_CATALOG) .'" target="_blank"><small>[&#8599;]</small></a>';
                        if (isset($result['internal_name']) && $result['internal_name']) {
                            $str .= '<br /><small>[ ' . $result['internal_name'] . ' ]</small>';
                        }
                        return $str;
                    }
                ],
                'status' => [
                    'name' => 'status',
                    'label' => 'Status',
                    'isSortable' => true,
                    'formatter' => function($result) {
                        return ($result['status']) ? $this->language->get('Active') : $this->language->get('Deactive');
                    }
                ],
                'publish' => [
                    'name' => 'publish',
                    'label' => 'Published',
                    'isSortable' => true,
                    'formatter' => function ($result) {
                        return ($result['publish']) ? $this->language->get('Yes') : $this->language->get('No');
                    }
                ],
                'date_publish_start' => [
                    'name' => 'date_publish_start',
                    'label' => 'To Publish From',
                    'isSortable' => true,
                    'formatter' => function($result) {
                        return "0000-00-00 00:00:00" != $result['date_publish_start'] ? date('d-m-Y h:i A', strtotime($result['date_publish_start'])) : "--";
                    }
                ],
                'date_publish_end' => [
                    'name' => 'date_publish_end',
                    'label' => 'To Publish To',
                    'isSortable' => true,
                    'formatter' => function ($result) {
                        return "0000-00-00 00:00:00" != $result['date_publish_end'] ? date('d-m-Y h:i A', strtotime($result['date_publish_end'])) : "--";
                    }
                ],
            ];

            return $data;
        });

        $this->addFilter("grid:result", function ($data) {
            $data['page_id'] = $data['post_id'];
            $data['internal_name'] = $this->modelPage->getProperty($data['post_id'], 'data', 'internal_name');
            return $data;
        });
    }
}