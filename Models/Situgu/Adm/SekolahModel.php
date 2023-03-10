<?php

namespace App\Models\Situgu\Adm;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class SekolahModel extends Model
{
    protected $table = "ref_sekolah";
    protected $column_order = array(null, null, 'nama', 'npsn', 'bentuk_pendidikan', 'status_sekolah', 'kecamatan');
    protected $column_search = array('nama', 'npsn');
    protected $order = array('status_sekolah' => 'asc', 'nama' => 'asc');
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
        // $this->dt->where('kode_kecamatan', $kecamatan);
        // $this->dt->whereIn('bentuk_pendidikan_id', [5]);
        $this->_get_datatables_query();
        if ($this->request->getPost('length') != -1)
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered()
    {
        // $this->dt->where('kode_kecamatan', $kecamatan);
        // $this->dt->whereIn('bentuk_pendidikan_id', [5]);
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
    public function count_all()
    {
        // $this->dt->where('kode_kecamatan', $kecamatan);
        // $this->dt->whereIn('bentuk_pendidikan_id', [5]);
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
}
