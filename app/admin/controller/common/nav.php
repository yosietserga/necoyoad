<?php

class ControllerCommonNav extends Controller {

    /**
     * ControllerCommonHeader::index()
     * 
     * @return
     */
    protected function index() {
        $this->load->auto('user/user');
        $image = $this->modelUser->getProperty($this->user->getId(), 'user', 'image');

        if (!empty($image) && file_exists(DIR_IMAGE . $image)) {
            $this->data['avatar'] = NTImage::resizeAndSave($image, 100, 100);
        } else {
            $this->data['avatar'] = NTImage::resizeAndSave('data/profiles/avatar.png', 100, 100);
        }

        $this->load->auto('setting/extension');

        $this->data['modelExtension'] = $this->modelExtension;
        $this->data['logged'] = $this->user->validSession();
        
        $this->id = 'navigation';

        $template = ($this->config->get('default_admin_view_nav')) ? $this->config->get('default_admin_view_nav') : 'common/nav.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->render();
    }

}
