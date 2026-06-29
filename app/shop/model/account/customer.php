<?php

class ModelAccountCustomer extends Model
{

    public function addCustomer($data)
    {
        if (!$this->getTotalCustomersByEmail($data['email'])) {
            $suffix = base_convert(rand(10e16, 10e20), 10, 36); //TODO: agregar sufijo a las contrasenas
            $result = $this->db->query("INSERT INTO `" . DB_PREFIX . "customer` SET 
              `store_id`    = '" . (int)STORE_ID . "', 
              `referenced_by`= '" . (int)$data['referenced_by'] . "', 
              `firstname`   = '" . $this->db->escape($data['firstname']) . "', 
              `lastname`    = '" . $this->db->escape($data['lastname']) . "', 
              `telephone`   = '" . $this->db->escape($data['telephone']) . "', 
              `email`       = '" . $this->db->escape($data['email']) . "',
              `birthday`    = '" . $this->db->escape($data['birthday']) . "', 
              `rif`         = '" . $this->db->escape($data['rif']) . "', 
              `company`     = '" . $this->db->escape($data['company']) . "', 
              `password`    = '" . $this->db->escape(md5(md5($data['password']) . $suffix) . ':' . $suffix) . "',
              `activation_code`      = '" . $this->db->escape($data['activation_code']) . "',
              `customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "', 
              `status`      = '1', 
              `date_added`  = NOW()");
            $customer_id = $this->db->getLastId();
            
            $result = $this->db->query("INSERT INTO `" . DB_PREFIX . "object_to_store` SET 
              `store_id`    = '" . (int)STORE_ID . "', 
              `object_type`    = 'customer', 
              `object_id`   = '" . (int)$customer_id . "'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "address SET 
              customer_id   = '" . (int)$customer_id . "', 
              firstname     = '" . $this->db->escape($data['firstname']) . "', 
              lastname      = '" . $this->db->escape($data['lastname']) . "', 
              company       = '" . $this->db->escape($data['company']) . "', 
              address_1     = '" . $this->db->escape($data['address_1']) . "',
              city          = '" . $this->db->escape($data['city']) . "', 
              postcode      = '" . $this->db->escape($data['postcode']) . "', 
              country_id    = '" . (int)$data['country_id'] . "', 
              zone_id       = '" . (int)$data['zone_id'] . "'");
            
            $address_id = $this->db->getLastId();
            
            $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
            
            if ($this->config->get('config_customer_approval')) {
                $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `approved` = '1' WHERE `customer_id` = '" . (int)$customer_id . "'");
            }
            return $customer_id;
        }
    }

    public function addCustomerFromGoogle($data)
    {
        if (!$this->getTotalCustomersByEmail($data['email'])) {
            $suffix = base_convert(rand(10e16, 10e20), 10, 36); //TODO: agregar sufijo a las contrasenas
            $password = substr(md5(mt_rand()), 0, 6);
            $result = $this->db->query("INSERT INTO `" . DB_PREFIX . "customer` SET 
              `store_id`    = '" . (int)STORE_ID . "', 
              `firstname`   = '" . $this->db->escape($data['firstname']) . "', 
              `lastname`    = '" . $this->db->escape($data['lastname']) . "', 
              `telephone`   = '" . $this->db->escape($data['telephone']) . "', 
              `email`       = '" . $this->db->escape($data['email']) . "',
              `birthday`    = '" . $this->db->escape($data['birthday']) . "', 
              `company`     = '" . $this->db->escape($data['company']) . "', 
              `sex`         = '" . $this->db->escape($data['sex']) . "', 
              `password`    = '" . $this->db->escape(md5(md5($password) . $suffix) . ':' . $suffix) . "',
              `activation_code` = '" . $this->db->escape(md5($data['email'])) . "',
              `customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "', 
              `status`      = '1', 
              `approved`    = '1', 
              `photo`       = '" . $this->db->escape($data['photo']) . "', 
              `google_oauth_id`    = '" . $this->db->escape($data['google_oauth_id']) . "', 
              `google_oauth_token` = '" . $this->db->escape($data['google_oauth_token']) . "', 
              `google_oauth_refresh` = '" . $this->db->escape($data['google_oauth_refresh']) . "', 
              `google_code` = '" . $this->db->escape($data['google_code']) . "', 
              `date_added`  = NOW()");

            $customer_id = $this->db->getLastId();

            if ((int)STORE_ID == 0) {
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '" . (int)STORE_ID . "', 
                  customer_id   = '" . (int)$customer_id . "'");
            } else {
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '" . (int)STORE_ID . "', 
                  customer_id   = '" . (int)$customer_id . "'");
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '0', 
                  customer_id   = '" . (int)$customer_id . "'");
            }
            return array('customer_id' => $customer_id, 'password' => $password);
        }
    }

    public function addCustomerFromMeli($data)
    {
        if (!$this->getTotalCustomersByEmail($data['email'])) {
            $suffix = base_convert(rand(10e16, 10e20), 10, 36); //TODO: agregar sufijo a las contrasenas
            $password = substr(md5(mt_rand()), 0, 6);
            $result = $this->db->query("INSERT INTO `" . DB_PREFIX . "customer` SET 
              `store_id`    = '" . (int)STORE_ID . "', 
              `firstname`   = '" . $this->db->escape($data['firstname']) . "', 
              `lastname`    = '" . $this->db->escape($data['lastname']) . "', 
              `telephone`   = '" . $this->db->escape($data['telephone']) . "', 
              `email`       = '" . $this->db->escape($data['email']) . "',
              `birthday`    = '" . $this->db->escape($data['birthday']) . "', 
              `company`     = '" . $this->db->escape($data['company']) . "', 
              `sex`         = '" . $this->db->escape($data['sex']) . "', 
              `password`    = '" . $this->db->escape(md5(md5($password) . $suffix) . ':' . $suffix) . "',
              `activation_code` = '" . $this->db->escape(md5($data['email'])) . "',
              `customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "', 
              `status`      = '1', 
              `approved`    = '1',
              `date_added`  = NOW()");

            $customer_id = $this->db->getLastId();

            $this->setProperty($customer_id, 'meli', 'meli_oauth_id', $data['meli_oauth_id']);
            $this->setProperty($customer_id, 'meli', 'meli_oauth_token', $data['meli_oauth_token']);
            $this->setProperty($customer_id, 'meli', 'meli_oauth_refresh', $data['meli_oauth_refresh']);
            $this->setProperty($customer_id, 'meli', 'meli_oauth_expire', $data['meli_oauth_expire']);
            $this->setProperty($customer_id, 'meli', 'meli_code', $data['meli_code']);

            if ((int)STORE_ID == 0) {
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '" . (int)STORE_ID . "', 
                  customer_id   = '" . (int)$customer_id . "'");
            } else {
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '" . (int)STORE_ID . "', 
                  customer_id   = '" . (int)$customer_id . "'");
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '0', 
                  customer_id   = '" . (int)$customer_id . "'");
            }
            return array('customer_id' => $customer_id, 'password' => $password);
        }
    }

    public function addCustomerFromLive($data)
    {
        if (!$this->getTotalCustomersByEmail($data['email'])) {
            $suffix = base_convert(rand(10e16, 10e20), 10, 36); //TODO: agregar sufijo a las contrasenas
            $password = substr(md5(mt_rand()), 0, 6);
            $result = $this->db->query("INSERT INTO `" . DB_PREFIX . "customer` SET 
              `store_id`    = '" . (int)STORE_ID . "', 
              `firstname`   = '" . $this->db->escape($data['firstname']) . "', 
              `lastname`    = '" . $this->db->escape($data['lastname']) . "', 
              `telephone`   = '" . $this->db->escape($data['telephone']) . "', 
              `email`       = '" . $this->db->escape($data['email']) . "',
              `birthday`    = '" . $this->db->escape($data['birthday']) . "', 
              `company`     = '" . $this->db->escape($data['company']) . "', 
              `sex`         = '" . $this->db->escape($data['sex']) . "', 
              `password`    = '" . $this->db->escape(md5(md5($password) . $suffix) . ':' . $suffix) . "',
              `activation_code` = '" . $this->db->escape(md5($data['email'])) . "',
              `customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "', 
              `status`      = '1', 
              `approved`    = '1', 
              `photo`       = '" . $this->db->escape($data['photo']) . "', 
              `live_oauth_id`    = '" . $this->db->escape($data['live_oauth_id']) . "', 
              `live_oauth_token` = '" . $this->db->escape($data['live_oauth_token']) . "',
              `live_code` = '" . $this->db->escape($data['live_code']) . "', 
              `date_added`  = NOW()");

            $customer_id = $this->db->getLastId();

            $this->setProperty($customer_id, 'live', 'live_oauth_id', $data['live_oauth_id']);
            $this->setProperty($customer_id, 'live', 'live_oauth_token', $data['live_oauth_token']);
            $this->setProperty($customer_id, 'live', 'live_code', $data['live_code']);

            if ((int)STORE_ID == 0) {
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '" . (int)STORE_ID . "', 
                  customer_id   = '" . (int)$customer_id . "'");
            } else {
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '" . (int)STORE_ID . "', 
                  customer_id   = '" . (int)$customer_id . "'");
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '0', 
                  customer_id   = '" . (int)$customer_id . "'");
            }
            return array('customer_id' => $customer_id, 'password' => $password);
        }
    }

    public function addCustomerFromFacebook($data)
    {
        if (!$this->getTotalCustomersByEmail($data['email'])) {
            $suffix = base_convert(rand(10e16, 10e20), 10, 36); //TODO: agregar sufijo a las contrasenas
            $password = substr(md5(mt_rand()), 0, 6);
            $result = $this->db->query("INSERT INTO `" . DB_PREFIX . "customer` SET 
              `store_id`    = '" . (int)STORE_ID . "', 
              `firstname`   = '" . $this->db->escape($data['firstname']) . "', 
              `lastname`    = '" . $this->db->escape($data['lastname']) . "', 
              `telephone`   = '" . $this->db->escape($data['telephone']) . "', 
              `email`       = '" . $this->db->escape($data['email']) . "',
              `birthday`    = '" . $this->db->escape($data['birthday']) . "', 
              `company`     = '" . $this->db->escape($data['company']) . "', 
              `sex`         = '" . $this->db->escape($data['sex']) . "', 
              `password`    = '" . $this->db->escape(md5(md5($password) . $suffix) . ':' . $suffix) . "',
              `activation_code` = '" . $this->db->escape(md5($data['email'])) . "',
              `customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "', 
              `status`      = '1', 
              `approved`    = '1', 
              `photo`       = '" . $this->db->escape($data['photo']) . "', 
              `facebook_oauth_id`    = '" . $this->db->escape($data['facebook_oauth_id']) . "', 
              `facebook_oauth_token` = '" . $this->db->escape($data['facebook_oauth_token']) . "',
              `facebook_code` = '" . $this->db->escape($data['facebook_code']) . "', 
              `date_added`  = NOW()");

            $customer_id = $this->db->getLastId();

            $this->setProperty($customer_id, 'facebook', 'facebook_oauth_id', $data['facebook_oauth_id']);
            $this->setProperty($customer_id, 'facebook', 'facebook_oauth_token', $data['facebook_oauth_token']);
            $this->setProperty($customer_id, 'facebook', 'facebook_code', $data['facebook_code']);

            if ((int)STORE_ID == 0) {
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '" . (int)STORE_ID . "', 
                  customer_id   = '" . (int)$customer_id . "'");
            } else {
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '" . (int)STORE_ID . "', 
                  customer_id   = '" . (int)$customer_id . "'");
                $result = $this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_to_store` SET 
                  `store_id`    = '0', 
                  customer_id   = '" . (int)$customer_id . "'");
            }
            return array('customer_id' => $customer_id, 'password' => $password);
        }
    }

    public function editCustomer($data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET 
        firstname = '" . $this->db->escape($data['firstname']) . "', 
        lastname = '" . $this->db->escape($data['lastname']) . "', 
        `birthday`    = '" . $this->db->escape($data['birthday']) . "', 
        telephone = '" . $this->db->escape($data['telephone']) . "', 
        fax = '" . $this->db->escape($data['fax']) . "', 
        sexo = '" . $this->db->escape($data['sexo']) . "', 
        blog = '" . $this->db->escape($data['blog']) . "', 
        website = '" . $this->db->escape($data['website']) . "', 
        profesion = '" . $this->db->escape($data['profesion']) . "', 
        titulo = '" . $this->db->escape($data['titulo']) . "', 
        msn = '" . $this->db->escape($data['msn']) . "', 
        gmail = '" . $this->db->escape($data['gmail']) . "', 
        yahoo = '" . $this->db->escape($data['yahoo']) . "', 
        skype = '" . $this->db->escape($data['skype']) . "', 
        facebook = '" . $this->db->escape($data['facebook']) . "', 
        twitter = '" . $this->db->escape($data['twitter']) . "', 
        foto = '" . $this->db->escape($data['foto']) . "',  
        rif = '" . $this->db->escape($data['rif']) . "',
        company = '" . $this->db->escape($data['company']) . "' 
        WHERE customer_id = '" . (int)$this->customer->getId() . "'");
    }

    public function addAddress($customer_id, $data)
    {

        $this->db->query("INSERT INTO " . DB_PREFIX . "address SET 
          customer_id = '" . (int)$customer_id . "', 
          firstname = '" . $this->db->escape($data['firstname']) . "', 
          lastname = '" . $this->db->escape($data['lastname']) . "', 
          company = '" . $this->db->escape($data['company']) . "', 
          address_1 = '" . $this->db->escape($data['address_1']) . "',
          city = '" . $this->db->escape($data['city']) . "', 
          street = '" . $this->db->escape($data['street']) . "', 
          postcode = '" . $this->db->escape($data['postcode']) . "', 
          country_id = '" . (int)$data['country_id'] . "', 
          zone_id = '" . (int)$data['zone_id'] . "'");

        $address_id = $this->db->getLastId();

        $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
        return $address_id;
    }
    
    public function addFoto($data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET 
        foto = '" . $this->db->escape($data['foto']) . "' 
        WHERE customer_id = '" . (int)$this->customer->getId() . "'");
    }

    public function completeUser()
    {
        $result = $this->db->query("UPDATE " . DB_PREFIX . "customer SET 
        complete = '1' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
        return $result;
    }

    public function editPassword($email, $password)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET 
          `password` = '" . $this->db->escape(md5($password)) . "' 
          WHERE `email` = '" . $this->db->escape($email) . "'");
    }

    public function editNewsletter($newsletter)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET 
        newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
    }

    public function getCustomer($customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
        return $query->row;
    }

    public function getCustomerByEmail($email)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
        return $query->row;
    }

    public function getTotalCustomersByEmail($email)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
        return $query->row['total'];
    }

    public function getCustomerByTwitter($data)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer 
        WHERE twitter_oauth_provider = 'twitter' 
        AND twitter_oauth_id = '" . intval($data['oauth_id']) . "' 
        AND twitter_oauth_token_secret = '" . $this->db->escape($data['oauth_token_secret']) . "'");

        return $query->row['total'];
    }

    public function getCustomerByGoogle($data)
    {
        $sql = "SELECT COUNT(*) AS total 
        FROM " . DB_PREFIX . "customer ";

        if (!empty($data['email'])) {
            $sql .= " WHERE email = '" . $this->db->escape($data['email']) . "'";
        } else {
            $sql .= " WHERE google_oauth_id = '" . $this->db->escape($data['google_oauth_id']) . "'";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getCustomerByLive($data)
    {
        $sql = "SELECT COUNT(*) AS total 
        FROM " . DB_PREFIX . "customer ";

        if (!empty($data['email'])) {
            $sql .= " WHERE email = '" . $this->db->escape($data['email']) . "'";
        } else {
            $sql .= " WHERE live_oauth_id = '" . $this->db->escape($data['live_oauth_id']) . "'";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getCustomerByMeli($data)
    {

        if (!empty($data['email'])) {
            $sql = "SELECT COUNT(*) AS total "
                . "FROM " . DB_PREFIX . "customer "
                . " WHERE email = '" . $this->db->escape($data['email']) . "'";
        } else {
            $sql = "SELECT COUNT(*) AS total "
                . "FROM " . DB_PREFIX . "property "
                . " WHERE `object_type` = 'customer' "
                . " AND `group` = 'meli' "
                . " AND `key` = 'meli_oauth_id' "
                . " AND `value` = '" . $this->db->escape($data['meli_oauth_id']) . "'";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function addTransferencia($data)
    {
        $strError = '';
        if (!$this->checkOrderStatus($data['order_id'])) {
            $strError .= "Estado Incorrecto";
            $error = true;
        }

        if ($data['forma_de_pago'] == 'Deposito') {
            if (!$this->checkPaymentMethod($data['order_id'], 'Cheque')) {
                $strError .= "<li>Lo siento, la forma de pago elegida para este pedido es diferente a <b>Dep&oacute;sito Bancario</b>.";
                $error = true;
            }
        }
        if ($data['forma_de_pago'] == 'Transferencia') {
            if (!$this->checkPaymentMethod($data['order_id'], 'Transferencia Bancaria')) {
                $strError .= "<li>Lo siento, la forma de pago elegida para este pedido es diferente a <b>Transferencia Bancaria</b>.";
                $error = true;
            }
        }

        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$data['order_id'] . "'");
        if ($order_query->num_rows) {
            if ($this->checkTransaccionID($data['order_id'], $order_query->row['customer_id'], $data['numero_transaccion'])) {
                $strError .= "<li>El n&uacute;mero de transacci&oacute;n ya existe.</li>";
                $error = true;
            }
        }

        if (!$this->checkFechaPago($data['order_id'], $data['fecha_pago'])) {
            $strError .= "<li>Por su seguridad, no puede reportar un pago con fecha inferior a la fecha del pedido.</li>";
            $error = true;
        }
        if (!$strError) {
            $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$data['order_id'] . "'");
            if ($order_query->num_rows) {
                $customer_id = $order_query->row['customer_id'];
                $resta = (float)$order_query->row['total'] - (float)$data['monto_cancelado'];
                $monto_a_devolver = 0;
                $monto_restante = 0;
                if ($resta > 0) {
                    $monto_restante = $resta;
                } elseif ($resta < 0) {
                    $monto_a_devolver = str_replace('-', '', $resta);
                }

                $pago_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pago` ORDER BY pago_id DESC LIMIT 1");
                if ($pago_query->num_rows) {
                    $pago_id = $pago_query->row['pago_id'] + 1;
                } else {
                    $pago_id = 1;
                }
                $codigo = sha1((int)$data['order_id'] . (int)$customer_id . $data['numero_transaccion']);
                $result = $this->db->query("INSERT INTO `" . DB_PREFIX . "pago` SET pago_id = '" . (int)$pago_id . "', order_id = '" . (int)$data['order_id'] . "', customer_id = '" . (int)$customer_id . "', numero_transaccion = '" . $this->db->escape($data['numero_transaccion']) . "', nombre = '" . $this->db->escape($data['nombre']) . "', mi_banco = '" . $this->db->escape($data['mi_banco']) . "', forma_de_pago = '" . $this->db->escape($data['forma_de_pago']) . "', tipo_deposito = '" . $this->db->escape($data['tipo_deposito']) . "', su_banco = '" . $this->db->escape($data['su_banco']) . "', monto_cancelado = '" . (float)$data['monto_cancelado'] . "', monto_del_pedido = '" . (float)$order_query->row['total'] . "', monto_a_devolver = '" . (float)$monto_a_devolver . "', monto_restante = '" . (float)$monto_restante . "', observacion = '" . $this->db->escape($data['observacion']) . "', codigo = '" . md5($codigo) . "', fecha_pago = '" . date('Y-m-d', strtotime($data['fecha_pago'])) . "', fecha_creado = now()");
                $pago_id = $this->db->getLastId();
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '7' WHERE order_id = '" . (int)$data['order_id'] . "'");
                return $result;
            }
        }
        $strError .= "<br><h3>Si posee alguna duda o pregunta sobre el proceso, por favor cont&aacute;ctenos.</h3>.";
        return $strError;
    }

    public function getTransferenciaByOrder($order_id)
    {
        $query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "transferencia WHERE order_id = '" . (int)$order_id . "'");
        return $query->row['total'];
    }

    public function getTransferencia($order_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pago WHERE order_id = '" . (int)$order_id . "'");
        return $query->row;
    }

    public function checkOrderStatus($order_id)
    {
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
        if (($order_query->row['order_status_id'] == 1) || ($order_query->row['order_status_id'] == 7)) {
            return true;
        }
    }

    public function checkPaymentMethod($order_id, $method, $language_id = 1)
    {
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `order_id` = '" . (int)$order_id . "' and `language_id` = '" . (int)$language_id . "'");
        if (($order_query->row['payment_method'] == $method)) {
            return true;
        }
    }

    public function checkTransaccionID($order_id, $customer_id, $transaccionID)
    {
        $pago_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pago`");
        if ($pago_query->num_rows) {
            $codigo = sha1($order_id . $customer_id . $transaccionID);
            foreach ($pago_query->rows as $value) {
                if (md5($codigo) == $value['codigo']) {
                    return true;
                }
            }
        }
    }

    public function checkFechaPago($order_id, $fecha)
    {
        if (!empty($order_id)) {
            $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
            if ($order_query->num_rows) {
                if ($fecha > date('d-m-Y', strtotime($order_query->row['date_added']))) {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getProperty($id, $group, $key) {
        return $this->__getProperty('customer', $id, $group, $key);
    }

    public function setProperty($id, $group, $key, $value) {
        return $this->__setProperty('customer', $id, $group, $key, $value);
    }

    public function deleteProperty($id, $group='*', $key='*') {
        return $this->__deleteProperties('customer', $id, $group, $key);
    }

    public function getAllProperties($id, $group = '*') {
        return $this->__getProperties('customer', $id, $group);
    }

    public function setAllProperties($id, $group, $data) {
        if (is_array($data) && !empty($data)) {
            $this->deleteProperty($id, $group);
            foreach ($data as $key => $value) {
                $this->setProperty($id, $group, $key, $value);
            }
        }
    }
}
