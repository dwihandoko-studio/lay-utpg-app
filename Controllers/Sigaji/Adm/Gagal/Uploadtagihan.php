<?php

namespace App\Controllers\Sigaji\Adm\Gagal;

use App\Controllers\BaseController;
use App\Models\Sigaji\Adm\Gagal\UploadtagihanModel;
use Config\Services;
use App\Libraries\Profilelib;
use App\Libraries\Sigaji\Apilib;
use App\Libraries\Helplib;

class Uploadtagihan
extends BaseController
{
    var $folderImage = 'masterdata';
    private $_db;
    private $model;
    private $_helpLib;

    function __construct()
    {
        helper(['text', 'file', 'form', 'session', 'array', 'imageurl', 'web', 'filesystem']);
        $this->_db      = \Config\Database::connect('sigaji');
        $this->_helpLib = new Helplib();
    }

    public function getAll()
    {
        $request = Services::request();
        $datamodel = new UploadtagihanModel($request);


        $lists = $datamodel->get_datatables();
        $data = [];
        $no = $request->getPost("start");
        foreach ($lists as $list) {
            $no++;
            $row = [];

            $row[] = $no;
            $action = '<div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->nip . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama_bank)) . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
                        </div>
                    </div>';
            // $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            $row[] = $list->tahun . '-' . $list->bulan;
            $row[] = $list->nama_bank;
            $row[] = $list->nip;
            $row[] = $list->instansi;
            $row[] = $list->kecamatan;
            $row[] = $list->jumlah_tagihan;
            // $row[] = $list->kode_instansi;

            $data[] = $row;
        }
        $output = [
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datamodel->count_all(),
            "recordsFiltered" => $datamodel->count_filtered(),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        return redirect()->to(base_url('sigaji/adm/gagal/uploadtagihan/data'));
    }

    public function data()
    {
        $data['title'] = 'DATA GAGAL UPLOAD TAGIHAN';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;
        $data['tw'] = $this->_db->table('_ref_tahun_bulan')->where('is_current', 1)->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getRowObject();
        $data['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();
        $data['banks'] = $this->_db->table('ref_bank')->orderBy('nama_bank', 'asc')->get()->getResult();

        return view('sigaji/adm/gagal/uploadtagihan/index', $data);
    }
}
