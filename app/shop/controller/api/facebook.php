<?php

require_once( DIR_SYSTEM . 'library/facebook/autoload.php' );


class ControllerApiFacebook extends Controller {

    protected $fb;
    protected $ftoken;

    private function initialize() {
        if ($this->config->get('social_facebook_app_id') && $this->config->get('social_facebook_app_secret')) {
            $this->fb = new Facebook\Facebook([
                'app_id' => $this->config->get('social_facebook_app_id'), // Replace {app-id} with your app id
                'app_secret' => $this->config->get('social_facebook_app_secret'),
                'default_graph_version' => 'v3.2'
            ]);
        } else {
            return false;
        }
    }

    public function index() {
        $Url = new Url($this->registry);
        if (!$this->initialize()) {
            if ($this->request->hasQuery('redirect')) {
                $_SESSION['fbaction'] = $this->request->getQuery('redirect');
            }
            if ($this->request->hasQuery('state')) {
                $_SESSION['fstate'] = $this->request->getQuery('state');
            }

            if ($this->request->hasQuery('code')) {
                $_SESSION['fcode'] = $this->request->getQuery('code');
                //$this->redirect($Url::createUrl('api/facebook'));
            }

            $redirect_uri = HTTP_HOME . 'api/facebook';
            $redirect_uri = str_replace('/web', '', $redirect_uri);

            $helper = $this->fb->getRedirectLoginHelper();


            if (isset($_SESSION['fcode'])) {
                $helper->getPersistentDataHandler()->set('code', $_SESSION['fcode']);
            }

            if (isset($_SESSION['fstate'])) {
                $helper->getPersistentDataHandler()->set('state', $_SESSION['fstate']);
            }

            $params = array(
                    'email',
                    'public_profile',
                    'publish_to_groups',
                    'publish_pages',
                    'user_posts',
                    'user_photos',
                    'pages_messaging',
                    'pages_messaging_subscriptions'
            );

            if (!isset($_SESSION['fcode']) && !$this->request->hasQuery('error_code')) {
                $this->redirect($helper->getLoginUrl($redirect_uri, $params));
            } elseif ($this->request->hasQuery('error_code')) {
                echo $this->request->getQuery('error_message');
                $this->redirect($Url::createUrl("account/login", array("error" => "No se pudo iniciar sesion utilizando Facebook, por favor intente con otro servicio")));
            }

            if (isset($_SESSION['ftoken'])) {
                $accessToken = new Facebook\Authentication\AccessToken($_SESSION['ftoken']);
            } else {
                try {
                    $accessToken = $helper->getAccessToken($redirect_uri);
                    if ($accessToken) $_SESSION['ftoken'] = $accessToken->getValue();
                    else if (!$_SESSION['ftoken']) $this->redirect($helper->getReRequestUrl($redirect_uri, $params));
                    else $this->redirect($helper->getReRequestUrl($redirect_uri, $params));
                    
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                    // When Graph returns an error
                    echo 'Graph returned an error: ' . $e->getMessage();
                    if ($e->getCode() == 100) {
                        $this->redirect($helper->getReRequestUrl($redirect_uri, $params));
                    }
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    // When validation fails or other local issues
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    exit;
                }
            }

            if ($this->fb) {

                $fbactions = array(
                    'login',
                    'promote'
                );
                if (in_array($_SESSION['fbaction'], $fbactions)) {

                    if ($_SESSION['fbaction'] == 'login') {
                        try {
                            $response = $this->fb->get('/me?fields=id,name,first_name,last_name,birthday,email', $_SESSION['ftoken']);
                            $this->login($response->getGraphUser());
                        } catch (Facebook\Exceptions\FacebookResponseException $e) {
                            echo __LINE__ . ': ' . $e->getCode() . '<br />';
                            echo __LINE__ . ': ' . $e->getMessage() . '<br />';
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                            echo __LINE__ . ': ' . $e->getCode() . '<br />';
                            echo __LINE__ . ': ' . $e->getMessage() . '<br />';
                        }
                    }

                    if ($_SESSION['fbaction'] === 'promote') {
                        $this->promote();
                    }
                } else {
                    unset($_SESSION['ftoken']);
                    unset($_SESSION['fcode']);
                    /*
                      if ($this->session->has('redirect')) {
                      $this->redirect($this->session->get('redirect'));
                      } else {
                      $this->redirect(HTTP_HOME);
                      }
                     */
                }
            } else {

                unset($_SESSION['ftoken']);
                unset($_SESSION['fcode']);
                if (!$this->request->hasQuery('error_code')) {
                    $this->redirect($helper->getLoginUrl($params));
                } else {
                    echo $this->request->getQuery('error_message');
                    //$this->redirect($Url::createUrl("account/login", array("error" => "No se pudo iniciar sesion utilizando Facebook, por favor intente con otro servicio")));
                }
            }
        }
    }

    public function login($fb) {

        if ($this->customer->isLogged()) {
            $this->redirect(Url::createUrl("account/account"));
        }

        if (!$this->customer->isLogged() && (!$this->config->get('social_facebook_app_id') || !$this->config->get('social_facebook_app_secret'))) {
            $this->redirect(Url::createUrl("account/login", array("error" => "No se pudo iniciar sesion utilizando Facebook, por favor intente con otro servicio")));
        }

        if ($fb) {
            $data = array(
                'email' => $fb->getEmail(),
                'company' => $fb->getName(),
                'firstname' => $fb->getFirstName(),
                'lastname' => $fb->getLastName(),
                'oauth_provider' => 'facebook',
                'facebook_oauth_id' => $fb->getId(),
                'facebook_oauth_token' => $_SESSION['ftoken'],
                'facebook_code' => $_SESSION['fcode']
            );
            $this->load->auto('account/customer');

            $result = $this->modelCustomer->getCustomerByEmail($fb->getEmail());

            if ($result) {
                if ($this->customer->loginWithFacebook($data)) {
                    if ($this->session->has('redirect')) {
                        $this->redirect(str_replace('&amp;', '&', $this->session->get('redirect')));
                    } else {
                        //$this->redirect(Url::createUrl("common/home"));
                    }
                } else {
                    //$this->redirect(Url::createUrl("account/login", array("error" => "No se pudo iniciar sesion utilizando Facebook, por favor intente con otro servicio")));
                }
            } elseif ($customer = $this->modelCustomer->addCustomerFromFacebook($data)) {
                //TODO: send welcome message
                //TODO: post in facebook wall just once saying
                //
                if ($this->config->get('marketing_email_send_password_and_welcome')) {
                    $this->load->model('marketing/newsletter');
                    $newsletter = $this->modelNewsletter->getById($this->config->get('marketing_email_send_password_and_welcome'));
                    if ($newsletter) {
                        $message = $this->prepareTemplate(html_entity_decode($newsletter['htmlbody']));
                        $message = str_replace('{%password%}', $customer['password'], $message);
                        $message = str_replace("{%fullname%}", $fb->getName(), $message);
                        $message = str_replace("{%rif%}", '', $message);
                        $message = str_replace("{%company%}", $fb->getName(), $message);
                        $message = str_replace("{%email%}", $fb->getEmail(), $message);
                        $message = str_replace("{%telephone%}", '', $message);
                        $this->load->library('email/mailer');
                        $this->mailer = new Mailer;
                        if ($this->config->get('config_smtp_method') == 'smtp') {
                            $this->mailer->IsSMTP();
                            $this->mailer->Hostname = $this->config->get('config_smtp_host');
                            $this->mailer->Username = $this->config->get('config_smtp_username');
                            $this->mailer->Password = base64_decode($this->config->get('config_smtp_password'));
                            $this->mailer->Port = $this->config->get('config_smtp_port');
                            $this->mailer->Timeout = $this->config->get('config_smtp_timeout');
                            $this->mailer->SMTPSecure = $this->config->get('config_smtp_ssl');
                            $this->mailer->SMTPAuth = ($this->config->get('config_smtp_auth')) ? true : false;
                        } elseif ($this->config->get('config_smtp_method') == 'sendmail') {
                            $this->mailer->IsSendmail();
                        } else {
                            $this->mailer->IsMail();
                        }

                        $this->mailer->AddAddress($fb->getEmail(), $fb->getName());
                        $this->mailer->IsHTML();
                        $this->mailer->SetFrom($this->config->get('config_email'), $this->config->get('config_name'));
                        $this->mailer->Subject = "Bienvenidos - " . $this->config->get('config_name');
                        $this->mailer->Body = $message;
                        $this->mailer->Send();
                    }
                }
                if ($this->customer->loginWithFacebook($data)) {
                    if ($this->session->has('redirect')) {
                        $this->redirect(str_replace('&amp;', '&', $this->session->get('redirect')));
                    } else {
                        $this->redirect(Url::createUrl("common/home"));
                    }
                } else {
                    $this->redirect(Url::createUrl("account/login", array("error" => "No se pudo iniciar sesion utilizando Facebook, por favor intente con otro servicio")));
                }
            }
        } else {
            $this->redirect(Url::createUrl("account/login", array("error" => "No se pudo iniciar sesion utilizando Facebook, por favor intente con otro servicio")));
        }
    }

    public function promote() {
        try {
            /*
            //posting in customer profile
            $response = $this->fb->post(
                '/me/feed', 
                [
                    'link' => 'https://www.necoyoad.com',
                    'message' => 'Test: Auto post from NecoTienda ' . date('d-m-Y h:i:s')
                ], 
                $_SESSION['ftoken']
            );
            */

            /*
            //posting in customer group
            $response = $this->fb->post(
                '/[facebook_group_id]/feed', 
                [
                    'link' => 'https://www.necoyoad.com',
                    'message' => 'Test: Auto post from NecoTienda ' . date('d-m-Y h:i:s')
                ], 
                $_SESSION['ftoken']
            );
            */

            /*
            //posting in customer managed page
            $response = $this->fb->post(
                '/[facebook_page_id]/feed', 
                [
                    'link' => 'https://www.necoyoad.com',
                    'message' => 'Test: Auto post from NecoTienda ' . date('d-m-Y h:i:s')
                ], 
                $_SESSION['ftoken']
            );
            */

            /*
            //getting user feed
            $response = $this->fb->get(
                '/me/feed',
                $_SESSION['ftoken']
            );
            */
            
            
            $graphObject = $response->getGraphEdge();
            echo "Posted with id: <a href=\"https://www.facebook.com/" . $graphObject->getId() . '">Ver Publicacion</a>';
            echo "Posted with id: <a href=\"https://www.facebook.com/" . $graphObject->getProperty('id') . '">Ver Publicacion</a>';

        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo __LINE__ . ': ' . $e->getCode() . '<br />';
            echo __LINE__ . ': ' . $e->getMessage() . '<br />';
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo __LINE__ . ': ' . $e->getCode() . '<br />';
            echo __LINE__ . ': ' . $e->getMessage() . '<br />';
        }
    }
}
