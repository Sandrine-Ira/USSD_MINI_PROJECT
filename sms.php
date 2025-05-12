<?php
require 'vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;

class Sms
{
    protected $phone;
    protected $AT;

    public function __construct($phone)
    {
        $this->phone = $phone;
        $apiKey = "atsk_48124c7298bdb48f9cd0429020e8b363020c2f446dd29a54bb6d12cf35eaef58b572a801";
        $this->AT = new AfricasTalking("sandbox", $apiKey);
    }

    public function sendSms($message, $recipients)
    {
        $sms = $this->AT->sms();
        $result = $sms->send([
            'username' => 'sandbox',
            'to' => $recipients,
            'message' => $message,
            'from' => "momoMoney"
        ]);
        return $result;
    }
}
