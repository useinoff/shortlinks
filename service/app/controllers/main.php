<?php

class MainController extends \Phalcon\Mvc\Controller
{
    /** @var string  */
    protected static $key_request = 'Ajdc8234zscnvc928341Advnzv98cqn34ncalsdch3$%ASDFASDfan8c#';

    /** @var string  */
    protected static $chars = "123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";

    /**
     * @param $id
     * @return string
     * @throws Exception
     */
    protected function convertIntToShortCode($id)
    {
        $id = intval($id);
        if ($id < 1) {
            throw new \Exception(
                "ID не является некорректным целым числом.");
        }

        $length = strlen(self::$chars);
        if ($length < 10) {
            throw new \Exception("Длина строки мала");
        }

        $code = "";
        while ($id > $length - 1) {
            $code = self::$chars[fmod($id, $length)] .
                $code;
            $id = floor($id / $length);
        }

        $code = self::$chars[$id] . $code;

        return $code;
    }

    /**
     * @throws Exception
     */
    public function indexAction()
    {
        $jsonInput = $this->request->getPost();
        $input = @json_decode($jsonInput, true);

        if ($input["api_key"] != self::$key_request) {
            throw new \Exception("Error: invalid key");
        };

        $url = @$input['reqs']['params']['url'];

        if (empty($url)) {
            throw new \Exception("Error: empty url");
        }

        if(!preg_match('/^(https?:\/\/)?([\w\.]+)\.([a-z]{2,6}\.?)(\/[\w\.]*)*\/?$/', $url)){
            throw new \Exception("Error: invalid url");
        }

        $link = Links::findFirst("long_url='$url'");

        if (!$link) {
            $link = new Links();
            $link->long_url = $url;
            $link->save();
            $link->short_code = $this->convertIntToShortCode($link->id);
            $link->save();
        }

        $response = [
            "jsonrpc" => 2.0,
            "status" => "OK",
            "result" => $link->short_code
        ];

        header('Content-Type: application/json');
        exit(json_encode($response));
    }
}