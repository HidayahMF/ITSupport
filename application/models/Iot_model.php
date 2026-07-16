<?php
class Iot_model extends CI_Model {

    private $dbSql;

    public function __construct()
    {
        parent::__construct();

        // load koneksi khusus sqlServer
        $this->dbSql = $this->load->database('sqlServer', TRUE);
    }

    public function insert_log($data)
    {
        return $this->dbSql
                    ->insert('IOT_SENSOR_LOG', $data);
    }

}
