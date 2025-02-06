<?php

namespace App\Models\Sigaji\Adm;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class PegawaiModel extends Model
{
    protected $table = "tb_pegawai_";
    protected $column_order = array(null, null, 'nama', 'nip', 'nik', 'golongan', 'mk_golongan', null, null);
    protected $column_search = array('nik', 'nip', 'nama', 'kode_instansi');
    protected $order = array('nama' => 'asc');
    protected $request;
    protected $db;
    protected $dt;

    function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect('sigaji');
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

        $this->dt->orderBy('kode_instansi', 'ASC');

        // if ($this->request->getPost('order')) {
        //     $this->dt->orderBy($this->column_order[$this->request->getPost('order')['0']['column']], $this->request->getPost('order')['0']['dir']);
        // } else if (isset($this->order)) {
        //     $order = $this->order;
        //     $this->dt->orderBy(key($order), $order[key($order)]);
        // }
    }
    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($this->request->getPost('length') != -1)
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered()
    {
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
    public function count_all()
    {
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
}
