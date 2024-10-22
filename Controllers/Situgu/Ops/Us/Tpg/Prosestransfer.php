<?php

namespace App\Controllers\Situgu\Ops\Us\Tpg;

use App\Controllers\BaseController;
use App\Models\Situgu\Ops\Tpg\ProsestransferModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Situgu\Kehadiranptklib;
use App\Libraries\Uuid;

class Prosestransfer extends BaseController
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
        $datamodel = new ProsestransferModel($request);

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
            // $action = '<div class="btn-group">
            //             <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
            //             <div class="dropdown-menu" style="">
            //                 <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama) . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
            //                 <a class="dropdown-item" href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Sync Dapodik</a>
            //             </div>
            //         </div>';
            $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . $list->id_tahun_tw . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                <i class="bx bxs-show font-size-16 align-middle"></i> DETAIL</button>
                </a>';
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            $row[] = str_replace('&#039;', "`", str_replace("'", "`", $list->nama));
            $row[] = $list->nik;
            $row[] = $list->nuptk;
            $row[] = $list->no_sk_dirjen;
            $row[] = $list->no_urut_sk;
            $row[] = $list->created_at;

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
        return redirect()->to(base_url('situgu/ops/us/tpg/prosestransfer/data'));
    }

    public function data()
    {
        $data['title'] = 'USULAN PROSES TRANSFER TUNJANGAN PROFESI GURU';
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
        return view('situgu/ops/us/tpg/prosestransfer/index', $data);
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

            $current = $this->_db->table('v_temp_usulan')
                ->where(['id_usulan' => $id, 'id_tahun_tw' => $tw])->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $data['penugasans'] = $this->_db->table('_ptk_tb_dapodik a')
                    ->select("a.*, b.npsn, b.nama as namaSekolah, (SELECT SUM(jam_mengajar_per_minggu) FROM _pembelajaran_dapodik WHERE ptk_id = a.ptk_id AND sekolah_id = a.sekolah_id AND semester_id = a.semester_id) as jumlah_total_jam_mengajar_perminggu")
                    ->join('ref_sekolah b', 'a.sekolah_id = b.id')
                    ->where('a.ptk_id', $current->id_ptk)
                    ->orderBy('a.ptk_induk', 'DESC')->get()->getResult();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/ks/verifikasi/tpg/detail', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function approve()
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

            $oldData = $this->_db->table('_tb_temp_usulan_detail')->where(['id' => $id])->get()->getRowObject();
            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 201;
                $response->message = "Usulan tidak ditemukan.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_tb_temp_usulan_detail')->where('id', $oldData->id)->update(['status_usulan' => 2, 'date_approve' => date('Y-m-d H:i:s'), 'admin_approve' => date('Y-m-d H:i:s')]);
                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Usulan $nama berhasil diverifikasi dan disetujui.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal memverifikasi usulan $nama.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal memverifikasi usulan $nama.";
                return json_encode($response);
            }
        }
    }

    public function formtolak()
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
            $response->data = view('situgu/ks/verifikasi/tpg/tolak', $data);
            return json_encode($response);
        }
    }

    public function tolak()
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
                . $this->validator->getError('nama')
                . $this->validator->getError('keterangan');
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
            $keterangan = htmlspecialchars($this->request->getVar('keterangan'), true);

            $oldData = $this->_db->table('_tb_temp_usulan_detail')->where(['id' => $id])->get()->getRowObject();
            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 201;
                $response->message = "Usulan tidak ditemukan.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_tb_temp_usulan_detail')->where('id', $oldData->id)->update(['status_usulan' => 1, 'keterangan_reject' => $keterangan, 'date_reject' => date('Y-m-d H:i:s')]);
                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Usulan $nama berhasil diverifikasi dan ditolak.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal memverifikasi usulan $nama.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal memverifikasi usulan $nama.";
                return json_encode($response);
            }
        }
    }

    public function getValidationAjuan()
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
                    'required' => 'Jenis tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Tw tidak boleh kosong. ',
                ]
            ],
            'id_ptk' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('action')
                . $this->validator->getError('id_ptk')
                . $this->validator->getError('tw');
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

            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $jenis_tunjangan = htmlspecialchars($this->request->getVar('id'), true);
            $id_ptk = htmlspecialchars($this->request->getVar('id_ptk'), true);

            $ptk = $this->_db->table('_upload_data_attribut a')
                ->select("b.*, a.id_tahun_tw, a.pang_golongan, a.pang_no, a.pang_tmt, a.pang_tgl, a.pang_tahun, a.pang_bulan, a.pangkat_terakhir as lampiran_pangkat, a.kgb_terakhir as lampiran_kgb, a.pernyataan_24jam as lampiran_pernyataan24, a.cuti as lampiran_cuti, a.pensiun as lampiran_pensiun, a.kematian as lampiran_kematian, a.lainnya as lampiran_att_lain, c.bulan_1, c.bulan_2, c.bulan_3, c.lampiran_absen1, c.lampiran_absen2, c.lampiran_absen3, c.pembagian_tugas as lampiran_pembagian_tugas, c.slip_gaji as lampiran_slip_gaji, c.doc_lainnya as lampiran_doc_absen_lain")
                ->join('_ptk_tb b', 'a.id_ptk = b.id')
                ->join('_absen_kehadiran c', 'a.id_ptk = c.id_ptk AND c.id_tahun_tw = a.id_tahun_tw')
                ->where(['a.id_ptk' => $id_ptk, 'a.id_tahun_tw' => $tw])
                ->get()->getRowObject();

            if (!$ptk) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            $data['tw'] = $this->_db->table('_ref_tahun_tw')->where('id', $tw)->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getRowObject();
            $data['ptk'] = $ptk;
            $response = new \stdClass;

            if ($ptk->no_rekening === null || $ptk->no_rekening === "" || $ptk->cabang_bank === null || $ptk->cabang_bank === "") {
                $response->status = 400;
                $response->message = "No Rekening dan Cabang tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                return json_encode($response);
            }

            if ($ptk->lampiran_ktp === null || $ptk->lampiran_ktp === "") {
                $response->status = 400;
                $response->message = "Lampiran Dokumen Master KTP tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                return json_encode($response);
            }

            if ($ptk->lampiran_npwp === null || $ptk->lampiran_npwp === "") {
                $response->status = 400;
                $response->message = "Lampiran Dokumen Master NPWP tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                return json_encode($response);
            }

            if ($ptk->lampiran_ijazah === null || $ptk->lampiran_ijazah === "") {
                $response->status = 400;
                $response->message = "Lampiran Dokumen Master Ijazah tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                return json_encode($response);
            }

            if ($ptk->lampiran_buku_rekening === null || $ptk->lampiran_buku_rekening === "") {
                $response->status = 400;
                $response->message = "Lampiran Dokumen Master Buku Rekening tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                return json_encode($response);
            }

            if ($ptk->lampiran_pernyataan24 === null || $ptk->lampiran_pernyataan24 === "" || $ptk->lampiran_slip_gaji === null || $ptk->lampiran_slip_gaji === "" || $ptk->lampiran_pembagian_tugas === null || $ptk->lampiran_pembagian_tugas === "" || $ptk->lampiran_absen1 === null || $ptk->lampiran_absen1 === "" || $ptk->lampiran_absen2 === null || $ptk->lampiran_absen2 === "" || $ptk->lampiran_absen3 === null || $ptk->lampiran_absen3 === "") {
                $response->status = 400;
                $response->message = "Lampiran Dokumen Atibut Pernyataan 24 Jam, Pembagian Tugas, Slip Gaji, Absen 1, Absen 2 dan Absen 3 tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                return json_encode($response);
            }

            if ($jenis_tunjangan === "tpg") {

                $canUsulTpg = canUsulTpg();

                if ($canUsulTpg && $canUsulTpg->code !== 200) {
                    return json_encode($canUsulTpg);
                }

                if ($ptk->nuptk === null || $ptk->nuptk === "" || $ptk->nrg === null || $ptk->nrg === "" || $ptk->no_peserta === ""  || $ptk->no_peserta === null || $ptk->bidang_studi_sertifikasi === null || $ptk->bidang_studi_sertifikasi === "") {
                    $response->status = 400;
                    $response->message = "Untuk mendapatkan Tunjangan Sertifikasi Guru, Harus Wajib mempunyai NUPTK, NRG, No Peserta, dan Bidang Studi Sertifikasi!!";
                    return json_encode($response);
                }

                $pendidikans = ['D4', 'S1', 'S2', 'S3'];
                // strtoupper()
                if (!array_search($ptk->pendidikan, $pendidikans)) {
                    $response->status = 400;
                    $response->message = "Untuk mendapatkan Tunjangan Sertifikasi, harus wajib memiliki Pendidikan minimal S1.";
                    return json_encode($response);
                }

                if ($ptk->lampiran_nrg === null || $ptk->lampiran_nrg === "" || $ptk->lampiran_nuptk === null || $ptk->lampiran_nuptk === "" || $ptk->lampiran_serdik === null || $ptk->lampiran_serdik === "") {
                    $response->status = 400;
                    $response->message = "Lampiran Dokumen Master NRG, NUPTK, dan Serdik tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                    return json_encode($response);
                }

                if ($ptk->status_kepegawaian === "PNS" || $ptk->status_kepegawaian === "PPPK" || $ptk->status_kepegawaian === "PNS Diperbantukan" || $ptk->status_kepegawaian === "PNS Depag" || $ptk->status_kepegawaian === "CPNS") {
                    if ($ptk->lampiran_pangkat === null || $ptk->lampiran_pangkat === "" || $ptk->lampiran_kgb === null || $ptk->lampiran_kgb === "") {
                        $response->status = 400;
                        $response->message = "Lampiran Dokumen Atribut Pangkat dan KGB tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!.";
                        return json_encode($response);
                    }
                    $response->data = view('situgu/ks/us/ajukan/tpg-asn', $data);
                } else {
                    if ($ptk->nomor_sk_impassing !== null) {
                        if ($ptk->lampiran_impassing === null || $ptk->lampiran_impassing === "") {
                            $response->status = 400;
                            $response->message = "Lampiran Dokumen Master Impassing tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!.";
                            return json_encode($response);
                        }
                    }
                    $response->data = view('situgu/ks/us/ajukan/tpg-honor', $data);
                }
            } else if ($jenis_tunjangan === "tamsil") {

                $canUsulTamsil = canUsulTamsil();

                if ($canUsulTamsil && $canUsulTamsil->code !== 200) {
                    return json_encode($canUsulTamsil);
                }

                if ($ptk->nuptk === null || $ptk->nuptk === "") {
                    $response->status = 400;
                    $response->message = "Untuk mendapatkan Tunjangan Penghasilan Guru PNS Non Sertifikasi (Tamsil), Harus Wajib mempunyai NUPTK!!";
                    return json_encode($response);
                }

                $pendidikans = ['D4', 'S1', 'S2', 'S3'];
                // strtoupper()
                if (!array_search($ptk->pendidikan, $pendidikans)) {
                    $response->status = 400;
                    $response->message = "Untuk mendapatkan Tunjangan Penghasilan Guru PNS Non Sertifikasi (Tamsil), harus wajib memiliki Pendidikan minimal S1.";
                    return json_encode($response);
                }

                if ($ptk->lampiran_nuptk === null || $ptk->lampiran_nuptk === "") {
                    $response->status = 400;
                    $response->message = "Lampiran Dokumen Master NUPTK tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                    return json_encode($response);
                }

                if ($ptk->status_kepegawaian === "PNS" || $ptk->status_kepegawaian === "PPPK" || $ptk->status_kepegawaian === "PNS Diperbantukan" || $ptk->status_kepegawaian === "PNS Depag") {
                    if ($ptk->lampiran_pangkat === null || $ptk->lampiran_pangkat === "" || $ptk->lampiran_kgb === null || $ptk->lampiran_kgb === "") {
                        $response->status = 400;
                        $response->message = "Lampiran Dokumen Atribut Pangkat dan KGB tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!.";
                        return json_encode($response);
                    }
                    $response->data = view('situgu/ks/us/ajukan/tamsil', $data);
                } else {
                    $response->status = 400;
                    $response->message = "Tunjangan Tamsil hanya diperuntukan bagi PNS / PPPK.";
                    return json_encode($response);
                }
            } else if ($jenis_tunjangan === "pghm") {

                $canUsulPghm = canUsulPghm();

                if ($canUsulPghm && $canUsulPghm->code !== 200) {
                    return json_encode($canUsulPghm);
                }

                if ($ptk->status_kepegawaian === "Guru Honor Sekolah") {
                    // if ($ptk->nuptk === null || $ptk->nuptk === "") {
                    //     $response->status = 400;
                    //     $response->message = "Untuk mendapatkan Tunjangan PGHM, Harus Wajib mempunyai NUPTK!!";
                    //     return json_encode($response);
                    // }

                    $pendidikans = ['D4', 'S1', 'S2', 'S3'];
                    // strtoupper()
                    if (!array_search($ptk->pendidikan, $pendidikans)) {
                        $response->status = 400;
                        $response->message = "Untuk mendapatkan Tunjangan PGHM, harus wajib memiliki Pendidikan minimal S1.";
                        return json_encode($response);
                    }
                    $response->data = view('situgu/ks/us/ajukan/pghm', $data);
                } else {
                    $response->status = 400;
                    $response->message = "Tunjangan PGHM hanya diperuntukan bagi Guru Honorer.";
                    return json_encode($response);
                }
            } else {
                $response->status = 400;
                $response->message = "Jenis tunjangan tidak tersedia.";
                return json_encode($response);
            }

            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            return json_encode($response);
        }
    }

    public function prosesajukan()
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
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('tw')
                . $this->validator->getError('jenis');
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

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $jenis = htmlspecialchars($this->request->getVar('jenis'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);

            $ptk = $this->_db->table('_upload_data_attribut a')
                ->select("b.*, (SELECT gaji_pokok FROM ref_gaji WHERE pangkat = a.pang_golongan AND masa_kerja = (IF(a.pang_tahun > 32, 32, a.pang_tahun)) LIMIT 1) as gajiPokok, a.id_tahun_tw, a.pang_jenis, a.pang_golongan, a.pang_no, a.pang_tmt, a.pang_tgl, a.pang_tahun, a.pang_bulan, a.pangkat_terakhir as lampiran_pangkat, a.kgb_terakhir as lampiran_kgb, a.pernyataan_24jam as lampiran_pernyataan24, a.cuti as lampiran_cuti, a.pensiun as lampiran_pensiun, a.kematian as lampiran_kematian, a.lainnya as lampiran_att_lain, c.bulan_1, c.bulan_2, c.bulan_3, c.lampiran_absen1, c.lampiran_absen2, c.lampiran_absen3, c.pembagian_tugas as lampiran_pembagian_tugas, c.slip_gaji as lampiran_slip_gaji, c.doc_lainnya as lampiran_doc_absen_lain")
                ->join('_ptk_tb b', 'a.id_ptk = b.id')
                ->join('_absen_kehadiran c', 'a.id_ptk = c.id_ptk AND c.id_tahun_tw = a.id_tahun_tw')
                ->where(['a.id_ptk' => $id, 'a.id_tahun_tw' => $tw])
                ->get()->getRowObject();

            if (!$ptk) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            if ($jenis === "tpg") {

                $canUsulTpg = canUsulTpg();

                if ($canUsulTpg && $canUsulTpg->code !== 200) {
                    return json_encode($canUsulTpg);
                }

                $uuidLib = new Uuid();
                $data = [
                    'id' => $uuidLib->v4(),
                    'id_ptk' => $id,
                    'id_tahun_tw' => $tw,
                    'jenis_tunjangan' => $jenis,
                    'us_pang_golongan' => $ptk->pang_golongan,
                    'us_pang_tmt' => $ptk->pang_tmt,
                    'us_pang_tgl' => $ptk->pang_tgl,
                    'us_pang_mk_tahun' => $ptk->pang_tahun,
                    'us_pang_mk_bulan' => $ptk->pang_bulan,
                    'us_pang_jenis' => $ptk->pang_jenis,
                    'us_gaji_pokok' => $ptk->gajiPokok ? ($ptk->gajiPokok > 0 ? $ptk->gajiPokok : 1500000) : 1500000,
                    'status_usulan' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            } else if ($jenis === "tamsil") {

                $canUsulTamsil = canUsulTamsil();

                if ($canUsulTamsil && $canUsulTamsil->code !== 200) {
                    return json_encode($canUsulTamsil);
                }

                $uuidLib = new Uuid();
                $data = [
                    'id' => $uuidLib->v4(),
                    'id_ptk' => $id,
                    'id_tahun_tw' => $tw,
                    'jenis_tunjangan' => $jenis,
                    'us_pang_golongan' => $ptk->pang_golongan,
                    'us_pang_tmt' => $ptk->pang_tmt,
                    'us_pang_tgl' => $ptk->pang_tgl,
                    'us_pang_mk_tahun' => $ptk->pang_tahun,
                    'us_pang_mk_bulan' => $ptk->pang_bulan,
                    'us_pang_jenis' => $ptk->pang_jenis,
                    'us_gaji_pokok' => $this->_helpLib->nilaiTamsil(),
                    'status_usulan' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            } else if ($jenis === "pghm") {

                $canUsulPghm = canUsulPghm();

                if ($canUsulPghm && $canUsulPghm->code !== 200) {
                    return json_encode($canUsulPghm);
                }

                $uuidLib = new Uuid();
                $data = [
                    'id' => $uuidLib->v4(),
                    'id_ptk' => $id,
                    'id_tahun_tw' => $tw,
                    'jenis_tunjangan' => $jenis,
                    'us_pang_golongan' => $ptk->pang_golongan,
                    'us_pang_tmt' => $ptk->pang_tmt,
                    'us_pang_tgl' => $ptk->pang_tgl,
                    'us_pang_mk_tahun' => $ptk->pang_tahun,
                    'us_pang_mk_bulan' => $ptk->pang_bulan,
                    'us_pang_jenis' => $ptk->pang_jenis,
                    'us_gaji_pokok' => $this->_helpLib->nilaiPghm(),
                    'status_usulan' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Jenis tunjangan tidak tersedia.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_tb_temp_usulan_detail')->insert($data);
                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Usulan $jenis berhasil diajukan.";
                    $response->data = $data;
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengajukan uslan $jenis.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal mengajukan uslan $jenis.";
                return json_encode($response);
            }
        }
    }
}
