<?php

class MainController extends \Phalcon\Mvc\Controller
{
    CONST SERVICE = 'localhost:4567';
    CONST KEY = 'Ajdc8234zscnvc928341Advnzv98cqn34ncalsdch3$%ASDFASDfan8c#';
    CONST KEY_RESPONSE = 'lsdch3$%ASDFASDfan8c#23498123762341';

    public function indexAction()
    {
    }

    public function generateAction()
    {
        $url = $this->request->getPost("url", "string");

        if (empty($url)) {
            throw new \Exception("Error: empty URL");
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception("Error: invalid URL");
        }

        $request = [
            "jsonrpc" => 2.0,
            "api_key" => self::KEY,
            "reqs" => [
                "method" => "generate",
                "params" => [
                    "url" => $url
                ]
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::SERVICE,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($request)
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $result = @json_decode($response, true);

        if ($result['status'] != 'ok') {
            throw new \Exception("Error: service error");
        }

        $shortUrl = $result['result'];
        echo "Your short link: ".$shortUrl;
    }
}