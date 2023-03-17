<?php

namespace App\Models\Situgu\Su\Tpg;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class AntrianModel extends Model
{
    protected $table = "v_antrian_usulan_tpg";
    protected $column_order = array(null, null, 'nama', 'nik', 'nuptk', 'jenis_ptk', 'created_at');
    protected $column_search = array('nik', 'nuptk', 'nama');
    protected $order = array('created_at' => 'asc', 'status_usulan' => 'asc');
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
    function get_datatables()
    {
        if ($this->request->getPost('tw')) {
            if ($this->request->getPost('tw') !== "") {

                $this->dt->where('id_tahun_tw', $this->request->getPost('tw'));
            }
        }
        $this->_get_datatables_query();
        if ($this->request->getPost('length') != -1)
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered()
    {
        if ($this->request->getPost('tw')) {
            if ($this->request->getPost('tw') !== "") {

                $this->dt->where('id_tahun_tw', $this->request->getPost('tw'));
            }
        }
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
    public function count_all()
    {
        if ($this->request->getPost('tw')) {
            if ($this->request->getPost('tw') !== "") {

                $this->dt->where('id_tahun_tw', $this->request->getPost('tw'));
            }
        }
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
}