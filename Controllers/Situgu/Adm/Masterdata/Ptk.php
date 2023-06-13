<?php

namespace App\Controllers\Situgu\Adm\Masterdata;

use App\Controllers\BaseController;
use App\Models\Situgu\Adm\PtkModel;
use App\Models\Situgu\Adm\SekolahModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;

class Ptk extends BaseController
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

    public function getAllPtk()
    {
        $request = Services::request();
        $datamodel = new PtkModel($request);

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
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
                            <a class="dropdown-item" href="javascript:actionEdit(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-edit-alt font-size-16 align-middle"></i> &nbsp;Edit</a>'
                . ($list->lampiran_impassing === "" || $list->lampiran_impassing === NULL) ? '' : '<a class="dropdown-item" href="javascript:actionEditInpassing(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-edit-alt font-size-16 align-middle"></i> &nbsp;Edit Inpassing</a>'
                . '<a class="dropdown-item" href="javascript:actionEditPendidikan(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="mdi mdi-school-outline font-size-16 align-middle"></i> &nbsp;Edit Default Pendidikan</a>
                            <a class="dropdown-item" href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Tarik Data</a>
                            <a class="dropdown-item" href="javascript:actionSyncDataPembenahan(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Syncrone Data Pembenahan</a>
                            <a class="dropdown-item" href="javascript:actionHapus(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-trash font-size-16 align-middle"></i> &nbsp;Ajukan Hapus Data</a>
                        </div>
                    </div>';
            // $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            $row[] = $list->nama;
            $row[] = $list->nik;
            $row[] = $list->nip;
            $row[] = $list->nuptk;
            $row[] = $list->jenis_ptk;
            $row[] = $list->last_sync;

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

    public function getAll()
    {
        $request = Services::request();
        $datamodel = new SekolahModel($request);

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


        // $kecamatan = $this->_helpLib->getKecamatan($userId);

        $lists = $datamodel->get_datatables();
        $data = [];
        $no = $request->getPost("start");
        foreach ($lists as $list) {
            $no++;
            $row = [];

            $row[] = $no;

            $action = '<a href="./sekolah?n=' . $list->id . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                <i class="bx bxs-show font-size-16 align-middle"> Detail</i></button>
                </a>';
            $row[] = $action;
            $row[] = $list->nama;
            $row[] = $list->npsn;
            $row[] = $list->bentuk_pendidikan;
            $row[] = $list->status_sekolah;
            $row[] = $list->kecamatan;

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
        return redirect()->to(base_url('situgu/adm/masterdata/ptk/data'));
    }

    public function data()
    {
        $data['title'] = 'SEKOLAH';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;

        return view('situgu/adm/masterdata/ptk/index', $data);
    }

    public function sekolah()
    {
        $data['title'] = 'DATA PTK SEKOLAH';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $id = htmlspecialchars($this->request->getGet('n'), true);

        $data['user'] = $user->data;
        $data['redirect'] = base_url('situgu/adm/masterdata/ptk');
        $sekolah = $this->_db->table('ref_sekolah')->where('id', $id)->get()->getRowObject();
        if (!$sekolah) {
            return view('404', $data);
        }
        $data['sekolah'] = $sekolah;
        return view('situgu/adm/masterdata/ptk/sekolah', $data);
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
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);

            $current = $this->_db->table('_ptk_tb a')
                ->select("a.*, b.no_hp as nohpAkun, b.email as emailAkun, b.wa_verified, b.image, c.kecamatan as kecamatan_sekolah")
                ->join('v_user b', 'a.id_ptk = b.ptk_id', 'left')
                ->join('ref_sekolah c', 'a.npsn = c.npsn')
                ->where('a.id', $id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $data['penugasans'] = $this->_db->table('_ptk_tb_dapodik a')
                    ->select("a.*, b.npsn, b.nama as namaSekolah, b.kecamatan as kecamatan_sekolah, (SELECT SUM(jam_mengajar_per_minggu) FROM _pembelajaran_dapodik WHERE ptk_id = a.ptk_id AND sekolah_id = a.sekolah_id AND semester_id = a.semester_id) as jumlah_total_jam_mengajar_perminggu")
                    ->join('ref_sekolah b', 'a.sekolah_id = b.id')
                    ->where('a.ptk_id', $current->id_ptk)
                    ->where("a.jenis_keluar IS NULL")
                    ->orderBy('a.ptk_induk', 'DESC')->get()->getResult();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/adm/masterdata/ptk/detail', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function detailbackbone()
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
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
            'nuptk' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NUPTK tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('nama')
                . $this->validator->getError('nuptk');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nuptk = htmlspecialchars($this->request->getVar('nuptk'), true);

            $current = $this->_db->table('_ptk_tb')->select("id_ptk, nuptk")
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $apiLib = new Apilib();
                if ($current->id_ptk !== null) {
                    $result = $apiLib->getPtkById($current->id_ptk);

                    $ptk = $result;
                } else {
                    $result = $apiLib->getPtkByNuptk($current->nuptk);

                    $ptk = $result;
                }

                $data['data'] = $ptk;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/adm/masterdata/ptk/get_detail_backbone', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function editdefaulpendidikan()
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
            'ptk_id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'PTK Id tidak boleh kosong. ',
                ]
            ],
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
            'npsn' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('nama')
                . $this->validator->getError('npsn')
                . $this->validator->getError('ptk_id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $ptk_id = htmlspecialchars($this->request->getVar('ptk_id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $current = $this->_db->table('_ptk_tb')
                ->where(['id' => $id, 'id_ptk' => $ptk_id, 'npsn' => $npsn])->get()->getRowObject();

            if ($current) {
                $this->_db->transBegin();
                try {
                    $this->_db->table('_ptk_tb')->where(['id' => $current->id, 'is_locked' => 0])->update(['pendidikan' => NULL, 'updated_at' => date('Y-m-d H:i:s')]);
                } catch (\Exception $e) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mendefault data pendidikan.";
                    return json_encode($response);
                }

                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Data pendidikan berhasil didefaulkan.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mendefault data pendidikan.";
                    return json_encode($response);
                }
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function edit()
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
            'ptk_id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'PTK Id tidak boleh kosong. ',
                ]
            ],
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
            'npsn' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('nama')
                . $this->validator->getError('npsn')
                . $this->validator->getError('ptk_id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $ptk_id = htmlspecialchars($this->request->getVar('ptk_id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $current = $this->_db->table('_ptk_tb')
                ->where(['id' => $id, 'id_ptk' => $ptk_id, 'npsn' => $npsn])->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/adm/masterdata/ptk/edit', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function syncAll()
    {
        if (!(grantTarikDataBackbone())) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Tarik data dari backbone masih dalam normalisasi system.";
            return json_encode($response);
        }

        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'npsn' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('npsn');
            return json_encode($response);
        } else {
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $apiLib = new Apilib();
            $result = $apiLib->syncPtk($npsn);

            if ($result) {
                if ($result->status == 200) {
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Tarik Data Semua PTK Berhasil Dilakukan.";
                    return json_encode($response);
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal Tarik Data";
                    return json_encode($response);
                }
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal Tarik Data";
                return json_encode($response);
            }
        }
    }

    public function sync()
    {
        if (!(grantTarikDataBackbone())) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Tarik data dari backbone masih dalam normalisasi system.";
            return json_encode($response);
        }

        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'ptk_id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
            'npsn' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('ptk_id')
                . $this->validator->getError('nama')
                . $this->validator->getError('npsn');
            return json_encode($response);
        } else {
            $idPtk = htmlspecialchars($this->request->getVar('ptk_id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $apiLib = new Apilib();
            $result = $apiLib->syncPtkId($idPtk, $npsn);

            if ($result) {
                if ($result->status == 200) {
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Tarik Data PTK $nama Berhasil Dilakukan.";
                    return json_encode($response);
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal Tarik Data";
                    return json_encode($response);
                }
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal Tarik Data";
                return json_encode($response);
            }
        }
    }

    public function syncpembenahan()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'ptk_id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
            'npsn' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('ptk_id')
                . $this->validator->getError('nama')
                . $this->validator->getError('npsn');
            return json_encode($response);
        } else {
            $idPtk = htmlspecialchars($this->request->getVar('ptk_id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $tw = $this->_helpLib->getCurrentTw();
            if (!$tw) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tahun Triwulan Active tidak ditemukan.";
                return json_encode($response);
            }

            $ptk = $this->_db->table('_ptk_tb')->where('id_ptk', $idPtk)->get()->getRowObject();

            if (!$ptk) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data PTK tidak ditemukan.";
                return json_encode($response);
            }

            $ptkAttr = $this->_db->table('_upload_data_attribut')->where(['id_ptk' => $ptk->id, 'id_tahun_tw' => $tw])->get()->getRowObject();
            if (!$ptkAttr) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data Atribut PTK tidak ditemukan.";
                return json_encode($response);
            }
            if ($ptkAttr->is_locked == 1) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data Atribut PTK Sudah Terkunci.";
                return json_encode($response);
            }

            $tgl = "";
            $golongan = "";
            $tmt = "";
            $tgl = "";
            $no = "";
            $jenis = "";
            $tahun = "";
            $bulan = "";

            if ($ptk->status_kepegawaian == "GTY/PTY") {
                $tgl = $ptk->tgl_sk_impassing;
                $golongan = $ptk->pangkat_golongan_ruang;
                $tmt = $ptk->tmt_sk_impassing;
                $no = $ptk->nomor_sk_impassing;
                $jenis = "pangkat";
                $tahun = $ptk->masa_kerja_tahun_impassing;
                $bulan = $ptk->masa_kerja_bulan_impassing;
            } else {
                if ($ptk->tmt_sk_kgb > $ptk->tmt_pangkat) {
                    $tgl = $ptk->tgl_sk_kgb;
                    $golongan = $ptk->pangkat_golongan_kgb;
                    $tmt = $ptk->tmt_sk_kgb;
                    $no = $ptk->sk_kgb;
                    $jenis = "kgb";
                    $tahun = $ptk->masa_kerja_tahun_kgb;
                    $bulan = $ptk->masa_kerja_bulan_kgb;
                } else {
                    $tgl = $ptk->tgl_sk_pangkat;
                    $golongan = $ptk->pangkat_golongan;
                    $tmt = $ptk->tmt_pangkat;
                    $no = $ptk->nomor_sk_pangkat;
                    $jenis = "pangkat";
                    $tahun = $ptk->masa_kerja_tahun;
                    $bulan = $ptk->masa_kerja_bulan;
                }
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_upload_data_attribut')->where('id', $ptkAttr->id)->update([
                    'pang_golongan' => $golongan,
                    'pang_jenis' => $jenis,
                    'pang_no' => $no,
                    'pang_tmt' => $tmt,
                    'pang_tgl' => $tgl,
                    'pang_tahun' => $tahun,
                    'pang_bulan' => $bulan,
                ]);
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal melakukan pembaharuan data attribut.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Pembaharuan Data Atribut PTK $nama Berhasil Dilakukan.";
                return json_encode($response);
            } else {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tidak ada Pembaharuan Data Atribut PTK $nama";
                return json_encode($response);
            }
        }
    }

    public function formhapus()
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
                . $this->validator->getError('nama');
            return json_encode($response);
        } else {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session telah habis";
                $response->redirect = base_url('auth');
                return json_encode($response);
            }
            $canUsulTamsil = canUsulTamsil();

            if ($canUsulTamsil && $canUsulTamsil->code !== 200) {
                return json_encode($canUsulTamsil);
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);

            $data['id'] = $id;
            $data['nama'] = $nama;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/adm/masterdata/ptk/hapus', $data);
            return json_encode($response);
        }
    }

    public function hapus()
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
            'keterangan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Keterangan tidak boleh kosong. ',
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
            $keterangan = htmlspecialchars($this->request->getVar('keterangan'), true);

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
            $current = $this->_db->table('_ptk_tb')
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $this->_db->transBegin();
                try {
                    $date = date('Y-m-d H:i:s');
                    $this->_db->query("INSERT INTO _ptk_tb_hapus(id, id_ptk, email, nama, gelar_depan, gelar_belakang, nik, nuptk, nip, nrg, no_peserta, npwp, no_rekening, cabang_bank, jenis_kelamin, tempat_lahir, tgl_lahir, status_tugas, tempat_tugas, npsn, kecamatan, id_kecamatan, no_hp, sk_cpns, tgl_cpns, sk_pengangkatan, tmt_pengangkatan, jenis_ptk, pendidikan, bidang_studi_pendidikan, bidang_studi_sertifikasi, status_kepegawaian, mapel_diajarkan, jam_mengajar_perminggu, jabatan_kepsek, jabatan_ks_plt, pangkat_golongan, nomor_sk_pangkat, tgl_sk_pangkat, tmt_pangkat, masa_kerja_tahun, masa_kerja_bulan, gaji_pokok, sk_kgb, tgl_sk_kgb, tmt_sk_kgb, masa_kerja_tahun_kgb, masa_kerja_bulan_kgb, gaji_pokok_kgb, mengajar_lain_satmikal, nomor_sk_impassing, tgl_sk_impassing, tmt_sk_impassing, jabatan_angka_kredit, pangkat_golongan_ruang, masa_kerja_tahun_impassing, masa_kerja_bulan_impassing, jumlah_tunjangan_pokok_impassing, lampiran_impassing, lampiran_foto, lampiran_karpeg, lampiran_ktp, lampiran_nrg, lampiran_nuptk, lampiran_serdik, lampiran_npwp, lampiran_buku_rekening, lampiran_ijazah, jenis_tunjangan, is_locked, created_at, updated_at, last_sync, created_ajuan, keterangan_penghapusan)
                                        SELECT id, id_ptk, email, nama, gelar_depan, gelar_belakang, nik, nuptk, nip, nrg, no_peserta, npwp, no_rekening, cabang_bank, jenis_kelamin, tempat_lahir, tgl_lahir, status_tugas, tempat_tugas, npsn, kecamatan, id_kecamatan, no_hp, sk_cpns, tgl_cpns, sk_pengangkatan, tmt_pengangkatan, jenis_ptk, pendidikan, bidang_studi_pendidikan, bidang_studi_sertifikasi, status_kepegawaian, mapel_diajarkan, jam_mengajar_perminggu, jabatan_kepsek, jabatan_ks_plt, pangkat_golongan, nomor_sk_pangkat, tgl_sk_pangkat, tmt_pangkat, masa_kerja_tahun, masa_kerja_bulan, gaji_pokok, sk_kgb, tgl_sk_kgb, tmt_sk_kgb, masa_kerja_tahun_kgb, masa_kerja_bulan_kgb, gaji_pokok_kgb, mengajar_lain_satmikal, nomor_sk_impassing, tgl_sk_impassing, tmt_sk_impassing, jabatan_angka_kredit, pangkat_golongan_ruang, masa_kerja_tahun_impassing, masa_kerja_bulan_impassing, jumlah_tunjangan_pokok_impassing, lampiran_impassing, lampiran_foto, lampiran_karpeg, lampiran_ktp, lampiran_nrg, lampiran_nuptk, lampiran_serdik, lampiran_npwp, lampiran_buku_rekening, lampiran_ijazah, jenis_tunjangan, is_locked, created_at, updated_at, last_sync, CONCAT('$date', '') AS created_ajuan, CONCAT('$keterangan', ' ') AS keterangan_penghapusan
                                        FROM _ptk_tb
                                        WHERE id = '$id'");

                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->table('_ptk_tb')->where('id', $id)->delete();
                        if ($this->_db->affectedRows() > 0) {
                            $this->_db->transCommit();
                            $response = new \stdClass;
                            $response->status = 200;
                            $response->message = "Berhasil mengajukan hapus data.";
                            return json_encode($response);
                        } else {
                            $this->_db->transRollback();
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Gagal mengajukan hapus data.";
                            return json_encode($response);
                        }
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal mengajukan hapus data.";
                        return json_encode($response);
                    }
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengajukan hapus data.";
                    return json_encode($response);
                }
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function editSave()
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
                    'required' => 'Id PTK tidak boleh kosong. ',
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
            $nrg = htmlspecialchars($this->request->getVar('nrg'), true);
            $pendidikan = htmlspecialchars($this->request->getVar('pendidikan'), true);
            $no_peserta = htmlspecialchars($this->request->getVar('no_peserta'), true);
            $bidang_studi_sertifikasi = htmlspecialchars($this->request->getVar('bidang_studi_sertifikasi'), true);
            $pangkat = htmlspecialchars($this->request->getVar('pangkat'), true);
            $no_sk_pangkat = htmlspecialchars($this->request->getVar('no_sk_pangkat'), true);
            $tgl_pangkat = htmlspecialchars($this->request->getVar('tgl_pangkat'), true);
            $tmt_pangkat = htmlspecialchars($this->request->getVar('tmt_pangkat'), true);
            $mkt_pangkat = htmlspecialchars($this->request->getVar('mkt_pangkat'), true);
            $mkb_pangkat = htmlspecialchars($this->request->getVar('mkb_pangkat'), true);
            $kgb = htmlspecialchars($this->request->getVar('kgb'), true);
            $no_sk_kgb = htmlspecialchars($this->request->getVar('no_sk_kgb'), true);
            $tgl_kgb = htmlspecialchars($this->request->getVar('tgl_kgb'), true);
            $tmt_kgb = htmlspecialchars($this->request->getVar('tmt_kgb'), true);
            $mkt_kgb = htmlspecialchars($this->request->getVar('mkt_kgb'), true);
            $mkb_kgb = htmlspecialchars($this->request->getVar('mkb_kgb'), true);

            $oldData =  $this->_db->table('_ptk_tb')->where('id', $id)->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($nrg !== "") {
                $data['nrg'] = $nrg;
            }
            if ($no_peserta !== "") {
                $data['no_peserta'] = $no_peserta;
            }
            if ($pendidikan !== "") {
                $data['pendidikan'] = $pendidikan;
            }
            if ($bidang_studi_sertifikasi !== "") {
                $data['bidang_studi_sertifikasi'] = $bidang_studi_sertifikasi;
            }
            if ($pangkat !== "") {
                $data['pangkat_golongan'] = $pangkat;
            }
            if ($no_sk_pangkat !== "") {
                $data['nomor_sk_pangkat'] = $no_sk_pangkat;
            }
            if ($tgl_pangkat !== "") {
                $data['tgl_sk_pangkat'] = $tgl_pangkat;
            }
            if ($tmt_pangkat !== "") {
                $data['tmt_pangkat'] = $tmt_pangkat;
            }
            if ($mkt_pangkat !== "") {
                $data['masa_kerja_tahun'] = $mkt_pangkat;
            }
            if ($mkb_pangkat !== "") {
                $data['masa_kerja_bulan'] = $mkb_pangkat;
            }
            if ($kgb !== "") {
                $data['pangkat_golongan_kgb'] = $kgb;
            }
            if ($no_sk_kgb !== "") {
                $data['sk_kgb'] = $no_sk_kgb;
            }
            if ($tgl_kgb !== "") {
                $data['tgl_sk_kgb'] = $tgl_kgb;
            }
            if ($tmt_kgb !== "") {
                $data['tmt_sk_kgb'] = $tmt_kgb;
            }
            if ($mkt_kgb !== "") {
                $data['masa_kerja_tahun_kgb'] = $mkt_kgb;
            }
            if ($mkb_kgb !== "") {
                $data['masa_kerja_bulan_kgb'] = $mkb_kgb;
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_ptk_tb')->where('id', $oldData->id)->update($data);
            } catch (\Exception $e) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan gambar baru.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil diupdate.";
                return json_encode($response);
            } else {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupate data";
                return json_encode($response);
            }
        }
    }

    public function editSaveInpassing()
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
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
            'pangkat' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Pangkat tidak boleh kosong. ',
                ]
            ],
            'no_sk_pangkat' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'No SK Inpassing tidak boleh kosong. ',
                ]
            ],
            'tgl_pangkat' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Tgl SK Inpassing tidak boleh kosong. ',
                ]
            ],
            'tmt_pangkat' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TMT SK Inpassing tidak boleh kosong. ',
                ]
            ],
            'mkt_pangkat' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'MK Tahun Inpassing tidak boleh kosong. ',
                ]
            ],
            'mkb_pangkat' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'MK Bulan Inpassing tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('pangkat')
                . $this->validator->getError('no_sk_pangkat')
                . $this->validator->getError('tgl_pangkat')
                . $this->validator->getError('tmt_pangkat')
                . $this->validator->getError('mkt_pangkat')
                . $this->validator->getError('mkb_pangkat');
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
            $pangkat = htmlspecialchars($this->request->getVar('pangkat'), true);
            $no_sk_pangkat = htmlspecialchars($this->request->getVar('no_sk_pangkat'), true);
            $tgl_pangkat = htmlspecialchars($this->request->getVar('tgl_pangkat'), true);
            $tmt_pangkat = htmlspecialchars($this->request->getVar('tmt_pangkat'), true);
            $mkt_pangkat = htmlspecialchars($this->request->getVar('mkt_pangkat'), true);
            $mkb_pangkat = htmlspecialchars($this->request->getVar('mkb_pangkat'), true);

            $oldData =  $this->_db->table('_ptk_tb')->where('id', $id)->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($pangkat !== "") {
                $data['pangkat_golongan_ruang'] = $pangkat;
            }
            if ($no_sk_pangkat !== "") {
                $data['nomor_sk_impassing'] = $no_sk_pangkat;
            }
            if ($tgl_pangkat !== "") {
                $data['tgl_sk_impassing'] = $tgl_pangkat;
            }
            if ($tmt_pangkat !== "") {
                $data['tmt_sk_impassing'] = $tmt_pangkat;
            }
            if ($mkt_pangkat !== "") {
                $data['masa_kerja_tahun_impassing'] = $mkt_pangkat;
            }
            if ($mkb_pangkat !== "") {
                $data['masa_kerja_bulan_impassing'] = $mkb_pangkat;
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_ptk_tb')->where('id', $oldData->id)->update($data);
            } catch (\Exception $e) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan data baru.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil diupdate.";
                return json_encode($response);
            } else {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupate data";
                return json_encode($response);
            }
        }
    }
}
