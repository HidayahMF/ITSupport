<?php
class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_dashboard');
    }

    public function index()
    {
        $data['title'] = "Dashboard | IT";
        $data['totalRequest'] = $this->M_dashboard->totalRequest();
        $data['selesaiReq'] = $this->M_dashboard->selesaiReq();
        $data['pendingReq'] = $this->M_dashboard->pendingReq();
        $data['jumlahKaryawan'] = $this->M_dashboard->jumlahKaryawan();
        $data['lateReq'] = $this->M_dashboard->lateReq();

        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidebar');
        $this->load->view('dashboard', $data);
        $this->load->view('templates/footer');
    }
}
