<?php

namespace App\Models\Situgu\Adm\Spj;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class VerifikasispjtpgdetailModel extends Model
{
    protected $table = "_tb_spj_tpg a";
    protected $column_order = array(null, null, 'a.kode_usulan', 'b.nama', 'b.nik', 'b.nuptk', 'b.jenis_ptk');
    protected $column_search = array('b.nik', 'b.nuptk', 'b.nama', 'b.npsn', 'b.nama');
    protected $order = array('a.date_prosestransfer' => 'asc');
    protected $request;
    protected $db;
    protected $dt;

    function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;

        $this->dt = $this->db->table($this->table);
    }
    private function _get_datatables_query()
    {
        $select = "a.*, b.nama, b.nik, b.nuptk, b.jenis_ptk";
        $this->dt->select($select);
        $this->dt->join('_ptk_tb b', 'b.id = a.id_ptk');

        $i = 0;
        foreach ($this->column_search as $item) {
            if ($this->request->getPost('search')['value']) {
                if ($i === 0) {
                    $this->dt->groupStart();
                    $this->dt->like($item, $this->request->getPost('search')['value']);
                } else {
                    $this->dt->orLike($item, $this->request->getPost('search')['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->dt->groupEnd();
            }
            $i++;
        }

        if ($this->request->getPost('order')) {
            $this->dt->orderBy($this->column_order[$this->request->getPost('order')['0']['column']], $this->request->getPost('order')['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->dt->orderBy(key($order), $order[key($order)]);
        }
    }
    function get_datatables($kode_usulan)
    {
        $this->dt->where('a.kode_usulan', $kode_usulan);
        $this->dt->where('a.status_usulan', 0);
        $this->dt->where("a.lampiran_pernyataan IS NOT NULL");
        $this->_get_datatables_query();
        if ($this->request->getPost('length') != -1)
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered($kode_usulan)
    {
        $this->dt->where('a.kode_usulan', $kode_usulan);
        $this->dt->where('a.status_usulan', 0);
        $this->dt->where("a.lampiran_pernyataan IS NOT NULL");
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
    public function count_all($kode_usulan)
    {
        $this->dt->where('a.kode_usulan', $kode_usulan);
        $this->dt->where('a.status_usulan', 0);
        $this->dt->where("a.lampiran_pernyataan IS NOT NULL");
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
}
