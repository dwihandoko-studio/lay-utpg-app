<?php

namespace App\Controllers\Situgu\Ptk\Spj\Tpg;

use App\Controllers\BaseController;
use App\Models\Situgu\Ptk\Spj\Tpg\UploadModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Uuid;

class Upload extends BaseController
{
    var $folderImage = 'masterdata';
    private $_db;
    private $model;
    private $_helpLib;

    function __construct()
    {
        helper(['text', 'file', 'form', 'session', 'array', 'imageurl', 'web', 'filesystem']);
        $this->_db      = \Config\Database::connect();
        $this->_helpLib = new Helplib();
    }

    public function getAll()
    {
        $request = Services::request();
        $datamodel = new UploadModel($request);


        $jwt = get_cookie('jwt');
        $token_jwt = getenv('token_jwt.default.key');
        if ($jwt) {
            try {
                $decoded = JWT::decode($jwt, new Key($token_jwt, 'HS256'));
                if ($decoded) {
                    $userId = $decoded->id;
                    $level = $decoded->level;
                } else {
                    $output = [
                        "draw" => $request->getPost('draw'),
                        "recordsTotal" => 0,
                        "recordsFiltered" => 0,
                        "data" => []
                    ];
                    echo json_encode($output);
                    return;
                }
            } catch (\Exception $e) {
                $output = [
                    "draw" => $request->getPost('draw'),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => []
                ];
                echo json_encode($output);
                return;
            }
        }
        $id = $this->_helpLib->getPtkId($userId);
        $lists = $datamodel->get_datatables($id);
        $data = [];
        $no = $request->getPost("start");
        foreach ($lists as $list) {
            $no++;
            $row = [];

            $row[] = $no;
            // $action = '<div class="btn-group">
            //             <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
            //             <div class="dropdown-menu" style="">
            //                 <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama) . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
            //                 <a class="dropdown-item" href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Sync Dapodik</a>
            //             </div>
            //         </div>';
            $action = '<a href="javascript:actionDetail(\'' . $list->id_usulan . '\', \'' . $list->id_ptk . '\', \'' . $list->id_tahun_tw . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                <i class="bx bxs-show font-size-16 align-middle"></i> DETAIL</button>
                </a>';
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            // $row[] = $action;
            // $row[] = str_replace('&#039;', "`", str_replace("'", "`", $list->nama));
            $row[] = $list->tahun;
            $row[] = $list->tw;
            $row[] = $list->tf_jumlah_diterima;
            if ($list->lock_upload_spj == 1) {
                $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/sekolah/sptjm') . '/' . $list->lampiran_pernyataan . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/sekolah/sptjm') . '/' . $list->lampiran_pernyataan . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
            } else {
                if ($list->lampiran_pernyataan == null || $list->lampiran_pernyataan == "") {
                    $row[] = '<a class="btn btn-sm btn-primary waves-effect waves-light" target="_blank" href="' . base_url('situgu/ks/sptjm/tpg/download') . '?id=' . $list->id . '"><i class="bx bxs-cloud-download font-size-16 align-middle me-2"></i> Download</a>&nbsp;&nbsp;'
                        . '<a class="btn btn-sm btn-primary waves-effect waves-light" href="javascript:actionUpload(\'' . $list->id . '\',\'' . $list->tahun . '\',\'' . $list->tw . '\');"><i class="bx bxs-cloud-upload font-size-16 align-middle me-2"></i> Upload</a>';
                } else {
                    $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/sekolah/sptjm') . '/' . $list->lampiran_pernyataan . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/sekolah/sptjm') . '/' . $list->lampiran_pernyataan . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
                }
            }

            $data[] = $row;
        }
        $output = [
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datamodel->count_all($id),
            "recordsFiltered" => $datamodel->count_filtered($id),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        return redirect()->to(base_url('situgu/ptk/spj/tpg/upload/data'));
    }

    public function data()
    {
        $data['title'] = 'UPLOAD SPJ TUNJANGAN PROFESI GURU';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }
        $id = $this->_helpLib->getPtkId($user->data->id);
        $data['user'] = $user->data;
        $data['tw'] = $this->_db->table('_ref_tahun_tw')->where('is_current', 1)->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getRowObject();
        $data['tws'] = $this->_db->table('_ref_tahun_tw')->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getRowObject();
        return view('situgu/ptk/spj/tpg/upload/index', $data);
    }

    public function detail()
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
            'id_ptk' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('id_ptk')
                . $this->validator->getError('tw')
                . $this->validator->getError('nama');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $id_ptk = htmlspecialchars($this->request->getVar('id_ptk'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);

            $current = $this->_db->table('v_antrian_usulan_tpg a')
                ->select("a.*, b.kecamatan as kecamatan_sekolah, c.lampiran_sptjm")
                ->join('ref_sekolah b', 'a.npsn = b.npsn')
                ->join('_tb_sptjm c', 'a.kode_usulan = c.kode_usulan')
                ->where(['a.id_usulan' => $id, 'a.id_tahun_tw' => $tw])->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $data['penugasans'] = $this->_db->table('_ptk_tb_dapodik a')
                    ->select("a.*, b.npsn, b.nama as namaSekolah, b.kecamatan as kecamatan_sekolah, (SELECT SUM(jam_mengajar_per_minggu) FROM _pembelajaran_dapodik WHERE ptk_id = a.ptk_id AND sekolah_id = a.sekolah_id AND semester_id = a.semester_id) as jumlah_total_jam_mengajar_perminggu")
                    ->join('ref_sekolah b', 'a.sekolah_id = b.id')
                    ->where('a.ptk_id', $current->id_ptk)
                    ->where("a.jenis_keluar IS NULL")
                    ->orderBy('a.ptk_induk', 'DESC')->get()->getResult();
                $data['igd'] = $this->_db->table('_info_gtk')->where('ptk_id', $current->id_ptk)->get()->getRowObject();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/opk/verifikasi/tpg/detail', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }
}
