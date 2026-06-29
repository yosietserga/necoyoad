<?php

class ControllerAccountReview extends Controller {

    private $error = [];

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/review"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/review');
        $this->load->model('store/review');

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );


        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/review"),
            'text' => $this->language->get('text_review'),
            'separator' => $this->language->get('text_separator')
        );


        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $page = ($this->request->get['page']) ? $this->request->get['page'] : 1;
        $data['sort'] = $sort = ($this->request->get['sort']) ? $this->request->get['sort'] : 'c.date_added';
        $data['order'] = $order = ($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $data['limit'] = $limit = ($this->request->get['limit']) ? $this->request->get['limit'] : 15;
        $data['keyword'] = ($this->request->get['keyword']) ? $this->request->get['keyword'] : null;
        $data['letter'] = ($this->request->get['letter']) ? $this->request->get['letter'] : null;
        $data['status'] = ($this->request->get['status']) ? $this->request->get['status'] : null;
        $data['start'] = ($page - 1) * $limit;
        $data['customer_id'] = $this->customer->getId();

        $url = '';
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $this->data['letters'] = range('A', 'Z');

        $review_total = $this->modelReview->getAllTotal($data);

        if ($review_total) {
            $reviews = $this->modelReview->getAll($data);
            foreach ($reviews as $key => $value) {

                $this->data['reviews'][$key] = array(
                    'review_id' => $value['review_id'],
                    'object_id' => $value['object_id'],
                    'object_type' => $value['object_type'],
                    'rating' => $value['rating'],
                    'status' => $value['status'] ? $this->language->get('text_approve') : $this->language->get('text_no_approve'),
                    'date_added' => date('d/m/Y h:i A', strtotime($value['dateAdded'])),
                    'text' => substr($value['text'], 0, 130) . "..."
                );

                if (in_array($value['object_type'], array('product','category','manufacturer'))) {
                    $this->data['reviews'][$key]['href'] = $Url::createUrl("store/". $value['object_type'], array($value['object_type'] .'_id' => $value['object_id']));
                }

                if (in_array($value['object_type'], array('post','post_category','page'))) {
                    $this->data['reviews'][$key]['href'] = $Url::createUrl("content/". $value['object_type'], array($value['object_type'] .'_id' => $value['object_id']));
                }
            }

            $this->load->library('pagination');
            $pagination = new Pagination(true);
            $pagination->total = $review_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = Url::createUrl('account/review') . $url . '&page={page}';
            $this->data['pagination'] = $pagination->render();
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        

        $this->session->set('landing_page','account/review');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');


        $template = ($this->config->get('default_view_account_review')) ? $this->config->get('default_view_account_review') : 'account/review.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function read() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/review"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/review');
        $this->load->model('store/review');

        $review_id = $this->request->get['review_id'];
        $review = $this->modelReview->getById($review_id);

        if ($review) {
            $this->document->breadcrumbs = [];

            $this->document->breadcrumbs[] = array(
                'href' => Url::createUrl("common/home"),
                'text' => $this->language->get('text_home'),
                'separator' => false
            );

            $this->document->breadcrumbs[] = array(
                'href' => Url::createUrl("account/review"),
                'text' => $this->language->get('text_review'),
                'separator' => $this->language->get('text_separator')
            );

            $this->document->breadcrumbs[] = array(
                'href' => Url::createUrl("account/review/read") . '&review_id=' . $this->request->get['review_id'],
                'text' => $this->language->get('text_review') . $this->request->get['review_id'],
                'separator' => $this->language->get('text_separator')
            );

            $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

            $this->data['review'] = [];
            if ((int) $review_id) {
                $this->data['heading_title'] = "Comentario #" . $review_id;
                $this->data['review'] = $review;
                $this->data['replies'] = $this->modelReview->getReplies($review_id);
                $this->load->auto('image');
                $image = !empty($this->data['review']['image']) ? $this->data['review']['image'] : 'no_image.jpg';
                $this->data['review']['thumb'] = NTImage::resizeAndSave($image, $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'));
                $this->data['review']['description'] = html_entity_decode($this->data['review']['description'], ENT_QUOTES, 'UTF-8');
            }
        }

        $this->session->set('landing_page','account/review/read');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');


        $template = ($this->config->get('default_view_account_review_read')) ? $this->config->get('default_view_account_review_read') : 'account/review_read.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function delete() {
        $this->load->auto('account/review');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelReview->delete($id, $this->customer->getId());
            }
        } else {
            $this->modelReview->delete($_GET['id'], $this->customer->getId());
        }
    }
}
