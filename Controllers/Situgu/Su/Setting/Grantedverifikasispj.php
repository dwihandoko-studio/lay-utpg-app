<?php

namespace App\Controllers\Situgu\Su\Setting;

use App\Controllers\BaseController;
use App\Models\Situgu\Su\GrantedverifikasispjModel;
use Config\Services;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;

class Grantedverifikasispj extends BaseController
{
    var $folderImage = 'masterdata';
    private $_db;
    private $model;

    function __construct()
    {
        helper(['text', 'file', 'form', 'session', 'array', 'imageurl', 'web', 'filesystem']);
        $this->_db      = \Config\Database::connect();
    }

    public function getAll()
    {
        $request = Services::request();
        $datamodel = new GrantedverifikasispjModel($request);


        $lists = $datamodel->get_datatables();
        $data = [];
        $no = $request->getPost("start");
        foreach ($lists as $list) {
            $no++;
            $row = [];

            $row[] = $no;
            $action = '<a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->fullname)  . '\', \'' . $list->role_name . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                <i class="bx bx-trash font-size-16 align-middle"></i></button>
                </a>';
            $row[] = $action;
            $row[] = $list->fullname;
            $row[] = $list->role_name;
            // $row[] = $list->nama_kecamatan;

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
        return redirect()->to(base_url('situgu/su/setting/grantedverifikasispj/data'));
    }

    public function data()
    {
        $data['title'] = 'GRANTED VERIFIKASI SPJ';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;

        return view('situgu/su/setting/grantedverifikasispj/index', $data);
    }

    public function add()
    {
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            $response = new \stdClass;
            $response->status = 401;
            $response->message = "Permintaan diizinkan";
            return json_encode($response);
        }

        $response = new \stdClass;
        $response->status = 200;
        $response->message = "Permintaan diizinkan";
        $response->data = view('situgu/su/setting/grantedverifikasispj/add');
        return json_encode($response);
    }

    public function addSave()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Pengguna tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id');
            return json_encode($response);
        } else {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Permintaan diizinkan";
                return json_encode($response);
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);

            $cekData = $this->_db->table('granted_verifikasi_spj')->where(['id' => $id])->get()->getRowObject();

            if ($cekData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Pengguna sudah ada dalam list.";
                return json_encode($response);
            }

            $cekDataPtk = $this->_db->table('_profil_users_tb')->where(['id' => $id])->get()->getRowObject();

            if (!$cekDataPtk) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Pengguna tidak ditemukan.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            $data = [
                'id' => $id,
                'created_at' => date('Y-m-d H:i:s')
            ];

            try {
                $this->_db->table('granted_verifikasi_spj')->insert($data);
                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Data berhasil disimpan.";
                    $response->data = $data;
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal menyimpan data.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan data.";
                return json_encode($response);
            }
        }
    }

    public function getPengguna()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'keyword' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Keyword tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('keyword');
            return json_encode($response);
        } else {
            $keyword = htmlspecialchars($this->request->getVar('keyword'), true);

            $current = $this->_db->table('_profil_users_tb')->select("id, fullname, email, kecamatan")
                ->where("role_user IN (2,3,4) AND (fullname = '$keyword' OR email LIKE '%$keyword%')")->get()->getResult();

            if (count($current) > 0) {
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = $current;
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function delete()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);

            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Permintaan diizinkan";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('granted_verifikasi_spj')->where('ptk_id', $id)->delete();

                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Data berhasil dihapus.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Data gagal dihapus.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data gagal dihapus.";
                return json_encode($response);
            }
        }
    }
}
