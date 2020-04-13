<?php
abstract class API
{
    protected $db;
    protected $params;
    protected $put_data = "";
    protected $method;

    public function __construct($params)
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        $this->dbConnect();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = explode('/', trim($params,'/'));
    }

    protected function dbConnect(){
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
    }

    protected function response($data, $status = 404):string {
        header("HTTP/1.1 ".$status." ".$this->requestStatus($status));
        return json_encode($data);
    }

    private function requestStatus($code):string {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
        );
        return ($status[$code])?$status[$code]:$status[404];
    }

    protected function getPutData():void {
        $put_data = fopen("php://input", "r");
        while ($data = fread($put_data, 1024))
            $this->put_data .= $data;
        fclose($put_data);
    }


    protected function execQuery($query):array {
        return $this->db->query($query)->fetchAll();
    }

    abstract public function start();
    abstract public function onGet();
    abstract public function onPut();
}