<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerStoreCategory extends ControllerAdmin {

    protected string $object_type       = 'category'; //this will be saved into related tables

    protected string $model_name        = 'modelCategory';  //this will load main model class
    protected string $model_route       = 'store/category'; //path to main model class
    protected string $model_object_type = 'category'; // to set into mode

    protected string $controller_name   = 'category'; //controller name
    protected string $controller_route  = 'store/category'; //controller route

    protected string $controller_template_basename = 'category'; //template controller name
    protected string $controller_template_route = 'store/category'; //template controller path

    protected array $form_vars = [
        'category_id' => [
            'name' => 'category_id',
            'type' => 'number',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'number',
        ],
        'image' => [
            'name' => 'image',
            'type' => 'text',
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
            'object_type' => 'category'
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
        'product' => [
            'name' => 'product',
            'type' => 'text',
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

    protected array $public_methods = ['insert', 'update', 'copy', 'delete', 'activate', 'nestedSortable', 'grid'];

    public function init() {
        parent::init();
        $this->addFilter("grid:filters", function ($data) {
            $this->data['categories'] = $this->getCategories(0, $data);
            return $data;
        });

        $this->addFilter("grid:data", function ($data) {
            $data['batch_available'] = ['copyAll', 'deleteAll'];

            $data['columns'] =
                [
                    'title' => [
                        'name' => 'title',
                        'label' => 'Title',
                    ],
                ];

            return $data;
        });
    }

    protected function getCategories($parent_id = 0, $data = array()) {
        $output = '';
        $data['parent_id'] = $parent_id;
        $data['language_id'] = $this->config->get('config_language_id');
        $data['sort'] = 'sort_order';
        $rows = $this->model->getAll($data,  ['sort_data' => ['sort_order']]);

        if ($rows) {
            $i = str_replace('%theme%', $this->config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE);
            $output .= ($parent_id == 0) ? '<ol class="items">' : '<ol>';
            foreach ($rows as $result) {
                $output .= '<li id="' . $result['category_id'] . '">';
                $output .= '<div class="item">';
                $output .= '<input title="Seleccionar para una acci&oacute;n" type="checkbox" name="selected[]" value="' . $result['category_id'] . '">';
                $output .= '<b class="name">' . $result['title'] . '</b>';

                $_img = ((int) $result['status'] == 1) ? 'good.png' : 'minus.png';

                $output .= '<div class="actions">';
                /*
                  $output .= '<a title="'. $this->language->get('text_see') .'" href="'. $this->getUrl("/see",array('category_id'=>$result['category_id'])) .'">';
                  $output .= '<img src="image/report.png" alt="'. $this->language->get('text_see') .'" />';
                  $output .= '</a>';
                 */
                $output .= '<a title="' . $this->language->get('text_edit') . '" href="' . $this->getUrl("/update", array('category_id' => $result['category_id'])) . '">';
                $output .= '<img src="' . $i . 'edit.png" alt="' . $this->language->get('text_edit') . '" />';
                $output .= '</a>';

                $output .= '<a title="' . $this->language->get('text_activate') . '" onclick="activate(' . $result['category_id'] . ')">';
                $output .= '<img id="img_' . $result['category_id'] . '" src="' . $i . $_img . '" alt="' . $this->language->get('text_activate') . '" />';
                $output .= '</a>';

                $output .= '<a title="' . $this->language->get('text_delete') . '" onclick="eliminar(' . $result['category_id'] . ')">';
                $output .= '<img src="' . $i . 'delete.png" alt="' . $this->language->get('text_delete') . '" />';
                $output .= '</a>';
                /*
                  $output .= '<a title="'. $this->language->get('text_copy') .'" onclick="copy('. $result['category_id'] .')">';
                  $output .= '<img src="image/copy.png" alt="'. $this->language->get('text_copy') .'" />';
                  $output .= '</a>';
                 */
                $output .= '</div>';

                $output .= '</div>';

                // subcategories
                $data['parent_id'] = $result['category_id'];
                $children = $this->model->getAll($data);
                if ($children) {
                    $output .= $this->getCategories($result['category_id'], $data);
                }

                $output .= '</li>';
            }
            $output .= '</ol>';
        }
        return $output;
    }
    
    public function attributes() {
        $this->load->model('store/category');
        $this->load->model('store/product');
        $this->load->model('store/attribute');
        $data = $results = $ids = [];
        $category_id = $this->request->hasQuery('category_id') ? explode("_", $this->request->getQuery('category_id')) : 0;
        $results = $this->modelAttribute->getAll(array(
            'category_id'=>$category_id
        ));
        $product_id = $this->request->getQuery("product_id");
        if ($product_id) {
            $attr = $this->modelProduct->getProperty($product_id, 'attributes', 'admin_attributes');
        }
        if (!empty($results)) {
            $data['success'] = 1;
            foreach ($results as $k => $v) {
                if (in_array($v['product_attribute_group_id'], $ids)) continue;
                
                $ids[] = $v['product_attribute_group_id'];

                $data['results'][$k] = $v;
                $data['results'][$k]['categoriesAttributes'] = array_unique($this->modelProduct->getCategoriesByAttributeGroupId($v['product_attribute_group_id']));
                $data['results'][$k]['attributes'] = $this->modelAttribute->getAllAttributes(["product_attribute_group_id" => $v['product_attribute_group_id']]);

                if (isset($attr[$v["product_attribute_group_id"]])) {
                    $attributes = [];
                    foreach ($attr[$v["product_attribute_group_id"]] as $kk => $vv) {
                        list($label, $attrid) = explode(":", $kk);
                        $attributes[$attrid] = [
                            "label"=>$label,
                            "value"=>$vv
                        ];
                    }
                    $data['results'][$k]['admin_attributes'] = $attributes;
                }
            }
        } else {
            $data['error'] = 1;
        }

        $this->load->library('json');
        $this->response->setOutput(Json::encode($data), $this->config->get('config_compression'));
    }

}