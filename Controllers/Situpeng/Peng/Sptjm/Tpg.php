<?php

namespace App\Controllers\Situpeng\Peng\Sptjm;

use App\Controllers\BaseController;
use App\Models\Situpeng\Peng\SptjmModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Situgu\Kehadiranptklib;
use App\Libraries\Uuid;
use App\Libraries\Downloadlib;
// use Smalot\PdfParser\Parser;
// use Smalot\PdfParser\Element\Image;
// use Smalot\PdfParser\Element\Text;
// use Smalot\PdfParser\Element\Rectangle;
// use Smalot\PdfParser\Element\Table;
// use Spatie\PdfToText\Pdf;
// use TCPDF;
// use setasign\Fpdi\Fpdi;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use mPDF;
use PhpOffice\PhpWord\TemplateProcessor;

class Tpg extends BaseController
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
        $datamodel = new SptjmModel($request);

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


        // $npsn = $this->_helpLib->getNpsn($userId);

        $lists = $datamodel->get_datatables($userId, 'tpg');
        $data = [];
        $no = $request->getPost("start");
        foreach ($lists as $list) {
            $no++;
            $row = [];

            $row[] = $no;
            $action = '<div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\', \'' . $list->kode_usulan . '\', \'' . $list->id_tahun_tw . '\', \'' . str_replace("'", "", $list->nama) . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>';
            if ($list->is_locked !== 1) {
                if ($list->lampiran_sptjm == null || $list->lampiran_sptjm == "") {
                    $action .= '<a class="dropdown-item" href="javascript:actionUpload(\'' . $list->id . '\',\'' . $list->tahun . '\',\'' . $list->tw . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Upload Lampiran</a>';
                } else {
                    $action .= '<a class="dropdown-item" href="javascript:actionEditUpload(\'' . $list->id . '\',\'' . $list->tahun . '\',\'' . $list->tw . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Edit Lampiran</a>';
                }
            }
            $action .= '</div>
                    </div>';
            // $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . $list->kode_usulan . '\', \'' . $list->id_tahun_tw . '\', \'' . str_replace("'", "", $list->nama) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i> DETAIL</button>
            //     </a>';
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            $row[] = $list->kode_usulan;
            $row[] = $list->tahun;
            $row[] = $list->tw;
            $row[] = $list->jumlah_pengawas;
            if ($list->is_locked == 1) {
                $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/pengawas/sptjm') . '/' . $list->lampiran_sptjm . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/pengawas/sptjm') . '/' . $list->lampiran_sptjm . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
            } else {
                if ($list->lampiran_sptjm == null || $list->lampiran_sptjm == "") {
                    $row[] = '<a class="btn btn-sm btn-primary waves-effect waves-light" target="_blank" href="' . base_url('situpeng/peng/sptjm/tpg/download') . '?id=' . $list->id . '"><i class="bx bxs-cloud-download font-size-16 align-middle me-2"></i> Download</a>&nbsp;&nbsp;'
                        . '<a class="btn btn-sm btn-primary waves-effect waves-light" href="javascript:actionUpload(\'' . $list->id . '\',\'' . $list->tahun . '\',\'' . $list->tw . '\');"><i class="bx bxs-cloud-upload font-size-16 align-middle me-2"></i> Upload</a>';
                } else {
                    $row[] = '<a target="popup" onclick="window.open(\'' . base_url('upload/pengawas/sptjm') . '/' . $list->lampiran_sptjm . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . base_url('upload/pengawas/sptjm') . '/' . $list->lampiran_sptjm . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
                }
            }

            $data[] = $row;
        }
        $output = [
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datamodel->count_all($userId, 'tpg'),
            "recordsFiltered" => $datamodel->count_filtered($userId, 'tpg'),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        return redirect()->to(base_url('situpeng/peng/sptjm/tpg/data'));
    }

    public function data()
    {
        $data['title'] = 'SPTJM USULAN TUNJANGAN PROFESI GURU';
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
        return view('situpeng/peng/sptjm/tpg/index', $data);
    }

    public function add()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

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

        $rules = [
            'id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id tidak boleh kosong. ',
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
                . $this->validator->getError('tw');
            return json_encode($response);
        } else {

            $jenis_tunjangan = htmlspecialchars($this->request->getVar('id'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);

            $jenjangPengawas = $this->_helpLib->getJenjangPengawas($user->data->id);

            if ($jenjangPengawas == "SD") {
                $current = $this->_db->table('_tb_temp_usulan_detail_pengawas a')
                    ->select("b.*, a.id_pengawas, a.id as id_usulan, a.id_tahun_tw, a.jenis_tunjangan, a.us_pang_golongan, a.us_pang_tmt, a.us_pang_tgl, a.us_pang_mk_tahun, a.us_pang_mk_bulan, a.us_pang_jenis, a.us_gaji_pokok, a.status_usulan, c.gaji_pokok as gaji_pokok_referensi, d.pang_no, d.pangkat_terakhir as lampiran_pangkat, d.kgb_terakhir as lampiran_kgb, d.pernyataan_24jam as lampiran_pernyataan, d.penugasan as lampiran_penugasan, d.kunjungan_binaan as lampiran_kunjungan_binaan, d.cuti as lampiran_cuti, d.pensiun as lampiran_pensiun, d.kematian as lampiran_kematian, d.lainnya as lampiran_attr_lainnya")
                    ->join('__pengawas_tb b', 'a.id_pengawas = b.id')
                    ->join('__pengawas_upload_data_attribut d', 'a.id_pengawas = d.id_ptk AND (a.id_tahun_tw = d.id_tahun_tw)')
                    ->join('ref_gaji c', 'a.us_pang_golongan = c.pangkat AND (c.masa_kerja = (IF(a.us_pang_mk_tahun > 32, 32, a.us_pang_mk_tahun)))', 'LEFT')
                    ->where(['a.jenis_tunjangan' => $jenis_tunjangan, 'a.status_usulan' => 2, 'a.id_tahun_tw' => $tw])
                    ->whereIn('b.jenjang_pengawas', ['SD', 'TK'])
                    ->get()->getResult();
            } else {
                $current = $this->_db->table('_tb_temp_usulan_detail_pengawas a')
                    ->select("b.*, a.id_pengawas, a.id as id_usulan, a.id_tahun_tw, a.jenis_tunjangan, a.us_pang_golongan, a.us_pang_tmt, a.us_pang_tgl, a.us_pang_mk_tahun, a.us_pang_mk_bulan, a.us_pang_jenis, a.us_gaji_pokok, a.status_usulan, c.gaji_pokok as gaji_pokok_referensi, d.pang_no, d.pangkat_terakhir as lampiran_pangkat, d.kgb_terakhir as lampiran_kgb, d.pernyataan_24jam as lampiran_pernyataan, d.penugasan as lampiran_penugasan, d.kunjungan_binaan as lampiran_kunjungan_binaan, d.cuti as lampiran_cuti, d.pensiun as lampiran_pensiun, d.kematian as lampiran_kematian, d.lainnya as lampiran_attr_lainnya")
                    ->join('__pengawas_tb b', 'a.id_pengawas = b.id')
                    ->join('__pengawas_upload_data_attribut d', 'a.id_pengawas = d.id_ptk AND (a.id_tahun_tw = d.id_tahun_tw)')
                    ->join('ref_gaji c', 'a.us_pang_golongan = c.pangkat AND (c.masa_kerja = (IF(a.us_pang_mk_tahun > 32, 32, a.us_pang_mk_tahun)))', 'LEFT')
                    ->where(['a.jenis_tunjangan' => $jenis_tunjangan, 'b.jenjang_pengawas' => $jenjangPengawas, 'a.status_usulan' => 2, 'a.id_tahun_tw' => $tw])
                    ->get()->getResult();
            }

            if (count($current) > 0) {
                $data['data'] = $current;
                $data['tw'] = $tw;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situpeng/peng/sptjm/tpg/add', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tidak ada data untuk dibuatkan SPTJM.";
                return json_encode($response);
            }
        }
    }

    public function generatesptjm()
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
            'jumlah' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Jumlah tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            'ptks' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'PTK terpilih tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('jenis')
                . $this->validator->getError('tw')
                . $this->validator->getError('jumlah')
                . $this->validator->getError('ptks');
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
            $canUsulTpg = canUsulTpgPengawas();

            if ($canUsulTpg && $canUsulTpg->code !== 200) {
                return json_encode($canUsulTpg);
            }

            $jenjangPengawas = $this->_helpLib->getJenjangPengawas($user->data->id);

            $jenis = htmlspecialchars($this->request->getVar('jenis'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $jumlah = htmlspecialchars($this->request->getVar('jumlah'), true);
            $ptks = $this->request->getVar('ptks');
            if (count($ptks) < 1) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Belum ada data ptk yang dipilih.";
                return json_encode($response);
            }

            $twActive = $this->_db->table('_ref_tahun_tw')->where('id', $tw)->get()->getRowObject();
            if (!$twActive) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengenerate SPTJM usulan TPG. TW active tidak ditemukan.";
                return json_encode($response);
            }

            $id_ptks = implode(",", $ptks);

            $this->_db->transBegin();

            try {
                $this->_db->table('_tb_temp_usulan_detail_pengawas')->where(['status_usulan' => 2, 'id_tahun_tw' => $twActive->id, 'jenis_tunjangan' => 'tpg'])->whereIn('id_pengawas', $ptks)->update(['status_usulan' => 5, 'updated_at' => date('Y-m-d H:i:s')]);
                if (!($this->_db->affectedRows() > 0)) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengenerate SPTJM usulan TPG. gagal mengupdate status.";
                    return json_encode($response);
                }
            } catch (\Throwable $thU) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($thU);
                $response->message = "Gagal mengenerate SPTJM usulan TPG. gagal mengupdate status.";
                return json_encode($response);
            }

            try {
                $uuidLib = new Uuid();
                $jenjangFix = ($jenjangPengawas == "SD") ? 'SD/TK' : $jenjangPengawas;
                $kodeUsulan = "TPG-" . $twActive->tahun . '-' . $twActive->tw . '-Pengawas-' . $jenjangFix . '-' . time();

                $this->_db->table('_tb_sptjm_pengawas')->insert(
                    [
                        'id' => $uuidLib->v4(),
                        'kode_usulan' => $kodeUsulan,
                        'jumlah_pengawas' => $jumlah,
                        'jenis_usulan' => 'tpg',
                        'id_pengawass' => $id_ptks,
                        'generate_sptjm' => 0,
                        'id_tahun_tw' => $twActive->id,
                        'jenjang_pengawas' => $jenjangFix,
                        'user_id' => $user->data->id,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]
                );
                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "SPTJM Usulan TPG Tahun {$twActive->tahun} TW {$twActive->tw} berhasil digenerate.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal Mengenerate SPTJM Usulan TPG.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal Mengenerate SPTJM Usulan TPG.";
                return json_encode($response);
            }
        }
    }

    public function download()
    {
        // if ($this->request->getMethod() != 'post') {
        //     $response = new \stdClass;
        //     $response->status = 400;
        //     $response->message = "Permintaan tidak diizinkan";
        //     return json_encode($response);
        // }

        // $rules = [
        //     'id' => [
        //         'rules' => 'required|trim',
        //         'errors' => [
        //             'required' => 'Id tidak boleh kosong. ',
        //         ]
        //     ],
        //     'tahun' => [
        //         'rules' => 'required|trim',
        //         'errors' => [
        //             'required' => 'Title tidak boleh kosong. ',
        //         ]
        //     ],
        //     'tw' => [
        //         'rules' => 'required|trim',
        //         'errors' => [
        //             'required' => 'Id PTK tidak boleh kosong. ',
        //         ]
        //     ],
        // ];

        // if (!$this->validate($rules)) {
        //     $response = new \stdClass;
        //     $response->status = 400;
        //     $response->message = $this->validator->getError('id')
        //         . $this->validator->getError('tahun')
        //         . $this->validator->getError('tw');
        //     return json_encode($response);
        // } else {
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
        // $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
        // $tw = htmlspecialchars($this->request->getVar('tw'), true);

        $current = $this->_db->table('_tb_sptjm_pengawas')->where('id', $id)->get()->getRowObject();
        if (!$current) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "SPTJM tidak ditemukan. Silahkan Generate terlebih dahulu.";
            return json_encode($response);
        }

        $ptks = explode(",", $current->id_pengawass);
        $dataPtks = [];
        foreach ($ptks as $key => $value) {
            $ptk = $this->_db->table('_tb_temp_usulan_detail_pengawas a')
                ->select("b.*, a.id_pengawas, a.id as id_usulan, a.id_tahun_tw, a.jenis_tunjangan, a.us_pang_golongan, a.us_pang_tmt, a.us_pang_tgl, a.us_pang_mk_tahun, a.us_pang_mk_bulan, a.us_pang_jenis, a.us_gaji_pokok, a.status_usulan, c.gaji_pokok as gaji_pokok_referensi, d.pang_no, d.pangkat_terakhir as lampiran_pangkat, d.kgb_terakhir as lampiran_kgb, d.pernyataan_24jam as lampiran_pernyataan, d.penugasan as lampiran_penugasan, d.kunjungan_binaan as lampiran_kunjungan_binaan, d.cuti as lampiran_cuti, d.pensiun as lampiran_pensiun, d.kematian as lampiran_kematian, d.lainnya as lampiran_attr_lainnya, e.tahun as tw_tahun, e.tw as tw_tw")
                ->join('__pengawas_tb b', 'a.id_pengawas = b.id')
                ->join('__pengawas_upload_data_attribut d', 'a.id_pengawas = d.id_ptk AND (a.id_tahun_tw = d.id_tahun_tw)')
                ->join('ref_gaji c', 'a.us_pang_golongan = c.pangkat AND (c.masa_kerja = (IF(a.us_pang_mk_tahun > 32, 32, a.us_pang_mk_tahun)))', 'LEFT')
                ->join('_ref_tahun_tw e', 'a.id_tahun_tw = e.id')
                ->where(['a.id_pengawas' => $value, 'a.status_usulan' => 5, 'a.jenis_tunjangan' => 'tpg'])
                ->get()->getRowObject();
            if ($ptk) {
                $dataPtks[] = $ptk;
            }
        }

        $sekolah = $this->_db->table('__pengawas_tb')->where('id', $user->data->ptk_id)->get()->getRowObject();
        if (!$sekolah) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Referensi pengawas tidak ditemukan.";
            return json_encode($response);
        }

        return $this->_download($dataPtks, $sekolah, $current);
        // }
    }

    private function _download($ptks, $sekolah, $usulan)
    {
        if (count($ptks) > 0) {
            $file = FCPATH . "upload/template/sptjm-tpg-pengawas-new-1.docx";
            $template_processor = new TemplateProcessor($file);
            $jenjang_pengawas = ($sekolah->jenjang_pengawas == "SD") ? "SD/TK" : $sekolah->jenjang_pengawas;
            $template_processor->setValue('JENJANG_SEKOLAH', $jenjang_pengawas);
            $template_processor->setValue('TW_TW', $ptks[0]->tw_tw);
            $template_processor->setValue('TW_TAHUN', $ptks[0]->tw_tahun);
            $template_processor->setValue('NOMOR_SPTJM', $usulan->kode_usulan);
            $nama_ks = "";
            if ($sekolah->gelar_depan && ($sekolah->gelar_depan !== "" || $sekolah->gelar_depan !== "-")) {
                $nama_ks .= $sekolah->gelar_depan;
                $nama_ks .= ' ';
            }
            $nama_ks .= $sekolah->nama;
            if ($sekolah->gelar_belakang && ($sekolah->gelar_belakang !== "" || $sekolah->gelar_belakang !== "-")) {
                $nama_ks .= ', ';
                $nama_ks .= $sekolah->gelar_belakang;
            }
            $template_processor->setValue('NAMA_ADMIN', $nama_ks);
            $template_processor->setValue('JUMLAH_PENGAWAS', $usulan->jumlah_pengawas);
            $template_processor->setValue('TANGGAL_SPTJM', tgl_indo(date('Y-m-d')));
            if ($ptks[0]->tw_tw == 1) {
                $template_processor->setValue('BL_TO_BL', "Januari s/d Maret");
            } else if ($ptks[0]->tw_tw == 2) {
                $template_processor->setValue('BL_TO_BL', "Mei s/d Juni");
            } else if ($ptks[0]->tw_tw == 3) {
                $template_processor->setValue('BL_TO_BL', "Juli s/d September");
            } else if ($ptks[0]->tw_tw == 4) {
                $template_processor->setValue('BL_TO_BL', "Oktober s/d Desember");
            }
            // $nipKs = "";
            // if ($sekolah->nip && ($sekolah->nip !== "" || $sekolah->nip !== "-")) {
            //     $nipKs .= $sekolah->nip;
            // } else {
            //     $nipKs .= "-";
            // }
            // $template_processor->setValue('NIP_KS', $nipKs);

            $dataPtnya = [];
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

                $dataPtnya[] = [
                    'NO' => $key + 1,
                    'NRG' => $v->nrg,
                    'NOPES' => $v->no_peserta,
                    'NUPTK' => $v->nuptk,
                    'NIP' => $v->nip,
                    'NAMA' => $v->nama,
                    'GOL' => $v->us_pang_golongan,
                    'TH' => $v->us_pang_mk_tahun,
                    'BL' => $v->us_pang_mk_bulan,
                    'GAPOK' => rpTanpaAwalan($v->us_gaji_pokok),
                    'JB' => 3,
                    'JU' => rpTanpaAwalan(($v->us_gaji_pokok * 3)),
                    'PPH' => $pph,
                    'JD' => rpTanpaAwalan(($v->us_gaji_pokok * 3) - (($v->us_gaji_pokok * 3) * $pph21)),
                    'NPWP' => $v->npwp,
                    'NOREK' => $v->no_rekening,
                    'BANK' => $v->cabang_bank,
                ];
            }
            $template_processor->cloneRowAndSetValues('NO', $dataPtnya);
            $template_processor->setImageValue('BARCODE', array('path' => 'https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=layanan.disdikbud.lampungtengahkab.go.id/verifiqrcode?token=' . $usulan->kode_usulan . '&choe=UTF-8', 'width' => 100, 'height' => 100, 'ratio' => false));

            $filed = FCPATH . "upload/generate/sptjm/tpg/word/" . $usulan->kode_usulan . ".docx";

            $template_processor->saveAs($filed);

            $downloadLib = new Downloadlib();

            $responseD = $downloadLib->downloaded($filed, $usulan->kode_usulan . ".pdf", "tpg");

            return $responseD;
        } else {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Gagal mendownload SPTJM.";
            return json_encode($response);
        }
    }

    public function formuploadedit()
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
            'tahun' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Title tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('tahun')
                . $this->validator->getError('tw');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);

            $current = $this->_db->table('_tb_sptjm_pengawas')->where(['id' => $id])->get()->getRowObject();

            if (!$current) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "SPTJM tidak ditemukan.";
                return json_encode($response);
            }

            if ($current->is_locked == 1) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data Pengawas dalam SPTJM ini sudah diverifikasi, sehingga tidak diperkenankan untuk mengedit.";
                return json_encode($response);
            }

            $data['data'] = $current;
            $data['tahun'] = $tahun;
            $data['tw'] = $tw;
            $data['id'] = $id;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situpeng/peng/sptjm/tpg/upload_edit', $data);
            return json_encode($response);
        }
    }

    public function uploadEditSave()
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
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            'id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Id tidak boleh kosong. ',
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
            $response->message = $this->validator->getError('tahun')
                . $this->validator->getError('id')
                . $this->validator->getError('tw')
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

            $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $id = htmlspecialchars($this->request->getVar('id'), true);

            $current = $this->_db->table('_tb_sptjm_pengawas')->where(['id' => $id])->get()->getRowObject();

            if (!$current) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "SPTJM tidak ditemukan.";
                return json_encode($response);
            }

            if ($current->is_locked == 1) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data Pengawas dalam SPTJM ini sudah diverifikasi, sehingga tidak diperkenankan untuk mengedit.";
                return json_encode($response);
            }

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $dir = FCPATH . "upload/pengawas/sptjm";
            $field_db = 'lampiran_sptjm';
            $table_db = '_tb_sptjm';

            $lampiran = $this->request->getFile('_file');
            $filesNamelampiran = $lampiran->getName();
            $newNamelampiran = _create_name_file($filesNamelampiran);

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
                $this->_db->table($table_db)->where(['id' => $id, 'is_locked' => 0])->update($data);
            } catch (\Exception $e) {
                unlink($dir . '/' . $newNamelampiran);

                $this->_db->transRollback();

                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($e);
                $response->message = "Gagal mengupdate data.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                try {
                    unlink($dir . '/' . $current->lampiran_sptjm);
                } catch (\Throwable $th) {
                    //throw $th;
                }
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil diupdate.";
                return json_encode($response);
            } else {
                unlink($dir . '/' . $newNamelampiran);

                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupdate data";
                return json_encode($response);
            }
        }
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
            'tahun' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Title tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('tahun')
                . $this->validator->getError('tw');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);

            $data['tahun'] = $tahun;
            $data['tw'] = $tw;
            $data['id'] = $id;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situpeng/peng/sptjm/tpg/upload', $data);
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
            'tahun' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Tahun tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            'id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Id tidak boleh kosong. ',
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
            $response->message = $this->validator->getError('tahun')
                . $this->validator->getError('id')
                . $this->validator->getError('tw')
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

            $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $id = htmlspecialchars($this->request->getVar('id'), true);

            $current = $this->_db->table('_tb_sptjm_pengawas')->where(['id' => $id])->get()->getRowObject();

            if (!$current) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "SPTJM tidak ditemukan.";
                return json_encode($response);
            }

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $dir = FCPATH . "upload/pengawas/sptjm";
            $field_db = 'lampiran_sptjm';
            $table_db = '_tb_sptjm_pengawas';

            $lampiran = $this->request->getFile('_file');
            $filesNamelampiran = $lampiran->getName();
            $newNamelampiran = _create_name_file($filesNamelampiran);

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
                $this->_db->table($table_db)->where(['id' => $id, 'is_locked' => 0])->update($data);
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
                $ptks = explode(",", $current->id_pengawass);
                $dataPtks = [];
                foreach ($ptks as $key => $value) {
                    $ptk = $this->_db->table('_tb_temp_usulan_detail_pengawas')->where(['id_pengawas' => $value, 'status_usulan' => 5, 'jenis_tunjangan' => 'tpg'])->get()->getRowObject();
                    if ($ptk) {
                        $this->_db->table('_tb_usulan_detail_tpg_pengawas')->insert([
                            'id' => $ptk->id,
                            'kode_usulan' => $current->kode_usulan,
                            'id_pengawas' => $ptk->id_pengawas,
                            'id_tahun_tw' => $ptk->id_tahun_tw,
                            'us_pang_golongan' => $ptk->us_pang_golongan,
                            'us_pang_tmt' => $ptk->us_pang_tmt,
                            'us_pang_tgl' => $ptk->us_pang_tgl,
                            'us_pang_mk_tahun' => $ptk->us_pang_mk_tahun,
                            'us_pang_mk_bulan' => $ptk->us_pang_mk_bulan,
                            'us_pang_jenis' => $ptk->us_pang_jenis,
                            'us_gaji_pokok' => $ptk->us_gaji_pokok,
                            'status_usulan' => 0,
                            'admin_pengawas' => $user->data->id,
                            'date_approve_ks' => $ptk->admin_approve,
                            'date_approve_sptjm' => date('Y-m-d H:i:s'),
                            'created_at' => $ptk->created_at,
                        ]);
                        if ($this->_db->affectedRows() > 0) {
                            $this->_db->table('_tb_temp_usulan_detail_pengawas')->where(['id' => $ptk->id, 'status_usulan' => 5, 'jenis_tunjangan' => 'tpg'])->delete();
                            if ($this->_db->affectedRows() > 0) {
                                continue;
                            } else {
                                unlink($dir . '/' . $newNamelampiran);

                                $this->_db->transRollback();
                                $response = new \stdClass;
                                $response->status = 400;
                                $response->message = "Gagal memindahkan data usulan.";
                                return json_encode($response);
                            }
                        } else {
                            unlink($dir . '/' . $newNamelampiran);

                            $this->_db->transRollback();
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Gagal mengupdate data usulan.";
                            return json_encode($response);
                        }
                    }
                }

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
}
