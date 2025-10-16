<?php

namespace App\Models\Situgu\Opk\Ptk\Upload;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class PangkatkgbModel extends Model
{
    protected $table = "tb_ptk_upload_pangkat_kgb a";
    protected $column_order = array(null, null, 'a.fullname', 'a.jumlah', 'a.lolos', 'a.gagal', 'a.done', 'a.created_at');
    protected $column_search = array('a.filename');
    protected $order = array('a.filename' => 'desc');
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
    function get_datatables($userId)
    {
        $this->dt->select("a.*");
        // $this->dt->join('_ref_tahun_tw b', 'a.id_tahun_tw = b.id');
        // $this->dt->whereIn('a.status_usulan', [2]);
        // if ($this->request->getPost('tw')) {
        //     if ($this->request->getPost('tw') !== "") {
        //         $this->dt->where('id_tahun_tw', $this->request->getPost('tw'));
        //     } else {
        //         if ($this->request->getPost('tw_active')) {
        //             if ($this->request->getPost('tw_active') !== "") {

        //                 $this->dt->where('id_tahun_tw', $this->request->getPost('tw_active'));
        //             }
        //         }
        //     }
        // } else {
        //     if ($this->request->getPost('tw_active')) {
        //         if ($this->request->getPost('tw_active') !== "") {

        $this->dt->where('user_id', $userId);
        //         }
        //     }
        // }

        $this->_get_datatables_query();
        if ($this->request->getPost('length') != -1)
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered($userId)
    {
        $this->dt->select("a.*");
        $this->dt->where('user_id', $userId);
        // $this->dt->join('_ref_tahun_tw b', 'a.id_tahun_tw = b.id');
        // if ($this->request->getPost('tw')) {
        //     if ($this->request->getPost('tw') !== "") {
        //         $this->dt->where('id_tahun_tw', $this->request->getPost('tw'));
        //     } else {
        //         if ($this->request->getPost('tw_active')) {
        //             if ($this->request->getPost('tw_active') !== "") {

        //                 $this->dt->where('id_tahun_tw', $this->request->getPost('tw_active'));
        //             }
        //         }
        //     }
        // } else {
        //     if ($this->request->getPost('tw_active')) {
        //         if ($this->request->getPost('tw_active') !== "") {

        //             $this->dt->where('id_tahun_tw', $this->request->getPost('tw_active'));
        //         }
        //     }
        // }
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
    public function count_all($userId)
    {
        $this->dt->select("a.*");
        $this->dt->where('user_id', $userId);
        // $this->dt->join('_ref_tahun_tw b', 'a.id_tahun_tw = b.id');
        // if ($this->request->getPost('tw')) {
        //     if ($this->request->getPost('tw') !== "") {
        //         $this->dt->where('id_tahun_tw', $this->request->getPost('tw'));
        //     } else {
        //         if ($this->request->getPost('tw_active')) {
        //             if ($this->request->getPost('tw_active') !== "") {

        //                 $this->dt->where('id_tahun_tw', $this->request->getPost('tw_active'));
        //             }
        //         }
        //     }
        // } else {
        //     if ($this->request->getPost('tw_active')) {
        //         if ($this->request->getPost('tw_active') !== "") {

        //             $this->dt->where('id_tahun_tw', $this->request->getPost('tw_active'));
        //         }
        //     }
        // }
        $this->_get_datatables_query();

        return $this->dt->countAllResults();
    }
}
