<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerContentPost extends ControllerAdmin
{

    protected string $post_type         = 'post'; //this will be saved into main table 
    protected string $object_type       = 'post'; //this will be saved into related tables
    
    protected string $model_name        = 'modelPost';  //this will load main model class
    protected string $model_route       = 'content/post'; //path to main model class
    protected string $model_object_type = 'post'; // to set into mode

    protected string $controller_name   = 'post'; //controller name
    protected string $controller_route  = 'content/post'; //controller route

    protected string $controller_template_basename = 'post'; //template controller name
    protected string $controller_template_route = 'content/post'; //template controller path

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
            'default' => 'post',
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
            'label' => 'Title',
            'type' => 'text',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'label' => 'Post Parent',
            'type' => 'number',
        ],
        'sort' => [
            'name' => 'sort',
            'label' => 'Sort By',
            'type' => 'option',
            'options'=>[
                'td.title'=>'Title',
                't.sort_order'=>'Sort Order',
                't.date_added'=>'Date Added',
            ]
        ],
        'limit' => [
            'name' => 'limit',
            'label' => 'Items Per Page',
            'type' => 'option',
            'options'=>[
                '10' => '10 Items per page',
                '25' => '25 Items per page',
                '50' => '50 Items per page',
                '100'=> '100 Items per page',
                '250'=> '250 Items per page',
            ]
        ],
    ];

    protected array $public_methods = ['insert', 'update', 'copy', 'delete', 'activate', 'sortable', 'grid'];

    public function init() {
        parent::init();
        $this->addFilter("grid:data", function ($data) {
            $data['batch_available'] = ['copyAll', 'deleteAll'];

            $data['columns'] =
            [
                'title' => [
                    'name' => 'title',
                    'label' => 'Title',
                    'isSortable' => true,
                    'formatter' => function ($result) {
                        $str = $result['title'];
                        $str .= '&nbsp;<a href="'. Url::createUrl("content/post",array('post_id'=>$result['post_id']),'NONSSL',HTTP_CATALOG) .'" target="_blank"><small>[&#8599;]</small></a>';
                        return $str;
                    }
                ],
                'post_type' => [
                    'name' => 'post_type',
                    'label' => 'Post Type',
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
    }
}