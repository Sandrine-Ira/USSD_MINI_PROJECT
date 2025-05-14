<?php
//require_once '/vendor/autoload.php';
require 'vendor/autoload.php';

//use GuzzleHttp\Client;
use AfricasTalking\SDK\AfricasTalking;

class Sms {
    protected $AT;

    function __construct($phone) {
        $this->phone = $phone;
       
        $this->AT = new AfricasTalking(Util::$API_USERNAME, Util::$API_KEY);
    }

    public function sendSMS($message, $recipients) {
        try {
            $sms = $this->AT->sms();
            $result = $sms->send([
                'to' => $recipients,
                'message' => $message,
                'from' => Util::$COMPANY_NAME
            ]);
            return $result;
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
