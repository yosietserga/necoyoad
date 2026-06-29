<?php

class ControllerCommonHome extends Controller {

    public function index() {
        //tracker
        $this->tracker->track(0, 'home_page');

        if ($this->session->has('ref_email') && !$this->session->has('ref_cid')) {
            $this->data['show_register_form_invitation'] = true;
        }

        $cacheId = 'html-homepage.' .
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int) $this->config->get('config_store_id');

        $cached = $this->cache->get($cacheId);

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $this->load->library('user');
        if ($cached && !$this->user->isLogged()) {
            $this->response->setOutput($cached, $this->config->get('config_compression'));
        } else {
            $this->document->title = $this->config->get('config_title_' . $this->config->get('config_language_id'));
            $this->document->description = $this->config->get('config_meta_description_' . $this->config->get('config_language_id'));

            $this->session->set('landing_page','common/home');
            $this->loadWidgets('featuredContent');
            $this->loadWidgets('main');
            $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

            if (!$this->user->isLogged()) {
                $this->cacheId = $cacheId;
            }
            $template = ($this->config->get('default_view_home')) ? $this->config->get('default_view_home') : 'common/home.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                $this->template = $this->config->get('config_template') . '/' . $template;
            } else {
                $this->template = 'choroni/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        }
    }

    public function getimage() {
        $this->load->library('image');
        $image = $this->request->getQuery('image');
        $width = $this->request->getQuery('width');
        $height = $this->request->getQuery('height');

        /**
        TODO:do some tracking, stats and other functions
        **/

        if (empty($image) || !file_exists(DIR_IMAGE . $image)) {
            $image = 'no_image.jpg';
        }

        $ext = substr($image, strrpos($image, '.') + 1);
        if ($width && $height) {
            $file = NTImage::resizeAndSave($image, $width, $height);
        } else {
            $file = HTTP_IMAGE . $image;
        }

        if (file_exists(realpath(DIR_IMAGE . $image))) {
            header('Cache-Control: no-cache');
            header('Content-type: image/'. $ext);
            readfile(realpath(DIR_IMAGE . $image));
        } else {
            echo 'image '. $this->request->getQuery('image') .' not found!';
        }
    }
}
