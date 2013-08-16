<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');

class MY_Email extends CI_Email {

    var    $starttls = FALSE;    // Issue STARTTLS after connection to switch to Secure SMTP over TLS (RFC 3207)
    
    function MY_Email()
    {
        parent::CI_Email();
        
        
    }

function _send_with_smtp()
    {
        if ($this->smtp_host == '')
        {
            $this->_set_error_message('email_no_hostname');
            return FALSE;
        }

        if (!$this->_smtp_connect()) {
            return FALSE;
        }

        if ($this->starttls) {
           if (! $this->_send_command('starttls')) {
                $this->_set_error_message('email_starttls_failed');
                return FALSE;
            }
            stream_socket_enable_crypto($this->_smtp_connect, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            // Re-issue hello to get updated service list (RFC 3207 section 4.2)
            $this->_send_command('hello');
        }

        $this->_smtp_authenticate();

        $this->_send_command('from', $this->clean_email($this->_headers['From']));

        foreach($this->_recipients as $val)
        {
            $this->_send_command('to', $val);
        }

        if (count($this->_cc_array) > 0)
        {
            foreach($this->_cc_array as $val)
            {
                if ($val != "")
                {
                    $this->_send_command('to', $val);
                }
            }
        }

        if (count($this->_bcc_array) > 0)
        {
            foreach($this->_bcc_array as $val)
            {
                if ($val != "")
                {
                    $this->_send_command('to', $val);
                }
            }
        }

        $this->_send_command('data');

        // perform dot transformation on any lines that begin with a dot
        $this->_send_data($this->_header_str . preg_replace('/^\./m', '..$1', $this->_finalbody));

        $this->_send_data('.');

        $reply = $this->_get_smtp_data();

        $this->_set_error_message($reply);

        if (strncmp($reply, '250', 3) != 0)
        {
            $this->_set_error_message('email_smtp_error', $reply);
            return FALSE;
        }

        $this->_send_command('quit');
        return TRUE;
    }
  
  
      function _send_command($cmd, $data = '')
    {
        switch ($cmd)
        {
            case 'hello' :

                    if ($this->_smtp_auth OR $this->_get_encoding() == '8bit')
                        $this->_send_data('EHLO '.$this->_get_hostname());
                    else
                        $this->_send_data('HELO '.$this->_get_hostname());

                        $resp = 250;
            break;
            case 'from' :

                        $this->_send_data('MAIL FROM:<'.$data.'>');

                        $resp = 250;
            break;
            case 'to'    :

                        $this->_send_data('RCPT TO:<'.$data.'>');

                        $resp = 250;
            break;
            case 'data'    :

                        $this->_send_data('DATA');

                        $resp = 354;
            break;
            case 'quit'    :

                        $this->_send_data('QUIT');

                        $resp = 221;
            break;
            case 'starttls' :

                        $this->_send_data('STARTTLS');

                        $resp = 220;
            break;
        }

        $reply = $this->_get_smtp_data();

        $this->_debug_msg[] = "<pre>".$cmd.": ".$reply."</pre>";

        if (substr($reply, 0, 3) != $resp)
        {
            $this->_set_error_message('email_smtp_error', $reply);
            return FALSE;
        }

        if ($cmd == 'quit')
        {
            fclose($this->_smtp_connect);
        }

        return TRUE;
    }
    
} 
