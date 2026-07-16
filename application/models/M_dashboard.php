<?php
class M_dashboard extends CI_Model
{
    public function getKaryawan()
    {
        $this->db->where('keluar = 0');
        return $this->db->get('KARYAWAN')->result_array();
    }

    public function totalRequest()
    {
        $q = $this->db->query("SELECT COUNT(*) AS total FROM IT.rpt_it");
        return $q->row()->total;
    }

    public function selesaiReq()
    {
        $q = $this->db->query("SELECT COUNT(*) AS total FROM IT.rpt_it WHERE report2 IS NOT NULL AND TRIM(report2) != ''");
        return $q->row()->total;
    }

    public function pendingReq()
    {
        $q = $this->db->query("SELECT COUNT(*) AS total FROM IT.rpt_it WHERE report2 IS NULL OR TRIM(report2)=''");
        return $q->row()->total;
    }

    public function jumlahKaryawan()
    {
        return $this->db->count_all('KARYAWAN');
    }

    public function lateReq()
    {
        $lateReq = $this->db->query("
    SELECT a.id, a.NONIK, a.report, a.tgl, a.report2
    FROM IT.rpt_it a 
    ORDER BY a.id DESC
    LIMIT 5
")->result_array();

        foreach ($lateReq as &$r) {
            $r['NM_KAR'] = $this->db
                ->select('NM_KAR')
                ->get_where('hc.KARYAWAN', ['NONIK' => $r['NONIK']])
                ->row('NM_KAR');
        }

        return $lateReq;
    }
}
