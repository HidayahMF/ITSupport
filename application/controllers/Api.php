<?php
class Api extends CI_Controller
{
    public function callApi()
    {
        header('Content-Type: application/json');

        $query = $this->db->query("
            SELECT MONTH(tgl) AS bulan, COUNT(*) AS total 
            FROM IT.rpt_it
            GROUP BY MONTH(tgl)
            ORDER BY bulan ASC
        ");

        echo json_encode($query->result());
    }


    // Buat IoT
    public function insert()
    {
        $this->sqlServer = $this->load->database('sqlServer', TRUE);
        $this->load->model('Iot_model');

        header('Content-Type: application/json');

        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        // ================= VALIDASI =================
        if (!$data) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'invalid json']);
            return;
        }

        if (!isset($data['device_id'], $data['temperature'], $data['voltage'], $data['alarm_status'])) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'msg' => 'missing field']);
            return;
        }

        $save = [
            'device_id'     => $data['device_id'],
            'temperature'   => (float)$data['temperature'],
            'voltage'       => (float)$data['voltage'],
            'alarm_status'  => (int)$data['alarm_status'],
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $result = $this->Iot_model->insert_log($save);

        if ($result) {
            http_response_code(200);
            echo json_encode(['status' => 'ok']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'db insert failed']);
        }
    }
}
