<?php

namespace App\Models\Situgu\Adm\Tamsil;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class ProsestransferModel extends Model
{
    protected $table = "_tb_usulan_tamsil_transfer a";
    protected $column_order = array(null, null, 'b.nama', 'b.nik', 'b.nuptk', 'b.jenis_ptk', 'a.date_prosestransfer');
    protected $column_search = array('b.nik', 'b.nuptk', 'b.nama');
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
        $this->dt->select("a.id as id_usulan, a.date_approve, a.kode_usulan, a.id_ptk, a.id_tahun_tw, a.status_usulan, a.date_approve_sptjm, b.nama, b.nik, b.nuptk, b.jenis_ptk, b.kecamatan, a.date_matching, a.date_prosestransfer");
        $this->dt->join('_ptk_tb b', 'a.id_ptk = b.id');
        $this->dt->whereIn('a.status_usulan', [5]);
        if ($this->request->getPost('tw')) {
            if ($this->request->getPost('tw') !== "") {
                $this->dt->where('a.id_tahun_tw', $this->request->getPost('tw'));
            } else {
                if ($this->request->getPost('tw_active')) {
                    if ($this->request->getPost('tw_active') !== "") {

                        $this->dt->where('a.id_tahun_tw', $this->request->getPost('tw_active'));
                    }
                }
            }
        } else {
            if ($this->request->getPost('tw_active')) {
                if ($this->request->getPost('tw_active') !== "") {

                    $this->dt->where('a.id_tahun_tw', $this->request->getPost('tw_active'));
                }
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
        $this->dt->select("a.id as id_usulan, a.date_approve, a.kode_usulan, a.id_ptk, a.id_tahun_tw, a.status_usulan, a.date_approve_sptjm, b.nama, b.nik, b.nuptk, b.jenis_ptk, b.kecamatan, a.date_matching, a.date_prosestransfer");
        $this->dt->join('_ptk_tb b', 'a.id_ptk = b.id');
        $this->dt->whereIn('a.status_usulan', [5]);
        if ($this->request->getPost('tw')) {
            if ($this->request->getPost('tw') !== "") {
                $this->dt->where('a.id_tahun_tw', $this->request->getPost('tw'));
            } else {
                if ($this->request->getPost('tw_active')) {
                    if ($this->request->getPost('tw_active') !== "") {

                        $this->dt->where('a.id_tahun_tw', $this->request->getPost('tw_active'));
                    }
                }
            }
        } else {
            if ($this->request->getPost('tw_active')) {
                if ($this->request->getPost('tw_active') !== "") {

                    $this->dt->where('a.id_tahun_tw', $this->request->getPost('tw_active'));
                }
            }
        }
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
    public function count_all()
    {
        $this->dt->select("a.id as id_usulan, a.date_approve, a.kode_usulan, a.id_ptk, a.id_tahun_tw, a.status_usulan, a.date_approve_sptjm, b.nama, b.nik, b.nuptk, b.jenis_ptk, b.kecamatan, a.date_matching, a.date_prosestransfer");
        $this->dt->join('_ptk_tb b', 'a.id_ptk = b.id');
        $this->dt->whereIn('a.status_usulan', [5]);
        if ($this->request->getPost('tw')) {
            if ($this->request->getPost('tw') !== "") {
                $this->dt->where('a.id_tahun_tw', $this->request->getPost('tw'));
            } else {
                if ($this->request->getPost('tw_active')) {
                    if ($this->request->getPost('tw_active') !== "") {

                        $this->dt->where('a.id_tahun_tw', $this->request->getPost('tw_active'));
                    }
                }
            }
        } else {
            if ($this->request->getPost('tw_active')) {
                if ($this->request->getPost('tw_active') !== "") {

                    $this->dt->where('a.id_tahun_tw', $this->request->getPost('tw_active'));
                }
            }
        }
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
}
