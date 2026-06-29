<?php
class CronPromoter {
    
    /**
     * @var $registry
     * */
    protected $registry;
    
    /**
     * @var $load
     * */
    protected $load;
    
    /**
     * @var $config
     * */
    protected $config;
    
    /**
     * @var $db
     * */
    protected $db;
    
    /**
     * @var $session
     * */
    protected $session;
    
    /**
     * @var $request
     * */
    protected $request;
    
    /**
     * @var $customer
     * */
    protected $customer;
    
    public function __construct($registry) {
        $this->registry   = $registry;
        $this->load   = $registry->get('load');
        $this->config = $registry->get('config');
        $this->db     = $registry->get('db');
        $this->session= $registry->get('session');
        $this->request= $registry->get('request');
        $this->customer= $registry->get('customer');
        
        $this->run();
    }
    
	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function __isset($key) {
		return $this->registry->has($key);
	}

    private function getProducts($email) {
        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."stat s
        LEFT JOIN ". DB_PREFIX ."product p ON (s.object_id=p.product_id)
        LEFT JOIN ". DB_PREFIX ."product_description pd ON (p.product_id=pd.product_id)
        WHERE DAY(s.date_added) = '". $this->db->escape(date('d')) ."'
            AND MONTH(s.date_added) = '". $this->db->escape(date('m')) ."'
            AND YEAR(s.date_added) = '". $this->db->escape(date('Y')) ."'
            AND s.object_type = 'product'
            AND s.status = 1
            AND s.email = '". $this->db->escape($email) ."'
            AND pd.language_id = 1
        GROUP BY s.object_id
        LIMIT 40");

        $products = [];

        $this->load->library('tax');
        $this->load->library('currency');
        $this->load->library('image');
        $this->load->library('url');
        $this->tax = new Tax($this->registry);
        $this->currency = new Currency($this->registry);

        foreach ($query->rows as $product) {
            $image = !empty($product['image']) ? $product['image'] : 'no_image.jpg';
            $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')),'','',false);
            $products[] = array(
                'product_id'=>$product['product_id'],
                'name'      =>$product['name'],
                'image'     =>NTImage::resizeAndSave($image, 150, 150),
                'model'     =>$product['model'],
                'href'      =>Url::createUrl('store/product',array('product_id'=>$product['product_id']))
            );
        }

        return $products;
    }

    public function run() {
        $query = $this->db->query("SELECT *, s.email AS c_email FROM ". DB_PREFIX ."stat s
        LEFT JOIN ". DB_PREFIX ."customer c ON (s.customer_id=c.customer_id)
        WHERE DAY(s.date_added) = '". $this->db->escape(date('d')) ."' 
            AND MONTH(s.date_added) = '". $this->db->escape(date('m')) ."'
            AND YEAR(s.date_added) = '". $this->db->escape(date('Y')) ."'
            AND s.object_type = 'product'
            AND s.status = 1
        GROUP BY s.email
        LIMIT 200");

        if ($query->num_rows && (int)$this->config->get('marketing_email_recommended_products')) {
            $this->load->library('tax');        
            $this->load->library('currency');
            $this->load->library('image');
            $this->load->library('url');
            $this->tax = new Tax($this->registry);
            $this->currency = new Currency($this->registry);

            $params = array(
                'job'=>'send_recommended_products',
                'newsletter_id'=>(int)$this->config->get('marketing_email_recommended_products')
            );
            
            $this->load->library('task');
            $task = new Task($this->registry);
            
            $task->object_id        = (int)$this->config->get('marketing_email_recommended_products');
            $task->object_type      = 'newsletter';
            $task->task             = 'recommended_products';
            $task->type             = 'send';
            $task->time_exec        = date('Y-m-d') . ' 08:00:00';
            $task->params           = $params;
            $task->time_interval    = "";
            $task->time_last_exec   = "";
            $task->run_once         = true;
            $task->status           = 1;
            $task->date_start_exec  = date('Y-m-d') . ' 08:00:00';
            $task->date_end_exec    = date('Y-m-d') . ' 23:00:00';
            
            foreach ($query->rows as $customer) {
                $params = array(
                    'customer_id'=>$customer['customer_id'],
                    'fullname'  =>$customer['firstname'] ." ". $customer['lastname'],
                    'company'   =>$customer['company'],
                    'rif'       =>$customer['rif'],
                    'telephone' =>$customer['telephone'],
                    'email'     =>$customer['c_email'],
                    'products'  =>$this->getProducts($customer['c_email'])
                );
                $queue = array(
                    "params"    =>$params,
                    "status"    =>1,
                    "time_exec" =>date('Y-m-d') . ' 08:00:00'
                );
                
                $task->addQueue($queue);
                
                $this->db->query("UPDATE ". DB_PREFIX ."stat SET status = 0 
                WHERE email = '". $customer['c_email'] ."'
                    AND DAY(date_added) = '". $this->db->escape(date('d')) ."'
                    AND MONTH(date_added) = '". $this->db->escape(date('m')) ."'
                    AND YEAR(date_added) = '". $this->db->escape(date('Y')) ."'");
            }
            $task->createSendTask();
        }
    }
}