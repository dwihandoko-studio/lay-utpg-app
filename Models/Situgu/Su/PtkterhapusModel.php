<?php

namespace App\Models\Situgu\Su;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class PtkterhapusModel extends Model
{
    protected $table = "_ptk_tb_hapus";
    protected $column_order = array(null, null, 'nama', 'nik', 'nuptk', 'npsn', 'tempat_tugas', 'kecamatan', 'jenis_ptk');
    protected $column_search = array('nik', 'nuptk', 'nama', 'npsn');
    protected $order = array('kecamatan' => 'asc', 'jenis_ptk' => 'desc');
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
        $this->dt->where('status_ajuan', 2);
        $this->_get_datatables_query();
        if ($this->request->getPost('length') != -1)
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered()
    {
        $this->dt->where('status_ajuan', 2);
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
    public function count_all()
    {
        $this->dt->where('status_ajuan', 2);
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
}
