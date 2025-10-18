<?php

namespace App\Controllers\Situgu\Ops\Masterdata;

use App\Controllers\BaseController;
use App\Models\Situgu\Ops\PtkModel;
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

    public function getAll()
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


        $npsn = $this->_helpLib->getNpsn($userId);

        $lists = $datamodel->get_datatables($npsn);
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
                            <a class="dropdown-item" href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Tarik Data</a>
                            <a class="dropdown-item" href="javascript:actionSyncDataPembenahan(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Syncrone Data Pembenahan</a>
                            <a class="dropdown-item" href="javascript:actionMutasi(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-trash font-size-16 align-middle"></i> &nbsp;Ajukan Mutasi PTK</a>
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
            "recordsTotal" => $datamodel->count_all($npsn),
            "recordsFiltered" => $datamodel->count_filtered($npsn),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        return redirect()->to(base_url('situgu/ops/masterdata/ptk/data'));
    }

    public function data()
    {
        $data['title'] = 'PTK';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;

        return view('situgu/ops/masterdata/ptk/index', $data);
    }

    public function syndapolocal()
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
                $response->message = "Session telah expired.";
                return json_encode($response);
            }

            $current = $this->_db->table('token_sync_local')
                ->where('npsn', $user->data->npsn)->get()->getRowObject();

            // if ($current) {
            $data['data'] = $current;
            $data['sekolah'] = $user->data;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ops/masterdata/ptk/syn_local_token', $data);
            return json_encode($response);
            // } else {
            //     $response = new \stdClass;
            //     $response->status = 400;
            //     $response->message = "Data tidak ditemukan";
            //     return json_encode($response);
            // }
        }
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
                $data['igd'] = $this->_db->table('_info_gtk')->where('ptk_id', $current->id_ptk)->get()->getRowObject();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/ops/masterdata/ptk/detail', $data);
                return json_encode($response);
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
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);

            $current = $this->_db->table('_users_tb')
                ->where('uid', $id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('a/setting/pengguna/edit', $data);
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

            $tw = $this->_helpLib->getCurrentTw();
            if (!$tw) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tahun Triwulan Active tidak ditemukan.";
                return json_encode($response);
            }

            $sekolahId = $this->_helpLib->getSekolahId($npsn);
            if (!$sekolahId) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Sekolah id tidak ditemukan.";
                return json_encode($response);
            }

            $checkAnySynToday = $this->_helpLib->checkAnySyncToday($npsn);

            // var_dump($checkAnySynToday);
            // die;

            $apiLib = new Apilib();
            if (!$checkAnySynToday) {
                $resultBack = $apiLib->syncPtkGetBackbone($npsn, $sekolahId);
                $insertAnySynToday = $this->_helpLib->insertSyncToday($npsn);
                if (!$insertAnySynToday) {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Data syncrone tidak ditemukan.";
                    return json_encode($response);
                }
            }

            if ($checkAnySynToday == date('Y-m-d')) {
                $resultBack = true;
            } else {
                $resultBack = $apiLib->syncPtkGetBackbone($npsn, $sekolahId);
            }



            if ($resultBack) {
                $result = $apiLib->syncPtk($npsn, $tw);

                if ($result) {
                    // var_dump($result);
                    // die;
                    if ($result->status == 200) {
                        $response = new \stdClass;
                        $response->status = 200;
                        $response->message = "Tarik Data Semua PTK Berhasil Dilakukan.";
                        return json_encode($response);
                    } else {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->result = $result;
                        $response->message = "Gagal Tarik Data";
                        return json_encode($response);
                    }
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
                . $this->validator->getError('id')
                . $this->validator->getError('nama')
                . $this->validator->getError('npsn');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
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

            $canGrantedPengajuan = canGrantedPengajuan($id, $tw);

            if ($canGrantedPengajuan && $canGrantedPengajuan->code !== 200) {
                return json_encode($canGrantedPengajuan);
            }

            $sekolahId = $this->_helpLib->getSekolahId($npsn);
            if (!$sekolahId) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Sekolah id tidak ditemukan.";
                return json_encode($response);
            }

            $checkAnySynToday = $this->_helpLib->checkAnySyncToday($npsn);
            $apiLib = new Apilib();
            if (!$checkAnySynToday) {
                $resultBack = $apiLib->syncPtkGetBackbone($npsn, $sekolahId);
                $insertAnySynToday = $this->_helpLib->insertSyncToday($npsn);
                if (!$insertAnySynToday) {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Data syncrone tidak ditemukan.";
                    return json_encode($response);
                }
            }

            if ($checkAnySynToday == date('Y-m-d')) {
                $resultBack = true;
            } else {
                $resultBack = $apiLib->syncPtkGetBackbone($npsn, $sekolahId);
            }

            if ($resultBack) {
                $result = $apiLib->syncPtkId($idPtk, $npsn, $tw);

                if ($result) {
                    if ($result->status == 200) {
                        $response = new \stdClass;
                        $response->status = 200;
                        $response->message = "Tarik Data PTK $nama Berhasil Dilakukan.";
                        return json_encode($response);
                    } else {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->error = $result;
                        $response->message = "Gagal Tarik Data.";
                        return json_encode($response);
                    }
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
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session telah habis.";
                return json_encode($response);
            }

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
                if ($ptk->tmt_sk_kgb == NULL || $ptk->tmt_sk_kgb == "" || $ptk->tmt_pangkat == NULL || $ptk->tmt_pangkat == "") {
                    if ($ptk->tmt_pangkat == NULL || $ptk->tmt_pangkat == "") {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal melakukan pembaharuan data attribut. Riwayat Kepangkatan pada master data PTK masih kosong.";
                        return json_encode($response);
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
            // $canUsulTamsil = canUsulTamsil();

            // if ($canUsulTamsil && $canUsulTamsil->code !== 200) {
            //     return json_encode($canUsulTamsil);
            // }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);

            $data['id'] = $id;
            $data['nama'] = $nama;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ops/masterdata/ptk/hapus', $data);
            return json_encode($response);
        }
    }

    public function formmutasi()
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
            // $canUsulTamsil = canUsulTamsil();

            // if ($canUsulTamsil && $canUsulTamsil->code !== 200) {
            //     return json_encode($canUsulTamsil);
            // }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);

            $data['id'] = $id;
            $data['nama'] = $nama;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ops/masterdata/ptk/hapus', $data);
            return json_encode($response);
        }
    }

    public function mutasi()
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
            'sekolah_tujuan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Sekolah Tujuan tidak boleh kosong. ',
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
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('sekolah_tujuan')
                . $this->validator->getError('keterangan');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $sekolah_tujuan = htmlspecialchars($this->request->getVar('sekolah_tujuan'), true);
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
                    $this->_db->query("INSERT INTO _ptk_tb_hapus(id, id_ptk, email, nama, gelar_depan, gelar_belakang, nik, nuptk, nip, nrg, no_peserta, npwp, no_rekening, cabang_bank, jenis_kelamin, tempat_lahir, tgl_lahir, status_tugas, tempat_tugas, npsn, kecamatan, id_kecamatan, no_hp, sk_cpns, tgl_cpns, sk_pengangkatan, tmt_pengangkatan, jenis_ptk, pendidikan, bidang_studi_pendidikan, bidang_studi_sertifikasi, status_kepegawaian, mapel_diajarkan, jam_mengajar_perminggu, jabatan_kepsek, jabatan_ks_plt, pangkat_golongan, nomor_sk_pangkat, tgl_sk_pangkat, tmt_pangkat, masa_kerja_tahun, masa_kerja_bulan, gaji_pokok, sk_kgb, tgl_sk_kgb, tmt_sk_kgb, masa_kerja_tahun_kgb, masa_kerja_bulan_kgb, gaji_pokok_kgb, mengajar_lain_satmikal, nomor_sk_impassing, tgl_sk_impassing, tmt_sk_impassing, jabatan_angka_kredit, pangkat_golongan_ruang, masa_kerja_tahun_impassing, masa_kerja_bulan_impassing, jumlah_tunjangan_pokok_impassing, lampiran_impassing, lampiran_foto, lampiran_karpeg, lampiran_ktp, lampiran_nrg, lampiran_nuptk, lampiran_serdik, lampiran_npwp, lampiran_buku_rekening, lampiran_ijazah, jenis_tunjangan, is_locked, created_at, updated_at, last_sync, created_ajuan, keterangan_penghapusan, sekolah_tujaun)
                                        SELECT id, id_ptk, email, nama, gelar_depan, gelar_belakang, nik, nuptk, nip, nrg, no_peserta, npwp, no_rekening, cabang_bank, jenis_kelamin, tempat_lahir, tgl_lahir, status_tugas, tempat_tugas, npsn, kecamatan, id_kecamatan, no_hp, sk_cpns, tgl_cpns, sk_pengangkatan, tmt_pengangkatan, jenis_ptk, pendidikan, bidang_studi_pendidikan, bidang_studi_sertifikasi, status_kepegawaian, mapel_diajarkan, jam_mengajar_perminggu, jabatan_kepsek, jabatan_ks_plt, pangkat_golongan, nomor_sk_pangkat, tgl_sk_pangkat, tmt_pangkat, masa_kerja_tahun, masa_kerja_bulan, gaji_pokok, sk_kgb, tgl_sk_kgb, tmt_sk_kgb, masa_kerja_tahun_kgb, masa_kerja_bulan_kgb, gaji_pokok_kgb, mengajar_lain_satmikal, nomor_sk_impassing, tgl_sk_impassing, tmt_sk_impassing, jabatan_angka_kredit, pangkat_golongan_ruang, masa_kerja_tahun_impassing, masa_kerja_bulan_impassing, jumlah_tunjangan_pokok_impassing, lampiran_impassing, lampiran_foto, lampiran_karpeg, lampiran_ktp, lampiran_nrg, lampiran_nuptk, lampiran_serdik, lampiran_npwp, lampiran_buku_rekening, lampiran_ijazah, jenis_tunjangan, is_locked, created_at, updated_at, last_sync, CONCAT('$date', '') AS created_ajuan, CONCAT('$keterangan', ' ') AS keterangan_penghapusan, CONCAT('$sekolah_tujuan', '') AS sekolah_tujaun
                                        FROM _ptk_tb
                                        WHERE id = '$id'");

                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->table('_ptk_tb')->where('id', $id)->delete();
                        if ($this->_db->affectedRows() > 0) {
                            $this->_db->transCommit();
                            $response = new \stdClass;
                            $response->status = 200;
                            $response->message = "Berhasil mengajukan mutasi data.";
                            return json_encode($response);
                        } else {
                            $this->_db->transRollback();
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Gagal mengajukan mutasi data.";
                            return json_encode($response);
                        }
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal mengajukan mutasi data.";
                        return json_encode($response);
                    }
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengajukan mutasi data.";
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
                    'required' => 'Id buku tidak boleh kosong. ',
                ]
            ],
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
            'email' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Email tidak boleh kosong. ',
                ]
            ],
            'nohp' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'No handphone tidak boleh kosong. ',
                ]
            ],
            'nip' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NIP tidak boleh kosong. ',
                ]
            ],
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat tidak boleh kosong. ',
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status tidak boleh kosong. ',
                ]
            ],
        ];

        $filenamelampiran = dot_array_search('file.name', $_FILES);
        if ($filenamelampiran != '') {
            $lampiranVal = [
                'file' => [
                    'rules' => 'uploaded[file]|max_size[file,512]|is_image[file]',
                    'errors' => [
                        'uploaded' => 'Pilih gambar profil terlebih dahulu. ',
                        'max_size' => 'Ukuran gambar profil terlalu besar. ',
                        'is_image' => 'Ekstensi yang anda upload harus berekstensi gambar. '
                    ]
                ],
            ];
            $rules = array_merge($rules, $lampiranVal);
        }

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('nama')
                . $this->validator->getError('id')
                . $this->validator->getError('email')
                . $this->validator->getError('nohp')
                . $this->validator->getError('nip')
                . $this->validator->getError('alamat')
                . $this->validator->getError('status')
                . $this->validator->getError('file');
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
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $email = htmlspecialchars($this->request->getVar('email'), true);
            $nohp = htmlspecialchars($this->request->getVar('nohp'), true);
            $nip = htmlspecialchars($this->request->getVar('nip'), true);
            $alamat = htmlspecialchars($this->request->getVar('alamat'), true);
            $status = htmlspecialchars($this->request->getVar('status'), true);

            $oldData =  $this->_db->table('_users_tb')->where('uid', $id)->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            if (
                $nama === $oldData->fullname
                && $email === $oldData->email
                && $nohp === $oldData->no_hp
                && $nip === $oldData->nip
                && $alamat === $oldData->alamat
                && (int)$status === (int)$oldData->is_active
            ) {
                if ($filenamelampiran == '') {
                    $response = new \stdClass;
                    $response->status = 201;
                    $response->message = "Tidak ada perubahan data yang disimpan.";
                    $response->redirect = base_url('a/setting/pengguna/data');
                    return json_encode($response);
                }
            }

            if ($email !== $oldData->email) {
                $cekData = $this->_db->table('_users_tb')->where(['email' => $email])->get()->getRowObject();
                if ($cekData) {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Email sudah terdaftar.";
                    return json_encode($response);
                }
            }

            $data = [
                'email' => $email,
                'fullname' => $nama,
                'no_hp' => $nohp,
                'nip' => $nip,
                'alamat' => $alamat,
                'is_active' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $dir = FCPATH . "uploads/user";

            if ($filenamelampiran != '') {
                $lampiran = $this->request->getFile('file');
                $filesNamelampiran = $lampiran->getName();
                $newNamelampiran = _create_name_foto($filesNamelampiran);

                if ($lampiran->isValid() && !$lampiran->hasMoved()) {
                    $lampiran->move($dir, $newNamelampiran);
                    $data['image'] = $newNamelampiran;
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengupload gambar.";
                    return json_encode($response);
                }
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_users_tb')->where('uid', $oldData->uid)->update($data);
            } catch (\Exception $e) {
                unlink($dir . '/' . $newNamelampiran);
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan gambar baru.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                try {
                    unlink($dir . '/' . $oldData->image);
                } catch (\Throwable $th) {
                }
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil diupdate.";
                $response->redirect = base_url('a/setting/pengguna/data');
                return json_encode($response);
            } else {
                unlink($dir . '/' . $newNamelampiran);
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupate data";
                return json_encode($response);
            }
        }
    }

    public function getSekolahMutasi()
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

            $current = $this->_db->table('ref_sekolah')->select("id, npsn, nama, bentuk_pendidikan, kecamatan")
                ->where("npsn = '$keyword' OR nama LIKE '%$keyword%'")->get()->getResult();

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
}
