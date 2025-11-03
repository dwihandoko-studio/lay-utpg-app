<?php

namespace App\Models\Backbone\Su;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class PenggunaModel extends Model
{
    protected $table = "v_user a";
    protected $column_order = array(null, null, 'a.npsn', 'a.fullname', 'a.email', 'a.no_hp', 'a.role_user', 'a.kecamatan', 'a.is_active', 'a.email_verified', 'a.wa_verified');
    protected $column_search = array('a.npsn', 'a.fullname', 'a.email', 'a.no_hp');
    protected $order = array('a.role_user' => 'asc', 'a.fullname' => 'asc');
    protected $request;
    protected $db;
    protected $dt;

    function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect('backbone');
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
        $this->dt->select("a.*, b.nama_kecamatan");
        $this->dt->join('ref_kecamatan b', 'a.kecamatan = b.kode_kecamatan');
        if ($this->request->getPost('role')) {
            $this->dt->where('a.role_user', $this->request->getPost('role'));
        }
        $this->_get_datatables_query();
        if ($this->request->getPost('length') != -1)
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered()
    {
        if ($this->request->getPost('role')) {
            $this->dt->where('a.role_user', $this->request->getPost('role'));
        }
        $this->_get_datatables_query();
        return $this->dt->countAllResults();
    }
    public function count_all()
    {
        if ($this->request->getPost('role')) {
            $this->dt->where('a.role_user', $this->request->getPost('role'));
        }
        $this->_get_datatables_query();
        return $this->dt->countAllResults();
    }
}
