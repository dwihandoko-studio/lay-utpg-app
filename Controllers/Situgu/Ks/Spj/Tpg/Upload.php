<?php

namespace App\Controllers\Situgu\Ks\Spj\Tpg;

use App\Controllers\BaseController;
use App\Models\Situgu\Ks\Spj\Tpg\UploadModel;
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
            // $action = '<a href="javascript:actionDetail(\'' . $list->id_usulan . '\', \'' . $list->id_ptk . '\', \'' . $list->id_tahun_tw . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i> DETAIL</button>
            //     </a>';
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
            // $row[] = rpAwalan($list->tf_jumlah_diterima);
            if ($list->lock_upload_spj == 1) {
                if ($list->lampiran_pernyataan == NULL || $list->lampiran_pernyataan == "") {
                    $row[] = '<a href="javascript:showNotifUpload(this);"><span class="badge rounded-pill badge-warning">Belum Upload</span></a>';
                } else {
                    $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/spj/tpg') . '/' . $list->lampiran_pernyataan . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/spj/tpg') . '/' . $list->lampiran_pernyataan . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
                }
                if ($list->lampiran_rekening_koran == NULL || $list->lampiran_rekening_koran == "") {
                    $row[] = '<a href="javascript:showNotifUpload(this);"><span class="badge rounded-pill badge-warning">Belum Upload</span></a>';
                } else {
                    $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/spj/tpg') . '/' . $list->lampiran_rekening_koran . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/spj/tpg') . '/' . $list->lampiran_rekening_koran . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
                }
                if ($list->lampiran_sk_dirgen == NULL || $list->lampiran_sk_dirgen == "") {
                    $row[] = '<a href="javascript:showNotifUpload(this);"><span class="badge rounded-pill badge-warning">Belum Upload</span></a>';
                } else {
                    $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/spj/tpg') . '/' . $list->lampiran_sk_dirgen . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/spj/tpg') . '/' . $list->lampiran_sk_dirgen . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
                }
            } else {
                if ($list->lampiran_pernyataan == null || $list->lampiran_pernyataan == "") {
                    // $row[] = '<a class="btn btn-sm btn-primary waves-effect waves-light" target="_blank" href="' . base_url('situgu/ptk/spj/tpg/download') . '?id=' . $list->id . '"><i class="bx bxs-cloud-download font-size-16 align-middle me-2"></i> Download</a>&nbsp;&nbsp;'
                    //     . '<a class="btn btn-sm btn-primary waves-effect waves-light" href="javascript:actionUpload(\'' . $list->id . '\',\'' . $list->tahun . '\',\'' . $list->tw . '\',\'pernyataan\');"><i class="bx bxs-cloud-upload font-size-16 align-middle me-2"></i> Upload</a>';
                    $row[] = '<a class="btn btn-sm btn-primary waves-effect waves-light" href="javascript:actionUpload(\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'pernyataan\',\'Pernyataan\');"><i class="bx bxs-cloud-upload font-size-16 align-middle me-2"></i> Upload</a>';
                } else {
                    // $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/spj/tpg') . '/' . $list->lampiran_pernyataan . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/spj/tpg') . '/' . $list->lampiran_pernyataan . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
                    $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/spj/tpg') . '/' . $list->lampiran_pernyataan . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/spj/tpg') . '/' . $list->lampiran_pernyataan . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                                <i class="bx bxs-show font-size-16 align-middle"></i></button>
                            </a>
                            <a href="javascript:actionEditFile(\'Pernyataan\',\'pernyataan\',\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'' . $list->lampiran_pernyataan . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                                <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                            </a>
                            <a href="javascript:actionHapusFile(\'Pernyataan\',\'pernyataan\',\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'' . $list->lampiran_pernyataan . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                                <i class="mdi mdi-trash-can-outline font-size-16 align-middle"></i></button>
                            </a>';
                }
                if ($list->lampiran_rekening_koran == null || $list->lampiran_rekening_koran == "") {
                    $row[] = '<a class="btn btn-sm btn-primary waves-effect waves-light" href="javascript:actionUpload(\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'rekeningkoran\',\'Rekening Koran\');"><i class="bx bxs-cloud-upload font-size-16 align-middle me-2"></i> Upload</a>';
                } else {
                    // $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/spj/tpg') . '/' . $list->lampiran_rekening_koran . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/spj/tpg') . '/' . $list->lampiran_rekening_koran . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
                    $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/spj/tpg') . '/' . $list->lampiran_rekening_koran . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/spj/tpg') . '/' . $list->lampiran_rekening_koran . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                                <i class="bx bxs-show font-size-16 align-middle"></i></button>
                            </a>
                            <a href="javascript:actionEditFile(\'Rekening Koran\',\'rekeningkoran\',\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'' . $list->lampiran_rekening_koran . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                                <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                            </a>
                            <a href="javascript:actionHapusFile(\'Rekening Koran\',\'rekeningkoran\',\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'' . $list->lampiran_rekening_koran . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                                <i class="mdi mdi-trash-can-outline font-size-16 align-middle"></i></button>
                            </a>';
                }
                if ($list->lampiran_sk_dirgen == null || $list->lampiran_sk_dirgen == "") {
                    $row[] = '<a class="btn btn-sm btn-primary waves-effect waves-light" href="javascript:actionUpload(\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'skdirgen\',\'SK Dirgen\');"><i class="bx bxs-cloud-upload font-size-16 align-middle me-2"></i> Upload</a>';
                } else {
                    // $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/spj/tpg') . '/' . $list->lampiran_sk_dirgen . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/spj/tpg') . '/' . $list->lampiran_sk_dirgen . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
                    $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/spj/tpg') . '/' . $list->lampiran_sk_dirgen . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/spj/tpg') . '/' . $list->lampiran_sk_dirgen . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                                <i class="bx bxs-show font-size-16 align-middle"></i></button>
                            </a>
                            <a href="javascript:actionEditFile(\'SK Dirgen\',\'skdirgen\',\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'' . $list->lampiran_sk_dirgen . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                                <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                            </a>
                            <a href="javascript:actionHapusFile(\'SK Dirgen\',\'skdirgen\',\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'' . $list->lampiran_sk_dirgen . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                                <i class="mdi mdi-trash-can-outline font-size-16 align-middle"></i></button>
                            </a>';
                }
            }

            switch ((int)$list->status_usulan) {
                case 1:
                    $row[] = '<a href="javascript:actionAjukanUlang(\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'' . $list->tahun . '\',\'' . $list->tw . '\');"><span class="badge rounded-pill badge-soft-info font-size-11">Ajukan Ulang</span></a>';
                    break;
                case 3:
                    $row[] = '<a href="javascript:actionAjukanUlang(\'' . $list->id . '\',\'' . $list->id_tahun_tw . '\',\'' . $list->tahun . '\',\'' . $list->tw . '\');"><span class="badge rounded-pill badge-soft-info font-size-11">Ajukan Ulang</span></a>';
                    break;
                case 2:
                    $row[] = '<span class="badge rounded-pill badge-success">Terverifikasi</span>';
                    break;

                default:
                    if ($list->lampiran_pernyataan == NULL || $list->lampiran_pernyataan == "" || $list->lampiran_rekening_koran == null || $list->lampiran_rekening_koran == "" || $list->lampiran_sk_dirgen == null || $list->lampiran_sk_dirgen == "") {
                        $row[] = '<span class="badge rounded-pill badge-danger">Belum Upload</span>';
                    } else {
                        $row[] = '<span class="badge rounded-pill badge-warning">Dalam Antrian</span>';
                    }
                    break;
            }

            $row[] = $list->status_usulan == 1 || $list->status_usulan == 3 ? $list->keterangan_reject : "";

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
        return redirect()->to(base_url('situgu/ks/spj/tpg/upload/data'));
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
        $data['tws'] = $this->_db->table('_ref_tahun_tw')->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getResult();
        return view('situgu/ks/spj/tpg/upload/index', $data);
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

    public function downloadnew()
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

        $id = htmlspecialchars($this->request->getGet('id'), true);

        $current = $this->_db->table('_tb_sptjm')->where('id', $id)->get()->getRowObject();
        if (!$current) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "SPTJM tidak ditemukan. Silahkan Generate terlebih dahulu.";
            return json_encode($response);
        }

        $ptks = explode(",", $current->id_ptks);
        $dataPtks = [];
        foreach ($ptks as $key => $value) {
            $ptk = $this->_db->table('v_temp_usulan')->where(['id_ptk_usulan' => $value, 'status_usulan' => 5, 'jenis_tunjangan_usulan' => 'tpg'])->get()->getRowObject();
            if ($ptk) {
                $dataPtks[] = $ptk;
            }
        }

        $sekolah = $this->_db->table('ref_sekolah')->where('npsn', $user->data->npsn)->get()->getRowObject();
        if (!$sekolah) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Referensi sekolah tidak ditemukan.";
            return json_encode($response);
        }

        $idUser = $this->_helpLib->getPtkId($user->data->id);
        $ks = $this->_db->table('_ptk_tb')->where('id', $idUser)->get()->getRowObject();

        return $this->_download_new($dataPtks, $sekolah, $ks, $current);
    }

    private function _download_new($ptks, $sekolah, $ks, $usulan)
    {
        if (count($ptks) > 0) {
            $m = new Merger();

            $dataFileGambar = file_get_contents(FCPATH . './uploads/tutwuri.png');
            $base64 = "data:image/png;base64," . base64_encode($dataFileGambar);

            $qrCode = "data:image/png;base64," . base64_encode(file_get_contents('https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=layanan.disdikbud.lampungtengahkab.go.id/verifiqrcode?token=' . $usulan->kode_usulan . '&choe=UTF-8'));

            $alamat = $sekolah->alamat_jalan ? ($sekolah->alamat_jalan !== "" ? $sekolah->alamat_jalan : "-") : "-";
            $no_telp = $sekolah->no_telepon ? ($sekolah->no_telepon !== "" ? $sekolah->no_telepon : "-") : "-";
            $email = $sekolah->email ? ($sekolah->email !== "" ? $sekolah->email : "-") : "-";

            if ($ptks[0]->tw_tw == 1) {
                $bulanTampil = 'Januari s/d Maret';
            } else if ($ptks[0]->tw_tw == 2) {
                $bulanTampil = 'April s/d Juni';
            } else if ($ptks[0]->tw_tw == 3) {
                $bulanTampil = 'Juli s/d September';
            } else if ($ptks[0]->tw_tw == 4) {
                $bulanTampil = 'Oktober s/d Desember';
            }

            $nama_ks = "";
            if ($ks->gelar_depan && ($ks->gelar_depan !== "" || $ks->gelar_depan !== "-")) {
                $nama_ks .= $ks->gelar_depan;
            }
            $nama_ks .= $ks->nama;
            if ($ks->gelar_belakang && ($ks->gelar_belakang !== "" || $ks->gelar_belakang !== "-")) {
                $nama_ks .= $ks->gelar_belakang;
            }

            $nipKs = "";
            if ($ks->nip && ($ks->nip !== "" || $ks->nip !== "-")) {
                $nipKs .= $ks->nip;
            } else {
                $nipKs .= "-";
            }

            $jabatan_ks = $ks->jabatan_ks_plt ? ($ks->jabatan_ks_plt == 0 ? "Kepala Sekolah" : "Plt. Kepala Sekolah") : "Kepala Sekolah";

            $html   =  '<html>
                        <head>
                            <link href="';
            $html   .=              base_url('uploads/bootstrap.css');
            $html   .=          '" rel="stylesheet">
                        </head>
                        <body>
                            <div class="container">
                                <div class="row">
                                    <table class="table table-responsive" style="border: none;">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <img class="image-responsive" width="110px" height="110px" src="';
            $html   .=                                      $base64;
            $html   .=                                  '"/>
                                                </td>
                                                <td>
                                                    &nbsp;&nbsp;&nbsp;
                                                </td>
                                                <td>
                                                    <h3 style="margin: 0rem;font-size: 16px; font-weight: 500;">PEMERINTAH KABUPATEN LAMPUNG TENGAH</h3>
                                                    <h3 style="margin: 0rem;font-size: 16px; font-weight: 500;">DINAS PENDIDIKAN DAN KEBUDAYAAN</h3>
                                                    <h3 style="margin: 0rem;font-size: 16px; font-weight: 500;">KABUPATEN LAMPUNG TENGAH</h3>
                                                    <h3 style="margin: 0rem;font-size: 16px; font-weight: 500;">';
            $html   .=                                      $sekolah->nama;
            $html   .=                                  '</h3>
                                                    <h3 style="margin: 0rem;font-size: 14px;font-weight: 400;"><b>';
            $html   .=                                      $sekolah->npsn;
            $html   .=                                      '</b><span>, ';
            $html   .=                                      $alamat;
            $html   .=                                      '</span></h3>
                                                    <h4 style="margin: 0rem;font-size: 12px;font-weight: 400;">';
            $html   .=                                      $sekolah->kecamatan;
            $html   .=                                      ', ' . getenv('setting.utpg.kabupaten') . ', ' . getenv('setting.utpg.provinsi') . '</h4>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div style="text-align: center;margin-top: 30px; border:1px solid black;">
                                        <h3 style="margin: 0rem;font-size: 14px;">SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK (SPTJM)</h3>
                                        <h3 style="margin: 0rem;font-size: 14px;">USULAN PENCARIAN TUNJANGAN PROFESI GURU (TPG)</h3>
                                        <h3 style="margin: 0rem;font-size: 14px;">TRIWULAN ';
            $html   .=                          $ptks[0]->tw_tw;
            $html   .=                          ' TAHUN ANGGARAN ';
            $html   .=                          $ptks[0]->tw_tahun;
            $html   .=                          '</h3>
                                        <h3 style="margin: 0rem;font-size: 12px;">NOMOR: ';
            $html   .=                          $usulan->kode_usulan;
            $html   .=                          '</h3>
                                    </div>
                                </div>
                                <div class="row" style="margin-left: 30px;margin-right:30px;">
                                    <div style="text-align: justify;margin-top: 30px;">
                                        <p style="margin-bottom: 15px;font-size: 12px;">Yang bertanda tangan di bawah ini :</p>
                                        <p style="margin-bottom: 15px; margin-top: 0px; font-size: 12px; padding-top: 0px; padding-bottom: 0px;">
                                            <table style="border: none;font-size: 12px; margin-bottom: 0px; margin-top: 0px; padding-bottom: 0px; padding-top: 0px;">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            Nama
                                                        </td>
                                                        <td>&nbsp;: &nbsp;</td>
                                                        <td>&nbsp;';
            $html   .=                                          $nama_ks;
            $html   .=                                      '</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Jabatan
                                                        </td>
                                                        <td>&nbsp;: &nbsp;</td>
                                                        <td>&nbsp;';
            $html   .=                                          $jabatan_ks;
            $html   .=                                      '</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Satuan Pendidikan
                                                        </td>
                                                        <td>&nbsp;: &nbsp;</td>
                                                        <td>&nbsp;';
            $html   .=                                          $sekolah->nama;
            $html   .=                                      '</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </p>
                                        <p style="margin-top: 20px;font-size: 12px;">
                                            Menyatakan dengan sesungguhnya bahwa: 
                                            <ol style="font-size: 12px;">
                                                <li style="font-size: 12px;">Bertanggung jawab penuh atas kebenaran dan kemutakhiran data yang diusulkan dan dikirimkan oleh masing-masing PTK di sekolahan kami, melalui aplikasi Si-Tugu (Sistem Informasi Tunjangan Guru) Kabupaten Lampung Tengah <b>Periode TW. ' . $ptks[0]->tw_tw . ' Tahun ' . $ptks[0]->tw_tahun . '</b>.</li>
                                                <li style="font-size: 12px;">Saya telah melakukan verifikasi dan validasi data guru yang diajukan, serta melakukan monitoring proses kinerja masing-masing guru di Satuan Pendidikan yang saya pimpin. Apabila dari data guru yang mengajukan, dari hasil verifikasi validasi data oleh Admin Dinas, ditemukan syarat dalam proses usulan pencairan TPG yang diatur berdasarkan PP 41 2017 dan PP 19 tentang Perubahan atas PP 41 tahun 2009, tidak sesuai/belum memenuhi persyaratan dengan kondisi keadaan yang sebenarnya, maka saya menerima usulan tersebut ditolak untuk dapat diperbaiki dan diajukan kembali pada periode jadwal yang telah ditetapkan.</li>
                                                <li style="font-size: 12px;">Apabila di kemudian hari terdapat ketidaksesuaian antara data yang dikirimkan/diajukan dengan keadaan yang sebenarnya, kami bertanggung jawab sepenuhnya dan bersedia menerima sanksi sesuai dengan ketentuan peraturan perundang-undangan.</li>
                                            </ol>
                                        </p>
                                        <p style="margin-bottom: 15px;font-size: 12px;">
                                            <table style="width: 100%;max-width: 100%;font-size: 12px;" border="1">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 50%;font-size: 12px;"><center>
                                                            <b>Jumlah PTK Yang Mengajukan Validasi Usulan TPG</b>
                                                            </center>
                                                        </td>
                                                        <td style="width: 50%;font-size: 12px;"><center><b>Keterangan</b></center>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 50%;font-size: 12px;"><center>' . $usulan->jumlah_ptk . '</center>
                                                        </td>
                                                        <td style="width: 50%;font-size: 12px;"><center>Di setujui Kepala Sekolah</center>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </p>
                                        <p style="font-size: 12px;">
                                            Demikan pernyataan ini saya buat dengan sebenar-benarnya.
                                        </p>
                                    </div>
                                </div>
                                <div class="row" style="margin-left: 30px;margin-right:30px;">
                                    <div>
                                        <table style="width: 100%;max-width: 100%;" border="0">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 60%"><center><img class="image-responsive" width="110px" height="110px" src="' . $qrCode . '"/></center></td>
                                                    <td style="width: 40%">
                                                        <br>
                                                        <br>
                                                        <span style="font-size: 12px;">Lampung Tengah, ';
            $html   .=                                          tgl_indo(date('Y-m-d'));
            $html   .=                                      '</span><br>
                                                        <span style="font-size: 12px;">Kepala Sekolah, ' . $sekolah->nama . '</span><br><br><br><span style="font-size: 10px; color: #1c1c1cb8;">Materai 10.000</span><br><br>
                                                        <span style="font-size: 12px;"><b><u>';
            $html   .=                                          $nama_ks;
            $html   .=                                      '</u></b></span><br>
                                                        <span style="font-size: 12px;">NIP. ';
            $html   .=                                          $nipKs;
            $html   .=                                      '</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                    </div>
                                </div>
                            </div>
                        </body>
                    </html>';

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('F4', 'potrait');
            $dompdf->render();
            $m->addRaw($dompdf->output());
            unset($dompdf);

            $lHtml   =  '<html>
                        <head>
                            <link href="';
            $lHtml   .=              base_url('uploads/bootstrap.css');
            $lHtml   .=          '" rel="stylesheet">
                        </head>
                        <body>
                            <div class="container">
                                <div class="row">
                                    <p style="margin-bottom: 0px; font-size: 12px">LAMPIRAN SPTJM TUNJANGAN PROFESI GURU (TPG) TRIWULAN ' . $ptks[0]->tw_tw . ', TAHUN ANGGARAN ' . $ptks[0]->tw_tahun . '</p>
                                    <p style="margin-top: 0px; margin-bottom: 0px; font-size: 14px;"><b>' . $sekolah->nama . '</b></p>
                                    <p style="margin-top: 0px; margin-bottom: 0px; font-size: 12px;">NPSN: ' . $sekolah->npsn . ', Alamat: ' . $alamat . ', ' . $sekolah->kecamatan . '</p>
                                    <p style="margin-top: 0px; margin-bottom: 0px; font-size: 12px;">' . getenv('setting.utpg.kabupaten') . ' - ' . getenv('setting.utpg.provinsi') . '</p>
                                    <p style="margin-bottom: 0px; margin-top: 0px; padding-top: 0px; padding-bottom: 0px; font-size: 12px;">Nomor : ';
            $lHtml   .=                  $usulan->kode_usulan;
            $lHtml   .=                  '</p>
                                    <p style="margin-top: 10px; margin-bottom: 0px;">
                                        <table class="table" style="border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17);">
                                            <thead style="border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17);">
                                                <tr style="border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17);">
                                                    <th rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-left-width: 1px; border-left-style: solid;border-left-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">No</th>
                                                    <th rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">NRG</th>
                                                    <th rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">No Peserta</th>
                                                    <th rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">NUPTK</th>
                                                    <th rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">NIP</th>
                                                    <th width="10%" rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">Nama</th>
                                                    <th rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 10px; padding-right: 10px;">Gol</th>
                                                    <th colspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">Masa Kerja</th>
                                                    <th width="5%" rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px;">Gaji Pokok Sesuai SPJ Gaji</th>
                                                    <th colspan="4" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">Pembayaran TW. ' . $ptks[0]->tw_tw . ' Bulan ' . $bulanTampil . '</th>
                                                    <th rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">NPWP</th>
                                                    <th rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">No Rekening</th>
                                                    <th rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">Cabang Bank</th>
                                                </tr>
                                                <tr>
                                                    <th style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px;">Thn</th>
                                                    <th style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px;">Bln</th>
                                                    <th style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px;">Jml Bln</th>
                                                    <th style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px;">Jml Uang</th>
                                                    <th style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px;">PPH 21</th>
                                                    <th style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px;">Jml Diterima</th>
                                                </tr>
                                            </thead>
                                            <tbody style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center;">';
            if (count($ptks) > 0) {
                foreach ($ptks as $key => $v) {
                    $pph = "0%";
                    $pph21 = 0;
                    if ($v->us_pang_golongan == NULL || $v->us_pang_golongan == "") {
                    } else {
                        $pang = explode("/", $v->us_pang_golongan);
                        if ($pang[0] == "III" || $pang[0] == "IX") {
                            $pph21 = (5 / 100);
                            $pph = "5%";
                        } else if ($pang[0] == "IV") {
                            $pph21 = (15 / 100);
                            $pph = "15%";
                        } else {
                            $pph21 = 0;
                            $pph = "0%";
                        }
                    }

                    $lHtml  .= '<tr style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center;">
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . (string)($key + 1) . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->nrg . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->no_peserta . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->nuptk . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->nip . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: left; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->nama . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . $v->us_pang_golongan . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . $v->us_pang_mk_tahun . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . $v->us_pang_mk_bulan . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . rpTanpaAwalan($v->us_gaji_pokok) . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">3</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . rpTanpaAwalan(($v->us_gaji_pokok * 3)) . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . $pph . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . rpTanpaAwalan(($v->us_gaji_pokok * 3) - (($v->us_gaji_pokok * 3) * $pph21)) . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->npwp . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: right; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->no_rekening . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: left; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->cabang_bank . '</td>
                                                </tr>';
                }
            }
            $lHtml   .=                          '</tbody>
                                        </table>
                                    </p>
                                    <p style="margin-top: 10px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px;">
                                        <div class="row" style="margin-left: 30px;margin-right:30px;margin-top: 10px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px;">
                                            <div style="margin-top: 10px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px;">
                                                <table style="width: 100%;max-width: 100%; margin-top: 10px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px;" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width: 60%"><center><img class="image-responsive" width="110px" height="110px" src="' . $qrCode . '"/></center></td>
                                                            <td style="width: 40%">
                                                                <br>
                                                                <br>
                                                                <span style="font-size: 12px;">Lampung Tengah, ';
            $lHtml   .=                                          tgl_indo(date('Y-m-d'));
            $lHtml   .=                                      '</span><br>
                                                                <span style="font-size: 12px;">Kepala Sekolah, ' . $sekolah->nama . '</span><br><br><span style="font-size: 10px; color: #1c1c1cb8;">&nbsp;</span><br><br>
                                                                <span style="font-size: 12px;"><b><u>';
            $lHtml   .=                                          $nama_ks;
            $lHtml   .=                                      '</u></b></span><br>
                                                                <span style="font-size: 12px;">NIP. ';
            $lHtml   .=                                          $nipKs;
            $lHtml   .=                                      '</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                
                                            </div>
                                        </div>
                                    </p>
                                </div>
                            </div>
                        </body>
                    </html>';

            $dompdf1 = new DOMPDF();
            // $dompdf = new Dompdf();
            $dompdf1->set_paper('F4', 'landscape');
            $dompdf1->load_html($lHtml);
            $dompdf1->render();
            $m->addRaw($dompdf1->output());

            $dir = FCPATH . "upload/generate/sptjm/tpg/pdf";
            $fileNya = $dir . '/' . $usulan->kode_usulan . '.pdf';

            file_put_contents($fileNya, $m->merge());

            sleep(3);

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($fileNya) . '"');
            header('Content-Length: ' . filesize($fileNya));
            readfile($fileNya);

            return;
        } else {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Gagal mendownload SPTJM.";
            return json_encode($response);
        }



        // Output the generated PDF to Browser 
        // $dompdf->stream('SPJTM - ' . $token);

        // // Output the generated PDF (1 = download and 0 = preview) 
        // return $dompdf->stream("SPJTM-" . $token, array("Attachment" => 1));
    }

    public function formupload()
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
            'jenis' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Jenis tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            'title' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Title tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('jenis')
                . $this->validator->getError('tw')
                . $this->validator->getError('title');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $jenis = htmlspecialchars($this->request->getVar('jenis'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);

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

            $canUploadSpj = canUploadSpjTpg();
            if ($canUploadSpj->code !== 200) {
                $canGrantedUploadSpj = canGrantedUploadSpj($user->data->ptk_id);
                if ($canGrantedUploadSpj->code !== 200) {
                    return json_encode($canUploadSpj);
                }
            }

            $data['id'] = $id;
            $data['title'] = $title;
            $data['jenis'] = $jenis;
            $data['tw'] = $tw;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ks/spj/tpg/upload/upload', $data);
            return json_encode($response);
        }
    }

    public function editformupload()
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
            'file' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Jenis tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            'title' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Title tidak boleh kosong. ',
                ]
            ],
            'old' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Old tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('file')
                . $this->validator->getError('tw')
                . $this->validator->getError('old')
                . $this->validator->getError('title');
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

            $canUploadSpj = canUploadSpjTpg();
            if ($canUploadSpj->code !== 200) {
                $canGrantedUploadSpj = canGrantedUploadSpj($user->data->ptk_id);
                if ($canGrantedUploadSpj->code !== 200) {
                    return json_encode($canUploadSpj);
                }
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $jenis = htmlspecialchars($this->request->getVar('file'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);
            $old = htmlspecialchars($this->request->getVar('old'), true);

            $data['id'] = $id;
            $data['title'] = $title;
            $data['old'] = $old;
            $data['jenis'] = $jenis;
            $data['tw'] = $tw;
            $data['old_url'] = base_url('upload/spj/tpg') . '/' . $old;

            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ks/spj/tpg/upload/editupload', $data);
            return json_encode($response);
        }
    }

    public function uploadSave()
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
            'jenis' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Jenis tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            'title' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Title tidak boleh kosong. ',
                ]
            ],
            '_file' => [
                'rules' => 'uploaded[_file]|max_size[_file,2048]|mime_in[_file,image/jpeg,image/jpg,image/png,application/pdf]',
                'errors' => [
                    'uploaded' => 'Pilih file terlebih dahulu. ',
                    'max_size' => 'Ukuran file terlalu besar, Maximum 2Mb. ',
                    'mime_in' => 'Ekstensi yang anda upload harus berekstensi gambar dan pdf. '
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('jenis')
                . $this->validator->getError('tw')
                . $this->validator->getError('title')
                . $this->validator->getError('_file');
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

            $canUploadSpj = canUploadSpjTpg();
            if ($canUploadSpj->code !== 200) {
                $canGrantedUploadSpj = canGrantedUploadSpj($user->data->ptk_id);
                if ($canGrantedUploadSpj->code !== 200) {
                    return json_encode($canUploadSpj);
                }
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $jenis = htmlspecialchars($this->request->getVar('jenis'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
                'status_usulan' => 0,
            ];

            $dir = "";
            $field_db = '';
            $table_db = '';

            switch ($jenis) {
                case 'pernyataan':
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_pernyataan';
                    $table_db = '_tb_spj_tpg';
                    break;
                case 'rekeningkoran':
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_rekening_koran';
                    $table_db = '_tb_spj_tpg';
                    break;
                case 'skdirgen':
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_sk_dirgen';
                    $table_db = '_tb_spj_tpg';
                    break;
                default:
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_pernyataan';
                    $table_db = '_tb_spj_tpg';
                    break;
            }

            $lampiran = $this->request->getFile('_file');
            $filesNamelampiran = $lampiran->getName();
            $newNamelampiran = _create_name_file_spj($filesNamelampiran, $jenis);

            if ($lampiran->isValid() && !$lampiran->hasMoved()) {
                $lampiran->move($dir, $newNamelampiran);
                $data[$field_db] = $newNamelampiran;
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupload file.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table($table_db)->where("id = '$id' AND (lock_upload_spj = 0 OR lock_upload_spj IS NULL)")->update($data);
            } catch (\Exception $e) {
                unlink($dir . '/' . $newNamelampiran);

                $this->_db->transRollback();

                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($e);
                $response->message = "Gagal menyimpan data.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                createAktifitas($user->data->id, "Mengupload file $title SPJ", "Mengupload File $title", "upload");
                $this->_db->transCommit();

                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil disimpan.";
                return json_encode($response);
            } else {
                unlink($dir . '/' . $newNamelampiran);

                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan data";
                return json_encode($response);
            }
        }
    }

    public function editUploadSave()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'jenis' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Jenis tidak boleh kosong. ',
                ]
            ],
            'old' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Old tidak boleh kosong. ',
                ]
            ],
            'id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Id tidak boleh kosong. ',
                ]
            ],
            'title' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Title tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            '_file' => [
                'rules' => 'uploaded[_file]|max_size[_file,2048]|mime_in[_file,image/jpeg,image/jpg,image/png,application/pdf]',
                'errors' => [
                    'uploaded' => 'Pilih file terlebih dahulu. ',
                    'max_size' => 'Ukuran file terlalu besar, Maximum 2Mb. ',
                    'mime_in' => 'Ekstensi yang anda upload harus berekstensi gambar dan pdf. '
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('jenis')
                . $this->validator->getError('title')
                . $this->validator->getError('tw')
                . $this->validator->getError('old')
                . $this->validator->getError('id')
                . $this->validator->getError('_file');
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

            $canUploadSpj = canUploadSpjTpg();
            if ($canUploadSpj->code !== 200) {
                $canGrantedUploadSpj = canGrantedUploadSpj($user->data->ptk_id);
                if ($canGrantedUploadSpj->code !== 200) {
                    return json_encode($canUploadSpj);
                }
            }

            $jenis = htmlspecialchars($this->request->getVar('jenis'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $old = htmlspecialchars($this->request->getVar('old'), true);
            $id = htmlspecialchars($this->request->getVar('id'), true);

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
                'status_usulan' => 0,
            ];

            $dir = "";
            $field_db = '';
            $table_db = '';

            switch ($jenis) {
                case 'pernyataan':
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_pernyataan';
                    $table_db = '_tb_spj_tpg';
                    break;
                case 'rekeningkoran':
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_rekening_koran';
                    $table_db = '_tb_spj_tpg';
                    break;
                case 'skdirgen':
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_sk_dirgen';
                    $table_db = '_tb_spj_tpg';
                    break;
                default:
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_pernyataan';
                    $table_db = '_tb_spj_tpg';
                    break;
            }

            $lampiran = $this->request->getFile('_file');
            $filesNamelampiran = $lampiran->getName();
            $newNamelampiran = _create_name_file_spj($filesNamelampiran, $jenis);

            if ($lampiran->isValid() && !$lampiran->hasMoved()) {
                $lampiran->move($dir, $newNamelampiran);
                $data[$field_db] = $newNamelampiran;
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupload file.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                // $this->_db->table($table_db)->where(['id' => $id_ptk, 'is_locked' => 0])->update($data);
                $this->_db->table($table_db)->where("id = '$id' AND (lock_upload_spj = 0 OR lock_upload_spj IS NULL)")->update($data);
            } catch (\Exception $e) {
                unlink($dir . '/' . $newNamelampiran);

                $this->_db->transRollback();

                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($e);
                $response->message = "Gagal menyimpan data.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                createAktifitas($user->data->id, "Mengedit upload file $title SPJ", "Mengedit Upload $title SPJ", "edit");
                $this->_db->transCommit();
                try {
                    unlink($dir . '/' . $old);
                } catch (\Throwable $th) {
                    //throw $th;
                }

                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil diupdate.";
                return json_encode($response);
            } else {
                unlink($dir . '/' . $newNamelampiran);

                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan data";
                return json_encode($response);
            }
        }
    }

    public function aksiajukanulang()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'tahun' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Tahun tidak boleh kosong. ',
                ]
            ],
            'triwulan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Triwulan tidak boleh kosong. ',
                ]
            ],
            'id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Id tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('tahun')
                . $this->validator->getError('triwulan')
                . $this->validator->getError('tw')
                . $this->validator->getError('id');
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

            $canUploadSpj = canUploadSpjTpg();
            if ($canUploadSpj->code !== 200) {
                $canGrantedUploadSpj = canGrantedUploadSpj($user->data->ptk_id);
                if ($canGrantedUploadSpj->code !== 200) {
                    return json_encode($canUploadSpj);
                }
            }

            $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
            $triwulan = htmlspecialchars($this->request->getVar('triwulan'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $id = htmlspecialchars($this->request->getVar('id'), true);

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
                'status_usulan' => 0,
            ];

            $dir = "";
            $field_db = '';
            $table_db = '_tb_spj_tpg';

            $this->_db->transBegin();
            try {
                // $this->_db->table($table_db)->where(['id' => $id_ptk, 'is_locked' => 0])->update($data);
                $this->_db->table($table_db)->where("id = '$id' AND (lock_upload_spj = 0 OR lock_upload_spj IS NULL)")->update($data);
            } catch (\Exception $e) {

                $this->_db->transRollback();

                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($e);
                $response->message = "Gagal mengajukan ulang laporan spj.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                createAktifitas($user->data->id, "Mengajukan ulang laporan SPJ TPG", "Mengajukan Ulang SPJ TPG", "edit");
                $this->_db->transCommit();

                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Laporan SPJ TPG Tahun $tahun Tw. $triwulan berhasil diajukan ulang.";
                return json_encode($response);
            } else {

                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengajukan ulang laporan spj.";
                return json_encode($response);
            }
        }
    }

    public function hapusfile()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            'title' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Title tidak boleh kosong. ',
                ]
            ],
            'jenis' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Jenis tidak boleh kosong. ',
                ]
            ],
            'old' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Old tidak boleh kosong. ',
                ]
            ],
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
            $response->message = $this->validator->getError('tw')
                . $this->validator->getError('jenis')
                . $this->validator->getError('old')
                . $this->validator->getError('file')
                . $this->validator->getError('id');
            return json_encode($response);
        } else {
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);
            $jenis = htmlspecialchars($this->request->getVar('jenis'), true);
            $old = htmlspecialchars($this->request->getVar('old'), true);
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

            $canUploadSpj = canUploadSpjTpg();
            if ($canUploadSpj->code !== 200) {
                $canGrantedUploadSpj = canGrantedUploadSpj($user->data->ptk_id);
                if ($canGrantedUploadSpj->code !== 200) {
                    return json_encode($canUploadSpj);
                }
            }

            switch ($jenis) {
                case 'pernyataan':
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_pernyataan';
                    $table_db = '_tb_spj_tpg';
                    break;
                case 'rekeningkoran':
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_rekening_koran';
                    $table_db = '_tb_spj_tpg';
                    break;
                case 'skdirgen':
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_sk_dirgen';
                    $table_db = '_tb_spj_tpg';
                    break;
                default:
                    $dir = FCPATH . "upload/spj/tpg";
                    $field_db = 'lampiran_pernyataan';
                    $table_db = '_tb_spj_tpg';
                    break;
            }

            $currentFile = $this->_db->table($table_db)->select("$field_db AS file, id")->where(['id' => $id, 'lock_upload_spj' => 0])->get()->getRowObject();
            if (!$currentFile) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menghapus file. Data tidak ditemukan.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table($table_db)->where(['id' => $currentFile->id, 'lock_upload_spj' => 0])->update([$field_db => null, 'updated_at' => date('Y-m-d H:i:s')]);
            } catch (\Exception $e) {
                $this->_db->transRollback();

                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($e);
                $response->message = "Gagal menghapus file $title SPJ.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                createAktifitas($user->data->id, "Menghapus file $title SPJ", "Menghapus File $title", "delete", $tw);
                $this->_db->transCommit();
                try {
                    unlink($dir . '/' . $currentFile->file);
                } catch (\Throwable $th) {
                    //throw $th;
                }


                $response = new \stdClass;
                $response->status = 200;
                $response->message = "File $title SPJ berhasil dihapus.";
                return json_encode($response);
            } else {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menghapus file $title";
                return json_encode($response);
            }
        }
    }
}
