<?php

/**
 * ControllerAdmin
 * 
 * @package  NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.1.0
 * @access public
 * @see Controller
 */
class ControllerAdmin extends Controller {

    protected string $post_type = '';
    protected string $object_type = '';
    protected string $model_name = '';
    protected string $model_route = '';
    protected string $model_object_type = '';
    protected string $controller_name = '';
    protected string $controller_route = '';
    protected string $controller_template_basename = '';
    protected string $controller_template_route = '';
    protected array $form_vars = [];
    protected array $filters = [];
    protected array $public_methods = [];

    protected $model = null;

    private $error = [];

    public function init() {
        if (!empty($this->model_name)) {
            $this->model = $this->load->model($this->model_route, true);
            if ($this->model) {
                $this->model->setObjectType($this->model_object_type);
            } else {
                throw new Exception("Can't load main model {$this->model_route} for controller {$this->ClassName} and route {$this->Route}");
            }
        }
    }

    public function index() {
        //do actions for this controller method 
        $hasToReturn = $this->runHook("index", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }
        $this->init();
        $this->document->title = $this->language->get('heading_title');

        $this->user->registerActivity(0, $this->object_type, 'Access for list of the class '. $this->ClassName .' on '. $this->Route, 'read');

        $this->getList();
    }

    public function insert() {
        if (!in_array('insert', $this->public_methods)) {
            //TODO:forward to not found or has not permissions 
        }
        //do actions for this controller method 
        $hasToReturn = $this->runHook("insert", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }
        $this->upsert();
    }

    public function update() {
        if (!in_array('update', $this->public_methods)) {
            //TODO:forward to not found or has not permissions 
        }
        //do actions for this controller method 
        $hasToReturn = $this->runHook("update", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }
        $this->upsert();
    }

    public function copy() {
        if (!in_array('copy', $this->public_methods)) {
            //TODO:forward to not found or has not permissions 
        }
        //do actions for this controller method 
        $hasToReturn = $this->runHook("copy", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $ids = $this->request->getPost('selected');
            foreach ($ids as $id) {
                $this->model->copy($id);

                $this->user->registerActivity($id, $this->object_type, ucfirst($this->object_type) .' copied or duplicated through ' . $this->ClassName . ' on ' . $this->Route, 'copy');

                $this->trigger("copy", [
                    'id' => $id,
                    'controller' => $this->ClassName,
                    'route' => $this->Route,
                ]);
            }
        } else {
            $this->model->copy($_GET['id']);
            
            $this->user->registerActivity($_GET['id'], $this->object_type, ucfirst($this->object_type) . ' copied or duplicated through ' . $this->ClassName . ' on ' . $this->Route, 'copy');
            
            $this->trigger("copy", [
                'id'=> $_GET['id'],
                'controller' => $this->ClassName,
                'route' => $this->Route,
            ]);
        }
        echo 1;
    }

    public function delete() {
        if (!in_array('delete', $this->public_methods) || !$this->validateDelete()) {
            //TODO:forward to not found or has not permissions 
        }

        //do actions for this controller method 
        $hasToReturn = $this->runHook("delete", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $data = $this->request->getPost();
            foreach ($data['selected'] as $id) {
                $this->model->delete($id);
                
                $this->user->registerActivity($id, $this->object_type, ucfirst($this->object_type) . ' deleted through ' . $this->ClassName . ' on ' . $this->Route, 'delete');

                $this->trigger("delete", [
                    'id' => $id,
                    'controller' => $this->ClassName,
                    'route' => $this->Route,
                ]);
            }
        } else {
            $this->model->delete($_GET['id']);
            $this->user->registerActivity($_GET['id'], $this->object_type, ucfirst($this->object_type) . ' deleted through ' . $this->ClassName . ' on ' . $this->Route, 'delete');

            $this->trigger("delete", [
                'id' => $_GET['id'],
                'controller' => $this->ClassName,
                'route' => $this->Route,
            ]);
        }
    }

    public function activate() {
        if (!in_array('activate', $this->public_methods)) {
            //TODO:forward to not found or has not permissions 
        }

        //do actions for this controller method 
        $hasToReturn = $this->runHook("avtivate", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        if (!isset($_GET['id']))
            return false;

        $status = $this->model->getById($_GET['id']);

        if ($status) {
            if ($status['status'] == 0) {
                $this->model->activate($_GET['id']);

                $this->user->registerActivity($_GET['id'], $this->object_type, ucfirst($this->object_type) . ' activated through ' . $this->ClassName . ' on ' . $this->Route, 'activate');

                $this->trigger("activate", [
                    'id' => $_GET['id'],
                    'controller' => $this->ClassName,
                    'route' => $this->Route,
                ]);
                echo 1;
            } else {
                $this->model->deactivate($_GET['id']);

                $this->user->registerActivity($_GET['id'], $this->object_type, ucfirst($this->object_type) . ' Deactivated through ' . $this->ClassName . ' on ' . $this->Route, 'deactivate');

                $this->trigger("deactivate", [
                    'id' => $_GET['id'],
                    'controller' => $this->ClassName,
                    'route' => $this->Route,
                ]);
                echo -1;
            }
        } else {
            echo 0;
        }
    }

    public function sortable() {
        if (!in_array('sortable', $this->public_methods)) {
            //TODO:forward to not found or has not permissions 
        }

        //do actions for this controller method 
        $hasToReturn = $this->runHook("sortable", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        $this->load->auto($this->model_route);
        $result = $this->model->sortTable($_POST);
        if ($result) {
            $this->user->registerActivity(0, $this->object_type, ucfirst($this->object_type) . ' ordered or sorted through ' . $this->ClassName . ' on ' . $this->Route, 'sort_order');

            echo 1;
        } else {
            echo 0;
        }
    }

    public function grid() {
        if (!in_array('grid', $this->public_methods)) {
            //TODO:forward to not found or has not permissions 
        }

        $this->__grid();
    }

    public function updateparent()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        if (empty($_GET['category_id']) && !isset($_GET['parent_id'])) {
            $data['error'] = 1;
            $data['msg'] = "No se encontr&oacute; la categor&iacute;a que se va a actualizar";
        }
        $result = $this->db->query("UPDATE " . DB_PREFIX . "category SET parent_id = " . (int) $_GET['parent_id'] . " WHERE object_type = '". $this->model_object_type ."' AND category_id = '" . (int) $_GET['category_id'] . "'");
        if ($result) {
            $data['success'] = 1;
        } else {
            $data['error'] = 1;
            $data['msg'] = "No se pudo actualizar la catego&iacute;a, por favor reporte esta falla a trav&eacute;s del formulario de sugerencias";
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($data), $this->config->get('config_compression'));
    }

    protected function getUrl(string $uri = '', array $params = [])
    {
        if (!empty($uri)) return Url::createAdminUrl($this->controller_route . $uri, $params);
        else return Url::createAdminUrl($this->controller_route, $params);
    }

    private function upsert() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $data = $this->request->getPost();

            $data = $this->applyFilters("formData", $data);

            if (isset($data['is_campaign'])) {
                $total_images = $total_embed_images = $total_trace_links = $total_links = $score = 0;
                $_links = $broken_rules = [];
            }

            if (isset($data['descriptions'])) {
                foreach ($data['descriptions'] as $language_id => $description) {
                    if (!isset($description['description']) || empty($description['description'])) continue;
                    $dom = new DOMDocument;
                    $dom->preserveWhiteSpace = false;
                    $dom->loadHTML(html_entity_decode($description['description']));
                    $images = $dom->getElementsByTagName('img');
                    $total_images = $total_embed_images = 0;
                    foreach ($images as $image) {
                        $src = $image->getAttribute('src');

                        if (isset($data['embed_image']) && $data['embed_image']) {
                            $src = str_replace(HTTP_IMAGE, DIR_IMAGE, $src);
                            if (file_exists($src)) {
                                $img = file_get_contents($src);
                                $ext = substr($src, (strrpos($src, '.') + 1));
                                $embed = base64_encode($img);
                                $image->setAttribute('src', "data:image/$ext;base64,$embed");
                                $total_embed_images++;
                            }
                            $total_images++;


                        } else {
                            if (preg_match('/data:([^;]*);base64,(.*)/', $src)) {
                                list($type, $img) = explode(",", $src);
                                $type = trim(substr($type, strpos($type, "/") + 1, 3));

                                //TODO: validar imagenes
                                $str = $this->config->get('config_name');
                                if ($str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                                    $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
                                $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
                                $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $str);
                                $str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
                                $str = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $str);
                                $str = strtolower(trim($str, '-'));

                                $filename = uniqid($str . "-") . "_" . time() . "." . $type;
                                $fp = fopen(DIR_IMAGE . "data/" . $filename, 'wb');
                                fwrite($fp, base64_decode($img));
                                fclose($fp);
                                $image->setAttribute('src', HTTP_IMAGE . "data/" . $filename);
                            }
                        }
                    }

                    //if is an email campaign
                    if (isset($data['is_campaign'])) {
                        $params = array(
                            'contact_id' => '{%contact_id%}',
                            'campaign_id' => '{%campaign_id%}'
                        );

                        //add an transparent image to track email
                        if (isset($data['trace_email']) && $data['trace_email']) {
                            $trace_url = Url::createUrl("marketing/campaign/trace", $params, 'NONSSL', HTTP_CATALOG);
                            $trackEmail = $dom->createElement('img');
                            $trackEmail->setAttribute('src', $trace_url);
                            $dom->appendChild($trackEmail);
                        }

                        //rewrite links to track it
                        if (isset($data['trace_click']) && $data['trace_click']) {
                            $links = $dom->getElementsByTagName('a');
                            $total_links = $total_trace_links = 0;
                            foreach ($links as $link) {
                                $href = $link->getAttribute('href');
                                $total_links++;
                                if (empty($href) || $href == "#" || strpos($href, "mailto"))
                                    continue;

                                //TODO: validar enlaces
                                //TODO: sanitizar enlaces

                                $params['link_index'] = md5(time() . mt_rand(1000000, 9999999) . $href);
                                $_link = Url::createUrl("marketing/campaign/link", $params, 'NONSSL', HTTP_CATALOG);
                                $link->setAttribute('href', $_link);
                                $_links[] = array(
                                    "url" => $_link,
                                    "redirect" => $href,
                                    "link_index" => $params['link_index']
                                );
                                $total_trace_links++;

                                if (!$link->getAttribute('title')) {
                                    $link->setAttribute('title', $this->config->get('config_name'));
                                }
                            }
                        }

                    }

                    //apply filters to description
                    $_dom = $this->applyFilters("formProcess:dom", ['dom' => $dom, 'data' => $data]);
                    if (isset($_dom['dom']) && $_dom['dom']) $dom = $_dom['dom'];

                    $description['description'] = htmlentities($dom->saveHTML());

                    //calculate spam score
                    if (isset($data['is_campaign'])) {
                        if (file_exists(DIR_SYSTEM . 'library/email/spam_rules.php')) {
                            require_once(DIR_SYSTEM . 'library/email/spam_rules.php');
                            foreach ($spam_rules as $rule) {
                                if (preg_match($rule[0], $description['description'])) {
                                    $score += $rule[2];
                                    $broken_rules[] = array($rule[1], $rule[2]);
                                }
                            }
                        }

                        //TODO: agregar info para rastrear con google analytics
                        $size = 0;
                        $email_size = mb_strlen($description['description'], '8bit') / 1000;
                        if ($email_size >= 1000) {
                            $size = round(($email_size / 1000), 2) . " MB";
                        } else {
                            $size = round($email_size, 2) . " KB";
                        }

                        $data['descriptions'][$language_id]['spam_score'] = $score;
                        $data['descriptions'][$language_id]['broken_rules'] = $broken_rules;
                        $data['descriptions'][$language_id]['total_embed_images'] = $total_embed_images;
                        $data['descriptions'][$language_id]['total_trace_links'] = $total_trace_links;
                        $data['descriptions'][$language_id]['total_images'] = $total_images;
                        $data['descriptions'][$language_id]['total_links'] = $total_links;
                        $data['descriptions'][$language_id]['email_size'] = $email_size;
                        $data['descriptions'][$language_id]['size'] = $size;
                    }

                    //apply filters to description
                    $description['description'] = $this->applyFilters("formProcess:description", $description['description']);

                    $data['descriptions'][$language_id] = $description;
                }
            }

            //TODO: add formatter option ti field tree 
            //TODO: add filter to foreach 
            foreach ($this->form_vars as $k => $var) {
                if (array_key_exists('isProperty', $var) || $var['isProperty']) continue;

                if ($var['type'] == 'date') {
                    if (isset($data[$var['name']]) && empty($data[$var['name']])) {
                        $data[$var['name']] = '0000-00-00';
                    } elseif (isset($data[$var['name']])) {
                        $dpe = explode("/", $data[$var['name']]);
                        $data[$var['name']] = $dpe[2] . "-" . $dpe[1] . "-" . $dpe[0];
                    }
                } elseif ($var['type'] == 'integer' || $var['type'] == 'number') {
                    $data[$var['name']] = isset($data[$var['name']]) ? (int)$data[$var['name']] : 0;
                } elseif ($var['type'] == 'float') {
                    $data[$var['name']] = isset($data[$var['name']]) ? (float)$data[$var['name']] : 0;
                } elseif ($var['type'] == 'boolean') {
                    $data[$var['name']] = isset($data[$var['name']]) && (bool)$data[$var['name']] === true ? 1 : 0;
                } else {
                    $data[$var['name']] = isset($data[$var['name']]) ? $data[$var['name']] : (isset($var['required']) && isset($var['default']) ? $var['default'] : '');
                }
            }

            $pkey = $this->model->getPkey();
            $id = $this->request->getQuery($pkey);
            if ($id) {
                $this->model->update($id, $data);

                $this->user->registerActivity($id, $this->object_type, ucfirst($this->object_type) . ' updated through ' . $this->ClassName . ' on ' . $this->Route, 'update');

                $this->trigger("edit", [
                    'id' => $id,
                    'controller' => $this->ClassName,
                    'route' => $this->Route,
                ]);
            } else {
                $id = $this->model->add($data);
                
                $this->user->registerActivity($id, $this->object_type, ucfirst($this->object_type) . ' created through ' . $this->ClassName . ' on ' . $this->Route, 'create');

                $this->trigger("new", [
                    'id' => $id,
                    'controller' => $this->ClassName,
                    'route' => $this->Route,
                ]);
            }
            //TODO: add formatter option ti field tree 
            //TODO: add filter to foreach 
            foreach ($this->form_vars as $var) {
                if (!isset($var['isProperty']) || !$var['isProperty']) continue;
                if (!isset($var['group']) || !isset($var['key'])) continue;

                if ($var['type'] == 'date') {
                    if (isset($data[$var['name']]) && empty($data[$var['name']])) {
                        $data[$var['name']] = '0000-00-00';
                    } elseif (isset($data[$var['name']])) {
                        $dpe = explode("/", $data[$var['name']]);
                        $data[$var['name']] = $dpe[2] . "-" . $dpe[1] . "-" . $dpe[0] ;
                    }
                } elseif ($var['type'] == 'integer' || $var['type'] == 'number') {
                    $data[$var['name']] = isset($data[$var['name']]) ? (int)$data[$var['name']] : 0;
                } elseif ($var['type'] == 'float') {
                    $data[$var['name']] = isset($data[$var['name']]) ? (float)$data[$var['name']] : 0;
                } elseif ($var['type'] == 'boolean') {
                    $data[$var['name']] = isset($data[$var['name']]) && (bool)$data[$var['name']] === true ? 1 : 0;
                } else {
                    $data[$var['name']] = isset($data[$var['name']]) ? $data[$var['name']] : (isset($var['required']) && isset($var['default']) ? $var['default'] : '');
                }
                $this->model->setProperty($id, $var['group'], $var['key'], $data[$var['name']]);
            }
            
            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect($this->getUrl('/update', ["$pkey" => $id]));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect($this->getUrl('/insert'));
            } else {
                $this->redirect($this->getUrl());
            }
        }
        $this->getForm();
    }

    private function getList() {
        //do actions for this controller method 
        $hasToReturn = $this->runHook("getList", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }
        
        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => $this->getUrl(),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        //apply filters to breadcrumbs
        $breadcrumbs = $this->applyFilters("breadcrumbs", [$this, 'breadcrumbs'=>$this->document->breadcrumbs]);
        if (isset($breadcrumbs['breadcrumbs']))  $this->document->breadcrumbs = $breadcrumbs;

        //TODO: crear funci�n para generar urls absolutas a partir de un controller						
        $this->data['insert'] = $this->getUrl("/insert");
        $this->data['delete'] = $this->getUrl("/delete");
        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_warning'] = $this->session->has('success') ? $this->session->get('success') : '';
        $this->session->clear('success');


        // SCRIPTS
        $scripts[] = array('id' => $this->controller_name.'List', 'method' => 'function', 'script' =>
            "function activate(e) {
            	$.ajax({
            	   'type':'get',
                   'dataType':'json',
                   'url':'" . $this->getUrl("/activate") . "&id=' + e,
                   'success': function(data) {
                        if (data > 0) {
                            $('#img_' + e).attr('src','image/good.png');
                        } else {
                            $('#img_' + e).attr('src','image/minus.png');
                        }
                   }
            	});
            }
            function copy(e) {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.getJSON('" . $this->getUrl("/copy") . "&id=' + e, function(data) {
                    $('#gridWrapper').load('" . $this->getUrl("/grid") . "',function(response){
                        $('#gridPreloader').hide();
                        $('#gridWrapper').show();
                    });
                });
            }
            function copyAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . $this->getUrl("/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . $this->getUrl("/grid") . "',function(){
                        $('#gridWrapper').show();
                        $('#gridPreloader').hide();
                    });
                });
                return false;
            } 
            function eliminar(e) {    
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                	$.ajax({
                	   'type':'get',
                       'dataType':'json',
                       'url':'" . $this->getUrl("/delete") . "&id=' + e
                	});
                    
                    $('#'+ e).remove();
                }
             }
            function deleteAll() {
                if (confirm('\\xbfDesea eliminar todos los objetos seleccionados?')) {
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                    $.post('" . $this->getUrl("/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . $this->getUrl("/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");

        $code = '';
        if (in_array('sortable', $this->public_methods)) {
            $code = "$('#list tbody').sortable({
                    opacity: 0.6, 
                    cursor: 'move',
                    handle: '.move',
                    update: function() {
                        $.ajax({
                            'type':'post',
                            'dateType':'json',
                            'url':'" . $this->getUrl("/sortable") . "',
                            'data': $(this).sortable('serialize'),
                            'success': function(data) {
                                if (data > 0) {
                                    var msj = '<div class=\"message success\">Se han ordenado los objetos correctamente</div>';
                                } else {
                                    var msj = '<div class=\"message warning\">Hubo un error al intentar ordenar los objetos, por favor intente m&aacute;s tarde</div>';
                                }
                                $('#msg').fadeIn().append(msj).delay(3600).fadeOut();
                            }
                        });
                    }
                }).disableSelection();
                $('.move').css('cursor','move');";
        }

        if (in_array('nestedSortable', $this->public_methods)) {
            $code = "$('ol.items').nestedSortable({
        			forcePlaceholderSize: true,
        			handle: 'div.item',
        			helper:	'clone',
        			items: 'li',
        			maxLevels: 3,
        			opacity: .6,
        			placeholder: 'placeholder',
        			revert: 250,
        			tabSize: 25,
        			tolerance: 'pointer',
        			toleranceElement: '> div.item',
                    update:  function (event, ui) {
                        var parent = ui.item.parents('li');
                        
                        if (parent.length > 0) {
                            parent_id = parent.attr('id');
                        } else {
                            parent_id = 0;
                        }
                        
                        $.getJSON('" . $this->getUrl("/updateparent") . "',{'parent_id':parent_id,'category_id':ui.item.attr('id')},function(data){
                            if (data.error) {
                                $('#msg').fadeIn().append('<div class=\"message warning\"'+ data.msg +'</div>').delay(3600).fadeOut();
                            }
                        });
                        
                        var sorts = {}; 
                        var i = 0;
                        $('ol.items li').each(function(){
                            i++;
                            sorts[i] = $(this).attr('id');
                        }); 
                        
                        $.post('" . $this->getUrl("/sortable") . "',sorts,
                        function(data){
                            if (data.error) {
                                $('#msg').fadeIn().append('<div class=\"message warning\"'+ data.msg +'</div>').delay(3600).fadeOut();
                            }
                        });
                    }
        		});";
        }
        
        $scripts[] = array('id' => 'grid', 'method' => 'ready', 'script' =>
        "$('#gridWrapper').load('" . $this->getUrl("/grid") . "', function(e){
            $('#gridPreloader').hide();
            {$code}
        });");

        $scripts[] = array('id' => 'loadGrid', 'method' => 'ready', 'script' =>
            "$('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . $this->getUrl("/grid") . "',
                beforeSend:function(){
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                },
                success:function(data){
                    $('#gridPreloader').hide();
                    $('#gridWrapper').html(data).show();
                    
                    {$code}
                }
            });
            $('#formFilter').on('keyup', function(e){
                var code = e.keyCode || e.which;
                if (code == 13){
                    $('#formFilter').ntForm('submit');
                }
            });");

        $this->scripts = array_merge($this->scripts, $scripts);

        //apply filters to $data
        $this->data = $this->applyFilters("getList:data", $this->data);

        //apply filters to $scripts
        $this->scripts = $this->applyFilters("getList:scripts", $this->scripts);

        $template = ($this->config->get('default_admin_view_'. $this->controller_template_basename . '_list')) ? $this->config->get('default_admin_view_'. $this->controller_template_basename . '_list') : $this->controller_template_route .'_list.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    private function __grid() {
        //do actions for this controller method 
        $hasToReturn = $this->runHook("grid", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        $url = '';
        $data = [];

        if (isset($this->filters) && is_array($this->filters) && !empty($this->filters)) {
            foreach ($this->filters as $filter) {
                if ($this->request->hasQuery($filter['name'])) {
                    $data[$filter['name']] = $this->request->getQuery($filter['name']);
                    $url .= "&filter_{$filter['name']}=" . $data[$filter['name']];
                    
                    if ($filter['type'] == 'date') {
                        if (isset($data[$filter['name']]) && empty($data[$filter['name']])) {
                            $data[$filter['name']] = '0000-00-00';
                        } elseif (isset($data[$filter['name']])) {
                            $dpe = explode("/", $data[$filter['name']]);
                            $data[$filter['name']] = $dpe[2] . "-" . $dpe[1] . "-" . $dpe[0];
                        }
                    } elseif ($filter['type'] == 'integer' || $filter['type'] == 'number') {
                        $data[$filter['name']] = isset($data[$filter['name']]) ? (int)$data[$filter['name']] : 0;
                    } elseif ($filter['type'] == 'float') {
                        $data[$filter['name']] = isset($data[$filter['name']]) ? (float)$data[$filter['name']] : 0;
                    } elseif ($filter['type'] == 'boolean') {
                        $data[$filter['name']] = isset($data[$filter['name']]) && (bool)$data[$filter['name']] === true ? 1 : 0;
                    } else {
                        $data[$filter['name']] = isset($data[$filter['name']]) ? $data[$filter['name']] : (isset($filter['required']) && isset($filter['default']) ? $filter['default'] : '');
                    }
                } elseif (isset($filter['default'])) {
                    $data[$filter['name']] = $filter['default'];
                    $url .= "&filter_{$filter['name']}=" . $data[$filter['name']];
                }
            }
        }

        if ($this->request->hasQuery('page')) {
            $page = $this->request->getQuery('page');
            $url .= '&page=' . $page;
        } else {
            $page = 1;
        }

        if ($this->request->hasQuery('sort')) {
            $sort = $this->request->getQuery('sort');
            $url .= '&sort=' . $sort;
        } else {
            $sort = 't.date_added';
        }

        if ($this->request->hasQuery('order')) {
            $order = $this->request->getQuery('order');
            $url .= '&order=' . $order;
        } else {
            $order = 'ASC';
        }

        if ($this->request->hasQuery('limit')) {
            $limit = $this->request->getQuery('limit');
            $url .= '&limit=' . $limit;
        } else {
            $limit = $this->config->get('config_admin_limit');
        }

        $data['sort']  = $sort;
        $data['order'] = $order;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['start'] = ($page - 1) * $limit;

        if (isset($this->form_vars['descriptions']) && !isset($data['language_id'])) {
            $data['language_id'] = $this->config->get('config_language_id');
        }
        if (isset($this->post_type)) {
            $data['post_type'] = $this->post_type;
        }

        //apply filters to filters
        $data = $this->applyFilters("grid:filters", $data);

        $total   = $this->model->getAllTotal($data);
        $results = $this->model->getAll($data);
        $pkey    = $this->model->getPkey();

        $i = str_replace('%theme%', $this->config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE);
        foreach ($results as $k => $result) {
            $actions = array(
                'activate' => array(
                    'action' => 'activate',
                    'text' => $this->language->get('text_activate'),
                    'href' => '',
                    'img' => $i . 'good.png'
                ),
                'edit' => array(
                    'action' => 'edit',
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->getUrl('/update') . '&'. $pkey .'=' . $result[$pkey] . $url,
                    'img' =>  $i . 'edit.png'
                ),
                'delete' => array(
                    'action' => 'delete',
                    'text' => $this->language->get('text_delete'),
                    'href' => '',
                    'img' => $i . 'delete.png'
                )
            );

            //apply filters to $result
            $results[$k] = $this->applyFilters("grid:result", $result);

            if (isset($this->form_vars['image'])) {
                if (isset($result['image']) && file_exists(DIR_IMAGE . $results[$k]['image'])) {
                    $results[$k]['image'] = NTImage::resizeAndSave($results[$k]['image'], 40, 40);
                } else {
                    $results[$k]['image'] = NTImage::resizeAndSave('no_image.jpg', 40, 40);
                }
            }

            if (isset($this->form_vars['photo'])) {
                if (isset($result['photo']) && file_exists(DIR_IMAGE . $results[$k]['photo'])) {
                    $results[$k]['photo'] = NTImage::resizeAndSave($results[$k]['photo'], 40, 40);
                } else {
                    $results[$k]['photo'] = NTImage::resizeAndSave('no_image.jpg', 40, 40);
                }
            }

            $results[$k]['id'] = $result[$pkey];
            $results[$k]['selected'] = $this->request->hasPost('selected') && in_array($result[$pkey], $this->request->getPost('selected'));
            $results[$k]['actions']  = $actions;
        }

        $this->data['results'] = $results;
        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_title'] = $this->getUrl('/grid') . '&sort=title' . $url;
        $this->data['sort_publish'] = $this->getUrl('/grid') . '&sort=publish' . $url;
        $this->data['sort_date_publish_start'] = $this->getUrl('/grid') . '&sort=date_publish_start' . $url;
        $this->data['sort_date_publish_end'] = $this->getUrl('/grid') . '&sort=date_publish_end' . $url;
        $this->data['sort_sort_order'] = $this->getUrl('/grid') . '&sort=pa.sort_order' . $url;

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (!class_exists('Pagination')) {
            $this->load->library('pagination');
        }
        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page  = $page;
        $pagination->ajax  = 'true';
        $pagination->ajax  = 'gridWrapper';
        $pagination->limit = $limit;
        $pagination->text  = $this->language->get('text_pagination');
        $pagination->url   = $this->getUrl('/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['sort']  = $sort;
        $this->data['order'] = $order;

        //apply filters to $data
        $this->data = $this->applyFilters("grid:data", $this->data);

        //apply filters to $scripts
        $this->scripts = $this->applyFilters("grid:scripts", $this->scripts);

        $template = ($this->config->get('default_admin_view_' . $this->controller_template_basename . '_grid')) ? $this->config->get('default_admin_view_' . $this->controller_template_basename . '_grid') : $this->controller_template_route . '_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    private function getForm() {

        //do actions for this controller method 
        $hasToReturn = $this->runHook("getForm", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        $__vars     = $this->form_vars;
        $post_data  = $this->request->getPost();
        $model_info = [];

        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => $this->getUrl(),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $pkey = $this->model->getPkey();
        $this->data['cancel'] = $this->getUrl();

        if ($this->request->hasQuery($pkey) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $model_info = $this->model->getById($this->request->getQuery($pkey));
        }

        $this->data['model_info'] = $model_info;

        if (!isset($model_info[$pkey])) {
            $this->data['action'] = $this->getUrl('/insert');
        } else {
            $this->data['action'] = $this->getUrl('/update',
                ["{$pkey}" => $model_info[$pkey]],
                ["id" => $model_info[$pkey]],
            );
        }

        if (isset($this->form_vars['descriptions'])) {
            if ($this->request->hasPost('descriptions')) {
                $this->data['descriptions'] = $post_data['descriptions'];
            } elseif (isset($model_info[$pkey])) {
                $this->data['descriptions'] = $this->model->getDescriptions($model_info[$pkey]);
            } else {
                $this->data['descriptions'] = [];
            }
            if (!isset($this->modelLanguage)) $this->load->model('localisation/language');
            $this->data['languages'] = $this->modelLanguage->getAll();
            unset($__vars['descriptions']);
        }

        if (isset($this->form_vars['categories'])) {
            if (!isset($this->modelCategory)) $this->load->model('store/category');
            $this->modelCategory->setObjectType($this->form_vars['categories']['object_type']);
            $this->data['categories'] = $this->modelCategory->getAll([
                'language_id' => $this->config->get('config_language_id'),
                'object_type' => $this->form_vars['categories']['object_type'],
            ]);
            
            if ($this->request->hasPost('categories')) {
                $this->data['category'] = $post_data['categories'];
            } elseif (isset($model_info[$pkey])) {
                $this->data['category'] = $this->model->getCategories($model_info[$pkey]);
            } else {
                $this->data['category'] = [];
            }

            unset($__vars['categories']);
        }

        if (isset($this->form_vars['stores'])) {
            if (!isset($this->modelStore)) $this->load->model('store/store');
            $this->data['stores'] = $this->modelStore->getAll();
            unset($__vars['stores']);
        }

        if (isset($this->form_vars['image'])) {
            if (!empty($model_info['image']) && file_exists(DIR_IMAGE . $model_info['image'])) {
                $this->data['preview'] = NTImage::resizeAndSave($model_info['image'], 100, 100);
            } else {
                $this->data['preview'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
            }
            unset($__vars['preview']);
        }

        if (isset($this->form_vars['customer_groups'])) {
            if (!isset($this->modelCustomergroup)) $this->load->model('sale/customergroup');
            $this->data['customerGroups'] = $this->modelCustomergroup->getAll();
        }

        if (isset($model_info[$pkey])) {
            $this->data['_stores'] = $this->model->getStores($model_info[$pkey]);
        } else {
            $this->data['_stores'] = [];
        }

        foreach ($__vars as $var) {
            if (!isset($var['isProperty']) || !$var['isProperty']) continue;
            if (!isset($var['group']) || !isset($var['key'])) continue;

            if (isset($model_info[$pkey])) {
                $this->data[$var['name']] = $this->model->getProperty($model_info[$pkey], $var['group'], $var['key']);
            } else {
                $this->data[$var['name']] = isset($var['default']) ? $var['default'] : '';
            }
        }
        
        foreach ($__vars as $var) {
            if (isset($var['isProperty']) && $var['isProperty']) continue;

            if ($var['type']=='date') {
                if (isset($post_data[$var['name']])) {
                    $this->data[$var['name']] = date('d-m-Y', strtotime($post_data[$var['name']]));
                } elseif (isset($model_info[$var['name']]) && strpos($model_info[$var['name']], '0000-00-00')===false) {
                    $this->data[$var['name']] = date('d-m-Y', strtotime($model_info[$var['name']]));
                } else {
                    $this->data[$var['name']] = '';
                }
            } elseif ($var['type'] == 'integer' || $var['type'] == 'float') {
                $this->setvar($var['name'], $model_info, isset($var['default']) ? $var['default'] : 0);
            } else {
                $this->setvar($var['name'], $model_info, isset($var['default']) ? $var['default'] : '');
            }
        }

        if (file_exists(DIR_CATALOG . 'view/theme/' . $this->config->get('config_template') . '/common/home.tpl')) {
            $folderTPL = DIR_CATALOG . 'view/theme/' . $this->config->get('config_template') . '/';
        } else {
            $folderTPL = DIR_CATALOG . 'view/theme/default/';
        }

        $directories = glob($folderTPL . "*", GLOB_ONLYDIR);
        $this->data['templates'] = [];
        foreach ($directories as $key => $directory) {
            $this->data['views'][$key]['folder'] = basename($directory);
            $files = glob($directory . "/*.tpl", GLOB_NOSORT);
            foreach ($files as $k => $file) {
                $this->data['views'][$key]['files'][$k] = str_replace("\\", "/", $file);
            }
        }

        if (isset($this->form_vars['descriptions']) &&isset($this->form_vars['descriptions']['fields']['description']) && isset($this->data['languages'])) {
            foreach ($this->data['languages'] as $language) {
                $code = "var editor" . $language["language_id"] . " = CKEDITOR.replace('description" . $language["language_id"] . "', {"
                        . "filebrowserBrowseUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                        . "filebrowserImageBrowseUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                        . "filebrowserFlashBrowseUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                        . "filebrowserUploadUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                        . "filebrowserImageUploadUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                        . "filebrowserFlashUploadUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                        . "height:600"
                    . "});"
                    . "editor". $language["language_id"] .".config.allowedContent = true;";
                $cssrules = "assets/theme/". ($this->config->get('config_template') ? $this->config->get('config_template') : 'choroni') ."/css/theme.css";
                if (file_exists(DIR_ROOT . $cssrules)) {
                    $code .= "editor". $language["language_id"] .".config.contentsCss = '". HTTP_CATALOG . $cssrules ."';";
                }
                $code .= "$('#description_" . $language["language_id"] . "_title').blur(function(e){
                        $.getJSON('" . Url::createAdminUrl('common/home/slug') . "',
                        { 
                            slug : $(this).val(),
                            query : '{$pkey}=" . $this->request->getQuery($pkey) . "',
                            language_id : '{$language["language_id"]}',
                        },
                        function(data){
                            $('#description_" . $language["language_id"] . "_keyword').val(data.slug);
                        });
                    });";
                $scripts[] = array('id' => $this->controller_name .'Language' . $language["language_id"], 'method' => 'ready', 'script' => $code );
            }
        }


        $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
            "if ($('#q')) {
                $('#q').on('change',function(e){
                    var that = this;
                    var valor = $(that).val().toLowerCase();
                    if (valor.length <= 0) {
                        $('#storesWrapper li').show();
                    } else {
                        $('#storesWrapper li b').each(function(){
                            if ($(this).text().toLowerCase().indexOf( valor ) != -1) {
                                $(this).closest('li').show();
                            } else {
                                $(this).closest('li').hide();
                            }
                        });
                    }
                });
            }

            if ($('#qCustomerGroups')) {
                $('#qCustomerGroups').on('change',function(e){
                    var that = this;
                    var valor = $(that).val().toLowerCase();
                    if (valor.length <= 0) {
                        $('#customerGroupsWrapper li').show();
                    } else {
                        $('#customerGroupsWrapper li b').each(function(){
                            if ($(this).text().toLowerCase().indexOf( valor ) != -1) {
                                $(this).closest('li').show();
                            } else {
                                $(this).closest('li').hide();
                            }
                        });
                    }
                });
            }
            
            if ($('.htabs2 .htab2')) {
                $('.htabs2 .htab2').on('click',function() {
                    $('.htab2').each(function(){
                    $($(this).attr('tab')).hide();
                    $(this).removeClass('selected'); 
                    });
                    $(this).addClass('selected');
                    $($(this).attr('tab')).show(); 
                });
                $('.htabs2 .htab2:first-child').trigger('click');
            }
           
            if ($('.vtabs_page')) {
                $('.vtabs_page').hide();
                $('.vtabs_page:first-child').show();
            }");

        if (isset($this->form_vars['image'])) {
            $scripts[] = array('id' => $this->controller_name .'Functions', 'method' => 'function', 'script' =>
                "function image_upload(field, preview) {
                    $('#dialog').remove();
                    $('.box').prepend('<div id=\"dialog\" style=\"padding: 3px 0px 0px 0px;z-index:10000;\"><iframe src=\"" . Url::createAdminUrl("common/filemanager") . "&field=' + encodeURIComponent(field) + '\" style=\"padding:0; margin: 0; display: block; width: 100%; height: 100%;z-index:10000;\" frameborder=\"no\" scrolling=\"auto\"></iframe></div>');
                    
                    $('#dialog').dialog({
                        title: '" . (isset($this->data['text_image_manager']) ? $this->data['text_image_manager'] : "") . "',
                        close: function (event, ui) {
                            if ($('#' + field).attr('value')) {
                                $.ajax({
                                    url: '" . Url::createAdminUrl("common/filemanager/image") . "',
                                    type: 'POST',
                                    data: 'image=' + encodeURIComponent($('#' + field).val()),
                                    dataType: 'text',
                                    success: function(data) {
                                        $('#' + preview).replaceWith('<img src=\"' + data + '\" id=\"' + preview + '\" class=\"image\" onclick=\"image_upload(\'' + field + '\', \'' + preview + '\');\">');
                                    }
                                });
                            }
                        },	
                        bgiframe: false,
                        width: 700,
                        height: 400,
                        resizable: false,
                        modal: false
                    });}");
        }

        if (is_array($scripts) && !empty($scripts)) $this->scripts = array_merge($this->scripts, $scripts);

        /* feedback form values */
        $this->data['domain']       = HTTP_HOME;
        $this->data['account_id']   = C_CODE;
        $this->data['local_ip']     = $_SERVER['SERVER_ADDR'];
        $this->data['remote_ip']    = $_SERVER['REMOTE_ADDR'];
        $this->data['server']       = serialize($_SERVER); //TODO: encriptar todos estos datos con una llave que solo yo poseo

        //apply filters to $data
        $this->data = $this->applyFilters("getForm:data", $this->data);

        //apply filters to $scripts
        $this->scripts = $this->applyFilters("getForm:scripts", $this->scripts);

        $template = ($this->config->get('default_admin_view_' . $this->controller_template_basename . '_form')) ? $this->config->get('default_admin_view_' . $this->controller_template_basename . '_form') : $this->controller_template_route . '_form.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', $this->controller_route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        //do actions for this controller method 
        $hasToReturn = $this->runHook("validateForm", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        if (isset($this->form_vars['descriptions'])) {
            foreach ($this->request->getPost('descriptions') as $language_id => $value) {
                if ((strlen(utf8_decode($value['title'])) < 2)) {
                    $this->error['name'][$language_id] = $this->language->get('error_name');
                }
            }
        }

        if (!$this->error) {
            return true;
        } else {
            if (!isset($this->error['warning'])) {
                $this->error['warning'] = $this->language->get('error_required_data');
            }

            return false;
        }
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', $this->controller_route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        //do actions for this controller method 
        $hasToReturn = $this->runHook("validateDelete", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
