<?php

/**
 * ControllerStyleTheme
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerStyleTheme extends Controller {

    private $error = [];

    /**
     * ControllerStyleTheme::index()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see getList
     * @return void
     */
    public function index() {
        $this->document->title = $this->language->get('heading_title');
        $this->getList();
    }

    /**
     * ControllerStyleTheme::insert()
     * 
     * @see Load
     * @see Document
     * @see Request
     * @see Session
     * @see Redirect
     * @see Language
     * @see getForm
     * @return void
     */
    public function insert() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            if (empty($this->request->post['date_publish_end'])) {
                $this->request->post['date_publish_end'] = '0000-00-00 00:00:00';
            } else {
                $dpe = explode("/", $this->request->post['date_publish_end']);
                $this->request->post['date_publish_end'] = date('Y-m-d h:i:s', strtotime($dpe[2] . "-" . $dpe[1] . "-" . $dpe[0]));
            }

            $dps = explode("/", $this->request->post['date_publish_start']);
            $this->request->post['date_publish_start'] = date('Y-m-d h:i:s', strtotime($dps[2] . "-" . $dps[1] . "-" . $dps[0]));

            $theme_id = $this->modelTheme->add($this->request->post);

            if ($this->request->post['default']) {
                $this->load->model('setting/setting');
                $this->modelSetting->updateProperty('theme', 'theme_default_id', $theme_id);
            }

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('style/theme/update', array('theme_id' => $theme_id)));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('style/theme/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('style/theme'));
            }
        }

        $this->getForm();
    }

    /**
     * ControllerStyleTheme::update()
     * 
     * @see Load
     * @see Document
     * @see Request
     * @see Session
     * @see Redirect
     * @see Language
     * @see getForm
     * @return void
     */
    public function update() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            if (empty($this->request->post['date_publish_end'])) {
                $this->request->post['date_publish_end'] = '0000-00-00 00:00:00';
            } else {
                $dpe = explode("/", $this->request->post['date_publish_end']);
                $this->request->post['date_publish_end'] = date('Y-m-d h:i:s', strtotime($dpe[2] . "-" . $dpe[1] . "-" . $dpe[0]));
            }

            $dps = explode("/", $this->request->post['date_publish_start']);
            $this->request->post['date_publish_start'] = date('Y-m-d h:i:s', strtotime($dps[2] . "-" . $dps[1] . "-" . $dps[0]));

            $theme_id = $this->modelTheme->update($this->request->get['theme_id'], $this->request->post);

            $this->load->model('setting/setting');
            if ($this->request->post['default']) {
                $this->modelSetting->updateProperty('theme', 'theme_default_id', $this->request->get['theme_id']);
            } elseif ($this->config->get('theme_default_id') == $this->request->getQuery('theme_id')) {
                $this->modelSetting->updateProperty('theme', 'theme_default_id', 0);
            }

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('style/theme/update', array('theme_id' => $theme_id)));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('style/theme/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('style/theme'));
            }
        }

        $this->getForm();
    }

    /**
     * ControllerStyleTheme::index()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see getList
     * @return void
     */
    public function save() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $filename = "custom-" . $this->request->getQuery('theme_id') . "-" . $this->request->getQuery('template') . ".css";
            if (file_exists(DIR_CSS . $filename)) {
                $css = trim(file_get_contents(DIR_CSS . $filename));
            } else {
                $css = "";
            }

            $data = [];

            foreach ($this->request->post as $selector => $properties) {
                $elements = array('body', 'html');
                if (!in_array($selector, $elements)) {
                    $selector = '#' . $selector;
                }
                $selector = str_replace("%20", " ", $selector);

                $css = trim(preg_replace("%(/\*\*$selector\*\*/)(.*?)(/\*\*$selector\*\*/)%s", "", $css));

                $css .= "\n/**$selector**/\n";
                $css .= $selector . " {\n";

                if (!empty($properties['background']['image'])) {
                    $css .= "\tbackground-image: url(" . $properties['background']['image'] . ");\n";
                    $data[$selector]['background-image'] = "url(" . $properties['background']['image'] . ")";
                }
                if (!empty($properties['background']['color'])) {
                    $css .= "\tbackground-color:" . $properties['background']['color'] . ";\n";
                    $data[$selector]['background-color'] = $properties['background']['color'];
                }
                if (!empty($properties['background']['repeat'])) {
                    $css .= "\tbackground-repeat:" . $properties['background']['repeat'] . ";\n";
                    $data[$selector]['background-repeat'] = $properties['background']['image'];
                }
                if (!empty($properties['background']['attachment'])) {
                    $css .= "\tbackground-attachment:" . $properties['background']['attachment'] . ";\n";
                    $data[$selector]['background-attachment'] = $properties['background']['attachment'];
                }
                if (!empty($properties['background']['positionX']) && !empty($properties['background']['positionY'])) {
                    $css .= "\tbackground-position:" . $properties['background']['positionX'] . " " . $properties['background']['positionY'] . ";\n";
                    $data[$selector]['background-position'] = $properties['background']['positionX'] . " " . $properties['background']['positionY'];
                }

                if (!empty($properties['dimensions']['top'])) {
                    $css .= "\ttop:" . $properties['dimensions']['top'] . ";\n";
                    $data[$selector]['top'] = $properties['dimensions']['top'];
                }
                if (!empty($properties['dimensions']['left'])) {
                    $css .= "\tleft:" . $properties['dimensions']['left'] . ";\n";
                    $data[$selector]['left'] = $properties['dimensions']['left'];
                }
                if (!empty($properties['dimensions']['width'])) {
                    $css .= "\twidth:" . $properties['dimensions']['width'] . ";\n";
                    $data[$selector]['width'] = $properties['dimensions']['width'];
                }
                if (!empty($properties['dimensions']['height'])) {
                    $css .= "\theight:" . $properties['dimensions']['height'] . ";\n";
                    $data[$selector]['height'] = $properties['dimensions']['height'];
                }
                if (!empty($properties['dimensions']['float'])) {
                    $css .= "\tfloat:" . $properties['dimensions']['float'] . ";\n";
                    $data[$selector]['float'] = $properties['dimensions']['float'];
                }
                if (!empty($properties['dimensions']['position'])) {
                    $css .= "\tposition:" . $properties['dimensions']['position'] . ";\n";
                    $data[$selector]['position'] = $properties['dimensions']['position'];
                }
                if (!empty($properties['dimensions']['overflow'])) {
                    $css .= "\toverflow:" . $properties['dimensions']['overflow'] . ";\n";
                    $data[$selector]['overflow'] = $properties['dimensions']['overflow'];
                }

                if (!empty($properties['font']['color'])) {
                    $css .= "\tcolor:" . $properties['font']['color'] . ";\n";
                    $data[$selector]['color'] = $properties['font']['color'];
                }
                if (!empty($properties['font']['family'])) {
                    $css .= "\tfont-family:" . $properties['font']['family'] . ";\n";
                    $data[$selector]['font-family'] = $properties['font']['family'];
                }
                if (!empty($properties['font']['weight'])) {
                    $css .= "\tfont-weight:" . $properties['font']['weight'] . ";\n";
                    $data[$selector]['font-weight'] = $properties['font']['weight'];
                }
                if (!empty($properties['font']['style'])) {
                    $css .= "\tfont-style:" . $properties['font']['style'] . ";\n";
                    $data[$selector]['font-style'] = $properties['font']['style'];
                }
                if (!empty($properties['font']['size'])) {
                    $css .= "\tfont-size:" . $properties['font']['size'] . ";\n";
                    $data[$selector]['font-size'] = $properties['font']['size'];
                }
                if (!empty($properties['font']['align'])) {
                    $css .= "\ttext-align:" . $properties['font']['align'] . ";\n";
                    $data[$selector]['text-align'] = $properties['font']['align'];
                }
                if (!empty($properties['font']['transform'])) {
                    $css .= "\ttext-transform:" . $properties['font']['transform'] . ";\n";
                    $data[$selector]['text-transform'] = $properties['font']['transform'];
                }
                if (!empty($properties['font']['decoration'])) {
                    $css .= "\ttext-decoration:" . $properties['font']['decoration'] . ";\n";
                    $data[$selector]['text-decoration'] = $properties['font']['decoration'];
                }
                if (!empty($properties['font']['letterspacing'])) {
                    $css .= "\tletter-spacing:" . $properties['font']['letterspacing'] . ";\n";
                    $data[$selector]['letter-spacing'] = $properties['font']['letterspacing'];
                }
                if (!empty($properties['font']['wordspacing'])) {
                    $css .= "\tword-spacing:" . $properties['font']['wordspacing'] . ";\n";
                    $data[$selector]['word-spacing'] = $properties['font']['wordspacing'];
                }
                if (!empty($properties['font']['lineheight'])) {
                    $css .= "\tline-height:" . $properties['font']['lineheight'] . ";\n";
                    $data[$selector]['line-height'] = $properties['font']['lineheight'];
                }

                $boxShadow = "";
                if (isset($properties['boxshadow']['inset'])) {
                    $boxShadow .= 'inset ';
                }
                if (!empty($properties['boxshadow']['x'])) {
                    $boxShadow .= $properties['boxshadow']['x'] . ' ';
                } else {
                    $boxShadow .= '0px ';
                }
                if (!empty($properties['boxshadow']['y'])) {
                    $boxShadow .= $properties['boxshadow']['y'] . ' ';
                } else {
                    $boxShadow .= '0px ';
                }
                if (!empty($properties['boxshadow']['blur'])) {
                    $boxShadow .= $properties['boxshadow']['blur'] . ' ';
                } else {
                    $boxShadow .= '0px ';
                }
                if (!empty($properties['boxshadow']['spread'])) {
                    $boxShadow .= $properties['boxshadow']['spread'] . ' ';
                } else {
                    $boxShadow .= '0px ';
                }
                if (!empty($properties['boxshadow']['color'])) {
                    $boxShadow .= $properties['boxshadow']['color'];
                }
                $data[$selector]['box-shadow'] = $boxShadow;
                $css .= "\tbox-shadow:" . $boxShadow . ";\n";

                if (empty($properties['border']['topcolor']) && empty($properties['border']['rightcolor']) && empty($properties['border']['bottomcolor']) && empty($properties['border']['leftcolor'])) {
                    if (!empty($properties['border']['color']))
                        $css .= "\tborder-color:" . $properties['border']['color'] . ";\n";
                    if (!empty($properties['border']['style']))
                        $css .= "\tborder-style:" . $properties['border']['style'] . ";\n";
                    if (!empty($properties['border']['width']))
                        $css .= "\tborder-width:" . $properties['border']['width'] . ";\n";
                    if (!empty($properties['border']['color']))
                        $data[$selector]['border-color'] = $properties['border']['color'];
                    if (!empty($properties['border']['style']))
                        $data[$selector]['border-style'] = $properties['border']['style'];
                    if (!empty($properties['border']['width']))
                        $data[$selector]['border-width'] = $properties['border']['width'];
                } else {
                    if (!empty($properties['border']['topcolor']))
                        $css .= "\tborder-top-color:" . $properties['border']['topcolor'] . ";\n";
                    if (!empty($properties['border']['topstyle']))
                        $css .= "\tborder-top-style:" . $properties['border']['topstyle'] . ";\n";
                    if (!empty($properties['border']['topwidth']))
                        $css .= "\tborder-top-width:" . $properties['border']['topwidth'] . ";\n";
                    if (!empty($properties['border']['topcolor']))
                        $data[$selector]['border-top-color'] = $properties['border']['topcolor'];
                    if (!empty($properties['border']['topstyle']))
                        $data[$selector]['border-top-style'] = $properties['border']['topstyle'];
                    if (!empty($properties['border']['topwidth']))
                        $data[$selector]['border-top-width'] = $properties['border']['topwidth'];

                    if (!empty($properties['border']['rightcolor']))
                        $css .= "\tborder-right-color:" . $properties['border']['rightcolor'] . ";\n";
                    if (!empty($properties['border']['rightstyle']))
                        $css .= "\tborder-right-style:" . $properties['border']['rightstyle'] . ";\n";
                    if (!empty($properties['border']['rightwidth']))
                        $css .= "\tborder-right-width:" . $properties['border']['rightwidth'] . ";\n";
                    if (!empty($properties['border']['rightcolor']))
                        $data[$selector]['border-right-color'] = $properties['border']['rightcolor'];
                    if (!empty($properties['border']['rightstyle']))
                        $data[$selector]['border-right-style'] = $properties['border']['rightstyle'];
                    if (!empty($properties['border']['rightwidth']))
                        $data[$selector]['border-right-width'] = $properties['border']['rightwidth'];

                    if (!empty($properties['border']['bottomcolor']))
                        $css .= "\tborder-bottom-color:" . $properties['border']['bottomcolor'] . ";\n";
                    if (!empty($properties['border']['bottomstyle']))
                        $css .= "\tborder-bottom-style:" . $properties['border']['bottomstyle'] . ";\n";
                    if (!empty($properties['border']['bottomwidth']))
                        $css .= "\tborder-bottom-width:" . $properties['border']['bottomwidth'] . ";\n";
                    if (!empty($properties['border']['bottomcolor']))
                        $data[$selector]['border-bottom-color'] = $properties['border']['bottomcolor'];
                    if (!empty($properties['border']['bottomstyle']))
                        $data[$selector]['border-bottom-style'] = $properties['border']['bottomstyle'];
                    if (!empty($properties['border']['bottomwidth']))
                        $data[$selector]['border-bottom-width'] = $properties['border']['bottomwidth'];

                    if (!empty($properties['border']['leftcolor']))
                        $css .= "\tborder-left-color:" . $properties['border']['leftcolor'] . ";\n";
                    if (!empty($properties['border']['leftstyle']))
                        $css .= "\tborder-left-style:" . $properties['border']['leftstyle'] . ";\n";
                    if (!empty($properties['border']['leftwidth']))
                        $css .= "\tborder-left-width:" . $properties['border']['leftwidth'] . ";\n";
                    if (!empty($properties['border']['leftcolor']))
                        $data[$selector]['border-left-color'] = $properties['border']['leftcolor'];
                    if (!empty($properties['border']['leftstyle']))
                        $data[$selector]['border-left-style'] = $properties['border']['leftstyle'];
                    if (!empty($properties['border']['leftwidth']))
                        $data[$selector]['border-left-width'] = $properties['border']['leftwidth'];
                }

                if (empty($properties['borderradius']['topleft']) && empty($properties['borderradius']['topright']) && empty($properties['borderradius']['bottomleft']) && empty($properties['borderradius']['bottomright'])) {
                    if (!empty($properties['borderradius']['all'])) {
                        $css .= "\t-moz-border-radius:" . $properties['borderradius']['all'] . ";\n";
                        $css .= "\t-webkit-border-radius:" . $properties['borderradius']['all'] . ";\n";
                        $css .= "\tborder-radius:" . $properties['borderradius']['all'] . ";\n";
                        $data[$selector]['border-radius'] = $properties['borderradius']['all'];
                    }
                } else {
                    $css .= "\t-moz-border-radius:" . $properties['borderradius']['topleft'] . " " .
                            $properties['borderradius']['topright'] . " " .
                            $properties['borderradius']['bottomright'] . " " .
                            $properties['borderradius']['bottomleft'] . ";\n";
                    $css .= "\t-webkit-border-radius:" . $properties['borderradius']['topleft'] . " " .
                            $properties['borderradius']['topright'] . " " .
                            $properties['borderradius']['bottomright'] . " " .
                            $properties['borderradius']['bottomleft'] . ";\n";
                    $css .= "\tborder-radius:" . $properties['borderradius']['topleft'] . " " .
                            $properties['borderradius']['topright'] . " " .
                            $properties['borderradius']['bottomright'] . " " .
                            $properties['borderradius']['bottomleft'] . ";\n";
                    $data[$selector]['border-radius'] = $properties['borderradius']['topleft'] . " " .
                            $properties['borderradius']['topright'] . " " .
                            $properties['borderradius']['bottomright'] . " " .
                            $properties['borderradius']['bottomleft'];
                }

                if (empty($properties['margin']['top']) && empty($properties['margin']['right']) && empty($properties['margin']['bottom']) && empty($properties['margin']['left'])) {
                    if (!empty($properties['margin']['all'])) {
                        $css .= "\tmargin:" . $properties['margin']['all'] . ";\n";
                        $data[$selector]['border-radius'] = $properties['borderradius']['all'];
                    }
                } else {
                    $css .= "\tmargin:" . $properties['margin']['top'] . " " .
                            $properties['margin']['right'] . " " .
                            $properties['margin']['bottom'] . " " .
                            $properties['margin']['left'] . ";\n";
                    $data[$selector]['margin'] = $properties['margin']['top'] . " " .
                            $properties['margin']['right'] . " " .
                            $properties['margin']['bottom'] . " " .
                            $properties['margin']['left'];
                }

                if (empty($properties['padding']['top']) && empty($properties['padding']['right']) && empty($properties['padding']['bottom']) && empty($properties['padding']['left'])) {
                    if (!empty($properties['padding']['all'])) {
                        $css .= "\tpadding:" . $properties['padding']['all'] . ";\n";
                        $data[$selector]['border-radius'] = $properties['borderradius']['all'];
                    }
                } else {
                    $css .= "\tpadding:" . $properties['padding']['top'] . " " .
                            $properties['padding']['right'] . " " .
                            $properties['padding']['bottom'] . " " .
                            $properties['padding']['left'] . ";\n";
                    $data[$selector]['padding'] = $properties['padding']['top'] . " " .
                            $properties['padding']['right'] . " " .
                            $properties['padding']['bottom'] . " " .
                            $properties['padding']['left'];
                }

                $css .= "}\n";
                $css .= "/**$selector**/\n";
            }

            $css = str_replace("\n\n", "", $css);

            file_put_contents(DIR_CSS . $filename, $css);
            $this->modelTheme->saveStyle($this->request->getQuery('theme_id'), $data);
        }
    }

    /**
     * ControllerMarketingNewsletter::copy()
     * duplicar un objeto
     * @return boolean
     */
    public function copy() {
        $this->load->auto('style/theme');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelTheme->copy($id);
            }
        } else {
            $this->modelTheme->copy($_GET['id']);
        }
        echo 1;
    }

    /**
     * ControllerStoreCategory::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        $this->load->auto('style/theme');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                unlink(DIR_CSS . "custom-" . $id . "-" . $this->request->getQuery('template') . ".css");
                $this->modelTheme->delete($id);
            }
        } else {
            unlink(DIR_CSS . "custom-" . $this->request->getQuery('id') . "-" . $this->request->getQuery('template') . ".css");
            $this->modelTheme->delete($_GET['id']);
        }
    }

    /**
     * ControllerStyleTheme::getById()
     * 
     * @see Load
     * @see Document
     * @see Request
     * @see Session
     * @see Response
     * @see Pagination
     * @see Language
     * @return void
     */
    private function getList() {
        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('style/theme') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('style/theme/insert') . $url;
        $this->data['delete'] = Url::createAdminUrl('style/theme/delete') . $url;

        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');
            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        // SCRIPTS
        $scripts[] = array('id' => 'themeList', 'method' => 'function', 'script' =>
            "function activate(e) {    
                $.ajax({
                   'type':'get',
                   'dataType':'json',
                   'url':'" . Url::createAdminUrl("style/theme/activate") . "&id=' + e,
                   'success': function(data) {
                        if (data > 0) {
                            $(\"#img_\" + e).attr('src','image/good.png');
                        } else {
                            $(\"#img_\" + e).attr('src','image/minus.png');
                        }
                   }
                });
             }
            function copy(e) {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.getJSON('" . Url::createAdminUrl("style/theme/copy") . "&id=' + e, function(data) {
                    $('#gridWrapper').load('" . Url::createAdminUrl("style/theme/grid") . "',function(response){
                        $('#gridPreloader').hide();
                        $('#gridWrapper').show();
                    });
                });
            }
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                    $.getJSON('" . Url::createAdminUrl("style/theme/delete") . "',{
                        id:e,
                        template:$('#tr_' + e).data('template')
                    });
                }
                return false;
             }
            function editAll() {
                return false;
            } 
            function addToList() {
                return false;
            } 
            function copyAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . Url::createAdminUrl("style/theme/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("style/theme/grid") . "',function(){
                        $('#gridWrapper').show();
                        $('#gridPreloader').hide();
                    });
                });
                return false;
            } 
            function deleteAll() {
                if (confirm('\\xbfDesea eliminar todos los objetos seleccionados?')) {
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                    $.post('" . Url::createAdminUrl("style/theme/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("style/theme/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("style/theme/grid") . "',function(e){
                $('#gridPreloader').hide();
                $('#list tbody').sortable({
                    opacity: 0.6, 
                    cursor: 'move',
                    handle: '.move',
                    update: function() {
                        $.ajax({
                            'type':'post',
                            'dateType':'json',
                            'url':'" . Url::createAdminUrl("style/theme/sortable") . "',
                            'data': $(this).sortable('serialize'),
                            'success': function(data) {
                                if (data > 0) {
                                    var msj = '<div class=\"messagesuccess\">Se han ordenado los objetos correctamente</div>';
                                } else {
                                    var msj = '<div class=\"messagewarning\">Hubo un error al intentar ordenar los objetos, por favor intente m&aacute;s tarde</div>';
                                }
                                $('#msg').fadeIn().append(msj).delay(3600).fadeOut();
                            }
                        });
                    }
                }).disableSelection();
                $('.move').css('cursor','move');
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("style/theme/grid") . "',
                beforeSend:function(){
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                },
                success:function(data){
                    $('#gridPreloader').hide();
                    $('#gridWrapper').html(data).show();
                }
            });
            $('#formFilter').on('keyup', function(e){
                var code = e.keyCode || e.which;
                if (code == 13){
                    $('#formFilter').ntForm('submit');
                }
            });");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_style_theme_list')) ? $this->config->get('default_admin_view_style_theme_list') : 'style/theme_list.tpl';
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

    public function grid() {
        $this->load->auto('image');

        $filter_name = isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : null;
        $filter_template = isset($this->request->get['filter_template']) ? $this->request->get['filter_template'] : null;
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null;
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'name';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $limit = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_admin_limit');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_template'])) {
            $url .= '&filter_template=' . $this->request->get['filter_template'];
        }
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (!empty($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $this->data['themes'] = [];

        $data = array(
            'filter_name' => $filter_name,
            'filter_template' => $filter_template,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $theme_total = $this->modelTheme->getAllTotal();

        $results = $this->modelTheme->getAll($data);

        $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
        foreach ($results as $result) {
            $action = array(
                'style' => array(
                    'action' => 'style',
                    'text' => $this->language->get('text_style'),
                    'target' => '_blank',
                    //'href' => HTTP_CATALOG . "/index.php?theme_editor=1&theme_id=" . $result['theme_id'] . "&template=" . $result['template'],
                    'href' => Url::createAdminUrl('style/theme/editor') . '&theme_id=' . $result['theme_id']  . "&template=" . $result['template'] . $url,
                    'img' => $i.'palette.png'
                ),
                'edit' => array(
                    'action' => 'edit',
                    'text' => $this->language->get('text_edit'),
                    'href' => Url::createAdminUrl('style/theme/update') . '&theme_id=' . $result['theme_id'] . $url,
                    'img' =>  $i.'edit.png'
                ),
                'delete' => array(
                    'action' => 'delete',
                    'text' => $this->language->get('text_delete'),
                    'href' => '',
                    'img' => $i .'delete.png'
                )
            );

            $this->data['themes'][] = array(
                'theme_id' => $result['theme_id'],
                'name' => $result['name'],
                'template' => $result['template'],
                'date_publish_start' => date('d-m-y h:i:s', strtotime($result['date_publish_start'])),
                'default' => $result['default'],
                'sort_order' => $result['sort_order'],
                'template_id' => $result['template_id'],
                'selected' => isset($this->request->post['selected']) && in_array($result['theme_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_name'] = Url::createAdminUrl('style/theme/grid') . '&sort=name' . $url;
        $this->data['sort_sort_order'] = Url::createAdminUrl('style/theme/grid') . '&sort=sort_order' . $url;

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->ajax = true;
        $pagination->ajaxTarget = "gridWrapper";
        $pagination->total = $theme_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('style/theme/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $this->data['text_no_results'] = $this->language->get('text_no_results');
        $this->data['column_template'] = $this->language->get('column_template');
        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_date_start'] = $this->language->get('column_date_start');
        $this->data['column_sort_order'] = $this->language->get('column_sort_order');
        $this->data['column_action'] = $this->language->get('column_action');

        $template = ($this->config->get('default_admin_view_style_theme_grid')) ? $this->config->get('default_admin_view_style_theme_grid') : 'style/theme_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }


        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * ControllerStyleTheme::getForm()
     * 
     * @see Load
     * @see Document
     * @see Request
     * @see Session
     * @see Response
     * @see Pagination
     * @see Language
     * @return void
     */
    private function getForm() {
        //TODO: condicionar el gestor de archivos para que solo permita seleccionar un (1) archivo de imagen
        //TODO: crear funciones para seleccionar varias imagenes a la vez y asociarlas con objeto, asi no se tiene que seleccionar de una en una
        //TODO: detectar los slugs que coincidan y agregarle un contador al final en caso de que hayan palabras claves ya creadas
        $this->data['error_warning'] = $this->error['warning'] ?? '';
        $this->data['error_name'] = $this->error['name'] ?? '';

        $url = '';
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('style/theme') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);
        $this->data['templates'] = [];
        foreach ($directories as $directory) {
            $this->data['templates'][] = basename($directory);
        }

        if (!isset($this->request->get['theme_id'])) {
            $this->data['action'] = Url::createAdminUrl('style/theme/insert') . $url;
        } else {
            $this->data['action'] = Url::createAdminUrl('style/theme/update') . '&theme_id=' . $this->request->getQuery('theme_id') . $url;
        }

        $this->data['cancel'] = Url::createAdminUrl('style/theme') . $url;

        $theme_info = [];
        if (isset($this->request->get['theme_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $theme_info = $this->modelTheme->getById($this->request->get['theme_id']);
            $this->data['isSaved'] = true;
        }

        $this->setvar('name', $theme_info, '');
        $this->setvar('template', $theme_info, '');
        $this->setvar('default', $theme_info, '');

        if (isset($this->request->post['date_publish_start'])) {
            $this->data['date_publish_start'] = date('d-m-Y', strtotime($this->request->post['date_publish_start']));
        } elseif ($theme_info) {
            $this->data['date_publish_start'] = date('d-m-Y', strtotime($theme_info['date_publish_start']));
        } else {
            $this->data['date_publish_start'] = date('d-m-Y');
        }

        if (isset($this->request->post['date_publish_end'])) {
            $this->data['date_publish_end'] = date('d-m-Y', strtotime($this->request->post['date_publish_end']));
        } elseif ($theme_info) {
            $this->data['date_publish_end'] = date('d-m-Y', strtotime($theme_info['date_publish_end']));
        } else {
            $this->data['date_publish_end'] = '';
        }

        $template = ($this->config->get('default_admin_view_style_theme_form')) ? $this->config->get('default_admin_view_style_theme_form') : 'style/theme_form.tpl';
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

    /**
     * ControllerStyleTheme::validateForm()
     * 
     * @see Request
     * @see Language
     * @return bool
     */
    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'style/theme')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        //TODO: colocar validaciones propias

        if (empty($this->request->post['name'])) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerStyleTheme::validateForm()
     * 
     * @see Request
     * @see Language
     * @return bool
     */
    private function validateSave() {
        if (!$this->user->hasPermission('modify', 'style/theme')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->hasQuery('theme_id')) {
            $this->error['theme_id'] = true;
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerStyleTheme::validateDelete()
     * 
     * @see Request
     * @see Language
     * @return bool
     */
    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'style/theme')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerStoreCategory::activate()
     * activar o desactivar un objeto accedido por ajax
     * @return boolean
     * */
    public function activate() {
        if (!isset($_GET['id']))
            return false;
        $this->load->auto('style/theme');
        $status = $this->modelTheme->getTheme($_GET['id']);
        if ($status) {
            if ($status['status'] == 0) {
                $this->modelTheme->activate($_GET['id']);
                echo 1;
            } else {
                $this->modelTheme->deactivate($_GET['id']);
                echo -1;
            }
        } else {
            echo 0;
        }
    }

    /**
     * ControllerStoreCategory::sortable()
     * ordenar el listado actualizando la posici�n de cada objeto
     * @return boolean
     * */
    public function sortable() {
        if (!isset($_POST['tr']))
            return false;
        $this->load->auto('style/theme');
        $result = $this->modelTheme->sortTable($_POST['tr']);
        if ($result) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function products() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/product");
        $this->load->auto("image");
        $this->load->auto("url");
        if ($this->request->hasQuery('theme_id')) {
            $rows = $this->modelProduct->getAllByThemeId($this->request->getQuery('theme_id'));
            $products_by_theme = [];
            foreach ($rows as $row) {
                $products_by_theme[] = $row['product_id'];
            }
        }
        $cache = $this->cache->get("products.for.theme.form");
        if ($cache) {
            $products = unserialize($cache);
        } else {
            $model = $this->modelProduct->getAll();
            $products = $model->obj;
            $this->cache->set("products.for.theme.form", serialize($products));
        }

        $this->data['Image'] = new NTImage();
        $this->data['Url'] = new Url;

        $output = [];

        foreach ($products as $product) {
            if (!empty($products_by_theme) && in_array($product->product_id, $products_by_theme)) {
                $output[] = array(
                    'product_id' => $product->product_id,
                    'pimage' => NTImage::resizeAndSave($product->pimage, 50, 50),
                    'pname' => $product->pname,
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'product_id' => $product->product_id,
                    'pimage' => NTImage::resizeAndSave($product->pimage, 50, 50),
                    'pname' => $product->pname,
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function editor() {
        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('style/theme') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->load->auto('store/store');
        $this->load->auto('style/widget');
        $this->load->auto('setting/extension');

        $extensions = $this->modelExtension->getInstalled('module');
        $this->data['extensions'] = [];
        $modules = glob(DIR_APPLICATION . "controller/module/*");
        if ($modules) {
            foreach ($modules as $module) {
                if (!file_exists($module . '/widget.php'))
                    continue;
                $extension = basename($module, '/widget.php');
                $m = basename($module);
                $this->load->language('module/' . $m);

                if (in_array($extension, $extensions)) {
                    $this->data['modules'][] = array(
                        'widget' => $extension,
                        'name' => $this->language->get('heading_title'),
                        'description' => $this->language->get('description')
                    );
                }
            }
        }

        $this->data['new_theme']= Url::createAdminUrl('style/theme/insert');
        $this->data['save_theme']= Url::createAdminUrl('style/theme/save',array('theme_id'=>$this->request->getQuery('theme_id'),'template'=>$this->request->getQuery('template')));
        $this->data['download_theme']= Url::createAdminUrl('style/theme/download',array('theme_id'=>$this->request->getQuery('theme_id'),'template'=>$this->request->getQuery('template')));

        $this->data['routes'] = $this->modelWidget->getRoutes();
        $this->data['stores'] = $this->modelStore->getAll();
        $this->data['store_id'] = ($this->request->hasQuery('store_id')) ? $this->request->getQuery('store_id') : 0;
        $this->data['landing_page'] = ($this->request->hasQuery('landing_page')) ? $this->request->getQuery('landing_page') : 'all';
        if ((int) $this->data['store_id'] !== 0)
            $this->data['store_exists'] = $this->modelStore->getById($this->data['store_id']);
        if ((int) $this->data['store_id'] === 0)
            $this->data['store_exists'] = true;

        //$template = ($this->config->get('default_admin_view_style_theme_list')) ? $this->config->get('default_admin_view_style_theme_list') : 'style/theme_editor.tpl';
        $template = 'style/theme_editor.tpl';
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

}
