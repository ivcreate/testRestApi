<?php


class ServicesApi extends API
{
    private $user_id;
    private $service_id;

    public function __construct($params)
    {
        parent::__construct($params);
        $this->user_id = $this->params[1];
        $this->service_id = $this->params[3];
    }

    public function start():string 
    {
        switch ($this->method) {
            case "GET":
                $result = $this->onGet();
                break;
            case "PUT":
                $result = $this->onPut();
                break;
            default:
                return $this->response("Method don't available");
        }
        if($result)
            return $this->response($result,200);
        else
            return $this->response("Data not found");
    }

    public function onGet()
    {
        if($this->params[4] != "tarifs")
            return false;

        $tarifs = $this->execQuery("SELECT t2.*, t.title as cur_title, t.link as cur_link, t.speed as cur_speed 
                                    FROM `tarifs` as t
                                    LEFT JOIN services as s ON s.tarif_id = t.id
                                    LEFT JOIN `tarifs` as t2 ON t.`tarif_group_id` = t2.`tarif_group_id`
                                    WHERE s.ID = {$this->service_id}");
        if(empty($tarifs))
            return false;
        $result = [
            "result" => "ok",
            "tarifs" => [
                "title" => $tarifs[0]['cur_title'],
                "link" => $tarifs[0]['cur_link'],
                "speed" => $tarifs[0]['cur_speed'],
            ]
        ];
        foreach ($tarifs as $tarif){
            if($tarif["pay_period"] < 10)
                $timezone = "0".$tarif["pay_period"]."00";
            else
                $timezone = $tarif["pay_period"]."00";
            $result['tarifs']['tarifs'][] = [
                "ID" => $tarif["ID"],
                "title" => $tarif["title"],
                "price" => (int) $tarif["price"],
                "pay_period" => $tarif["pay_period"],
                "new_payday" => strtotime(date("Y-m-d"))."+".$timezone,
                "speed" => $tarif["speed"],
            ];
        }
        return $result;
    }

    public function onPut():array 
    {
        if($this->params[4] != "tarif")
            return false;

        $this->getPutData();
        $data = json_decode($this->put_data);

        if(empty($data->tarif_id) || !is_int($data->tarif_id))
            return ["result" => "error"];

        $service = $this->execQuery("SELECT * FROM `services` WHERE `ID` = {$this->service_id}");
        $user = $this->execQuery("SELECT * FROM `users` WHERE `ID` = {$this->user_id}");
        $tarif = $this->execQuery("SELECT * FROM `tarifs` WHERE `ID` = {$data->tarif_id}");
        if(empty($user) || empty($tarif) || empty($service))
            return ["result" => "error"];

        if ($data->tarif_id != $service[0]['tarif_id'])
            $this->execQuery("UPDATE `services` SET `tarif_id`={$data->tarif_id},`payday`='".date("Y-m-d")."' WHERE `ID` = {$this->service_id}");
        return ["result" => "ok"];
    }

}