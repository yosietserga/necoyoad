<?php
class ControllerPaymentDebit extends Controller {
	protected function index() {
		$this->language->load('payment/debit');
		$this->load->model('account/order');
		
        $model = $this->db->query("SELECT * FROM ". DB_PREFIX ."balance WHERE customer_id = '". (int)$this->customer->getId() ."' ORDER BY balance_id DESC LIMIT 1");
        
        $this->data['balance'] = $model->row;
        $this->data['balance']['available'] = $this->currency->format($model->row['amount_available']);
	    
        if ($this->request->hasQuery('order_id')) {
            $this->data['order_id'] = $this->request->getQuery('order_id');
        } elseif ($this->request->hasPost('order_id')) {
            $this->data['order_id'] = $this->request->getPost('order_id');
        } elseif ($this->session->has('order_id')) {
            $this->data['order_id'] = $this->session->get('order_id');
        } else {
            $this->data['order_id'] = 0;
        }
        
        foreach ($this->modelOrder->getOrders(array('limit'=>1000)) as $key => $result) {
        	$this->data['orders'][] = array(
                'order_id'   => $result['order_id'],
          		'date_added' => date('d-m-Y h:i A', strtotime($result['dateAdded'])),
          		'total'      => $this->currency->format($result['total'], $result['currency'], $result['value'])
  		    );
        }



        $this->id = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/payment/debit.tpl')) {
			$this->template = $this->config->get('config_template') . '/payment/debit.tpl';
		} else {
			$this->template = 'default/payment/debit.tpl';
		}	
		
		$this->render(); 
	}

	public function confirm() {
        $this->language->load('payment/debit');
  		$this->load->model('account/balance');
        $order_id = ($this->request->post['order_id']) ? $this->request->post['order_id'] : $this->request->get['order_id'];
        $credits = $this->modelBalance->getLastBalance();
        $json = [];
	   if (!$this->customer->isLogged()) {
            $json['error'] = 1;
            $json['msg'] = $this->language->get('error_not_logged');
	   } elseif (!$order_id) {
            $json['error'] = 1;
            $json['msg'] = $this->language->get('error_not_order');
       } elseif ($credits['amount_available'] <= 0) {
            $json['error'] = 1;
            $json['msg'] = $this->language->get('error_no_credits');
       } elseif ($this->request->post['amount'] <= 0) {
            $json['error'] = 1;
            $json['msg'] = $this->language->get('error_no_amount');
       } else {
    		$this->load->model('account/order');
    		$this->load->model('account/payment');
            $order_info = $this->modelOrder->getOrder($this->request->post['order_id']);
            
            if ($order_info) {
                if ($order_info['order_status_id'] == $this->config->get('config_order_status_id')) {
                
                    $total_payment = $this->modelPayment->getSumPayments(array(
                        'order_id'=>$order_info['order_id'],
                        'limit'=>1000
                    ));
                    
                    $total = $order_info['total'] - $total_payment;
                    
                    $amount = explode(",",$this->request->post['amount']);
                    $payment = round(str_replace(".","",$amount[0]) .".". $amount[1],2);
                    $this->request->post['amount'] = $payment;
                    
            		$comment  = $this->request->post['comment'] . "\n\n";
            		$comment  = $this->language->get('text_payable') . "\n";
            		$comment .= $this->config->get('debit_payable') . "\n\n";
            		$comment .= $this->language->get('text_payment') . "\n";

                    $data = [];
                    $diff = $payment - $total;
                    if ($diff < 0) { //falta dinero
                        $json['error'] = 1;
                        $json['msg'] = $this->language->get('error_moneyless');
                        $json['order_status_id'] = $data['order_status_id'] = (int)$this->config->get('config_order_status_id');
                        $order_payment_status_id = $this->config->get('config_order_payment_status_id');
                    } elseif ($diff > 0) { //sobra dinero
                        $json['error'] = 1;
                        $json['msg'] = $this->language->get('error_moneymore');
                        $json['order_status_id'] = $data['order_status_id'] = (int)$this->config->get('debit_order_status_id');
                        $order_payment_status_id = $this->config->get('config_order_payment_status_approved');
                    }
                    
                    if (!$data['order_status_id'] && $order_info['order_status_id']) {
                        $json['order_status_id'] = $data['order_status_id'] = (int)$order_info['order_status_id']; 
                    }
                    
                    if (!$data['order_status_id'] && !$order_info['order_status_id']) {
                        $json['order_status_id'] = $data['order_status_id'] = (int)$this->config->get('config_order_status_id'); 
                    }
                    
                    $data['comment'] = $comment;
                    $this->modelOrder->addOrderHistory($order_info['order_id'],$data);
                    if ($data['order_status_id']) $this->modelOrder->updateStatus($order_info['order_id'],$data['order_status_id']);
                    $this->modelOrder->updatePaymentMethod($order_info['order_id'],'debit');
                    
                    $data = [];
                    $data['order_id'] = $order_info['order_id'];
                    $data['bank_account_id'] = $this->request->post['bank_account_id'];
                    $data['transac_number'] = $this->request->post['transact'];
                    $data['order_payment_status_id'] = $order_payment_status_id;
                    $data['payment_method'] = 'debit';
                    $data['amount'] = $this->request->post['amount'];
                    $data['comment'] = $comment;
                    $json['payment_id'] = $payment_id = $this->modelPayment->add($data);
                    
                    $data = [];
                    $data['order_payment_status_id'] = $order_payment_status_id;
                    $data['type'] = 'out';
                    $data['description'] = 'Pago abonado al <a href="'. Url::createUrl('account/order',array('order_id'=>$order_id)) .'">Pedido #'. $order_id .'</a> con recibo de <a href="'. Url::createUrl('account/payment',array('payment_id'=>$payment_id)) .'">Pago #'. $payment_id .'</a>';
                    $data['payment_method'] = 'debit';
                    $data['amount'] = $this->request->post['amount'];
                    $data['amount_available'] = $credits['amount_available'] - $this->request->post['amount'];
                    $data['amount_blocked'] = $credits['amount_blocked'];
                    $data['amount_total'] = $data['amount_blocked'] + $data['amount_available'];
                    $balance_id = $this->modelBalance->add($data);
                    
                    unset($json['error']);
                    $json['success'] = 1;
                    $json['amount_available'] = $this->currency->format($data['amount_available']);
                    $json['msg'] = str_replace('{%url_receipt%}',Url::createUrl("account/payment/receipt",array('payment_id'=>$payment_id)),$this->language->get('text_success'));
                    //$json['redirect'] = Url::createUrl("account/payment/receipt",array('payment_id'=>$payment_id));
                    $this->notify($payment_id,$data);
                } else {
                    $json['error'] = 1;
                    $json['msg'] = $this->language->get('error_order_not_open');
                }
            } else {
                $json['error'] = 1;
                $json['msg'] = $this->language->get('error_not_order');
            }
        }
    		$this->load->library('json');
    		$this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
	}
    
    protected function notify($payment_id,$data) {
        if ($this->config->get('debit_email_template')) {
            $this->load->library('email/mailer');
            $this->load->model("marketing/newsletter");                
            $this->load->library('BarcodeQR');
            $this->load->library('Barcode39');
                $mailer     = new Mailer;
                $qr         = new BarcodeQR;
                $barcode    = new Barcode39(C_CODE);
                $mailer     = new Mailer;
                
                $text = $this->config->get('config_owner') . "\n"; 
                $text .= "Pago ID: " . $payment_id . "\n"; 
                $text .= "Pedido ID: " . $data['order_id'] . "\n"; 
                $text .= "Fecha Emision: " . date('d-m-Y h:i A') . "\n"; 
                $text .= "Cliente: " . $this->customer->getCompany() . "\n"; 
                $text .= "RIF: " . $this->customer->getRif() . "\n";
                $text .= "Direccion IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
                $text .= "Monto: ". $data['amount'] ."\n";
                
                $qrStore = "cache/" . str_replace(".","_",$this->config->get('config_owner')).'.png';
                $qrPayment = "cache/" . str_replace(" ","_",$this->config->get('config_owner') ."_qr_code_payment_" . $payment_id) . '.png';
                $eanStore = "cache/" . str_replace(" ","_",$this->config->get('config_owner') ."_barcode_39") . '.gif';
                                
                
                $qr->text($text);
                $qr->draw(150,DIR_IMAGE . $qrPayment);
                if (!file_exists(DIR_IMAGE . $qrStore)) {
                    $qr->url(HTTP_HOME);
                    $qr->draw(150,DIR_IMAGE . $qrStore);
                }
                if (!file_exists(DIR_IMAGE . $eanStore)) {
                    $barcode->draw(DIR_IMAGE . $eanStore);
                }
                
                $bankaccount = $this->db->query("SELECT * FROM ". DB_PREFIX ."bank_account ba LEFT JOIN ". DB_PREFIX ."bank b ON (ba.bank_id=b.bank_id) WHERE ba.status = 1 AND bank_account_id = '". (int)$data['bank_account_id'] ."'");
        
                $html = "<table>";
                $html .= "<tr><td>". $this->language->get('text_payment_id') ."</td><td><b>". (int)$payment_id ."</b></td></tr>";
                $html .= "<tr><td>". $this->language->get('text_company') ."</td><td><b>". $this->customer->getCompany() ."</b></td></tr>";
                $html .= "<tr><td>". $this->language->get('text_rif') ."</td><td><b>". $this->customer->getRif() ."</b></td></tr>";
                $html .= "<tr><td>". $this->language->get('text_email') ."</td><td><b>". $this->customer->getEmail() ."</b></td></tr>";
                $html .= "<tr><td>". $this->language->get('text_telephone') ."</td><td><b>". $this->customer->getTelephone() ."</b></td></tr>";
                $html .= "<tr><td>". $this->language->get('text_order_id') ."</td><td><b>". (int)$data['order_id'] ."</b></td></tr>";
                $html .= "<tr><td>". $this->language->get('text_bank_account') ."</td><td><b>". $bankaccount->row['number'] ." - ". $bankaccount->row['accountholder'] ."</b></td></tr>";
                $html .= "<tr><td>". $this->language->get('text_transac_number') ."</td><td><b>". $data['transac_number'] ."</b></td></tr>";
                $html .= "<tr><td>". $this->language->get('text_payment_method') ."</td><td><b>". $data['payment_method'] ."</b></td></tr>";
                $html .= "<tr><td>". $this->language->get('text_amount') ."</td><td><b>". $data['amount'] ."</b></td></tr>";
                $html .= "<tr><td>". $this->language->get('text_comment') ."</td><td><b>". strip_tags(html_entity_decode($data['comment'])) ."</b></td></tr>";
                $html .= "</table>";

                $result = $this->modelNewsletter->getById($this->config->get('debit_email_template'));
                $message = $result['htmlbody'];

                $message = str_replace("{%title%}",'Pago N&deg; ' . $payment_id . " - " . $this->config->get('config_name'),$message);
                $message = str_replace("{%store_logo%}",'<img src="'. HTTP_IMAGE . $this->config->get('config_logo') .'" alt="'. $this->config->get('config_name') .'" />',$message);
                $message = str_replace("{%store_url%}",HTTP_HOME,$message);
                $message = str_replace("{%store_owner%}",$this->config->get('config_owner'),$message);
                $message = str_replace("{%store_name%}",$this->config->get('config_name'),$message);
                $message = str_replace("{%store_rif%}",$this->config->get('config_rif'),$message);
                $message = str_replace("{%store_email%}",$this->config->get('config_email'),$message);
                $message = str_replace("{%store_telephone%}",$this->config->get('config_telephone'),$message);
                $message = str_replace("{%store_address%}",$this->config->get('config_address'),$message);
                $message = str_replace("{%payment%}",$html,$message);
                $message = str_replace("{%order_id%}",$data['order_id'],$message);
                $message = str_replace("{%payment_id%}",$payment_id,$message);
                $message = str_replace("{%rif%}",$this->customer->getRif(),$message);
                $message = str_replace("{%fullname%}",$this->customer->getFirstName() ." ". $this->customer->getFirstName(),$message);
                $message = str_replace("{%company%}",$this->customer->getCompany(),$message);
                $message = str_replace("{%email%}",$this->customer->getEmail(),$message);
                $message = str_replace("{%telephone%}",$this->customer->getTelephone(),$message);
                $message = str_replace("{%payment_method%}",$data['payment_method'],$message);
                $message = str_replace("{%date_added%}",date('d-m-Y h:i A'),$message);
                $message = str_replace("{%ip%}",$_SERVER['REMOTE_ADDR'],$message);
                $message = str_replace("{%qr_code_store%}",'<img src="'. HTTP_IMAGE . $qrStore .'" alt="QR Code" />',$message);
                $message = str_replace("{%comment%}",$data['comment'],$message);
                $message = str_replace("{%qr_code_order%}",'<img src="'. HTTP_IMAGE . $qrPayment .'" alt="QR Code" />',$message);
                $message = str_replace("{%barcode_39_order_id%}",'<img src="'. HTTP_IMAGE . $eanStore .'" alt="QR Code" />',$message);
                
                $message .= "<p style=\"text-align:center\">Powered By Necotienda&reg; ". date('Y') ."</p>";
                
            if ($this->config->get('config_smtp_method')=='smtp') {
                $mailer->IsSMTP();
                $mailer->Host = $this->config->get('config_smtp_host');
                $mailer->Username = $this->config->get('config_smtp_username');
                $mailer->Password = base64_decode($this->config->get('config_smtp_password'));
                $mailer->Port     = $this->config->get('config_smtp_port');
                $mailer->Timeout  = $this->config->get('config_smtp_timeout');
                $mailer->SMTPSecure = $this->config->get('config_smtp_ssl');
                $mailer->SMTPAuth = ($this->config->get('config_smtp_auth')) ? true : false;
            } elseif ($this->config->get('config_smtp_method')=='sendmail') {
                $mailer->IsSendmail();
            } else {
                $mailer->IsMail();
            }
            $mailer->IsHTML();
            $mailer->AddAddress($this->customer->getEmail(),$this->customer->getCompany());
            $mailer->AddBCC($this->config->get('config_email'),$this->config->get('config_name'));
            $mailer->SetFrom($this->config->get('config_email'),$this->config->get('config_name'));
            $mailer->Subject = $this->config->get('config_name') ." - Reporte de Pago";
            $mailer->Body = html_entity_decode($message);
            $mailer->Send();
        }
    }
}
