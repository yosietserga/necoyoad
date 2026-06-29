<?php

class ControllerSettingSetting extends Controller {

    private $error = [];

    public function index() {
        $this->load->library('url');
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->library('email/mailer');
            $mailer = new Mailer;

            $subject = "Sugerencias NecoTienda";

            $message .= "<p>Dominio:<b>" . $this->request->post['domain'] . "</b></p><br />";
            $message .= "<p>IP Browser:<b>" . $this->request->post['remote_ip'] . "</b></p><br />";
            $message .= "<p>IP Server:<b>" . $this->request->post['server_ip'] . "</b></p><br />";
            $message .= "<p>Cliente ID:<b>" . C_CODE . "</b></p><br />";
            $message .= "<p>Sugerencia:<b>" . HTTP_HOME . "</b></p><br />";
            $message .= "<p>" . $this->request->post['feedback'] . "</p>";
            $message .= "<hr />";
            $message .= "Server Vars:" . serialize($_SERVER);


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
            $mailer->AddAddress("soporte@necotienda.com", "Support NecoTienda");
            $mailer->SetFrom($this->config->get('config_email'), $this->config->get('config_name'));
            $mailer->Subject = $subject;
            $mailer->Body = $message;
            $mailer->Send();
        }
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function ping() {
        //TODO: crear sentencias para comprobar licencia cada vez que se carga una p�gina nueva
        // por cada pedido, cliente, contacto, producto, marca nuevo enviar datos an�nimos
    }

}
