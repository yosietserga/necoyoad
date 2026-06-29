<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleBanner extends ControllerModuleModuleController
{
    protected string $moduleName = 'banner';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];
            if (isset($settings['banner_id'])) {
                $this->data['Image'] = new NTImage;
                $this->load->model('content/banner');
                $this->data['banner'] = $this->modelBanner->getById((int)$settings['banner_id']);
                $this->data['banner']['items'] = [];
                if (isset($this->data['banner']['banner_id'])) {
                    $items = $this->modelBanner->getItems($this->data['banner']['banner_id']);

                    foreach ($items as $k => $item) {
                        $items[$k]['descriptions'] = $this->modelBanner->getDescriptions($item['banner_item_id']);
                        $this->load->helper('widgets');
                        $w = new NecoWidget($this->registry, $this->Route);

                        $params = array(
                            'store_id' => STORE_ID,
                            'landing_page' => 'all',
                            'object_type' => 'banner_item',
                            'object_id' => $item['banner_item_id'],
                            'position' => 'main'
                        );

                        if (!isset($this->user)) $this->load->library('user');
                        $widgets = $w->getWidgets($params, !$this->user->getId());

                        foreach ($widgets as $v) {
                            $s = (array)unserialize($v['settings']);
                            $this->children[$v['name']] = $s['route'];
                            $this->widget[$v['name']] = $v;
                            $items[$k]['widgets'][$v['name']] = array(
                                'name' => $v['name'],
                                'offsetY' => $s['offsetY'],
                                'offsetX' => $s['offsetX']
                            );
                        }
                    }

                    $this->data['banner']['items'] = $items;
                }

                $settings['banner'] = $this->data['banner'];

                if (!empty($this->data['banner']['jquery_plugin'])) {
                    if (file_exists(DIR_JS . 'sliders/' . $this->data['banner']['jquery_plugin'] . '/slider.js')) {
                        $this->javascripts = array_merge($this->javascripts, array($this->data['banner']['jquery_plugin'] => HTTP_JS . 'sliders/' . $this->data['banner']['jquery_plugin'] . '/slider.js'));
                    }

                    if (file_exists(DIR_CSS . 'sliders/' . $this->data['banner']['jquery_plugin'] . '/slider.css')) {
                        $this->styles = array_merge($this->styles, array(array(
                            'href' => HTTP_CSS . 'sliders/' . $this->data['banner']['jquery_plugin'] . '/slider.css',
                            'media' => 'all'
                        )));
                    }

                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/banner/' . $this->data['banner']['jquery_plugin'] . '.tpl')) {
                        $this->template = $this->config->get('config_template') . '/banner/' . $this->data['banner']['jquery_plugin'] . '.tpl';
                    } elseif (file_exists(DIR_TEMPLATE . 'choroni/banner/' . $this->data['banner']['jquery_plugin'] . '.tpl')) {
                        $this->template = 'choroni/banner/' . $this->data['banner']['jquery_plugin'] . '.tpl';
                    } else {
                        $this->template = 'choroni/banner/nivo-slider.tpl';
                    }
                }
            }
            
            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
    
    public function carousel() {
        $json = [];
        $this->load->model('content/banner');
        $this->load->auto('image');
        $this->load->auto('json');

        $this->data['Image'] = new NTImage;
        $banner = $this->modelBanner->getById($this->request->getQuery('banner_id'));
        $json['results'] = $banner['items'];

        $width = isset($_GET['width']) ? $_GET['width'] : 80;
        $height = isset($_GET['height']) ? $_GET['height'] : 80;
        foreach ($json['results'] as $k => $v) {
            if (!file_exists(DIR_IMAGE . $v['image']))
                $json['results'][$k]['image'] = HTTP_IMAGE . "no_image.jpg";
            $json['results'][$k]['thumb'] = NTImage::resizeAndSave($v['image'], $width, $height);
            $json['results'][$k]['title'] = $v['title'];
            $json['results'][$k]['description'] = $v['description'];
            $json['results'][$k]['link'] = $v['link'];
        }

        if (!count($json['results']))
            $json['error'] = 1;

        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }
}
