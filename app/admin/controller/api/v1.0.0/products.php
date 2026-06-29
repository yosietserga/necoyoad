<?php

$this->load->auto('store/product');
$this->load->auto('store/review');
$this->load->auto('store/attribute');
$this->load->auto('image');
$this->load->auto('json');

$return = [];
$request_type = $this->request->server['REQUEST_METHOD'];

switch(strtolower($request_type)) {
    case 'get':
    default:
        $this->load->auto('pagination');

        $filters = [];
        $items = [];

        $_filters = array(
            //unique indexes
            'id'=>'',
            'product_id'=>'',

            //int indexes
            'category_id'=>'',
            'language_id'=>'',
            'manufacturer_id'=>'',
            'store_id'=>'',
            'length_class_id'=>'',
            'weight_class_id'=>'',
            'stock_status_id'=>'',
            'status'=>'',
            'search_in_description'=>'',

            //float indexes
            'price'=>'',
            'from_price'=>'',
            'to_price'=>'',
            'quantity'=>'',
            'from_quantity'=>'',
            'to_quantity'=>'',

            //text filters
            'title'=>'',
            'q'=>'',
            'type'=>'',
            'model'=>'',

            //date filters
            'publish_date_start'=>'',
            'publish_date_end'=>'',
            'date_start'=>'',
            'date_end'=>'',

            //array filters
            'properties'=>'',

            //not null
            'page'=>1,
            'sort'=>'td.title',
            'order'=>'ASC',
            'limit'=>$this->config->get('config_admin_limit'),
        );

        foreach ($_filters as $k=>$v) {
            $p = $this->request->getQuery($k);

            if ($k==='title' || $k==='q') {
                $t = $this->request->getQuery('title');
                $q = $this->request->getQuery('q');

                if ($t && $q && $t !== $q) {
                    $filters['queries'] = explode(' ',$t .' '. $q);
                } elseif ($q) {
                    $filters['queries'] = explode(' ',$q);
                } elseif ($t) {
                    $filters['queries'] = explode(' ',$t);
                }
            }

            if (!empty($p)) {
                $filters[$k] = $p;
            } else if (!empty($v)) {
                $filters[$k] = $v;
            }
        }

        $url = '';
        foreach ($filters as $k=>$v) {
            if ($this->request->hasQuery($k) && !empty($v)) $url .= "&{$k}=" . $v;
        }

        $total = $this->modelProduct->getAllTotal($filters);
        $results = $this->modelProduct->getAll($filters);

        foreach ($results as $l => $result) {
            $id = $result['pid'];
            $items[$l] = $result;
            $items[$l]['id'] = $id;

            $items[$l]['stores']            = $this->modelProduct->getStores($id);
            $items[$l]['customer_groups']   = $this->modelProduct->getProperty($id, 'customer_groups', 'customer_groups');
            $items[$l]['descriptions']      = $this->modelProduct->getDescriptions($id);
            $items[$l]['tags']              = $this->modelProduct->getTags($id);
            $items[$l]['options']           = $this->modelProduct->getOptions($id);
            $items[$l]['discounts']         = $this->modelProduct->getDiscounts($id);
            $items[$l]['specials']          = $this->modelProduct->getSpecials($id);
            $items[$l]['downloads']         = $this->modelProduct->getDownloads($id);
            $items[$l]['categories']        = $this->modelProduct->getCategories($id);
            $items[$l]['related']           = $this->modelProduct->getRelated($id);
            $items[$l]['rating']            = round($this->modelReview->getAllAvg(array(
                                                'object_id'=>$id,
                                                'object_type'=>'product'
                                            )), 0);

            /* product attributes */
            $items[$l]['attributes'] = [];
            $this->load->auto('store/attribute');
            foreach ($this->modelProduct->getAllProperties( $id, 'attribute' ) as $attribute) {
                list($name, $attribute_id, $attribute_group_id) = explode(':', $attribute['key']);
                $attrValues[$attribute_group_id][$attribute_id] = $attribute['value'];
            }

            foreach ($attrValues as $att_idx => $attr) {
                $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."product_attribute_group WHERE product_attribute_group_id = ". (int)$att_idx);
                $attribute_group = $query->row;

                $rows = $this->modelAttribute->getAll(array(
                    'product_attribute_group_id'=>$att_idx
                ));
                $items[$l]['attributes'][$att_idx] = $rows[0];
                $items[$l]['attributes'][$att_idx]['categoriesAttributes'] = array_unique($this->modelProduct->getCategoriesByAttributeGroupId($att_idx));
                foreach ($items[$l]['attributes'][$att_idx]['attributes'] as $j => $attribute) {
                    $items[$l]['attributes'][$att_idx]['attributes'][$j]['value'] = $attr[$attribute['product_attribute_id']];
                }
            }
            /* /product attributes */

            /* product images */
            $items[$l]['images'] = [];
            $images = $this->modelProduct->getImages($id);
            foreach ($images as $image) {
                if ($image['image'] && file_exists(DIR_IMAGE . $image['image'])) {
                    $items[$l]['images'][] = array(
                        'preview' => NTImage::resizeAndSave($image['image'], 100, 100),
                        'file' => HTTP_IMAGE . $image['image']
                    );
                } else {
                    $items[$l]['images'][] = array(
                        'preview' => NTImage::resizeAndSave('no_image.jpg', 100, 100),
                        'file' => HTTP_IMAGE . $image['image']
                    );
                }
            }

            if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {           
                array_unshift($items[$l]['images'], array(
                    'preview' => NTImage::resizeAndSave($result['image'], 100, 100),
                    'file' => HTTP_IMAGE . $result['image']
                ));
            } else {
                array_unshift($items[$l]['images'], array(
                    'preview' => NTImage::resizeAndSave('no_image.jpg', 100, 100),
                    'file' => HTTP_IMAGE . $result['image']
                ));
            }

            /* /product images */

        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $filters['page'];
        $pagination->limit = $filters['limit'];
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('api/v1/products') . $url . '&page={page}';

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'results'=>$items,
            'filters'=>$filters,
            'pagination'=>$pagination->render(),
            'total'=>$total
        );
    break;

    case 'post':
        $this->request->post = json_decode(file_get_contents('php://input'), true);

        $id = $this->modelProduct->add($this->prepareData('products', $this->request->post));

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'product_id'=>$id,
            'id'=>$id
        );
        break;
    case 'put':
        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."product WHERE product_id = '". (int)$this->request->getQuery('id') ."'");
        $query->row['sc'] = $this->request->getQuery('sc');
        $product = $query->row;
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        if ($product['product_id']) {
            $this->modelProduct->update($product['product_id'], $this->prepareData('products', $product));

            $return['status'] = array(
                'code'=>200,
                'message'=>'OK'
            );

            $return['error'] = array(
                'code'=>null,
                'message'=>''
            );

            $return['payload'] = array(
                'product_id'=>$product['product_id'],
                'id'=>$product['product_id']
            );
        } else {
            $this->error404();
            return;
        }
        break;
    case 'delete':
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        $id = $this->request->hasPost('id') ? $this->request->getPost('id') : $this->request->getQuery('id');
        $ids = (is_array($id)) ? $id : array($id);
        foreach ($ids as $id) {
            if (!(int)$id) continue;
            $this->modelProduct->delete($id);
        }
        break;
}