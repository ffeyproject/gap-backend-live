<?php

namespace app\components;

use Yii;

class WhacenterService
{
    protected $to;
    protected $lines = [];
    protected $baseUrl = 'https://app.whacenter.com/api';
    protected $deviceId;

    public function __construct()
    {
        $this->deviceId = Yii::$app->params['whacenterDeviceId'];
    }

    public function line($line)
    {
        $this->lines[] = $line;
        return $this;
    }

    public function to($to)
    {
        $this->to = $to;
        return $this;
    }

    public function send()
    {
        if (empty($this->to) || count($this->lines) === 0) {
            throw new \Exception('Message not correct.');
        }
    
        $data = [
            'device_id' => $this->deviceId,
            'number' => $this->to,
            'message' => implode("\n", $this->lines),
        ];
    
        $ch = curl_init($this->baseUrl . '/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->deviceId,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        }
    
        curl_close($ch);
    
        return [
            'http_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }

    public function getDeviceStatus()
    {
        $url = $this->baseUrl . '/statusDevice?device_id=' . $this->deviceId;
        return json_decode(file_get_contents($url), true);
    }
}