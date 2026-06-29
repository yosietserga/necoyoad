<?php

class ControllerCheckoutSuccess extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->load->model('checkout/extension');

        $Url = new Url($this->registry);
        if ($this->config->get('config_store_mode') != 'store') {
            $this->redirect(HTTP_HOME);
        }

        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("checkout/cart"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('checkout/success');

        $this->data['heading_title'] = $this->document->title = $this->language->get('heading_title');

        $this->load->auto('account/address');
        $address = $this->modelAddress->getAddress($this->customer->getAddressId());
        $method_data = [];
        $results = $this->modelExtension->getExtensions('payment');
        foreach ($results as $result) {
            $this->load->model('payment/' . $result['key']);
            $this->language->load('payment/' . $result['key']);
            $method = $this->{'model_payment_' . $result['key']}->getMethod($address);
            if ($method) {
                $method_data[$result['key']] = $method;
            }
        }
        $sort_order = [];
        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $method_data);
        $this->data['payment_methods'] = $method_data;

        foreach ($method_data as $key => $value) {
            $this->children[$key] = 'payment/' . $key;
        }

        $order_id = 0;
        if ($this->session->has('order_id')) {
            $order_id = $this->session->get('order_id');
        } elseif ($this->request->hasPost('order_id')) {
            $order_id = $this->request->getPost('order_id');
        } elseif ($this->request->hasQuery('order_id')) {
            $order_id = $this->request->getQuery('order_id');
        }
        $this->data['order_id'] = $order_id;
        if ($order_id) {
            if ($this->config->get('marketing_email_new_order')) {
                $this->load->model('account/order');
                $this->load->model('account/payment');
                $this->load->model("marketing/newsletter");
                $this->load->library('email/mailer');
                $this->load->library('BarcodeQR');
                $this->load->library('Barcode39');
                $this->load->library('tcpdf/config/lang/spa');
                $this->load->library('tcpdf/tcpdf');
                $mailer = new Mailer;
                $qr = new BarcodeQR;
                $barcode = new Barcode39(C_CODE);
                $this->data['Currency'] = $this->currency;
                $this->data['order'] = $order = $this->modelOrder->getOrder($order_id);
                $this->data['products'] = $products = $this->modelOrder->getOrderProducts($order_id);
                $this->data['totals'] = $totals = $this->modelOrder->getOrderTotals($order_id);
                $this->data['payments'] = $payments = $this->modelPayment->getPayments(array(
                    'order_id' => $order_id,
                    'order_payment_status_id' => $this->config->get('order_payment_status_approved')
                ));

                $shipping_address = $order['shipping_address_1'] . ", " . $order['shipping_city'] . ". " . $order['shipping_zone'] . " - " . $order['shipping_country'] . ". CP " . $order['shipping_zone_code'];

                $payment_address = $order['payment_address_1'] . ", " . $order['payment_city'] . ". " . $order['payment_zone'] . " - " . $order['payment_country'] . ". CP " . $order['payment_zone_code'];

                $text = $this->config->get('config_owner') . "\n";
                $text .= "Pedido ID: " . $order_id . "\n";
                $text .= "Fecha Emision: " . date('d-m-Y h:i A', strtotime($order['date_added'])) . "\n";
                $text .= "Cliente: " . $this->customer->getCompany() . "\n";
                $text .= "RIF: " . $this->customer->getRif() . "\n";
                $text .= "Direccion IP: " . $order['ip'] . "\n";
                $text .= "Productos (" . count($products) . ")\n";
                $text .= "Modelo\tCant.\tTotal\n";

                foreach ($products as $key => $product) {
                    $text .= $product['model'] . "\t" .
                            $product['quantity'] . "\t" .
                            $this->currency->format($product['total'], $order['currency'], $order['value']) . "\n";
                }


                $qrStore = "cache/" . str_replace(".", "_", $this->config->get('config_owner')) . '.jpg';
                $qrOrder = "cache/" . str_replace(" ", "_", $this->config->get('config_owner') . "_qr_code_order_" . $order_id) . '.jpg';
                $eanStore = "cache/" . str_replace(" ", "_", $this->config->get('config_owner') . "_barcode_39_order_id_" . $order_id) . '.gif';

                $qr->text($text);
                $qr->draw(150, DIR_IMAGE . $qrOrder);
                $qr->url(HTTP_HOME);
                $qr->draw(150, DIR_IMAGE . $qrStore);
                $barcode->draw(DIR_IMAGE . $eanStore);

                $product_html = "<table><thead><tr style=\"background:#ccc;color:#666;\"><th>Item</th><th>" . $this->language->get('column_description') . "</th><th>" . $this->language->get('column_model') . "</th><th>" . $this->language->get('column_quantity') . "</th><th>" . $this->language->get('column_price') . "</th><th>" . $this->language->get('column_total') . "</th></tr></thead><tbody>";
                foreach ($products as $key => $product) {
                    $options = $this->modelOrder->getOrderOptions($order_id, $product['order_product_id']);
                    $option_data = "";
                    foreach ($options as $option) {
                        $option_data .= "&nbsp;&nbsp;&nbsp;&nbsp;- " . $option['name'] . "<br />";
                    }

                    $attributes = $this->modelOrder->getAllProperties($order_id, 'product_attribute');
                    $attributes_data = "<p>". $this->language->get("Especificaciones") ."</p>";
                    foreach ($attributes as $option) {
                        $attributes_data .= "&nbsp;&nbsp;&nbsp;&nbsp;- " . $option['key'] .': '. $option['value'] . "<br />";
                    }

                    $product_html .= "<tr>";
                    $product_html .= "<td style=\"width:5%\">" . (int) ($key + 1) . "</td>";
                    $product_html .= "<td style=\"width:45%\">" . $product['name'] . "<br />" . $option_data . "<br />" . $attributes_data . "</td>";
                    $product_html .= "<td style=\"width:20%\">" . $product['model'] . "</td>";
                    $product_html .= "<td style=\"width:10%\">" . $product['quantity'] . "</td>";
                    $product_html .= "<td style=\"width:10%\">" . $this->currency->format($product['price'], $order['currency'], $order['value']) . "</td>";
                    $product_html .= "<td style=\"width:10%\">" . $this->currency->format($product['total'], $order['currency'], $order['value']) . "</td>";
                    $product_html .= "</tr>";
                }
                $product_html .= "</tbody></table>";

                $total_html = "<div class=\"clear:both;float:none;\"></div><br /><table style=\"float:right;\">";
                foreach ($totals as $total) {
                    $total_html .= "<tr>";
                    $total_html .= "<td style=\"text-align:right;\">" . $total['title'] . "</td>";
                    $total_html .= "<td style=\"text-align:right;\">" . $total['text'] . "</td>";
                    $total_html .= "</tr>";
                }
                $total_html .= "</table>";

                $result = $this->modelNewsletter->getById($this->config->get('marketing_email_new_order'));
                $message = $result['htmlbody'];

                $message = str_replace("{%title%}", 'Pedido N&deg; ' . $order_id . " - " . $this->config->get('config_name'), $message);
                $message = str_replace("{%store_logo%}", '<img src="' . HTTP_IMAGE . $this->config->get('config_logo') . '" alt="' . $this->config->get('config_name') . '" />', $message);
                $message = str_replace("{%store_url%}", HTTP_HOME, $message);
                $message = str_replace("{%store_owner%}", $this->config->get('config_owner'), $message);
                $message = str_replace("{%store_name%}", $this->config->get('config_name'), $message);
                $message = str_replace("{%store_rif%}", $this->config->get('config_rif'), $message);
                $message = str_replace("{%store_email%}", $this->config->get('config_email'), $message);
                $message = str_replace("{%store_telephone%}", $this->config->get('config_telephone'), $message);
                $message = str_replace("{%store_address%}", $this->config->get('config_address'), $message);
                $message = str_replace("{%products%}", $product_html, $message);
                $message = str_replace("{%totals%}", $total_html, $message);
                $message = str_replace("{%order_id%}", $this->config->get('config_invoice_prefix') . $order_id, $message);
                $message = str_replace("{%invoice_id%}", $this->config->get('config_invoice_prefix') . $invoice_id, $message);
                $message = str_replace("{%rif%}", $this->customer->getRif(), $message);
                $message = str_replace("{%fullname%}", $this->customer->getFirstName() . " " . $this->customer->getFirstName(), $message);
                $message = str_replace("{%company%}", $this->customer->getCompany(), $message);
                $message = str_replace("{%email%}", $this->customer->getEmail(), $message);
                $message = str_replace("{%telephone%}", $this->customer->getTelephone(), $message);
                $message = str_replace("{%payment_address%}", $payment_address, $message);
                $message = str_replace("{%payment_method%}", $order['payment_method'], $message);
                $message = str_replace("{%shipping_address%}", $shipping_address, $message);
                $message = str_replace("{%shipping_method%}", $order['shipping_method'], $message);
                $message = str_replace("{%date_added%}", date('d-m-Y h:i A', strtotime($order['date_added'])), $message);
                $message = str_replace("{%ip%}", $order['ip'], $message);
                $message = str_replace("{%qr_code_store%}", '<img src="' . HTTP_IMAGE . $qrStore . '" alt="QR Code" />', $message);
                $message = str_replace("{%comment%}", $order['comment'], $message);
                $message = str_replace("{%qr_code_order%}", '<img src="' . HTTP_IMAGE . $qrOrder . '" alt="QR Code" />', $message);
                $message = str_replace("{%barcode_39_order_id%}", '<img src="' . HTTP_IMAGE . $eanStore . '" alt="QR Code" />', $message);

                $message .= "<p style=\"text-align:center\">Powered By <a href=\"https://www.necotienda.org\">Necotienda</a>&reg; " . date('Y') . "</p>";

                if ($this->config->get('marketing_email_order_pdf')) {
                    $pdfFile = DIR_CACHE . str_replace(" ", "_", $this->config->get('config_owner') . "_pedido_" . $order_id) . '.pdf';
                    $result = $this->modelNewsletter->getById($this->config->get('marketing_email_order_pdf'));
                    $pdfBody = html_entity_decode($result['htmlbody']);

                    $pdfBody = str_replace("{%store_url%}", HTTP_HOME, $pdfBody);
                    $pdfBody = str_replace("{%title%}", 'Pedido N&deg; ' . $order_id . " - " . $this->config->get('config_name'), $pdfBody);
                    $pdfBody = str_replace("{%store_owner%}", $this->config->get('config_owner'), $pdfBody);
                    $pdfBody = str_replace("{%store_name%}", $this->config->get('config_name'), $pdfBody);
                    $pdfBody = str_replace("{%store_rif%}", $this->config->get('config_rif'), $pdfBody);
                    $pdfBody = str_replace("{%store_email%}", $this->config->get('config_email'), $pdfBody);
                    $pdfBody = str_replace("{%store_telephone%}", $this->config->get('config_telephone'), $pdfBody);
                    $pdfBody = str_replace("{%store_address%}", $this->config->get('config_address'), $pdfBody);
                    $pdfBody = str_replace("{%products%}", $product_html, $pdfBody);
                    $pdfBody = str_replace("{%totals%}", $total_html, $pdfBody);
                    $pdfBody = str_replace("{%order_id%}", $this->config->get('config_invoice_prefix') . $order_id, $pdfBody);
                    $pdfBody = str_replace("{%invoice_id%}", $this->config->get('config_invoice_prefix') . $invoice_id, $pdfBody);
                    $pdfBody = str_replace("{%rif%}", $this->customer->getRif(), $pdfBody);
                    $pdfBody = str_replace("{%fullname%}", $this->customer->getFirstName() . " " . $this->customer->getFirstName(), $pdfBody);
                    $pdfBody = str_replace("{%company%}", $this->customer->getCompany(), $pdfBody);
                    $pdfBody = str_replace("{%email%}", $this->customer->getEmail(), $pdfBody);
                    $pdfBody = str_replace("{%telephone%}", $this->customer->getTelephone(), $pdfBody);
                    $pdfBody = str_replace("{%payment_address%}", $payment_address, $pdfBody);
                    $pdfBody = str_replace("{%payment_method%}", $order['payment_method'], $pdfBody);
                    $pdfBody = str_replace("{%shipping_address%}", $shipping_address, $pdfBody);
                    $pdfBody = str_replace("{%shipping_method%}", $order['shipping_method'], $pdfBody);
                    $pdfBody = str_replace("{%date_added%}", date('d-m-Y h:i A', strtotime($order['date_added'])), $pdfBody);
                    $pdfBody = str_replace("{%ip%}", $order['ip'], $pdfBody);
                    $pdfBody = str_replace("{%comment%}", $order['comment'], $pdfBody);

                    if (file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
                        $pdfBody = str_replace("{%store_logo%}", '<img src="' . HTTP_IMAGE . $this->config->get('config_logo') . '" alt="' . $this->config->get('config_name') . '" />', $pdfBody);
                    } else {
                        $pdfBody = str_replace("{%store_logo%}", '', $pdfBody);
                    }

                    if (file_exists(DIR_IMAGE . $qrStore)) {
                        $pdfBody = str_replace("{%qr_code_store%}", '<img src="' . HTTP_IMAGE . $qrStore . '" alt="QR Code" />', $pdfBody);
                    } else {
                        $pdfBody = str_replace("{%qr_code_store%}", '', $pdfBody);
                    }

                    if (file_exists(DIR_IMAGE . $qrOrder)) {
                        $pdfBody = str_replace("{%qr_code_order%}", '<img src="' . HTTP_IMAGE . $qrOrder . '" alt="QR Code" />', $pdfBody);
                    } else {
                        $pdfBody = str_replace("{%qr_code_order%}", '', $pdfBody);
                    }

                    $pdfBody = str_replace("{%barcode_39_order_id%}", '<img src="' . HTTP_IMAGE . $eanStore . '" alt="QR Code" />', $pdfBody);

                    $pdfBody .= "<p style=\"text-align:center\">Powered By Necotienda&reg; " . date('Y') . "</p>";

                    // create new PDF document
                    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                    // set document information
                    $pdf->SetCreator("Powered By NecoTienda&reg;");
                    $pdf->SetTitle($this->config->get('config_name'));
                    $pdf->SetAuthor($this->config->get('config_name'));
                    $pdf->SetSubject($this->config->get('config_owner') . " " . $this->language->get('text_order') . " #" . $order_id);
                    //$pdf->SetKeywords($this->config->get('config_name') . ', ' . $product_tags . ',pdf');

                    // set default header data
                    $pdf->SetHeaderData($this->config->get('config_logo'), PDF_HEADER_LOGO_WIDTH, $this->config->get('config_owner'), $this->config->get('config_name'));

                    // set header and footer fonts
                    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                    // set default monospaced font
                    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                    //set margins
                    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                    //set auto page breaks
                    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                    //set image scale factor
                    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                    //set some language-dependent strings
                    $pdf->setLanguageArray($l);

                    // set font
                    $pdf->SetFont('dejavusans', '', 10);

                    // add a page
                    $pdf->AddPage();

                    // output the HTML content
                    $pdf->writeHTML($pdfBody, true, false, true, false, '');

                    //Close and output PDF document
                    $pdf->Output($pdfFile, 'F');
                }

                $subject = $this->config->get('config_owner') . " " . $this->language->get('text_new_order') . " #" . $order_id;
                if ($this->config->get('config_smtp_method') == 'smtp') {
                    $mailer->IsSMTP();
                    $mailer->Host = $this->config->get('config_smtp_host');
                    $mailer->Username = $this->config->get('config_smtp_username');
                    $mailer->Password = base64_decode($this->config->get('config_smtp_password'));
                    $mailer->Port = $this->config->get('config_smtp_port');
                    $mailer->Timeout = $this->config->get('config_smtp_timeout');
                    $mailer->SMTPSecure = $this->config->get('config_smtp_ssl');
                    $mailer->SMTPAuth = ($this->config->get('config_smtp_auth')) ? true : false;
                } elseif ($this->config->get('config_smtp_method') == 'sendmail') {
                    $mailer->IsSendmail();
                } else {
                    $mailer->IsMail();
                }

                $mailer->IsHTML();
                $mailer->AddAddress($this->customer->getEmail(), $this->customer->getCompany());
                $mailer->AddBCC($this->config->get('config_email'), $this->config->get('config_name'));
                $mailer->SetFrom($this->config->get('config_email'), $this->config->get('config_name'));
                $mailer->Subject = $subject;
                $mailer->Body = html_entity_decode($message);
                if ($pdfFile && file_exists($pdfFile)) {
                    $mailer->AddAttachment($pdfFile);
                }
                $mailer->Send();
            }
            $order_id = $this->session->get('order_id');

            $this->cart->clear();

            $this->session->clear('shipping_method');
            $this->session->clear('shipping_methods');
            $this->session->clear('payment_method');
            $this->session->clear('payment_methods');
            $this->session->clear('guest');
            $this->session->clear('comment');
            $this->session->clear('order_id');
            $this->session->clear('coupon');
        }

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("checkout/cart"),
            'text' => $this->language->get('text_basket'),
            'separator' => $this->language->get('text_separator')
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("checkout/success"),
            'text' => $this->language->get('text_checkout_success'),
            'separator' => $this->language->get('text_separator')
        );
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        if ($this->config->get('page_order_success')) {
            $this->load->model('content/page');
            $page = $this->modelPage->getById($this->config->get('page_order_success'));
            $this->data['text_message'] = html_entity_decode($page['description']);
        } else {
            $this->data['text_message'] = sprintf($this->language->get('text_message'), Url::createUrl("account/account"), Url::createUrl("account/order"), Url::createUrl("page/contact"));
        }

        $this->data['Currency'] = $this->currency;

        // style files
        $csspath = defined("CDN") ? CDN . CSS : HTTP_CSS;
        $styles[] = array('media' => 'all', 'href' => $csspath . 'jquery-ui/jquery-ui.min.css');
        $styles[] = array('media' => 'all', 'href' => $csspath . 'neco.form.css');
        $this->data['styles'] = $this->styles = array_merge($this->styles, $styles);
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/checkout/success.tpl')) {
            $this->template = $this->config->get('config_template') . '/checkout/success.tpl';
        } else {
            $this->template = 'choroni/checkout/success.tpl';
        }

        

        $this->session->set('landing_page','checkout/success');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

}
