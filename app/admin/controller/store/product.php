<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerStoreProduct extends ControllerAdmin {

    private $aKey = "";
    protected string $object_type       = 'product'; //this will be saved into related tables

    protected string $model_name        = 'modelProduct';  //this will load main model class
    protected string $model_route       = 'store/product'; //path to main model class
    protected string $model_object_type = 'product'; // to set into mode

    protected string $controller_name   = 'product'; //controller name
    protected string $controller_route  = 'store/product'; //controller route

    protected string $controller_template_basename = 'product'; //template controller name
    protected string $controller_template_route = 'store/product'; //template controller path


    protected array $form_vars = [
        'product_id' => [
            'name' => 'product_id',
            'type' => 'number',
        ],
        'manufacturer_id' => [
            'name' => 'manufacturer_id',
            'type' => 'number',
        ],
        'tax_class_id' => [
            'name' => 'tax_class_id',
            'type' => 'number',
        ],
        'weight_class_id' => [
            'name' => 'weight_class_id',
            'type' => 'number',
        ],
        'length_class_id' => [
            'name' => 'length_class_id',
            'type' => 'number',
        ],
        'stock_status_id' => [
            'name' => 'stock_status_id',
            'type' => 'number',
        ],
        'model' => [
            'name' => 'model',
            'type' => 'string',
        ],
        'sku' => [
            'name' => 'sku',
            'type' => 'string',
        ],
        'location' => [
            'name' => 'location',
            'type' => 'string',
        ],
        'image' => [
            'name' => 'image',
            'type' => 'string',
        ],
        'shipping' => [
            'name' => 'shipping',
            'type' => 'boolean',
        ],
        'subtract' => [
            'name' => 'subtract',
            'type' => 'boolean',
        ],
        'quantity' => [
            'name' => 'quantity',
            'type' => 'number',
        ],
        'minimum' => [
            'name' => 'minimum',
            'type' => 'number',
        ],
        'price' => [
            'name' => 'price',
            'type' => 'float',
        ],
        'cost' => [
            'name' => 'cost',
            'type' => 'float',
        ],
        'length' => [
            'name' => 'length',
            'type' => 'float',
        ],
        'width' => [
            'name' => 'width',
            'type' => 'float',
        ],
        'height' => [
            'name' => 'height',
            'type' => 'float',
        ],
        'weight' => [
            'name' => 'weight',
            'type' => 'float',
        ],
        'sort_order' => [
            'name' => 'sort_order',
            'type' => 'number',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'boolean',
        ],
        'stores' => [
            'name' => 'stores',
            'type' => 'array',
        ],
        'categories' => [
            'name' => 'categories',
            'type' => 'array',
            'object_type' => 'category'
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
        'customer_groups' => [
            'name' => 'customer_groups',
            'type' => 'array',
            'isProperty' => true,
            'group' => 'customer_groups',
            'key'  => 'customer_groups',
            'default' => []
        ],
        'date_publish_start' => [
            'name' => 'date_publish_start',
            'type' => 'date',
            'isProperty' => true,
            'group' => 'data',
            'key'  => 'date_publish_start',
        ],
        'date_publish_end' => [
            'name' => 'date_publish_end',
            'type' => 'date',
            'isProperty' => true,
            'group' => 'data',
            'key'  => 'date_publish_end',
        ],
        'layout' => [
            'name' => 'layout',
            'type' => 'string',
            'isProperty' => true,
            'group' => 'style',
            'key'  => 'view',
        ],
    ];
    //TODO: add formatter index to formvars 
    
    protected array $filters = [
        'product' => [
            'name' => 'product',
            'type' => 'string',
        ],
        'model' => [
            'name' => 'model',
            'type' => 'string',
        ],
        'quantity' => [
            'name' => 'quantity',
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

    protected array $public_methods = ['insert', 'update', 'copy', 'delete', 'activate', 'grid'];

    public function init() {
        parent::init();
        $this->addFilter("grid:data", function ($data) {
            $data['batch_available'] = ['copyAll', 'deleteAll'];

            $data['columns'] =
            [
                'image' => [
                    'name' => 'image',
                    'label' => 'Image',
                    'formatter' => function ($column) {
                        return '<img src="' . $column['image'] . '" alt="' . $column['name'] . '" />';
                    }
                ],
                'title' => [
                    'name' => 'title',
                    'label' => 'Title',
                ],
                'model' => [
                    'name' => 'model',
                    'label' => 'Model',
                ],
                'quantity' => [
                    'name' => 'quantity',
                    'label' => 'QTY',
                    'formatter'=>function($column) {
                        if ($column['quantity'] <= 0) {
                            return '<span style="color: #FF0000;">'. $column['quantity'] .'</span>';
                        } elseif ($column['quantity'] <= 5) {
                            return '<span style="color: #FFA500;">' . $column['quantity'] . '</span>';
                        } else {
                            return '<span style="color: #008000;">' . $column['quantity'] . '</span>';
                        }
                    }
                ],
                'date_available' => [
                    'name' => 'date_available',
                    'label' => 'Fecha Disponible',
                    'isSortable' => true,
                    'formatter' => function ($result) {
                        return "0000-00-00 00:00:00" != $result['date_available'] ? date('d-m-Y h:i A', strtotime($result['date_available'])) : "--";
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
            ];

            return $data;
        });

        $this->addFilter("getForm:data", function ($data) {
            if (!isset($this->modelLanguage)) $this->load->model('localisation/language');
            if (!isset($this->modelStockstatus)) $this->load->model('localisation/stockstatus');
            if (!isset($this->modelTaxclass)) $this->load->model('localisation/taxclass');
            if (!isset($this->modelLengthclass)) $this->load->model('localisation/lengthclass');
            if (!isset($this->modelWeightclass)) $this->load->model('localisation/weightclass');
            if (!isset($this->modelDownload)) $this->load->model('store/download');
            if (!isset($this->modelStore)) $this->load->model('store/store');
            if (!isset($this->modelManufacturer)) $this->load->model('store/manufacturer');
            
            $array_language_id = ['language_id' => $this->config->get('config_language_id')];
            $data['languages'] = $this->modelLanguage->getAll();
            $data['manufacturers'] = $this->modelManufacturer->getAll();
            $data['stock_statuses'] = $this->modelStockstatus->getAll($array_language_id);
            $data['tax_classes'] = $this->modelTaxclass->getAll($array_language_id);
            $data['weight_classes'] = $this->modelWeightclass->getAll($array_language_id);
            $data['length_classes'] = $this->modelLengthclass->getAll($array_language_id);
            $data['downloads'] = $this->modelDownload->getAll($array_language_id);

            $product_info = $data['model_info'];
            $data['language_id'] = $this->config->get('config_language_id');

            if ($this->request->hasPost('product_tags')) {
                $data['product_tags'] = $this->request->getPost('product_tags');
            } elseif (isset($product_info['product_id'])) {
                $data['product_tags'] = $this->modelProduct->getTags($this->request->get['product_id']);
            } else {
                $data['product_tags'] = [];
            }

            if (isset($product_info['image']) && file_exists(DIR_IMAGE . $product_info['image'])) {
                $data['preview'] = NTImage::resizeAndSave($product_info['image'], 100, 100);
            } else {
                $data['preview'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
            }

            if ($this->request->hasPost('date_available')) {
                $data['date_available'] = $this->request->getPost('date_available');
            } elseif (isset($product_info['product_id'])) {
                $data['date_available'] = date('d/m/Y', strtotime($product_info['date_available']));
            } else {
                $data['date_available'] = date('d/m/Y', time() - 86400);
            }

            $weight_info = $this->modelWeightclass->getDescriptionByUnit($this->config->get('config_weight_class'));
            if ($this->request->hasPost('weight_class_id')) {
                $data['weight_class_id'] = $this->request->getPost('weight_class_id');
            } elseif (isset($product_info['product_id'])) {
                $data['weight_class_id'] = $product_info['weight_class_id'];
            } elseif (isset($weight_info['weight_class_id'])) {
                $data['weight_class_id'] = $weight_info['weight_class_id'];
            } else {
                $data['weight_class_id'] = '';
            }

            $length_info = $this->modelLengthclass->getDescriptionByUnit($this->config->get('config_length_class'));
            if ($this->request->hasPost('length_class_id')) {
                $data['length_class_id'] = $this->request->getPost('length_class_id');
            } elseif (isset($product_info['product_id'])) {
                $data['length_class_id'] = $product_info['length_class_id'];
            } elseif (isset($length_info['length_class_id'])) {
                $data['length_class_id'] = $length_info['length_class_id'];
            } else {
                $data['length_class_id'] = '';
            }

            if ($this->request->hasPost('options')) {
                $data['options'] = $this->request->getPost('options');
            } elseif (isset($product_info['product_id'])) {
                $data['options'] = $this->model->getOptions($product_info['product_id']);
            } else {
                $data['options'] = [];
            }

            if ($this->request->hasPost('discounts')) {
                $data['discounts'] = $this->request->getPost('discounts');
            } elseif (isset($product_info['product_id'])) {
                $data['discounts'] = $this->model->getDiscounts($product_info['product_id']);
            } else {
                $data['discounts'] = [];
            }

            if ($this->request->hasPost('specials')) {
                $data['specials'] = $this->request->getPost('specials');
            } elseif (isset($product_info['product_id'])) {
                $data['specials'] = $this->model->getSpecials($product_info['product_id']);
            } else {
                $data['specials'] = [];
            }

            if ($this->request->hasPost('downloads')) {
                $data['product_download'] = $this->request->getPost('downloads');
            } elseif (isset($product_info['product_id'])) {
                $data['product_download'] = $this->model->getDownloads($product_info['product_id']);
            } else {
                $data['product_download'] = [];
            }

            if ($this->request->hasPost('related')) {
                $data['product_related'] = $this->request->getPost('related');
            } elseif (isset($product_info['product_id'])) {
                $data['product_related'] = $this->model->getRelated($product_info['product_id']);
            } else {
                $data['product_related'] = [];
            }

            /* product attributes */
            /*
            if (isset($product_info['product_id'])) {
                $this->load->auto('store/attribute');
                $attrValues = [];
                foreach ($this->model->getAllProperties($this->request->getQuery('product_id'), 'attribute') as $attribute) {
                    list($name, $attribute_id, $attribute_group_id) = explode(':', $attribute['key']);
                    if (empty($attribute_group_id) || empty($attribute_id)) continue;
                    $attrValues[$attribute_group_id][$attribute_id] = $attribute['value'];
                }
                $data['attributes'] = [];
                foreach ($attrValues as $attribute_group_id => $attr) {
                    $rows = $this->modelAttribute->getAll(array(
                        'product_attribute_group_id' => $attribute_group_id
                    ));

                    $data['attributes'][$attribute_group_id] = $rows[0];
                    $data['attributes'][$attribute_group_id]['categoriesAttributes'] = array_unique($this->modelProduct->getCategoriesByAttributeGroupId($attribute_group_id));

                    foreach ($data['attributes'][$attribute_group_id]['attributes'] as $j => $attribute) {
                        $data['attributes'][$attribute_group_id]['attributes'][$j]['value'] = $attr[$attribute['product_attribute_id']];
                    }
                }
            }
            */
            /* /product attributes */

            $data['no_image'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
            $data['images'] = [];
            if (isset($product_info['product_id'])) {
                $results = $this->model->getImages($product_info['product_id']);
                foreach ($results as $result) {
                    if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
                        $data['images'][] = array(
                            'preview' => NTImage::resizeAndSave($result['image'], 100, 100),
                            'file' => $result['image']
                        );
                    } else {
                        $data['images'][] = array(
                            'preview' => NTImage::resizeAndSave('no_image.jpg', 100, 100),
                            'file' => $result['image']
                        );
                    }
                }
            }

            return $data;
        });

        $this->addFilter("getForm:scripts", function ($scripts) {

            $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
            "$('#accordion').accordion({
                collapsible: true
            });
           
            $('.vtabs_page').hide();
            $('.vtabs_page:first-child').show();");

            return $scripts;
        });

        $this->addFilter("formData", function ($data) {
            foreach ($data['discounts'] as $key => $discount) {
                $discount['date_start'] = str_replace("-", "/", $discount['date_start']);
                $dps = explode("/", $discount['date_start']);
                $data['discounts'][$key]['date_start'] = $dps[2] . "-" . $dps[1] . "-" . $dps[0];

                $discount['date_end'] = str_replace("-", "/", $discount['date_end']);
                $dpe = explode("/", $discount['date_end']);
                $data['discounts'][$key]['date_end'] = $dpe[2] . "-" . $dpe[1] . "-" . $dpe[0];
            }

            foreach ($data['specials'] as $key => $special) {
                $special['date_start'] = str_replace("-", "/", $special['date_start']);
                $dps = explode("/", $special['date_start']);
                $data['specials'][$key]['date_start'] = $dps[2] . "-" . $dps[1] . "-" . $dps[0];
                
                $special['date_end'] = str_replace("-", "/", $special['date_end']);
                $dpe = explode("/", $special['date_end']);
                $data['specials'][$key]['date_end'] = $dpe[2] . "-" . $dpe[1] . "-" . $dpe[0];
            }

            $data['price'] = str_replace('.', '', $data['price']);
            $data['price'] = str_replace(',', '.', $data['price']);
            
            return $data;
        });

        $this->addFilter("grid:result", function ($data) {
            $data['date_publish_start'] = $this->model->getProperty($data['product_id'], 'data', 'date_publish_start');
            $data['date_publish_end'] = $this->model->getProperty($data['product_id'], 'data', 'date_publish_end');
            return $data;
        });
    }

    /**
     * ControllerStoreProduct::visits()
     * 
     * Estadisticas de visitas
     * 
     * @see Load
     * @see Request
     * @see Model
     * @return void
     */
    public function visits()
    {
        //TODO: mantener y capturar los filtros en todos los enlaces
        $filter_date_start = ($this->request->hasQuery('ds')) ? $this->request->getQuery('ds') : null;
        $filter_date_end = ($this->request->hasQuery('de')) ? $this->request->getQuery('de') : null;
        $product_id = ($this->request->hasQuery('product_id')) ? $this->request->getQuery('product_id') : 0;

        $url = '';

        if ($this->request->hasQuery('ds')) {
            $url .= '&ds=' . $this->request->getQuery('ds');
        }
        if ($this->request->hasQuery('de')) {
            $url .= '&de=' . $this->request->getQuery('de');
        }
        if ($this->request->hasQuery('product_id')) {
            $url .= '&product_id=' . $this->request->getQuery('product_id');
        }

        $de = new DateTime($filter_date_start, new DateTimeZone('America/Caracas'));
        $ds = new DateTime($filter_date_end, new DateTimeZone('America/Caracas'));

        $data = array(
            'object' => 'product',
            'object_id' => $product_id,
            'date_start' => $de->format('Y-m-d h:i:s'),
            'date_end' => $ds->format('Y-m-d h:i:s')
        );

        echo $de->format('Y-m-d h:i:s') . '<br>';
        echo $ds->format('Y-m-d h:i:s') . '<br>';

        $this->load->auto('stats/traffic');
        $this->data['browsers'] = $this->modelTraffic->getAllByBrowser($data);
        $this->data['os'] = $this->modelTraffic->getAllByOS($data);
        $this->data['customers'] = $this->modelTraffic->getAllByCustomer($data);
        $this->data['ips'] = $this->modelTraffic->getAllByIP($data);
        /*
          $this->data['os'] = $this->modelTraffic->getAllByOS($data);
          $this->data['ips'] = $this->modelTraffic->getAllByIp($data);
         */

        $this->data['params'] = $url;

        $template = ($this->config->get('default_admin_view_store_product_see_visits')) ? $this->config->get('default_admin_view_store_product_see_visits') : 'store/product_see_visits.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/' . $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function import()
    {
        $this->document->title = $this->data['heading_title'] = "Importar Productos";

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl("store/product"),
            'text' => "Productos",
            'separator' => ' :: '
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl("store/product/import"),
            'text' => "Importar Productos",
            'separator' => ' :: '
        );

        $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
        "$('#gridWrapper').load('" . Url::createAdminUrl("store/product/importwizard", array('step' => 1)) . "',function(e){
                $('#gridPreloader').hide();
                $('#q').on('keyup',function(e){
                    var that = this;
                    var valor = $(that).val().toLowerCase();
                    if (valor.length <= 0) {
                        $('#categoriesWrapper li').show();
                    } else {
                        $('#categoriesWrapper li b').each(function(){
                            if ($(this).text().toLowerCase().indexOf( valor ) != -1) {
                                $(this).closest('li').show();
                            } else {
                                $(this).closest('li').hide();
                            }
                        });
                    }
                }); 
                
            });");

        $scripts[] = array('id' => 'importFunctions', 'method' => 'function', 'script' =>
        "function file_delete(field, preview) {
                $('#' + field).val('');
                $('#' + preview).parent('.row').find('.clear').remove();
                $('#' + preview).replaceWith('<a class=\"button\" id=\"'+ preview +'\" onclick=\"file_upload(\\'file_to_import\\', \\'preview\\');\">Seleccionar Archivo</a>');
            }
            
            function file_upload(field, preview) {
                var height = $(window).height() * 0.8;
                var width = $(window).width() * 0.8;
            	$('#dialog').remove();
            	$('#form').prepend('<div id=\"dialog\" style=\"padding: 3px 0px 0px 0px;z-index:10000;\"><iframe src=\"" . Url::createAdminUrl("common/filemanager") . "&field=' + encodeURIComponent(field) + '\" style=\"padding:0; margin: 0; display: block; width: 100%; height: 100%;z-index:10000;\" frameborder=\"no\" scrolling=\"auto\"></iframe></div>');
                
                $('#dialog').dialog({
            		title: '" . $this->data['text_image_manager'] . "',
            		close: function (event, ui) {
            			var csv = $('#' + field).val();
            			if (csv) {
            				$('#' + preview).replaceWith('<input type=\"text\" value=\"' + csv.replace('data/','') + '\" id=\"' + preview + '\" disabled=\"disabled\" /><div class=\"clear\"></div>');
            			}
            		},	
            		bgiframe: false,
            		width: width,
            		height: height,
            		resizable: false,
            		modal: false
            	});}");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_store_product_import')) ? $this->config->get('default_admin_view_store_product_import') : 'store/product_import.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/' . $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }


        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function importwizard()
    {
        $this->data['Url'] = new Url;
        switch ((int) $_GET['step']) {
            case 1:
            default:
                $this->load->auto("store/category");
                $this->data['categories'] = $this->modelCategory->getAll();
                $template = ($this->config->get('default_admin_view_store_product_import_1')) ? $this->config->get('default_admin_view_store_product_import_1') : 'store/product_import_1.tpl';
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/' . $template)) {
                    $this->template = $this->config->get('config_admin_template') . '/' . $template;
                } else {
                    $this->template = 'default/' . $template;
                }

                $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
                break;
            case 2:
                $data = unserialize(file_get_contents(DIR_CACHE . "temp_product_data.csv"));

                $handle = fopen(DIR_IMAGE . $data['file'], "r+");
                $this->data['header'] = fgetcsv($handle, 1000, $data['separator'], $data['enclosure']);
                $this->data['fields']['Producto'] = array(
                    'product_id' => 'Producto ID',
                    'model' => 'Modelo',
                    'quantity' => 'Catnidad',
                    'price' => 'Precio',
                    'tax_class_id' => 'Impuesto ID',
                    'sku' => 'SKU',
                    'stock_status_id' => 'Stock Status ID',
                    'manufacturer_id' => 'Fabricante ID',
                    'date_available' => 'Fecha de Disponibilidad',
                    'weight' => 'Peso',
                    'weight_class_id' => 'Unidad de Peso ID',
                    'minimum' => 'Cantidad M&iacute;nima'
                );
                $this->data['fields']['Descripciones'] = array(
                    'language_id' => 'Idioma ID',
                    'name' => 'Nombre del Producto',
                    'description' => 'Descripci&oacute;n del Producto',
                    'meta_description' => 'Resumen',
                    'meta_keywords' => 'Palabras Claves'
                );
                $this->data['fields']['Opciones'] = array(
                    'language_id' => 'Idioma ID',
                    'option_id' => 'Opci&oacute;n ID',
                    'option_name' => 'Grupo de la Opci&oacute;n',
                    'option_label' => 'Nombre de la Opci&oacute;n',
                    'option_quantity' => 'Cantidad de la Opci&oacute;n',
                    'option_price' => 'Precio de la Opci&oacute;n',
                    'option_prefix' => 'Prefijo de la Opci&oacute;n'
                );
                $template = ($this->config->get('default_admin_view_store_product_import_2')) ? $this->config->get('default_admin_view_store_product_import_2') : 'store/product_import_2.tpl';
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/' . $template)) {
                    $this->template = $this->config->get('config_admin_template') . '/' . $template;
                } else {
                    $this->template = 'default/' . $template;
                }

                $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
                break;
            case 3:
                $template = ($this->config->get('default_admin_view_store_product_import_3')) ? $this->config->get('default_admin_view_store_product_import_3') : 'store/product_import_3.tpl';
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/' . $template)) {
                    $this->template = $this->config->get('config_admin_template') . '/' . $template;
                } else {
                    $this->template = 'default/' . $template;
                }

                $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
                break;
        }
    }

    public function importprocess()
    {
        switch ($_GET['step']) {
            case 2:
                $data = [];
                if ($this->request->hasPost('categories')) {
                    $data['categories'] = serialize($this->request->post['categories']);
                }
                $data['file'] = ($this->request->post['file']) ? $this->request->post['file'] : '';
                $data['separator'] = ($this->request->post['separator']) ? $this->request->post['separator'] : ";";
                $data['enclosure'] = ($this->request->post['enclosure'] && $this->request->post['enclosure'] != '&quote;') ? $this->request->post['enclosure'] : '"';
                $data['escape'] = ($this->request->post['escape']) ? $this->request->post['escape'] : '\\';
                $data['update'] = (int) $this->request->post['update'];
                $data['header'] = (int) $this->request->post['header'];

                $handle = fopen(DIR_IMAGE . $data['file'], "r+");
                $handle2 = fopen(DIR_CACHE . "temp_product_data.csv", "w+");
                $handle3 = fopen(DIR_CACHE . "temp_product_header.csv", "w+");
                fputcsv($handle3, (fgetcsv($handle, 1000, $data['separator'], $data['enclosure'])), $data['separator'], $data['enclosure']);
                fclose($handle3);

                fputs($handle2, serialize($data));
                fclose($handle2);

                fclose($handle);
                unset($handle, $handle2, $handle3);
                break;
            case 3:
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
                header("Cache-Control: no-cache, must-revalidate");
                header("Pragma: no-cache");
                header("Content-type: application/json");

                $return = [];
                $data = unserialize(file_get_contents(DIR_CACHE . "temp_product_data.csv"));
                $handle = fopen(DIR_IMAGE . $data['file'], "r+");
                $handle2 = fopen(DIR_CACHE . "temp_product_header.csv", "r+");

                if ($data['header'])
                $header = fgetcsv($handle2, 1000, $data['separator'], $data['enclosure']);

                $keys = [];
                if (!in_array('model', $this->request->post['Header'])) {
                    $return['error'] = 1;
                    $return['msg'] = "Debe seleccionar el campo correspondiente al modelo del producto, de lo contrario no se podr&aacute;n cargar los productos";
                }

                if (!$return['error']) {
                    $product = array(
                        'product_id',
                        'model',
                        'sku',
                        'location',
                        'quantity',
                        'stock_status_id',
                        'manufacturer_id',
                        'price',
                        'tax_class_id',
                        'date_available',
                        'weight',
                        'weight_class_id',
                        'minimum'
                    );
                    $descriptions = array(
                        'language_id',
                        'description',
                        'meta_description',
                        'meta_keywords',
                        'title'
                    );
                    $options = array(
                        'language_id',
                        'option_id',
                        'option_name',
                        'option_label',
                        'option_quantity',
                        'option_price',
                        'option_prefix'
                    );

                    $d = $data;
                    $new = $updated = $bad = $total = 1;
                    $headers = $this->request->post['Header'];
                    while ($data = fgetcsv($handle, 1000, $d['separator'], $d['enclosure'])) {
                        $product_id = $model = $forceUpdate = null;
                        if ($data == $header && $d['header'])
                        continue;
                        $return['total'] = $total++;

                        if ($d['update']) {
                            $sql = "UPDATE " . DB_PREFIX . "product SET ";
                            $sql_desc = "UPDATE " . DB_PREFIX . "description SET ";

                            $sql_options = "UPDATE " . DB_PREFIX . "product_option SET ";
                            $sql_options_value = "UPDATE " . DB_PREFIX . "product_option_value SET ";
                            $sql_options_description = "UPDATE " . DB_PREFIX . "product_option_descrption SET ";
                            $sql_options_value_description = "UPDATE " . DB_PREFIX . "product_option_value_description SET ";
                        } else {
                            $sql = "INSERT INTO " . DB_PREFIX . "product SET ";
                            $sql_desc = "INSERT INTO " . DB_PREFIX . "description SET ";

                            $sql_options = "INSERT INTO " . DB_PREFIX . "product_option SET ";
                            $sql_options_value = "INSERT INTO " . DB_PREFIX . "product_option_value SET ";
                            $sql_options_description = "INSERT INTO " . DB_PREFIX . "product_option_descrption SET ";
                            $sql_options_value_description = "INSERT INTO " . DB_PREFIX . "product_option_value_description SET ";
                        }

                        foreach ($header as $key => $col) { //$key = 0; $col = 'Nombre'
                            $data[$key] = preg_replace('/<\s*html.*?>/', '', $data[$key]);
                            $data[$key] = preg_replace('/<\s*\/\s*html\s*.*?>/', '', $data[$key]);
                            $data[$key] = preg_replace('@<head[^>]*?>.*?</head>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<style[^>]*?>.*?</style>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<script[^>]*?.*?</script>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<object[^>]*?.*?</object>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<embed[^>]*?.*?</embed>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<applet[^>]*?.*?</applet>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<iframe[^>]*?.*?</iframe>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<noframes[^>]*?.*?</noframes>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<noscript[^>]*?.*?</noscript>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<noembed[^>]*?.*?</noembed>@siu', '', $data[$key]);
                            foreach ($headers as $column => $field) { //$column = 'Nombre'; $field = 'name'; <select name="Header[name]">
                                $col = str_replace(" ", "_", $col);

                                //TODO: validar cada campo de acuerdo a su tipo y longitud para evitar la insercion de datos basura
                                if (!empty($field) && $col == $column) {
                                    if (in_array($field, $product)) {
                                        $keys[$key] = $field;
                                        $sql .= "`$field`='" . $this->db->escape($data[$key]) . "',";
                                    } elseif (in_array($field, $descriptions)) {
                                        $keys[$key] = $field;
                                        if ($field == 'language_id') {
                                            $language_id = (int) $data[$key];
                                        }
                                        $sql_desc .= "`$field`='" . $this->db->escape($data[$key]) . "',";
                                        $hasDescription = true;
                                    } elseif (in_array($field, $options)) {
                                        $keys[$key] = $field;
                                        if ($field == 'option_id') {
                                            $option_id = (int) $data[$key];
                                        }
                                        if ($field == 'language_id') {
                                            $language_id = (int) $data[$key];
                                        }
                                        if ($field == 'language_id' || $field == 'option_name') {
                                            $sql_options_description .= "`" . str_replace("option_", "", $field) . "`='" . $this->db->escape($data[$key]) . "',";
                                        }
                                        if ($field == 'option_quantity' || $field == 'option_price' || $field == 'option_prefix') {
                                            $sql_options_value .= "`" . str_replace("option_", "", $field) . "`='" . $this->db->escape($data[$key]) . "',";
                                        }
                                        if ($field == 'option_label' || $field == 'language_id') {
                                            $sql_options_value_description .= "`" . str_replace("option_label", "name", $field) . "`='" . $this->db->escape($data[$key]) . "',";
                                        }
                                        $hasOptions = true;
                                    }
                                }
                            }
                        }

                        $pid = array_search('product_id', $keys);
                        $idx = array_search('model', $keys);

                        if (!array_search('date_added', $keys))
                            $sql .= "`date_added`=NOW(),";
                        if (!array_search('date_modified', $keys))
                            $sql .= "`date_modified`=NOW(),";
                        if (!array_search('language_id', $keys)) {
                            $sql_desc .= "`language_id`='1',";
                            $sql_options_description .= "`language_id`='1',";
                            $sql_options_value_description .= "`language_id`='1',";
                            $language_id = 1;
                        }

                        $forceOptionUpdate = false;
                        if (!empty($option_id)) {
                            $res = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE option_id='" . (int) $option_id . "'");
                            if ($res->num_rows) {
                                $forceOptionUpdate = true;
                            }

                            $sql_options_value .= "`option_id`='" . (int) $option_id . "',";
                            $sql_options_description .= "`option_id`='" . (int) $option_id . "',";
                            $sql_options_value_description .= "`option_id`='" . (int) $option_id . "',";
                        }

                        if (!$pid && !$idx) {
                            $return['error'] = 1;
                            $return['msg'] = "Debe especificar el modelo del producto";
                            break;
                        }

                        if ($pid) {
                            $product_id = (int) $data[$pid];
                        }
                        if ($idx) {
                            $model = $data[$idx];
                        }

                        $forceUpdate = false;
                        if (!empty($product_id) && !empty($model)) {
                            $res = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id='" . (int) $product_id . "' OR model='" . $this->db->escape($model) . "'");
                            if ($res->num_rows && !$d['update']) {
                                continue;
                            } elseif ($res->num_rows && $d['update']) {
                                $forceUpdate = true;
                            }
                        } elseif (!empty($model)) {
                            $res = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE model='" . $this->db->escape($model) . "'");
                            if ($res->num_rows && !$d['update']) {
                                continue;
                            } elseif ($res->num_rows && $d['update']) {
                                $product_id = $res->row['product_id'];
                                $forceUpdate = true;
                            }
                        } elseif (!empty($product_id)) {
                            $res = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id='" . (int) $product_id . "'");
                            if ($res->num_rows && !$d['update']) {
                                continue;
                            } elseif ($res->num_rows && $d['update']) {
                                $forceUpdate = true;
                            }
                        }

                        $sql = substr($sql, 0, (strlen($sql) - 1));
                        $sql_desc = substr($sql_desc, 0, (strlen($sql_desc) - 1));
                        $sql_options = substr($sql_options, 0, (strlen($sql_options) - 1));
                        $sql_options_value = substr($sql_options_value, 0, (strlen($sql_options_value) - 1));
                        $sql_options_description = substr($sql_options_description, 0, (strlen($sql_options_description) - 1));
                        $sql_options_value_description = substr($sql_options_value_description, 0, (strlen($sql_options_value_description) - 1));

                        if ($d['update']) {
                            if (!$forceUpdate) {
                                $sql = str_replace("UPDATE", "INSERT INTO", $sql);
                                $insert = true;
                            } else {
                                $sql = str_replace("INSERT INTO", "UPDATE", $sql) . " WHERE `product_id` = '" . (int) $product_id . "'";
                            }
                            $result = $this->db->query($sql);
                            if (!$forceUpdate)
                                $product_id = $this->db->getLastId();

                            if (!$forceUpdate) {
                                $sql_desc .= ",`product_id`='" . (int) $product_id . "'";
                                $sql_desc = str_replace("UPDATE", "INSERT INTO", $sql_desc);
                                $insert = true;
                            } else {
                                $sql_desc = str_replace("INSERT INTO", "UPDATE", $sql_desc) . " WHERE `product_id` = '" . (int) $product_id . "' AND `language_id` = '" . (int) $language_id . "'";
                            }
                            if (isset($hasDescription) && $result)
                                $this->db->query($sql_desc);

                            if ($product_id) {
                                $sql_options .= ",`product_id`='" . (int) $product_id . "'";
                                $sql_options_value .= ",`product_id`='" . (int) $product_id . "'";
                                $sql_options_description .= ",`product_id`='" . (int) $product_id . "'";
                                $sql_options_value_description .= ",`product_id`='" . (int) $product_id . "'";
                            }

                            if (!$forceOptionUpdate) {
                                $sql_options = str_replace("UPDATE", "INSERT INTO", $sql_options);
                                $sql_options_value = str_replace("UPDATE", "INSERT INTO", $sql_options_value);
                                $sql_options_description = str_replace("UPDATE", "INSERT INTO", $sql_options_description);
                                $sql_options_value_description = str_replace("UPDATE", "INSERT INTO", $sql_options_value_description);
                            } else {
                                $sql_options = str_replace("INSERT INTO", "UPDATE", $sql_options) . " WHERE `option_id` = '" . (int) $option_id . "'";
                                $sql_options_value = str_replace("INSERT INTO", "UPDATE", $sql_options_value) . " WHERE `option_id` = '" . (int) $option_id . "'";
                                $sql_options_description = str_replace("INSERT INTO", "UPDATE", $sql_options_description) . " WHERE `option_id` = '" . (int) $option_id . "' AND `language_id` = '" . (int) $language_id . "'";
                                $sql_options_value_description = str_replace("INSERT INTO", "UPDATE", $sql_options_value_description) . " WHERE `option_id` = '" . (int) $option_id . "' AND `language_id` = '" . (int) $language_id . "'";
                            }
                            if (isset($hasOptions) && $result)
                                $this->db->query($sql_options);

                            if ($result && isset($insert)) {
                                $return['nuevo'] = $new++;
                            } elseif ($result && !isset($insert)) {
                                $return['updated'] = $updated++;
                            } else {
                                $return['bad'] = $bad++;
                            }
                        } else {
                            $result = $this->db->query($sql);
                            $product_id = $this->db->getLastId();

                            if ($product_id) {
                                $sql_desc .= ",`product_id`='" . (int) $product_id . "'";
                                $sql_options .= ",`product_id`='" . (int) $product_id . "'";
                                $sql_options_value .= ",`product_id`='" . (int) $product_id . "'";
                                $sql_options_description .= ",`product_id`='" . (int) $product_id . "'";
                                $sql_options_value_description .= ",`product_id`='" . (int) $product_id . "'";
                            }

                            if (isset($hasDescription) && $result) {
                                $result_desc = $this->db->query($sql_desc);
                            }

                            if (isset($hasOptions) && $result) {
                                $result_options = $this->db->query($sql_options);
                                $result_options = $this->db->query($sql_options_value);
                                $result_options = $this->db->query($sql_options_description);
                                $result_options = $this->db->query($sql_options_value_description);
                            }

                            if ($result) {
                                $return['nuevo'] = $new++;
                            } else {
                                $return['bad'] = $bad++;
                            }
                        }

                        //TODO: asociar las categorias a cada producto
                    }
                }
                unlink(DIR_CACHE . "temp_product_header.csv");
                unlink(DIR_CACHE . "temp_product_data.csv");
                unlink(DIR_CACHE . "temp_product_categories.csv");
                $this->load->library('json');
                $this->response->setOutput(Json::encode($return), $this->config->get('config_compression'));
                break;
        }
    }

    protected function ntASort($a, $b)
    { //(&$array, $key) {
        /*
          $sorter=[];
          $ret=[];
          reset($array);
          foreach ($array as $ii => $va) {
          $sorter[$ii]=$va[$key];
          }
          asort($sorter);
          foreach ($sorter as $ii => $va) {
          $ret[$ii]=$array[$ii];
          }
          $array=$ret;
         */
        return $a[$this->aKey] - $b[$this->aKey];
    }

    protected function msort($array, $key, $sort_flags = SORT_REGULAR)
    {
        if (is_array($array) && count($array) > 0) {
            if (!empty($key)) {
                $mapping = [];
                foreach ($array as $k => $v) {
                    $sort_key = '';
                    if (!is_array($key)) {
                        $sort_key = $v[$key];
                    } else {
                        // @TODO This should be fixed, now it will be sorted as string
                        foreach ($key as $key_key) {
                            $sort_key .= $v[$key_key];
                        }
                        $sort_flags = SORT_STRING;
                    }
                    $mapping[$k] = $sort_key;
                }
                asort($mapping, $sort_flags);
                $sorted = [];
                foreach ($mapping as $k => $v) {
                    $sorted[] = $array[$k];
                }
                return $sorted;
            }
        }
        return $array;
    }

    public function products() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->auto('store/product');
        $this->load->auto('image');
        if ($this->request->hasQuery('product_id') > 0) {
            $products_related = $this->modelProduct->getRelated($this->request->getQuery('product_id'));
        }

        $cache = $this->cache->get("products.for.product.form");
        if ($cache) {
            $products = $cache;
        } else {
            $products = $this->modelProduct->getAll();
            $this->cache->set("products.for.product.form", $products);
        }

        $this->data['Image'] = new NTImage();

        $output = [];

        foreach ($products as $product) {
            if (!empty($products_related) && in_array($product['product_id'], $products_related)) {
                $output[] = array(
                    'product_id' => $product['product_id'],
                    'image' => NTImage::resizeAndSave($product['image'], 50, 50),
                    'pname' => $product['pname'],
                    'class' => 'added',
                    'value' => $product['product_id']
                );
            } else {
                $output[] = array(
                    'product_id' => $product['product_id'],
                    'image' => NTImage::resizeAndSave($product['image'], 50, 50),
                    'pname' => $product['pname'],
                    'class' => 'add',
                    'value' => $product['product_id']
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function checkmodel() {
        $json = [];
        if ($this->request->hasQuery('model')) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE model = '" . $this->db->escape($this->request->getQuery('model')) . "'");
            if ($query->num_rows && $query->row['product_id'] != $this->request->getQuery('product_id')) {
                $json['error'] = 1;
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }

    /**
     * ControllerStoreProduct::category()
     * 
     * @see Load
     * @see Model
     * @see Response
     * @see Request
     * @see Language
     * @return void
     */
    public function category() {
        $this->load->auto('store/product');

        if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];
        } else {
            $category_id = 0;
        }

        $product_data = [];

        $results = $this->modelProduct->getAllByCategoryId($category_id);

        foreach ($results as $result) {
            $product_data[] = array(
                'product_id' => $result['product_id'],
                'title' => $result['pname'],
                'model' => $result['model']
            );
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($product_data));
    }

    /**
     * ControllerStoreProduct::related()
     * 
     * @see Load
     * @see Model
     * @see Response
     * @see Request
     * @see Language
     * @return void
     */
    public function related() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->auto('store/product');

        if ($this->request->hasPost('product_related')) {
            $products = $this->request->post['product_related'];
        } else {
            $products = [];
        }

        $product_data = [];

        foreach ($products as $product_id) {
            $product_info = $this->modelProduct->getById($product_id);

            if ($product_info) {
                $product_data[] = array(
                    'product_id' => $product_info['product_id'],
                    'title' => $product_info['pname'],
                    'model' => $product_info['model']
                );
            }
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($product_data));
    }
}
