<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'third_party/AfricasTalkingGateway.php';

class Africastalking extends AfricasTalkingGateway
{
    private $CI;            //incase we may need to use the Codeigniter Instance
    private $config;
    private $alt_config;
    
    public function __construct()
    {
        $this->CI = & get_instance();
        /*$this->CI->load->config('africastalking', TRUE);*/
        $this->CI->load->model('smssettings_m', TRUE);
        $astalking_bind = array();
        $get_astalkings = $this->CI->smssettings_m->get_order_by_astalking();
        foreach ($get_astalkings as $key => $get_astalking) {
            $astalking_bind[$get_astalking->field_names] = $get_astalking->field_values;
        }
        parent::__construct($astalking_bind['astalking_username'],$astalking_bind['astalking_password']);

        /*$this->config = $this->CI->config->item('africastalking');
        parent::__construct($this->config['username'], $this->config['apiKey']);*/


    }
    
    public function send_sms($recipients_, $message_)
    {
        $recipients_ = $this->_prepare_sms_recipients($recipients_);
        
        if(empty($recipients_))
        {
            echo 'Empty Error';
            return array('error'=>TRUE, 'message'=>'The numbers provided were either empty or invalid');
        }
        
        $result_array = array('error'=>FALSE,'message'=>'Messages sent successfully');
        echo'Sent';
        
        try
        {
            $at_result = $this->sendMessage($recipients_, $message_, $this->config['sms_sender']);
            
            foreach ($at_result as $result)
            {
                $result_array[] = array(
                                        'Number'    => $result->number,
                                        'Status'    => $result->status,
                                        'MessageId' => $result->messageId,
                                        'Cost'      => $result->cost
                                        );
                echo'Sent';
            }
        } catch (AfricasTalkingGatewayException $e)
        {
            log_message('error',"Encountered an error while sending: " . $e->getMessage());
            echo 'not sent Exception';
            
            $result_array['error']      = TRUE;
            $result_array['message']    = 'Encountered an error while sending, see log for details'; 
        }
        
        return $result_array;
    }
    
    private function _prepare_sms_recipients($recipients_)
    {
        if( ! is_array($recipients_))
        {
            $recipients_ = explode(',',str_replace(' ','',$recipients_));
        }
        
        $recipients_array = array();
        
        foreach($recipients_ as $recipient)
        {
            if(strlen($recipient) === 10 && substr($recipient, 0, 1) === '0')
            {
                $recipient = preg_replace('/^(0)/',$this->config['default_country_code'], $recipient);
            }
            else if(strlen($recipient) === 9 && substr($recipient, 0, 1) == '7')
            {
                $recipient = $this->config['default_country_code'].$recipient;
            }
            else if(strlen($recipient) === 13 && substr($recipient, 0, 2) === '+2')
            {
                //The number is ay,ok
            }
            else continue; //The number is invalid
            
            array_push($recipients_array, $recipient);
        }
        
        return implode(',', $recipients_array);
    }
}