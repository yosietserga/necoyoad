<?php

/**
 * @author Inversiones Necoyoad, C.A.
 * @copyright 2010
 */

final class email extends EmailCore {

    /**
     * An array of recipient custom fields.
     *
     * @see AddCustomFieldInfo
     *
     * @var Array
     */
    var $_RecipientsCustomFields = [];

    /**
     * An array of recipient dynamic fields.
     *
     * @see AddDynamicContentInfo
     *
     * @var Array
     */
    var $_RecipientsDynamicFields = [];

    /**
     * ForceChecks
     * Whether we check for unsubscribe and modify details links or not.
     *
     * @see ForceLinkChecks
     * @see ForgetEmail
     * @see CheckUnsubscribeLink
     */
    var $forcechecks = false;

    /**
     * TrackLinks
     * Whether we track urls in the newsletter or not.
     *
     * @see TrackLinks
     * @see ForgetEmail
     */
    var $tracklinks = false;

    /**
     * TrackOpens
     * Whether we track email opens in the newsletter or not.
     *
     * @see TrackOpen
     * @see ForgetEmail
     */
    var $trackopens = false;

    /**
     * _FoundLinks
     * An array of links found in each part of the email contents. We can then check that against the database to see if it needs to be tracked.
     *
     * @see _GetLinks
     * @see TrackLinks
     * @see _ReplaceLinks
     */
    var $_FoundLinks = array('h' => array(), 't' => array(), 'm' => array());

    /**
     * checked_links
     * A boolean for checking whether the _GetLinks function has been called before or not.
     *
     * @see _GetLinks
     */
    var $checked_links = false;

    /**
     * _convertedlinks
     * An array of links that have been converted. This is used when replacing found links with new (temporary) links. It saves hitting the database over and over again.
     *
     * @see _ReplaceLinks
     * @see ForgetEmail
     *
     * @var Array
     */
    var $_convertedlinks = [];


    /**
     * disableunsubscribe
     * This is used by send-friend so unsubscribe, modify etc links don't work.
     *
     * @see DisableUnsubscribe
     * @see _ReplaceCustomFields
     */
    var $disableunsubscribe = false;

    /**
     * Put who sent the email in the headers.
     *
     * @see _SetupHeaders
     *
     * @var String
     */
    var $SentBy = false;

    /**
     * Put the listid in the headers.
     *
     * @see _SetupHeaders
     *
     * @var Array
     */
    var $listids = [];

    /**
     * Put the statid in the headers.
     *
     * @see _SetupHeaders
     *
     * @var Int
     */
    var $statid = 0;

    /**
     * Database reference.
     *
     * @see GetDb
     * @see SaveLink
     */
    var $Db = null;

    /**
     * Whether placeholders for an email have already been changed or not.
     *
     * @see _ChangePlaceholders
     */
    var $placeholders_changed = false;

    /**
     * Whether listid's have already been checked or not.
     *
     * @see CheckIntVars
     */
    var $ids_checked = false;
    
    var $mailer;
    var $config;

    /**
     * Constructor.
     *
     * Calls the parent constructor
     * Then sets up the class variable 'message_id_server' if sendstudio is set up.
     *
     * @see Email_API
     */
    function __construct($mailer,$config) {
        $this->mailer = $mailer;
        $this->config = $config;
        /*
        $this->safe_mode = (bool)ini_get('safe_mode');
        $this->use_curl = (bool)function_exists('curl_init');
        $this->allow_fopen = (bool)ini_get('allow_url_fopen');
        $this->memory_limit = (bool)function_exists('memory_get_usage');

        $this->LogFile = DIR_LOGS . 'email.debug.log';
        $this->MemoryLogFile = DIR_LOGS . 'email_memory.debug.log';

        $this->message_id_server = $_SERVER['SERVER_NAME'];
        $this->ServerURL = HTTP_CATALOG;
        $this->ServerRootDirectory = DIR_CATALOG;

        $this->_sendmailparameters = $this->config->get('config_mail_parameter');
        */
        if ($this->config->get('config_smtp_method') == 'smtp') {
            $this->mailer->IsSMTP();
        } elseif ($this->config->get('config_smtp_method') == 'sendmail') {
            $this->mailer->IsSendmail();
        } else {
            $this->mailer->IsMail();
        }
        if ($this->config->get('config_smtp_auth') && ((int)$this->config->get('config_smtp_auth') == 1)) {
            $this->mailer->SMTPAuth   = true;  
        } else {
            $this->mailer->SMTPAuth   = false;  
        }        
        if ($this->config->get('config_replyto_email')) {
            $this->mailer->AddReplyTo($this->config->get('config_replyto_email'),$this->config->get('config_name'));
        } else {
            $this->mailer->AddReplyTo($this->config->get('config_email'),$this->config->get('config_name'));
        }
        $this->mailer->SMTPSecure = $this->config->get('cofig_smtp_ssl');
        $this->mailer->Timeout    = $this->config->get('cofig_smtp_timeout');
        $this->mailer->Host       = $this->config->get('config_smtp_host');
        $this->mailer->Port       = $this->config->get('config_smtp_port');
        $this->mailer->Username   = $this->config->get('config_smtp_username');
        $this->mailer->Password   = base64_decode($this->config->get('config_smtp_password'));
        $this->mailer->IsHTML();
    }
    
    /**
     * email::_send()
     * Envia un correo de prueba para verificar la conexi�n y la inicializaci�n de la clase self 
     * 
     * @param array $data Todo el contenido del email a enviar
     * @return Boolean
     */
    public function _send($data) {        
        if (!is_array($data)) return 'Tipo de datos incorrecto<br>';
        $from_email = isset($data['from_email']) ? $data['from_email'] : $this->config->get('config_email');
        $from_name = isset($data['from_name']) ? $data['from_name'] : $this->config->get('config_name');
        $this->mailer->SetFrom($from_email, $from_name);
        
        $subject = isset ($data['subject']) ? $data['subject'] : 'Mensaje de ' . $this->config->get('config_name');
        if (is_array($data['to']) && !empty($data['to'])) {
            foreach ($data['to'] as $address) {
                $this->mailer->AddAddress($address['email'], $address['name']);
            }
        } else {
            return 'No hay destinatarios<br>';
        }
        if (array_key_exists('cc',$data)) {
          if (is_array($data['cc'])) {
            foreach ($data['cc'] as $address) {
                $this->mailer->AddCC($address['email'], $address['name']);
            }
          }
        }
        if (array_key_exists('bcc',$data)) {
          if (is_array($data['bcc'])) {
            foreach ($data['bcc'] as $address) {
                $this->mailer->AddBCC($address['email'], $address['name']);
            }
          }
        }
        if (!empty($data['alt_body'])) {
            $this->mailer->AltBody = $data['alt_body'];
        }
        if (!empty($data['body'])) {
            $this->mailer->MsgHTML(html_entity_decode($data['body']));
        } else {
            return 'El cuerpo del mensaje no puede estar vac&iacute;o<br>';
        }
        return $this->mailer->send();
    }
    
    /**
     * email::sendTest()
     * Envia un correo de prueba para verificar la conexi�n y la inicializaci�n de la clase self 
     * 
     * @return Void
     */
    public function sendTest() {
        echo "config_smtp_method = ".$this->config->get('config_smtp_method')."<br>\n";
        echo "config_smtp_auth = ".$this->config->get('config_smtp_auth')."<br>\n";
        echo "config_smtp_ssl = ".$this->config->get('config_smtp_ssl')."<br>\n";
        echo "config_smtp_timeout = ".$this->config->get('config_smtp_timeout')."<br>\n";
        echo "config_smtp_host = ".$this->config->get('config_smtp_host')."<br>\n";
        echo "config_smtp_port = ".$this->config->get('config_smtp_port')."<br>\n";        
        echo "config_smtp_password = ".base64_decode($this->config->get('config_smtp_password'))."<br>\n";
        
        try {
            $this->mailer->SMTPDebug  = 2;        
            $this->mailer->SetFrom($this->config->get('config_email'), $this->config->get('config_name'));
            $this->mailer->AddAddress($this->config->get('config_email'), $this->config->get('config_name'));
            $this->mailer->Subject = 'Test from newsletter controller';
            $this->mailer->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
            $this->mailer->MsgHTML('Probando la inicializaci�n de la clase email()');
            $this->mailer->send();
        } catch (phpmailerException $e) {
            echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
        }
    }

    /**
     * InsertAtEnd
     * Inserts some HTML at the end of an HTML document but before the </body></html> tags (if applicable).
     *
     * @return String $body with $item appended at the end, but without breaking the HTML.
     */
    function InsertAtEnd($body, $item)
    {
        if (preg_match('%</body>%i', $body)) {
            return preg_replace('%</body>%i', $item . '</body>', $body);
        }
        if (preg_match('%</html>%i', $body)) {
            return preg_replace('%</html>%i', $item . '</body></html>', $body);
        }
        return $body . "\n\n" . $item . '</body></html>';
    }

    /**
     * TrackLinks
     * Whether to track links or not. This is off by default.
     *
     * @param Boolean $tracklinks Whether to track links or not.
     *
     * @see tracklinks
     *
     * @return Void Sets the class var, doesn't return anything.
     */
    function TrackLinks($tracklinks = false)
    {
        $this->tracklinks = (bool)$tracklinks;
    }

    /**
     * TrackOpens
     * Whether to track email opens or not. This is off by default.
     *
     * @param Boolean $trackopens Whether to track opens or not.
     *
     * @see trackopens
     *
     * @return Void Sets the class var, doesn't return anything.
     */
    function TrackOpens($trackopens = false)
    {
        $this->trackopens = (bool)$trackopens;
    }

    /**
     * Send
     * Overrides parent class's method to add custom field replacement and tracking.
     *
     * @return Array Result status and error messages.
     */
    function Send($replace = false, $disconnect_from_smtp = true)
    {

        $this->DebugMemUsage('sending');

        $extra_headers = [];

        $this->listids = $this->CheckIntVars($this->listids);

        if (sizeof($this->listids) > 0) {
            $extra_headers[] = 'X-Mailer-LID: ' . implode(',', $this->listids);
        }

        if ((int)$this->statid > 0) {
            $extra_headers[] = 'List-Unsubscribe: <%%HEADER_UNSUBSCRIBELINK%%>';
            $extra_headers[] = 'X-Mailer-RecptId: %%HEADER_SUBSCRIBERID%%';
            $extra_headers[] = 'X-Mailer-SID: ' . (int)$this->statid;
        }

        if ($this->SentBy !== false) {
            $header = 'X-Mailer-Sent-By: ';
            if (!is_numeric($this->SentBy)) {
                $header .= '"' . $this->SentBy . '"';
            } else {
                $header .= $this->SentBy;
            }
            $extra_headers[] = $header;
        }
        $this->extra_headers = $extra_headers;

        $results = array('success' => 0, 'fail' => array());

        $this->DebugMemUsage();

        $this->_ChangePlaceholders();

        $this->DebugMemUsage();

        $this->_AddOpenTrack();

        $this->DebugMemUsage();

        $this->_GetLinks();

        $this->DebugMemUsage();

        $this->CheckUnsubscribeLink();

        $this->DebugMemUsage();

        $headers = $this->_SetupHeaders();

        $this->DebugMemUsage();

        $this->_SetupAttachments();

        $this->DebugMemUsage();

        $this->_SetupImages();

        $this->DebugMemUsage();

        $body = $this->_SetupBody();

        $this->DebugMemUsage();

        $stop_sending_the_rest = false;
        $stop_sending_reson = '';

        foreach ($this->_Recipients as $p => $details) {
            $this->DebugMemUsage();

            $rcpt_to = $details['address'];

            if ($stop_sending_the_rest) {
                $results['fail'][] = array($rcpt_to, $stop_sending_reson);
                continue;
            }

            $to = $details['address'];
            if ($details['name']) {
                $to = '"' . $this->_utf8_encode($details['name']) . '" <' . $to . '>';
            }

            $headers = $this->_GetHeaders($details['format']);
            $body = $this->_GetBody($details['format']);

            if (!$headers || !$body) {
                $results['fail'][] = array($rcpt_to, 'BlankEmail');
                continue;
            }

            $this->DebugMemUsage();

            if ($replace) {
                $this->_ReplaceDynamicContentFields($body, $details['subscriberid']);
            }

            if ($replace) {
                $this->_ReplaceCustomFields($body, $rcpt_to);
            }

            $this->DebugMemUsage();

            $subject = $this->Subject;

            if ($replace) {
                $this->_ReplaceCustomFields($subject, $rcpt_to);
            }

            $this->DebugMemUsage();

            if ($replace) {
                $this->_ReplaceCustomFields($headers, $rcpt_to, true);
            }

            $this->DebugMemUsage();

            $this->_ReplaceLinks($body, $rcpt_to);

            $this->DebugMemUsage();

            list($mail_result, $reason) = $this->_Send_Recipient($to, $rcpt_to, $subject, $details['format'],
                $headers, $body);

            $this->DebugMemUsage();

            if ($mail_result) {
                $results['success']++;
            } else {
                $results['fail'][] = array($rcpt_to, $reason);

                /**
                 * The following condition is made so that the script will not try to send the rest of the email out.
                 * This is because we do not want to hammer the SMTP server when it is down.
                 * Or when there isn't enough space in the SMTP server to queue our message.
                 *
                 * TODO: I'm not sure if we should STOP sending instead of marking them as failed.
                 * However, there isn't enough feedback to the intterface that will allow me to
                 * stop this AND give feedback at the same time.
                 */
                if (in_array($this->ErrorCodeSMTPEnhanced, array('4.3.1'))) {
                    $stop_sending_the_rest = true;
                    $stop_sending_reson = $reason;
                }
            }

            if (isset($this->_RecipientsCustomFields[$rcpt_to])) {
                unset($this->_RecipientsCustomFields[$rcpt_to]);
            }

            $this->DebugMemUsage();

        }

        if ($disconnect_from_smtp) {
            $this->_Close_Smtp_Connection();
        }
        return $results;
    }

    /**
     * ForgetEmail
     * Forgets the email settings ready for another send.
     *
     * @return Void Doesn't return anything.
     */
    function ForgetEmail()
    {
        $this->ids_checked = false;
        $this->placeholders_changed = false;
        $this->checked_links = false;

        $this->listids = [];
        $this->statid = 0;

        $this->forcechecks = false;

        $this->tracklinks = false;

        $this->trackopens = false;

        $this->_FoundLinks = array('h' => array(), 't' => array(), 'm' => array());

        $this->_convertedlinks = [];

        $this->disableunsubscribe = false;

        $this->SentBy = false;

        parent::ForgetEmail();
    }

    /**
     * CheckIntVars
     * This goes through the array passed in and strips out any non-numeric characters. This can then be used safely for implodes for searching particular listid's or subscriberid's without worrying about sql injection.
     * create_function creates a dynamic function so we don't have another function to call inside this one.
     * Quoted numbers such as '2' or "11" will get returned without the quotes as per is_numeric functionality.
     *
     * <b>Example</b>
     * <code>
     * $vals = array(1,'12', 'f', "string");
     * $vals = CheckIntVars($vals);
     * </code>
     * This will become:
     * <code>
     * $vals = array(1, 12);
     * </code>
     *
     * @param Array $array_to_check Array of values to check and make sure they are integers.
     *
     * @see RemoveBannedEmails
     * @see RemoveUnsubscribedEmails
     * @see RemoveFromQueue
     * @see MarkAsProcessed
     *
     * @return Array Array of values which are numbers only. All non-numeric characters or strings are removed.
     */
    function CheckIntVars($array_to_check = array())
    {
        if ($this->ids_checked) {
            return $array_to_check;
        }

        if (!is_array($array_to_check)) {
            return [];
        }
        foreach ($array_to_check as $p => $var) {
            if (!is_numeric($var)) {
                unset($array_to_check[$p]);
            }
        }
        $this->ids_checked = true;
        return $array_to_check;
    }

    /**
     * SaveLink
     * Saves a url into the database for tracking purposes. This is then loaded later on to redirect to the right url.
     * This checks whether the url has already been saved. If it has, it will return that linkid. If it has not, it will save it and then return the new id.
     *
     * @param String $url The URL to save into the database.
     * @param Int $statid The statid to save the url for.
     *
     * @see GetDb
     *
     * @return Int Returns either the existing linkid from the database or the newly created one.
     */
    function SaveLink($url = '', $statid = 0)
    {
        $this->GetDb();

        $url = html_entity_decode(trim($url));

        $query = "SELECT l.linkid AS linkid, statid FROM " . SENDSTUDIO_TABLEPREFIX .
            "links l LEFT OUTER JOIN " . SENDSTUDIO_TABLEPREFIX .
            "stats_links sl ON l.linkid=sl.linkid WHERE ";

        /**
         * The binary check is needed because if you have two links like:
         * https://domain.com/Path and https://domain.com/path
         * mysql treats them as the same even though they have different cases
         * The binary keyword makes it check the case as well as the actual url.
         */
        if (SENDSTUDIO_DATABASE_TYPE == 'mysql') {
            $query .= " BINARY ";
        }
        $query .= " url='" . $this->Db->Quote($url) . "'";

        $result = $this->Db->Query($query);

        $linkid = false;
        while ($row = $this->Db->Fetch($result)) {
            // if the link is already stored for this particular stat, return the linkid straight away.
            if ($row['statid'] == $statid) {
                return $row['linkid'];
            }
            $linkid = $row['linkid'];
        }

        // if we get into the loop over $row, then we found the link.
        // which sets found_link to true.
        if (!$linkid) {
            $query = "INSERT INTO " . SENDSTUDIO_TABLEPREFIX . "links(url) VALUES ('" . $this->
                Db->Quote($url) . "')";
            $this->Db->Query($query);
            $linkid = $this->Db->LastId(SENDSTUDIO_TABLEPREFIX . 'links_sequence');
        }

        if ($statid) {
            $query = "INSERT INTO " . SENDSTUDIO_TABLEPREFIX .
                "stats_links(linkid, statid) VALUES ('" . $this->Db->Quote($linkid) . "', '" . $this->
                Db->Quote($statid) . "')";
            $this->Db->Query($query);
        }

        return $linkid;
    }

    /**
     * GetDb
     * Sets up the database object for this and the child objects to use.
     * If the Db var is already set up and the connection is a valid resource, this will return true straight away.
     * If the Db var is null or the connection is not valid, it will fetch it and store it for easy reference.
     * If it's unable to setup the database (or it's null or false) it will trigger an error.
     *
     * @see Db
     * @see IEM::getDatabase()
     *
     * @return Boolean True if it works or false if it fails. Failing also triggers a fatal error.
     */
    function GetDb()
    {
        if (is_object($this->Db) && is_resource($this->Db->connection)) {
            return true;
        }

        if (is_null($this->Db) || !$this->Db->connection) {
            $Db = IEM::getDatabase();
            $this->Db = &$Db;
        }

        if (!is_object($this->Db) || !is_resource($this->Db->connection)) {
            trigger_error('Unable to connect to database', SENDSTUDIO_ERROR_FATAL);
            return false;
        }
        return true;
    }


    public function getSpamRules()
    {
        $this->general = new General();
        $spam_rule_files = $this->general->listFiles(DIR_SYSTEM .
            'library/email/spam_rules');

        foreach ($spam_rule_files as $spam_rule) {
            $filename_parts = pathinfo($spam_rule);
            if (isset($filename_parts['extension']) && $filename_parts['extension'] == 'php') {
                require (DIR_SYSTEM . 'library/email/spam_rules/' . $spam_rule);
            }
        }

        $this->rules = &$GLOBALS['Spam_Rules'];
    }

    public function checkEmailForSpam($content)
    {
        $this->getSpamRules();
        $score = 0;
        $broken_rules = [];

        foreach ($this->rules as $category) {
            // TODO: chequear para spam para el contenido de las cabeceras y asunto del email
            foreach ($category['body'] as $rule) {
                if (preg_match($rule[0], $content)) {
                    $score += $rule[2];
                    $broken_rules[] = array($rule[1], $rule[2]);
                }
            }
        }
        return array('score' => $score, 'broken_rules' => $broken_rules, );
    }

    public function processEmailForSpam($text, $html = false)
    {
        $broken_rules = [];
        $score = [];
        foreach (array('text', 'html') as $type) {
            $broken_rules[$type] = [];
            $score[$type] = 0;
            $content = $$type;
            if (!$content) {
                continue;
            }
            $result = $this->checkEmailForSpam($content);
            $score[$type] = $result['score'];
            $broken_rules[$type] = $result['broken_rules'];
        }
        if (empty($text)) {
            // Spamassasin has a rule where emails must always contains "text" part.
            $score['text'] += 1.2;
            $broken_rules['text'][] = array('Message only has text/html MIME parts', '1.2');
        }

        // Spamassasin has a rule where HTML and TEXT contents should be similar.
        if (!empty($html) && !empty($text)) {
            $similarity = 0;
            similar_text(strip_tags($html), $text, $similarity);
            if ($similarity < 50) {
                $rule = array('HTML and text parts are different', '1.5');
                $broken_rules['text'][] = $rule;
                $broken_rules['html'][] = $rule;
                $score['text'] += 1.5;
                $score['html'] += 1.5;
            }
        }

        // Determine final spam rating based on the score
        $rating = [];
        $rating_list = array(self::RATING_NOT_SPAM, self::RATING_ALERT, self::
            RATING_SPAM);
        foreach (array('text', 'html') as $type) {
            foreach ($rating_list as $rating_score) {
                if ($score[$type] >= $rating_score) {
                    $rating[$type] = $rating_score;
                }
            }
        }

        // Return the appropriate results.
        $result = array('text' => array(), 'html' => array());

        foreach (array('text', 'html') as $type) {
            if (empty($$type) && $score[$type] == 0) {
                continue;
            }
            $result[$type] = array('rating' => $rating[$type], 'score' => $score[$type],
                'broken_rules' => $broken_rules[$type], );
        }
        return $result;
    }


}

class EmailCore
{
    private $rules = [];
    private $general;
    private $config;

    const RATING_NOT_SPAM = 0;
    const RATING_ALERT = 4;
    const RATING_SPAM = 5;


    var $_newline = "\n";

    /**
     * Boundary between parts. Used with multipart emails and also if there are any attachments.
     *
     * @see _SetupBody
     *
     * @var String
     */
    var $_Boundaries = [];


    /**
     * The base href found in the email body.
     *
     * @see _GetBaseHref
     * @var String
     */
    var $_basehref = HTTP_CATALOG;

    /**
     * The From email address.
     *
     * @var String
     */
    var $FromEmail;

    /**
     * The From name.
     *
     * @var String
     */
    var $FromName;

    /**
     * The bounce email address (used if safe-mode is off).
     *
     * @var String
     */
    var $BounceEmail;

    /**
     * The reply-to email address.
     *
     * @var String
     */
    var $ReplyTo;

    /**
     * The subject of the email.
     *
     * @var String
     */
    var $Subject;

    /**
     * The character set of the email.
     *
     * @var String
     */
    var $CharSet = 'iso-8859-1';

    /**
     * The content encoding of the email.
     *
     * @var String
     */
    var $ContentEncoding = '7bit';

    /**
     * SMTP Server Information. The server name to connect to.
     *
     * @see SetSmtp
     * @see _Send_SMTP
     * @see _Put_Smtp_Connection
     * @see _get_response
     * @see _Get_Smtp_Connection
     * @see _Close_Smtp_Connection
     *
     * @var String
     */
    var $SMTPHost = false;

    /**
     * SMTP Server Information. The smtp username used for authentication.
     *
     * @see SetSmtp
     * @see _Send_SMTP
     * @see _Put_Smtp_Connection
     * @see _get_response
     * @see _Get_Smtp_Connection
     * @see _Close_Smtp_Connection
     *
     * @var String
     */
    var $SMTPUsername = false;

    /**
     * SMTP Server Information. The smtp password used for authentication.
     *
     * @see SetSmtp
     * @see _Send_SMTP
     * @see _Put_Smtp_Connection
     * @see _get_response
     * @see _Get_Smtp_Connection
     * @see _Close_Smtp_Connection
     *
     * @var String
     */
    var $SMTPPassword = false;

    /**
     * SMTP Server Information. The smtp port number.
     *
     * @see SetSmtp
     * @see _Send_SMTP
     * @see _Put_Smtp_Connection
     * @see _get_response
     * @see _Get_Smtp_Connection
     * @see _Close_Smtp_Connection
     *
     * @var Int
     */
    var $SMTPPort = 25;

    /**
     * Whether to use SMTP Pipelining or not. Pipelining is described in RFC 2920.
     *
     * @see _Get_Smtp_Connection
     * @see _Send_SMTP
     *
     * @var Boolean
     */
    var $_SMTPPipeline = false;

    /**
     * An array of recipients to send the email to. You go through this one by one and send emails individually.
     *
     * @var Array
     */
    var $_Recipients = [];

    /**
     * Sendmail parameters is used to temporarily store the sendmail-from information.
     * Should only be set up once per sending session.
     *
     * @see _Send_Email
     *
     * @var String
     */
    var $_sendmailparameters = null;

    /**
     * SMTP connection to see if we are connected to the smtp server. By default this is null.
     * When you reach _smtp_max_email_count it will drop the connection and re-establish it.
     *
     * @see _Send_SMTP
     * @see _Put_Smtp_Connection
     * @see _get_response
     * @see _Get_Smtp_Connection
     * @see _Close_Smtp_Connection
     * @see _smtp_max_email_count
     *
     * @var String
     */
    var $_smtp_connection = null;

    /**
     * Max number of emails to send per smtp connection.
     *
     * @see _Send_SMTP
     * @see _Put_Smtp_Connection
     * @see _get_response
     * @see _Get_Smtp_Connection
     * @see _Close_Smtp_Connection
     *
     * @var Int
     */
    var $_smtp_max_email_count = 50;

    /**
     * Number of emails that have been sent with this particular smtp connection. Gets reset after a set number of emails.
     *
     * @see _smtp_max_email_count
     * @see _Send_SMTP
     * @see _Put_Smtp_Connection
     * @see _get_response
     * @see _Get_Smtp_Connection
     * @see _Close_Smtp_Connection
     *
     * @var Int
     */
    var $_smtp_email_count = 0;

    /**
     * Newline characters for the smtp servers to use.
     *
     * @see _smtp_max_email_count
     * @see _Send_SMTP
     * @see _Put_Smtp_Connection
     * @see _get_response
     * @see _Get_Smtp_Connection
     * @see _Close_Smtp_Connection
     *
     * @var String
     */
    var $_smtp_newline = "\r\n";

    /**
     * wrap_length
     * The number of characters to wrap the emails at.
     * RFC 2822 says lines can be longer than 72 characters but no more than 988 characters (under "2.1.1. Line Length Limits").
     *
     * @var Int
     *
     * @see _SetupBody
     * @see https://www.faqs.org/rfcs/rfc2822.html
     */
    var $wrap_length = 75;

    /**
     * extra_headers
     * In case we need any extra email headers.
     * These are without any newlines.
     *
     * @var Array
     *
     * @see _SetupHeaders
     */
    var $extra_headers = [];

    /**
     * message_id_server
     * The server the message is coming from.
     * This defaults to 'localhost.localdomain', but should be overwritten where possible.
     *
     * @var String
     *
     * @see _SetupHeaders
     */
    var $message_id_server = 'localhost.localdomain';

    /**
     * memory_limit
     * Whether the server has 'memory_get_usage' available or not.
     * This is only used for debugging.
     */
    var $memory_limit = false;

    /**
     * ServerRootDirectory
     * The server root directory
     */
    var $ServerRootDirectory = '';

    /**
     * ServerURL
     * The server url
     */
    var $ServerURL = '';

    /**
     * Holds error description from a failed sending
     * @var String
     */
    var $Error = '';

    /**
     * Holds error code from a failed sending
     * @var String
     */
    var $ErrorCode = false;

    /**
     * New "enhanced" SMTP status code were defined in RFC5248
     * @var String
     */
    var $ErrorCodeSMTPEnhanced = false;
    var $mailer;
    /**
     * Constructor
     * Sets up the logfile in case debug mode gets switched on.
     *
     * @return Void Doesn't return anything.
     */
    function __construct() {
        $this->mailer = new Mailer();
        $this->config = new Config();
        $this->safe_mode = (bool)ini_get('safe_mode');
        $this->use_curl = (bool)function_exists('curl_init');
        $this->allow_fopen = (bool)ini_get('allow_url_fopen');
        $this->memory_limit = (bool)function_exists('memory_get_usage');

        $this->LogFile = DIR_LOGS . 'email.debug.log';
        $this->MemoryLogFile = DIR_LOGS . 'email_memory.debug.log';

        $this->message_id_server = $_SERVER['SERVER_NAME'];
        $this->ServerURL = HTTP_CATALOG;
        $this->ServerRootDirectory = DIR_CATALOG;

        $this->SMTPHost = $this->config->get('config_smtp_host');
        $this->SMTPUsername = $this->config->get('config_smtp_username');
        $this->SMTPPassword = $this->config->get('config_smtp_password');
        $this->SMTPPort = $this->config->get('config_smtp_port');
        $this->_sendmailparameters = $this->config->get('config_mail_parameter');
        
        if ($this->config->get('config_smtp_method') == 'smtp') {
            $this->mailer->IsSMTP();
        } elseif ($this->config->get('config_smtp_method') == 'sendmail') {
            $this->mailer->IsSendmail();
        } else {
            $this->mailer->IsMail();
        }
        if ($this->config->get('config_smtp_auth') && ((int)$this->config->get('config_smtp_auth') == 1)) {
            $this->mailer->SMTPAuth   = true;  
        } else {
            $this->mailer->SMTPAuth   = false;  
        }
        $this->mailer->Host       = $this->SMTPHost;
        $this->mailer->Port       = $this->SMTPPort;
        $this->mailer->Username   = $this->SMTPUsername;
        $this->mailer->Password   = base64_decode($this->SMTPPassword);
    }

    /**
     * From
     * Set the from email and name in one go.
     *
     * @param String $email The email address to set the from address to.
     * @param String $name The name to set the from name to.
     *
     * @return Boolean Returns false if it can't be set (invalid data), or true if it worked.
     */
    function From($email = '', $name = '')
    {
        if (!preg_match("%\n|\r%", $name) && !preg_match("%\n|\r%", $email)) {
            $this->Set('ReplyTo', $email);
            $this->Set('BounceEmail', $email);
            $this->Set('FromEmail', $email);
            if (!empty($name)) {
                $this->Set('FromName', $name);
            }
            return true;
        }
        return false;
    }

    /**
     * Adds an address and name to the list of recipients to email.
     *
     * @param String $address Email Address to add.
     * @param String $name Their name (if applicable). This is checked before constructing the email to make sure it's available.
     * @param String $format Which format the recipient wants to receive. Either 'h' or 't'.
     *
     * @see _Recipients
     *
     * @return Void Doesn't return anything - just adds the information to the _Recipients array.
     */
    function AddRecipient($address, $name = '', $format = 'h')
    {
        $curr = count($this->_Recipients);
        $this->_Recipients[$curr]['address'] = trim($address);
        $this->_Recipients[$curr]['name'] = $name;
        $this->_Recipients[$curr]['format'] = strtolower($format);
    }

    /**
     * ClearRecipients
     * Clears out all recipients for the email. Useful if you want to send emails one by one.
     *
     * @see _Recipients
     * @see _RecipientsCustomFields
     *
     * @return Void Doesn't return anything - just empties out the recipients information.
     */
    function ClearRecipients()
    {
        $this->_Recipients = [];
    }

    /**
     * _SetupHeaders
     * Sets up the headers for each type ('m'ultipart, 't'ext and 'h'tml).
     * Each type is slightly different with different requirements for boundaries and content-type's.
     * We also set up all of the boundaries here.
     *
     * @return Void Doesn't return anything, everything gets stored in the _AssembledEmail['Headers'] array.
     */
    function _SetupHeaders()
    {
        // the headers are already set up? Just return.
        // one or the other must have been set up, this handles whether the email is text or html.
        if (!is_null($this->_AssembledEmail['Headers']['t']) || !is_null($this->
            _AssembledEmail['Headers']['h'])) {
            return;
        }

        if (strtolower($this->CharSet) == 'utf-8') {
            $this->ContentEncoding = '8bit';
        }

        $this->_AssembledEmail['Headers']['m'] = null;
        $this->_AssembledEmail['Headers']['t'] = null;
        $this->_AssembledEmail['Headers']['h'] = null;

        $this->DebugMemUsage('assembling headers');

        $headers = 'Return-Path: ' . $this->BounceEmail . $this->_newline;

        $headers .= 'Date: ' . date('r') . $this->_newline;

        $headers .= 'From: ';
        if ($this->FromName) {
            $headers .= '"' . $this->_utf8_encode($this->FromName) . '" ';
        }

        $headers .= '<' . $this->FromEmail . '>' . $this->_newline;

        $headers .= 'Reply-To: ' . $this->ReplyTo . $this->_newline;

        $semi_rand = md5(uniqid('ntb', true));

        $mime_boundary = 'b1_' . $semi_rand;

        $this->_Boundaries = array($mime_boundary);
        $this->_Boundaries[] = str_replace('b1_', 'b2_', $mime_boundary);
        $this->_Boundaries[] = str_replace('b1_', 'b3_', $mime_boundary);

        $headers .= 'MIME-Version: 1.0' . $this->_newline;

        if (!empty($this->extra_headers)) {
            foreach ($this->extra_headers as $p => $header) {
                $headers .= $header . $this->_newline;
            }
        }

        $multipart_headers = $headers;
        $html_headers = $headers;
        $text_headers = $headers;

        if (!empty($this->_EmbeddedImages)) {
            $this->EmbedImages = true;
            $this->_SaveImages();
        } else {
            $this->EmbedImages = false;
        }

        $html_headers .= 'Content-Type: text/html';
        $multipart_headers .= 'Content-Type: multipart/alternative';
        $text_headers .= 'Content-Type: text/plain; format=flowed';


        $html_headers .= '; charset="' . $this->CharSet . '"';
        $text_headers .= '; charset="' . $this->CharSet . '"';
        $multipart_headers .= '; charset="' . $this->CharSet . '"';

        // regardless of whether we are adding the boundary,
        // we need a newline anyway before the content-transfer-encoding.
        $html_headers .= $this->_newline;
        $text_headers .= $this->_newline;

        // multipart headers always need the boundary, so we'll do it now.
        $multipart_headers .= "; boundary=" . '"' . $mime_boundary . '"' . $this->
            _newline;

        $html_headers .= 'Content-Transfer-Encoding: ' . $this->ContentEncoding . $this->
            _newline;
        $text_headers .= 'Content-Transfer-Encoding: ' . $this->ContentEncoding . $this->
            _newline;
        $multipart_headers .= 'Content-Transfer-Encoding: ' . $this->ContentEncoding . $this->
            _newline;

        if ($add_boundary) {
            $html_headers .= "Content-Disposition: inline" . $this->_newline;
            $text_headers .= "Content-Disposition: inline" . $this->_newline;
            $multipart_headers .= "Content-Disposition: inline" . $this->_newline;
        }

        if ($this->Debug) {
            error_log('Line ' . __line__ . '; time ' . time() . '; html_headers: ' . $html_headers .
                "\n", 3, $this->LogFile);
            error_log('Line ' . __line__ . '; time ' . time() . '; text_headers: ' . $text_headers .
                "\n", 3, $this->LogFile);
            error_log('Line ' . __line__ . '; time ' . time() . '; multipart_headers: ' . $multipart_headers .
                "\n", 3, $this->LogFile);
            if ($this->memory_limit) {
                error_log(basename(__file__) . "\t" . __line__ . "\t" . __function__ . "\t" .
                    number_format((memory_get_usage() / 1024), 5) . "\n", 3, $this->MemoryLogFile);
            }
        }

        $this->_AssembledEmail['Headers']['m'] = $multipart_headers;
        $this->_AssembledEmail['Headers']['t'] = $text_headers;
        $this->_AssembledEmail['Headers']['h'] = $html_headers;
    }

    /**
     * _JoinBody
     * Returns the body with the correct content type, character set, encoding and boundaries set up.
     *
     * The only time the boundary won't be set to '2' will be when there is a multipart email with no attachments and no embedded images.
     *
     * @param String $type The type of body we're trying to join together ('h'tml or 't'ext).
     * @param Int $boundary The boundary to put around the content.
     * @param Boolean $add_bottom_boundary Whether to add the bottom boundary or not. Multipart emails don't need the bottom boundary between the text/html components, but attachments/embedded images do need it.
     *
     * @see _Boundaries
     * @see CharSet
     * @see ContentEncoding
     * @see body
     *
     * @return String The body with the content type, character set, encoding and boundaries set up.
     */
    function _JoinBody($type = '', $boundary = 2, $add_bottom_boundary = true)
    {
        $type = strtolower($type{0});
        $content_type = ($type == 'h') ? 'text/html' : 'text/plain; format=flowed';
        $body = '';
        $body .= '--' . $this->_Boundaries[$boundary] . $this->_newline;

        $body .= 'Content-Type: ' . $content_type . '; charset="' . $this->CharSet . '"' .
            $this->_newline;
        $body .= 'Content-Transfer-Encoding: ' . $this->ContentEncoding . $this->
            _newline . $this->_newline;

        $body .= $this->body[$type];
        $body .= $this->_newline . $this->_newline;
        if ($add_bottom_boundary) {
            $body .= '--' . $this->_Boundaries[$boundary] . '--' . $this->_newline;
        }
        return $body;
    }

    /**
     * _GetHeaders
     * Gets the assembled headers based on the format of the recipient.
     * If multipart is enabled (and both body types are available), then the multipart header is always returned.
     * If the recipient prefers html, make sure that the header has been assembled & there is a html body present.
     * If neither of those conditions are true, then return the text headers.
     *
     * @param String $format The preferred format of the recipient.
     *
     * @see Multipart
     * @see _SetupBody
     * @see _SetupHeaders
     * @see _AssembledEmail
     *
     * @return String Returns the right header for the email based on the format.
     */
    function _GetHeaders($format = '')
    {
        $semi_rand = md5(uniqid('ntb', true));

        $message_id = 'Message-ID: <' . $semi_rand . '@' . $this->message_id_server .
            '>' . $this->_newline;

        if ($this->Multipart) {
            $headers = $this->_AssembledEmail['Headers']['m'];

            return $message_id . $headers;
        }

        /**
         * make sure there is a header & body present.
         * otherwise if the header has been assembled, but no body is present we end up with a mismatch.
         * the header says 'text/html' but the body is plain text.
         */
        if ($format == 'h' && !is_null($this->_AssembledEmail['Headers']['h']) && !
            is_null($this->_AssembledEmail['Body']['h'])) {
            $headers = $this->_AssembledEmail['Headers']['h'];
        } else {
            $headers = $this->_AssembledEmail['Headers']['t'];
        }

        return $message_id . $headers;
    }

    /**
     * Send
     * Sends the email to each of the recipients.
     *
     * @see _SetupHeaders
     * @see _SetupAttachments
     * @see _SetupImages
     * @see _SetupBody
     * @see _Recipients
     * @see _GetBody
     * @see _GetHeaders
     * @see _Send_Recipient
     *
     * @return Array Returns an array of results. The number that sent ok and the email addresses that failed to be sent an email.
     */
    function Send()
    {
        $results = array('success' => 0, 'fail' => array());
        $stop_sending_the_rest = false;
        $stop_sending_reason = '';

        $headers = $this->_SetupHeaders();
        $body = $this->_SetupBody();

        foreach ($this->_Recipients as $p => $details) {
            $rcpt_to = $details['address'];

            if ($stop_sending_the_rest) {
                $results['fail'][] = array($rcpt_to, $stop_sending_reason);
                continue;
            }

            $to = $details['address'];
            if ($details['name']) {
                $to = '"' . $this->_utf8_encode($details['name']) . '" <' . $to . '>';
            }

            $headers = $this->_GetHeaders($details['format']);
            $body = $this->_GetBody($details['format']);

            $subject = $this->Subject;

            list($mail_result, $reason) = $this->_Send_Recipient($to, $rcpt_to, $subject, $details['format'],
                $headers, $body);

            if ($mail_result) {
                $results['success']++;
            } else {
                $results['fail'][] = array($rcpt_to, $reason);

                /**
                 * The following condition is made so that the script will not try to send the rest of the email out.
                 * This is because we do not want to hammer the SMTP server when it is down.
                 * Or when there isn't enough space in the SMTP server to queue our message.
                 */
                if (in_array($this->ErrorCodeSMTPEnhanced, array('4.3.1'))) {
                    $stop_sending_the_rest = true;
                    $stop_sending_reason = $reason;
                }
            }
        }
        $this->_Close_Smtp_Connection();
        return $results;
    }

    /**
     * _Send_Recipient
     * Grab a whole lot of information and pass it to the _Send_Email function.
     * Why have this function? Because sendstudio needs to change placeholders & links and running everything through this function means less duplication of code.
     *
     * @param String $to The "to" address of the recipient.
     * @param String $rcpt_to The bare email address of the recipient.
     * @param Char $format The format the recipient wants the email in.
     * @param String $headers The headers for the email.
     * @param String $body The body of the email.
     *
     * @see _GetHeaders
     * @see _GetBody
     * @see Multipart
     * @see _ImageBody
     * @see _AttachmentBody
     * @see _Send_Email
     *
     * @return Array Returns the status from _Send_Email.
     */
    function _Send_Recipient(&$to, &$rcpt_to, &$subject, &$format, &$headers, &$body)
    {
        /**
         * Do the checking for null characters before we add the image or attachments to the body.
         * Saves a little bit of memory doing it this way
         */
        $body = wordwrap($body, $this->wrap_length);

        // Avoid a bug with the mail command
        // See https://www.php-security.org/MOPB/MOPB-33-2007.html for details
        $body = str_replace("\0", "", $body);

        // Shouldn't have a null in the headers either
        $headers = str_replace("\0", "", $headers);

        // Fix for https://www.php-security.org/MOPB/MOPB-34-2007.html
        $subject = str_replace(array("\r", "\n", "\t"), ' ', $subject);
        $to = str_replace(array("\r", "\n", "\t"), ' ', $to);
        $rcpt_to = str_replace(array("\r", "\n", "\t"), ' ', $rcpt_to);

        return $this->_Send_Email($rcpt_to, $to, $subject, $body, $headers);
    }

    /**
     * _Send_Email
     * This decides whether to try and send the email through the smtp server (if specified) or send it through the php mail function (which is the default method).
     *
     * @param String $rcpt_to The 'receipt to' address to send the email to. This is a bare email address only.
     * @param String $to The 'to' address to send this to. This can contain a name / email address in the standard format ("Name" <email@address>)
     * @param String $subject The subject of the email to send.
     * @param String $body The body of the email to send.
     * @param String $headers The headers of the email to send.
     *
     * @see Send
     * @see _Send_SMTP
     * @see safe_mode
     * @see BounceEmail
     * @see _sendmailparameters
     *
     * @return Array Returns an array including whether the email was sent and a possible error message (for logging).
     */
    function _Send_Email(&$rcpt_to, &$to, &$subject, &$body, &$headers)
    {

        $this->DebugMemUsage('rcpt_to: ' . $rcpt_to . '; to: ' . $to . '; subject: ' . $subject .
            '; headers: ' . $headers);

        $subject = $this->_utf8_encode($subject);

        $this->DebugMemUsage('rcpt_to: ' . $rcpt_to . '; to: ' . $to . '; subject: ' . $subject .
            '; headers: ' . $headers);

        if ($this->TestMode) {
            if ($this->SMTPServer) {
                if ($this->Debug) {
                    error_log('Line ' . __line__ . '; time ' . time() .
                        '; We are in "TestMode" (smtp details are set)' . "\n", 3, $this->LogFile);
                }
            } else {
                if ($this->Debug) {
                    error_log('Line ' . __line__ . '; time ' . time() .
                        '; We are in "TestMode" (no smtp details set)' . "\n", 3, $this->LogFile);
                }
            }
        }

        if (!$this->TestMode) {
            if ($this->SMTPServer) {
                $this->DebugMemUsage('sending through smtp server');
                return $this->_Send_SMTP($rcpt_to, $to, $subject, $body, $headers);
            }
        }

        $this->DebugMemUsage('sending through php mail');


        $reason = false;

        /*
        * We change the "to" address here to the bare rcpt_to address if it's a windows server.
        * Windows smtp servers will only take bare addresses in the mail() command.
        */
        if ((substr(strtolower(PHP_OS), 0, 3) == 'win')) {
            $to = $rcpt_to;
        }

        $php_errormsg = '';

        if ($this->safe_mode || !$this->BounceEmail) {
            if (!$this->TestMode) {
                $mail_result = mail($to, $subject, $body, rtrim($headers));
            } else {
                $mail_result = true;
            }

            $this->DebugMemUsage('no bounce address or safe mode is on');
        } else {
            if (is_null($this->_sendmailparameters)) {
                $old_from = ini_get('sendmail_from');
                ini_set('sendmail_from', $this->BounceEmail);
                $params = sprintf('-f%s', $this->BounceEmail);
                $this->_sendmailparameters = $params;
            }

            $this->DebugMemUsage('bounce address set to ' . $this->_sendmailparameters);

            if (!$this->TestMode) {
                $mail_result = mail($to, $subject, $body, rtrim($headers), $this->
                    _sendmailparameters);
            } else {
                $mail_result = true;
            }
        }

        if (!$mail_result) {
            $this->DebugMemUsage('Mail broken');
            $reason = 'Unable To Email (not queued), reason: ' . $php_errormsg;
        } else {
            $this->DebugMemUsage('Mail queued');
        }
        return array($mail_result, $reason);
    }

    /**
     * _Send_SMTP
     * Send an email through an smtp server instead of through the php mail function.
     * This handles all of the commands that need to be sent and return code checking for each part of the process.
     *
     * @param String $rcpt_to The 'receipt to' address to send the email to. This is a bare email address only.
     * @param String $to The 'to' address to send this to. This can contain a name / email address in the standard format ("Name" <email@address>)
     * @param String $subject The subject of the email to send.
     * @param String $body The body of the email to send.
     * @param String $headers The headers of the email to send.
     *
     * @see _Get_Smtp_Connection
     * @see _Put_Smtp_Connection
     * @see _Close_Smtp_Connection
     * @see ErrorCode
     * @see Error
     * @see _get_response
     * @see _smtp_email_count
     *
     * @return Array Returns an array including whether the email was sent and a possible error message (for logging).
     */
    function _Send_SMTP(&$rcpt_to, &$to, &$subject, &$body, &$headers)
    {
        $connection = $this->_Get_Smtp_Connection();

        $this->DebugMemUsage('Connection is ' . gettype($connection));


        if (!$connection) {
            $this->DebugMemUsage('No connection');
            return array(false, $this->Error);
        }

        if ($this->_SMTPPipeline) {
            $cmds = [];
            $cmds[] = "MAIL FROM:<" . $this->BounceEmail . ">";
            $cmds[] = "RCPT TO:<" . $rcpt_to . ">";
            $data = implode($cmds, $this->_smtp_newline);
            if (!$this->_Put_Smtp_Connection($data)) {
                $this->ErrorCode = 5;
                $this->ErrorCodeSMTPEnhanced = false;
                $this->Error = 'Unable to send multiple commands in pipeline mode';
                $this->_Close_Smtp_Connection();
                return array(false, $this->Error);
            }
            $response_count = sizeof($cmds);
            for ($response_check = 1; $response_check <= $response_count; $response_check++) {
                $response = $this->_get_response();
                $responsecode = substr($response, 0, 3);
                if ($responsecode != '250') {
                    $this->ErrorCodeSMTPEnhanced = $this->_GetSMTPEnhancedErrorCode($response);
                    $this->ErrorCode = $responsecode;
                    $this->Error = $response;
                    $this->_Close_Smtp_Connection();

                    $this->DebugMemUsage('Got error ' . $this->Error);
                    return array(false, $this->Error);
                }
            }
            return $this->_Send_SmtpData($rcpt_to, $to, $subject, $body, $headers);
        }

        $data = "MAIL FROM:<" . $this->BounceEmail . ">";

        $this->DebugMemUsage('Trying to put ' . $data);

        if (!$this->_Put_Smtp_Connection($data)) {
            $this->ErrorCode = 10;
            $this->ErrorCodeSMTPEnhanced = false;
            $this->Error = GetLang('UnableToSendEmail_MailFrom');
            $this->_Close_Smtp_Connection();

            $this->DebugMemUsage('Got error ' . $this->Error);

            return array(false, $this->Error);
        }

        $response = $this->_get_response();

        $this->DebugMemUsage('Got response ' . $response);

        $responsecode = substr($response, 0, 3);
        if ($responsecode != '250') {
            $this->ErrorCodeSMTPEnhanced = $this->_GetSMTPEnhancedErrorCode($response);
            $this->ErrorCode = $responsecode;
            $this->Error = $response;
            $this->_Close_Smtp_Connection();

            $this->DebugMemUsage('Got error ' . $this->Error);

            return array(false, $this->Error);
        }

        $data = "RCPT TO:<" . $rcpt_to . ">";

        $this->DebugMemUsage('Trying to put ' . $data);

        if (!$this->_Put_Smtp_Connection($data)) {
            $this->ErrorCode = 11;
            $this->ErrorCodeSMTPEnhanced = false;
            $this->Error = GetLang('UnableToSendEmail_RcptTo');
            $this->_Close_Smtp_Connection();

            $this->DebugMemUsage('Got error ' . $this->Error);

            return array(false, $this->Error);
        }

        $response = $this->_get_response();

        $this->DebugMemUsage('Got response ' . $response);

        $responsecode = substr($response, 0, 3);

        if ($responsecode != '250') {
            $this->ErrorCodeSMTPEnhanced = $this->_GetSMTPEnhancedErrorCode($response);
            $this->ErrorCode = $responsecode;
            $this->Error = $response;
            $this->_Close_Smtp_Connection();

            $this->DebugMemUsage('Got error ' . $this->Error);

            return array(false, $this->Error);
        }

        return $this->_Send_SmtpData($rcpt_to, $to, $subject, $body, $headers);
    }

    /**
     * _Send_SmtpData
     * Handles the SMTP negotiation for sending the email header and body.
     *
     * @param String $rcpt_to The 'receipt to' address to send the email to. This is a bare email address only.
     * @param String $to The 'to' address to send this to. This can contain a name / email address in the standard format ("Name" <email@address>)
     * @param String $subject The subject of the email to send.
     * @param String $body The body of the email to send.
     * @param String $headers The headers of the email to send.
     *
     * @see _Send_SMTP
     *
     * @return Array Returns an array including whether the email was sent and a possible error message (for logging).
     */
    function _Send_SmtpData(&$rcpt_to, &$to, &$subject, &$body, &$headers)
    {

        $data = "DATA";

        $this->DebugMemUsage('Trying to put ' . $data);

        if (!$this->_Put_Smtp_Connection($data)) {
            $this->ErrorCode = 12;
            $this->ErrorCodeSMTPEnhanced = false;
            $this->Error = GetLang('UnableToSendEmail_Data');
            $this->_Close_Smtp_Connection();

            $this->DebugMemUsage('Got error ' . $this->Error);

            return array(false, $this->Error);
        }

        $response = $this->_get_response();

        $this->DebugMemUsage('Got response ' . $response);

        $responsecode = substr($response, 0, 3);

        if ($responsecode != '354') {
            $this->ErrorCode = $responsecode;
            $this->ErrorCodeSMTPEnhanced = $this->_GetSMTPEnhancedErrorCode($response);
            $this->Error = $response;
            $this->_Close_Smtp_Connection();

            $this->DebugMemUsage('Got error ' . $this->Error);

            return array(false, $this->Error);
        }

        $msg = "To: " . $to . $this->_smtp_newline . "Subject: " . $subject . $this->
            _smtp_newline . $headers . $this->_smtp_newline . preg_replace('/^\.(\r|\n)/m',
            ' .${1}', $body);

        $msg = str_replace("\r\n", "\n", $msg);
        $msg = str_replace("\r", "\n", $msg);
        $lines = explode("\n", $msg);
        foreach ($lines as $no => $line) {
            // we need to rtrim here so we don't get rid of tabs before the start of the line.
            // the tab is extremely important for boundaries (eg sending multipart + attachment)
            // so it needs to stay.
            $data = rtrim($line);

            $this->DebugMemUsage('Trying to put ' . $data);

            if (!$this->_Put_Smtp_Connection($data)) {
                $this->ErrorCode = 13;
                $this->ErrorCodeSMTPEnhanced = false;
                $this->Error = GetLang('UnableToSendEmail_DataWriting');
                $this->_Close_Smtp_Connection();

                $this->DebugMemUsage('Got error ' . $this->Error);

                return array(false, $this->Error);
            }
        }

        $data = $this->_smtp_newline . ".";

        $this->DebugMemUsage('Trying to put ' . $data);

        if (!$this->_Put_Smtp_Connection($data)) {
            $this->ErrorCode = 14;
            $this->ErrorCodeSMTPEnhanced = false;
            $this->Error = GetLang('UnableToSendEmail_DataFinished');
            $this->_Close_Smtp_Connection();

            $this->DebugMemUsage('Got error ' . $this->Error);

            return array(false, $this->Error);
        }

        $response = $this->_get_response();

        $this->DebugMemUsage('Got response ' . $response);

        $responsecode = substr($response, 0, 3);
        if ($responsecode != '250') {
            $this->ErrorCodeSMTPEnhanced = $this->_GetSMTPEnhancedErrorCode($response);
            $this->ErrorCode = $responsecode;
            $this->Error = $response;
            $this->_Close_Smtp_Connection();

            $this->DebugMemUsage('Got error ' . $this->Error);

            return array(false, $this->Error);
        }

        $this->DebugMemUsage('Mail accepted ');

        /**
         * We got this far, this means we didn't encounter any errors.
         * Cleanup previous error codes and variables since they are no longer relevant
         * with the current process iteration.
         */
        $this->Error = '';
        $this->ErrorCode = false;
        $this->ErrorCodeSMTPEnhanced = false;

        $this->_smtp_email_count++;
        return array(true, false);
    }

    /**
     * Return "enhanced" SMTP error code
     *
     * This method will only return an error code.
     * It does not attempt to categorized the error code.
     *
     * It is the responsibility of the called to make use of this new
     * "enhanced" error code.
     *
     * NOTE: The enhanced error code is defined in RFC5248
     *
     * @param String $response SMTP Response
     * @return Mixed Returns error code string in this format x.x.x if found, false otherwise
     */
    function _GetSMTPEnhancedErrorCode($response)
    {
        if (!preg_match('/^\d{3} (\d+\.\d+\.\d+)/', $response, $matches)) {
            return false;
        }

        if (!isset($matches[1])) {
            return false;
        }

        return $matches[1];
    }

    /**
     * SetSmtp
     * Sets smtp server information
     * If the servername is set to false, then this will "forget" the current smtp information by setting the class variables back to their defaults.
     *
     * @param String $servername SMTP servername to use to send emails through
     * @param String $username SMTP username to authenticate with when sending through the smtp server
     * @param String $password SMTP password to authenticate with when sending through the smtp server
     * @param Int $port The SMTP port number to use when sending
     *
     * @see SMTPServer
     * @see SMTPUsername
     * @see SMTPPassword
     * @see SMTPPort
     *
     * @return True Always returns true.
     */
    function SetSmtp($servername = false, $username = false, $password = false, $port =
        25)
    {
        if (!$servername) {
            $this->SMTPServer = false;
            $this->SMTPUsername = false;
            $this->SMTPPassword = false;
            $this->SMTPPort = 25;
            return true;
        }

        $this->SMTPServer = $servername;
        $this->SMTPUsername = $username;
        $this->SMTPPassword = $password;
        $this->SMTPPort = (int)$port;
        return true;
    }

    /**
     * _Put_Smtp_Connection
     * This puts data through the smtp connection.
     * If a valid connection isn't passed in, the _smtp_connection is used instead.
     *
     * @param String $data The data to put through the connection. A newline is automatically added here, there is no need to do it before calling this function.
     * @param Resource $connection The connection to send the data through. If not specified, the resource _smtp_connection is used instead.
     *
     * @see _smtp_newline
     * @see _smtp_connection
     *
     * @return Mixed Returns whether the 'fputs' works to the connection resource.
     */
    function _Put_Smtp_Connection($data = '', $connection = null)
    {
        $data .= $this->_smtp_newline;
        if (is_null($connection)) {
            $connection = $this->_smtp_connection;
        }

        return fputs($connection, $data, strlen($data));
    }

    /**
     * SMTP_Logout
     * A wrapper for the _Close_Smtp_Connection function
     *
     * @see _Close_Smtp_Connection
     *
     * @return Void Doesn't return anything.
     */
    function SMTP_Logout()
    {
        $this->_Close_Smtp_Connection();
    }

    /**
     * _Get_Smtp_Connection
     * This fetches the smtp connection stored in _smtp_connection
     * If that isn't valid, this will attempt to set it up and authenticate (if necessary).
     * If the number of emails sent through the connection has reached the maximum (most smtp servers will only let you send a certain number of emails per connection), the connection will be reset.
     * If the connection is not available or has been reset, this will then attempt to re-set up the connection socket.
     *
     * @see _smtp_connection
     * @see _smtp_email_count
     * @see _smtp_max_email_count
     * @see SMTPServer
     * @see SMTPUsername
     * @see SMTPPassword
     * @see SMTPPort
     * @see ErrorCode
     * @see Error
     * @see _Put_Smtp_Connection
     * @see _get_response
     *
     * @return False|Resource If the connection in _smtp_connection is valid, this will return that connection straight away. If it's not valid it will try to re-establish the connection. If it can't be done, this will return false. If it can be done, the connection will be stored in _smtp_connection and returned.
     */
    function _Get_Smtp_Connection()
    {
        if ($this->_smtp_email_count > $this->_smtp_max_email_count) {
            $this->_Close_Smtp_Connection();
            $this->_smtp_email_count = 0;
        }

        if (!is_null($this->_smtp_connection)) {
            return $this->_smtp_connection;
        }

        $server = $this->SMTPServer;
        $username = $this->SMTPUsername;
        $password = $this->SMTPPassword;
        $port = (int)$this->SMTPPort;

        if ($port <= 0) {
            $port = 25;
        }

        $this->DebugMemUsage('smtp details: server: ' . $server . '; username: ' . $username .
            '; password: ' . $password . '; port: ' . $port);

        $timeout = 10;

        $socket = @fsockopen($server, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            $this->ErrorCode = 1;
            $this->Error = sprintf('UnableToConnectToEmailServer', $errstr . '(' . $errno .
                ')');
            return false;
        }

        $response = $this->_get_response($socket);

        $this->DebugMemUsage('Got response ' . $response);

        $responsecode = substr($response, 0, 3);

        if ($responsecode != '220') {
            $this->ErrorCode = $responsecode;
            $this->Error = $response;
            fclose($socket);
            return false;
        }

        // say hi!
        $data = 'EHLO ' . $this->message_id_server;
        $this->DebugMemUsage('Trying to put ' . $data);

        if (!$this->_Put_Smtp_Connection($data, $socket)) {
            $this->ErrorCode = 2;
            $this->Error = GetLang('UnableToConnectToMailServer_EHLO');
            fclose($socket);

            $this->DebugMemUsage('Got error ' . $this->Error);

            return false;
        }

        $response = $this->_get_response($socket);

        $this->DebugMemUsage('Got response ' . $response);

        $responses = explode($this->_smtp_newline, $response);
        $response = array_shift($responses);

        $responsecode = substr($response, 0, 3);
        if ($responsecode == '501') {
            $this->DebugMemUsage('Got responsecode ' . $responsecode);
            $this->ErrorCode = 7;
            $this->Error = GetLang('UnableToConnectToMailServer_EHLO');
            return false;
        }

        $this->_SMTPPipeline = false;

        // before we check for authentication, put the first response at the start of the stack.
        // just in case the first line is 250-auth login or something
        // if we didn't do this i'm sure it would happen ;)
        array_unshift($responses, $response);

        $requireauth = false;

        foreach ($responses as $line) {
            $this->DebugMemUsage('checking line ' . $line);

            if (preg_match('%250[\s|-]auth(.*?)login%i', $line)) {
                $requireauth = true;
            }

            if (preg_match('%250[\s-]pipelining%i', $line)) {
                $this->_SMTPPipeline = true;
            }
        }

        if ($this->Debug) {
            error_log('Line ' . __line__ . '; time ' . time() . '; require authentication: ' .
                (int)$requireauth . "\n", 3, $this->LogFile);
            error_log('Line ' . __line__ . '; time ' . time() .
                '; server supports pipelining: ' . (int)$this->_SMTPPipeline . "\n", 3, $this->
                LogFile);
            if ($this->memory_limit) {
                error_log(basename(__file__) . "\t" . __line__ . "\t" . __function__ . "\t" .
                    number_format((memory_get_usage() / 1024), 5) . "\n", 3, $this->MemoryLogFile);
            }
        }

        if ($requireauth && $username) {
            if (!$password) {
                $this->ErrorCode = 3;
                $this->Error = GetLang('UnableToConnectToMailServer_RequiresAuthentication');
                fclose($socket);

                $this->DebugMemUsage('Got error ' . $this->Error);

                return false;
            }
            $data = "AUTH LOGIN";

            $this->DebugMemUsage('Trying to put ' . $data);

            if (!$this->_Put_Smtp_Connection($data, $socket)) {
                $this->ErrorCode = 4;
                $this->Error = GetLang('UnableToConnectToMailServer_AuthLogin');
                fclose($socket);

                $this->DebugMemUsage('Got error ' . $this->Error);

                return false;
            }

            $response = $this->_get_response($socket);

            $this->DebugMemUsage('Got response ' . $response);

            $responsecode = substr($response, 0, 3);
            if ($responsecode != '334') {
                $this->ErrorCode = 5;
                $this->Error = GetLang('UnableToConnectToMailServer_AuthLoginNotSupported');
                fclose($socket);

                $this->DebugMemUsage('Got error ' . $this->Error);

                return false;
            }

            $data = base64_encode(rawurldecode($username));

            $this->DebugMemUsage('Trying to put ' . $data);

            if (!$this->_Put_Smtp_Connection($data, $socket)) {
                $this->ErrorCode = 6;
                $this->Error = GetLang('UnableToConnectToMailServer_UsernameNotWritten');
                fclose($socket);

                $this->DebugMemUsage('Got error ' . $this->Error);

                return false;
            }

            $response = $this->_get_response($socket);

            $this->DebugMemUsage('Got response ' . $response);

            $responsecode = substr($response, 0, 3);
            if ($responsecode != '334') {
                $this->ErrorCode = $responsecode;
                $this->Error = $response;
                fclose($socket);

                $this->DebugMemUsage('Got error ' . $this->Error);

                return false;
            }

            $data = base64_encode($password);

            $this->DebugMemUsage('Trying to put ' . $data);

            if (!$this->_Put_Smtp_Connection($data, $socket)) {
                $this->ErrorCode = 7;
                $this->Error = GetLang('UnableToConnectToMailServer_PasswordNotWritten');
                fclose($socket);

                $this->DebugMemUsage('Got error ' . $this->Error);

                return false;
            }

            $response = $this->_get_response($socket);

            $this->DebugMemUsage('Got response ' . $response);

            $responsecode = substr($response, 0, 3);
            if ($responsecode != '235') {
                $this->ErrorCode = $responsecode;
                $this->Error = 'Login failed, please check the username and password and try again.';
                fclose($socket);

                $this->DebugMemUsage('Got error ' . $this->Error);

                return false;
            }
        }

        $this->_smtp_connection = $socket;
        return $this->_smtp_connection;
    }

    /**
     * _Close_Smtp_Connection
     * Closes the smtp connection by issuing a 'QUIT' command and then forgets the smtp server connection.
     * If the smtp connection isn't valid, this will return straight away.
     *
     * @see _smtp_connection
     * @see _Put_Smtp_Connection
     *
     * @return Void Doesn't return anything.
     */
    function _Close_Smtp_Connection()
    {
        if (is_null($this->_smtp_connection)) {
            return;
        }

        $this->_Put_Smtp_Connection('QUIT');
        fclose($this->_smtp_connection);
        $this->_smtp_connection = null;
    }

    /**
     * _get_response
     * Gets the response from the last message sent to the smtp server.
     * This is only used by smtp sending. If the connection passed in is not valid, this will return nothing.
     *
     * @param Resource $connection The smtp server connection we're trying to fetch information from. If this is not passed in, we check the _smtp_connection to see if that's available.
     *
     * @see _smtp_connection
     *
     * @return String Returns the response from the smtp server.
     */
    function _get_response($connection = null)
    {
        if (is_null($connection)) {
            $connection = $this->_smtp_connection;
        }

        if (is_null($connection)) {
            return;
        }

        $data = "";
        while ($str = fgets($connection, 515)) {
            $data .= $str;
            # if the 4th character is a space then we are done reading
            # so just break the loop
            if (substr($str, 3, 1) == " " || $str == "") {
                break;
            }
        }
        return trim($data);
    }

    /**
     * This is used to fix stylesheets so that class elements have a space before the "."
     * otherwise the mta strips off the dot and the stylesheet element doesn't work.
     */
    function _FixStyles()
    {
        $matches = [];
        preg_match('%<style[^>]*>(.*)</style>%is', $this->body['h'], $matches);

        if (isset($matches[1])) {
            $new_styles = str_replace("\n.", "\n .", $matches[1]);
            $this->body['h'] = str_replace($matches[1], $new_styles, $this->body['h']);
        }
    }

    /**
     * _utf8_encode
     * This encodes a string based on the character set in the email class.
     * This basically base64-encodes the subject or to/from 'name's so utf-8 characters
     * show up properly in an email program.
     *
     * This works around us having to require multibyte character support (mb_ functions in PHP).
     *
     * RFC 2822 says lines can be longer than 72 characters but no more than 988 characters (under "2.1.1. Line Length Limits").
     * The length of the line this function creates is a max of 512 (was previously 75).
     * Some php versions and/or mail servers complain about the new line in the middle of the subject
     * But it is meant to be allowed according to that RFC (under "2.2.3. Long Header Fields").
     * By setting it to a max of 512 characters we should fix the problem of having a long subject when sending in utf-8 format -
     * which need to be broken up over multiple lines and preceded with a space
     * and appeasing broken php/mail servers which complain about the newline in the middle of the subject.
     * So it becomes a compromise really.
     *
     * @param String $in_str The string you want to encode
     *
     * @see CharSet
     * @see _smtp_newline
     * @see https://www.faqs.org/rfcs/rfc2822.html
     *
     * @return String Returns the encoded string
     */
    function _utf8_encode($in_str)
    {
        $out_str = $in_str;
        if (strtolower($this->CharSet) == 'utf-8' && preg_match('/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f-\xff]/',
            $in_str)) {
            if ($out_str) {
                // define start delimimter, end delimiter and spacer
                $end = "?=";
                $start = "=?" . $this->CharSet . "?B?";
                $spacer = $end . $this->_newline . ' ' . $start;

                // determine length of encoded text within chunks
                // and ensure length is even
                $length = 512 - strlen($start) - strlen($end);
                $length = floor($length / 4) * 4;

                // encode the string and split it into chunks
                // with spacers after each chunk
                $out_str = base64_encode($out_str);
                $out_str = chunk_split($out_str, $length, $spacer);

                // remove trailing spacer and
                // add start and end delimiters
                $spacer = preg_quote($spacer);
                $out_str = preg_replace("/" . $spacer . "$/", "", $out_str);
                $out_str = $start . $out_str . $end;
            }
        }
        return $out_str;
    }
}

