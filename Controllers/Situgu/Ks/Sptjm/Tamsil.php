<?php

namespace App\Controllers\Situgu\Ks\Sptjm;

use App\Controllers\BaseController;
use App\Models\Situgu\Ks\SptjmModel;
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

use iio\libmergepdf\Merger;
use Dompdf\Dompdf;

use App\Libraries\MinioClient;

class Tamsil extends BaseController
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


        $npsn = $this->_helpLib->getNpsn($userId);

        $lists = $datamodel->get_datatables($npsn, 'tamsil');
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
            $row[] = $list->jumlah_ptk;
            if ($list->is_locked == 1) {
                $row[] = '<a target="popup" onclick="window.open(\'' . (strpos($list->lampiran_sptjm, '/') !== false ? getDokumentPreviewStorage('situgu', $list->lampiran_sptjm) : base_url('upload/sekolah/sptjm') . '/' . $list->lampiran_sptjm) . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . (strpos($list->lampiran_sptjm, '/') !== false ? getDokumentPreviewStorage('situgu', $list->lampiran_sptjm) : base_url('upload/sekolah/sptjm') . '/' . $list->lampiran_sptjm) . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
            } else {
                if ($list->lampiran_sptjm == null || $list->lampiran_sptjm == "") {
                    $row[] = '<a class="btn btn-sm btn-primary waves-effect waves-light" target="_blank" href="' . base_url('situgu/ks/sptjm/tamsil/download') . '?id=' . $list->id . '"><i class="bx bxs-cloud-download font-size-16 align-middle me-2"></i> Download</a>&nbsp;&nbsp;'
                        . '<a class="btn btn-sm btn-primary waves-effect waves-light" href="javascript:actionUpload(\'' . $list->id . '\',\'' . $list->tahun . '\',\'' . $list->tw . '\');"><i class="bx bxs-cloud-upload font-size-16 align-middle me-2"></i> Upload</a>';
                } else {
                    $row[] = '<a target="popup" onclick="window.open(\'' . (strpos($list->lampiran_sptjm, '/') !== false ? getDokumentPreviewStorage('situgu', $list->lampiran_sptjm) : base_url('upload/sekolah/sptjm') . '/' . $list->lampiran_sptjm) . '\',\'popup\',\'width=600,height=600\'); return false;" href="' . (strpos($list->lampiran_sptjm, '/') !== false ? getDokumentPreviewStorage('situgu', $list->lampiran_sptjm) : base_url('upload/sekolah/sptjm') . '/' . $list->lampiran_sptjm) . '"><span class="badge rounded-pill badge-soft-dark">Lihat</span></a>';
                }
            }

            $data[] = $row;
        }
        $output = [
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datamodel->count_all($npsn, 'tamsil'),
            "recordsFiltered" => $datamodel->count_filtered($npsn, 'tamsil'),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        return redirect()->to(base_url('situgu/ks/sptjm/tamsil/data'));
    }

    public function data()
    {
        $data['title'] = 'SPTJM USULAN TUNJANGAN TAMSIL';
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
        return view('situgu/ks/sptjm/tamsil/index', $data);
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

            $current = $this->_db->table('v_temp_usulan')
                ->where(['jenis_tunjangan_usulan' => $jenis_tunjangan, 'npsn' => $user->data->npsn, 'status_usulan' => 2, 'id_tahun_tw' => $tw])->get()->getResult();

            if (count($current) > 0) {
                $data['data'] = $current;
                $data['tw'] = $tw;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/ks/sptjm/tamsil/add', $data);
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
            $canUsulTamsil = canUsulTamsil();

            if ($canUsulTamsil && $canUsulTamsil->code !== 200) {
                $grantedCreateUsulan = canGrantedUsulanCustom($user->data->id);
                if ($grantedCreateUsulan && $grantedCreateUsulan->code !== 200) {
                    return json_encode($canUsulTamsil);
                }
            }

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
                $response->message = "Gagal mengenerate SPTJM usulan tamsil. TW active tidak ditemukan.";
                return json_encode($response);
            }

            $id_ptks = implode(",", $ptks);

            $this->_db->transBegin();

            try {
                $this->_db->table('_tb_temp_usulan_detail')->where(['status_usulan' => 2, 'id_tahun_tw' => $twActive->id, 'jenis_tunjangan' => 'tamsil'])->whereIn('id_ptk', $ptks)->update(['status_usulan' => 5, 'updated_at' => date('Y-m-d H:i:s')]);
                if (!($this->_db->affectedRows() > 0)) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengenerate SPTJM usulan tamsil. gagal mengupdate status.";
                    return json_encode($response);
                }
            } catch (\Throwable $thU) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($thU);
                $response->message = "Gagal mengenerate SPTJM usulan tamsil. gagal mengupdate status.";
                return json_encode($response);
            }

            try {
                $uuidLib = new Uuid();
                $kodeUsulan = "TAMSIL-" . $twActive->tahun . '-' . $twActive->tw . '-' . $user->data->npsn . '-' . time();

                $this->_db->table('_tb_sptjm')->insert(
                    [
                        'id' => $uuidLib->v4(),
                        'kode_usulan' => $kodeUsulan,
                        'jumlah_ptk' => $jumlah,
                        'jenis_usulan' => 'tamsil',
                        'id_ptks' => $id_ptks,
                        'generate_sptjm' => 0,
                        'id_tahun_tw' => $twActive->id,
                        'npsn' => $user->data->npsn,
                        'kecamatan' => $user->data->kecamatan,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]
                );
                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "SPTJM Usulan Tamsil Tahun {$twActive->tahun} TW {$twActive->tw} berhasil digenerate.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal Mengenerate SPTJM Usulan Tamsil.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal Mengenerate SPTJM Usulan Tamsil.";
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
            $ptk = $this->_db->table('v_temp_usulan')->where(['id_ptk_usulan' => $value, 'status_usulan' => 5, 'jenis_tunjangan_usulan' => 'tamsil'])->get()->getRowObject();
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
        // return $this->_download($dataPtks, $sekolah, $ks, $current);
        // }
    }

    private function _download($ptks, $sekolah, $ks, $usulan)
    {
        if (count($ptks) > 0) {
            $file = FCPATH . "upload/template/sptjm-tamsil-new-1.docx";
            $template_processor = new TemplateProcessor($file);
            $template_processor->setValue('NAMA_SEKOLAH', $sekolah->nama);
            $template_processor->setValue('NPSN_SEKOLAH', $sekolah->npsn);
            $alamat = $sekolah->alamat_jalan ? ($sekolah->alamat_jalan !== "" ? $sekolah->alamat_jalan : "-") : "-";
            $template_processor->setValue('ALAMAT_SEKOLAH', $alamat);
            $template_processor->setValue('KECAMATAN_SEKOLAH', $sekolah->kecamatan);
            $template_processor->setValue('KABUPATEN_SEKOLAH', getenv('setting.utpg.kabupaten'));
            $template_processor->setValue('PROV_SEKOLAH', getenv('setting.utpg.provinsi'));
            $no_telp = $sekolah->no_telepon ? ($sekolah->no_telepon !== "" ? $sekolah->no_telepon : "-") : "-";
            $template_processor->setValue('TELP_SEKOLAH', $no_telp);
            $email = $sekolah->email ? ($sekolah->email !== "" ? $sekolah->email : "-") : "-";
            $template_processor->setValue('EMAIL_SEKOLAH', $no_telp);
            $template_processor->setValue('TW_TW', $ptks[0]->tw_tw);
            $template_processor->setValue('TW_TAHUN', $ptks[0]->tw_tahun);
            $template_processor->setValue('NOMOR_SPTJM', $usulan->kode_usulan);
            $nama_ks = "";
            if ($ks->gelar_depan && ($ks->gelar_depan !== "" || $ks->gelar_depan !== "-")) {
                $nama_ks .= $ks->gelar_depan;
            }
            $nama_ks .= $ks->nama;
            if ($ks->gelar_belakang && ($ks->gelar_belakang !== "" || $ks->gelar_belakang !== "-")) {
                $nama_ks .= $ks->gelar_belakang;
            }
            $template_processor->setValue('NAMA_KS', $nama_ks);
            $jabatan_ks = $ks->jabatan_ks_plt ? ($ks->jabatan_ks_plt == 0 ? "Kepala Sekolah" : "Plt. Kepala Sekolah") : "Kepala Sekolah";
            $template_processor->setValue('JABATAN_KS', $jabatan_ks);
            $template_processor->setValue('JUMLAH_PTK', $usulan->jumlah_ptk);
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
            $nipKs = "";
            if ($ks->nip && ($ks->nip !== "" || $ks->nip !== "-")) {
                $nipKs .= $ks->nip;
            } else {
                $nipKs .= "-";
            }
            $template_processor->setValue('NIP_KS', $nipKs);

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
                    'NUPTK' => $v->nuptk,
                    'NIP' => $v->nip,
                    'NAMA' => $v->nama,
                    'GOL' => $v->us_pang_golongan,
                    'TH' => $v->us_pang_mk_tahun,
                    'BL' => $v->us_pang_mk_bulan,
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
            $template_processor->setImageValue('BARCODE', array('path' => 'http://192.168.33.16:8020/generate?data=https://layanan.disdikbud.lampungtengahkab.go.id/verifiqrcode?token=' . $usulan->kode_usulan, 'width' => 100, 'height' => 100, 'ratio' => false));

            $filed = FCPATH . "upload/generate/sptjm/tamsil/word2/" . $usulan->kode_usulan . ".docx";

            // $template_processor->saveAs($filed);

            // $filedTmp = FCPATH . "upload/generate/sptjm/tamsil/pdf2/" . $usulan->kode_usulan . ".pdf";

            // $domPdfPath = realpath('/var/www/vendor/dompdf/dompdf');
            // \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
            // \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

            // $phpWord = IOFactory::load($filed);
            // $phpWord->save($filedTmp,'PDF');

            // sleep(3);

            // // header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            // header('Content-Type: application/pdf');
            // header('Content-Disposition: attachment; filename="' . basename($filedTmp) . '"');
            // header('Content-Length: ' . filesize($filedTmp));
            // readfile($filedTmp);
            // exit;

            // return;



            $downloadLib = new Downloadlib();

            $responseD =  $downloadLib->downloaded($filed, $usulan->kode_usulan . ".pdf", "tamsil");

            return $responseD;
            // if ($responseD->status == 200) {
            //     $filePdf = $responseD->file;
            $this->response->setHeader('Content-Type', 'application/octet-stream');
            //     $this->response->setHeader('Content-Disposition', 'attachment; filename="' . basename($filePdf) . '"');
            //     $this->response->setHeader('Content-Length', filesize($filePdf));
            //     return $this->response->download($filePdf, null);
            // } else {
            //     $response = new \stdClass;
            //     $response->status = 400;
            //     $response->error = $responseD;
            //     $response->message = "Gagal mendownload SPTJM.";
            //     return json_encode($response);
            // }
        } else {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Gagal mendownload SPTJM.";
            return json_encode($response);
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
            $ptk = $this->_db->table('v_temp_usulan')->where(['id_ptk_usulan' => $value, 'status_usulan' => 5, 'jenis_tunjangan_usulan' => 'tamsil'])->get()->getRowObject();
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

            $qrCode = "data:image/png;base64," . base64_encode(file_get_contents('http://192.168.33.16:8020/generate?data=https://layanan.disdikbud.lampungtengahkab.go.id/verifiqrcode?token=' . $usulan->kode_usulan));

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
                                        <h3 style="margin: 0rem;font-size: 14px;">USULAN PENCAIRAN TUNJANGAN TAMBAHAN PENGHASILAN GURU PNS NON-SERTIFIKASI (TAMSIL)</h3>
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
                                                <li style="font-size: 12px;">Saya telah melakukan verifikasi dan validasi data guru yang diajukan, serta melakukan monitoring proses kinerja masing-masing guru di Satuan Pendidikan yang saya pimpin. Apabila dari data guru yang mengajukan, dari hasil verifikasi validasi data oleh Admin Dinas, ditemukan syarat dalam proses usulan pencairan Tunjangan Tambahan Penghasilan Guru PNS Non-Sertifikasi (TAMSIL) yang diatur berdasarkan PP 41 2017 dan PP 19 tentang Perubahan atas PP 41 tahun 2009, tidak sesuai/belum memenuhi persyaratan dengan kondisi keadaan yang sebenarnya, maka saya menerima usulan tersebut ditolak untuk dapat diperbaiki dan diajukan kembali pada periode jadwal yang telah ditetapkan.</li>
                                                <li style="font-size: 12px;">Apabila di kemudian hari terdapat ketidaksesuaian antara data yang dikirimkan/diajukan dengan keadaan yang sebenarnya, kami bertanggung jawab sepenuhnya dan bersedia menerima sanksi sesuai dengan ketentuan peraturan perundang-undangan.</li>
                                            </ol>
                                        </p>
                                        <p style="margin-bottom: 15px;font-size: 12px;">
                                            <table style="width: 100%;max-width: 100%;font-size: 12px;" border="1">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 50%;font-size: 12px;"><center>
                                                            <b>Jumlah PTK Yang Mengajukan Validasi Usulan TAMSIL</b>
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
                                    <p style="margin-bottom: 0px; font-size: 12px">LAMPIRAN SPTJM TUNJANGAN TAMBAHAN PENGHASILAN GURU PNS NON-SERTIFIKASI (TAMSIL) TRIWULAN ' . $ptks[0]->tw_tw . ', TAHUN ANGGARAN ' . $ptks[0]->tw_tahun . '</p>
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
                                                    <th width="5%" rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-left-width: 1px; border-left-style: solid;border-left-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">No</th>
                                                    <th width="10%" rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">NUPTK</th>
                                                    <th width="10%" rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">NIP</th>
                                                    <th width="15%" rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">Nama</th>
                                                    <th width="5%" rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 10px; padding-right: 10px;">Gol</th>
                                                    <th width="10%" colspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">Masa Kerja</th>
                                                    <th width="30%" colspan="4" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">Pembayaran TW. ' . $ptks[0]->tw_tw . ' Bulan ' . $bulanTampil . '</th>
                                                    <th width="10%" rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">NPWP</th>
                                                    <th width="10%" rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">No Rekening</th>
                                                    <th width="10%" rowspan="2" style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; align-items: center; font-size: 10px; padding-left: 7px; padding-right: 7px;">Cabang Bank</th>
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
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->nuptk . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->nip . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: left; font-size: 10px; padding-left: 3px; padding-right: 3px;">' . $v->nama . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . $v->us_pang_golongan . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . $v->us_pang_mk_tahun . '</td>
                                                    <td style="border-top-width: 1px; border-top-style: solid;border-top-color: rgb(17, 17, 17);border-bottom-width: 1px; border-bottom-style: solid;border-bottom-color: rgb(17, 17, 17);border-right-width: 1px; border-right-style: solid;border-right-color: rgb(17, 17, 17);border-left-color: rgb(17, 17, 17); text-align: center; font-size: 10px;">' . $v->us_pang_mk_bulan . '</td>
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

            $dir = FCPATH . "upload/generate/sptjm/tamsil/pdf";
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

            $current = $this->_db->table('_tb_sptjm')->where(['id' => $id])->get()->getRowObject();

            if (!$current) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "SPTJM tidak ditemukan.";
                return json_encode($response);
            }

            if ($current->is_locked == 1) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data PTK dalam SPTJM ini sudah diverifikasi, sehingga tidak diperkenankan untuk mengedit.";
                return json_encode($response);
            }

            $data['data'] = $current;
            $data['tahun'] = $tahun;
            $data['tw'] = $tw;
            $data['id'] = $id;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ks/sptjm/tamsil/upload_edit', $data);
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

            $current = $this->_db->table('_tb_sptjm')->where(['id' => $id])->get()->getRowObject();

            if (!$current) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "SPTJM tidak ditemukan.";
                return json_encode($response);
            }

            if ($current->is_locked == 1) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data PTK dalam SPTJM ini sudah diverifikasi, sehingga tidak diperkenankan untuk mengedit.";
                return json_encode($response);
            }

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $dir = FCPATH . "upload/sekolah/sptjm";
            $field_db = 'lampiran_sptjm';
            $table_db = '_tb_sptjm';

            $lampiran = $this->request->getFile('_file');
            $filesNamelampiran = $lampiran->getName();
            $newNamelampiran = $field_db . '/' . _create_name_file($filesNamelampiran);

            if ($lampiran->isValid() && !$lampiran->hasMoved()) {
                $minioClient = new MinioClient();
                $bucketName = 'situgu';
                $tempFilePath = $lampiran->getTempName();
                $uploadResult = $minioClient->uploadFile(
                    $bucketName,
                    $newNamelampiran,
                    $tempFilePath,
                    [
                        'x-amz-meta-uploader' => 'situgu-app',
                        'Content-Type' => $lampiran->getMimeType()
                    ]
                );

                if ($uploadResult) {
                    $data[$field_db] = $newNamelampiran;
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengupload file ke Storage.";
                    return json_encode($response);
                }
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
                try {
                    if (strpos($newNamelampiran, '/') !== false) {
                        $minioClient = new MinioClient();
                        $isDeleted = $minioClient->deleteObject("situgu", $newNamelampiran);
                    } else {
                        unlink($dir . '/' . $newNamelampiran);
                    }
                } catch (\Throwable $th) {
                }

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
                try {
                    if (strpos($current->lampiran_sptjm, '/') !== false) {
                        $minioClient = new MinioClient();
                        $isDeleted = $minioClient->deleteObject("situgu", $current->lampiran_sptjm);
                    } else {
                        unlink($dir . '/' . $current->lampiran_sptjm);
                    }
                } catch (\Throwable $th) {
                }

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
            $response->data = view('situgu/ks/sptjm/tamsil/upload', $data);
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

            $current = $this->_db->table('_tb_sptjm')->where(['id' => $id])->get()->getRowObject();

            if (!$current) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "SPTJM tidak ditemukan.";
                return json_encode($response);
            }

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $dir = FCPATH . "upload/sekolah/sptjm";
            $field_db = 'lampiran_sptjm';
            $table_db = '_tb_sptjm';

            $lampiran = $this->request->getFile('_file');
            $filesNamelampiran = $lampiran->getName();
            $newNamelampiran = $field_db . '/' . _create_name_file($filesNamelampiran);

            if ($lampiran->isValid() && !$lampiran->hasMoved()) {
                $minioClient = new MinioClient();
                $bucketName = 'situgu';
                $tempFilePath = $lampiran->getTempName();
                $uploadResult = $minioClient->uploadFile(
                    $bucketName,
                    $newNamelampiran,
                    $tempFilePath,
                    [
                        'x-amz-meta-uploader' => 'situgu-app',
                        'Content-Type' => $lampiran->getMimeType()
                    ]
                );

                if ($uploadResult) {
                    $data[$field_db] = $newNamelampiran;
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengupload file ke Storage.";
                    return json_encode($response);
                }
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
                try {
                    if (strpos($newNamelampiran, '/') !== false) {
                        $minioClient = new MinioClient();
                        $isDeleted = $minioClient->deleteObject("situgu", $newNamelampiran);
                    } else {
                        unlink($dir . '/' . $newNamelampiran);
                    }
                } catch (\Throwable $th) {
                }

                $this->_db->transRollback();

                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($e);
                $response->message = "Gagal menyimpan data.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                $ptks = explode(",", $current->id_ptks);
                $dataPtks = [];
                foreach ($ptks as $key => $value) {
                    $ptk = $this->_db->table('_tb_temp_usulan_detail')->where(['id_ptk' => $value, 'status_usulan' => 5, 'jenis_tunjangan' => 'tamsil'])->get()->getRowObject();
                    if ($ptk) {
                        $this->_db->table('_tb_usulan_detail_tamsil')->insert([
                            'id' => $ptk->id,
                            'kode_usulan' => $current->kode_usulan,
                            'id_ptk' => $ptk->id_ptk,
                            'id_tahun_tw' => $ptk->id_tahun_tw,
                            'us_pang_golongan' => $ptk->us_pang_golongan,
                            'us_pang_tmt' => $ptk->us_pang_tmt,
                            'us_pang_tgl' => $ptk->us_pang_tgl,
                            'us_pang_mk_tahun' => $ptk->us_pang_mk_tahun,
                            'us_pang_mk_bulan' => $ptk->us_pang_mk_bulan,
                            'us_pang_jenis' => $ptk->us_pang_jenis,
                            'us_gaji_pokok' => $ptk->us_gaji_pokok,
                            'status_usulan' => 0,
                            'date_approve_ks' => $ptk->admin_approve,
                            'date_approve_sptjm' => date('Y-m-d H:i:s'),
                            'created_at' => $ptk->created_at,
                        ]);
                        if ($this->_db->affectedRows() > 0) {
                            $this->_db->table('_tb_temp_usulan_detail')->where(['id' => $ptk->id, 'status_usulan' => 5, 'jenis_tunjangan' => 'tamsil'])->delete();
                            if ($this->_db->affectedRows() > 0) {
                                continue;
                            } else {
                                try {
                                    if (strpos($newNamelampiran, '/') !== false) {
                                        $minioClient = new MinioClient();
                                        $isDeleted = $minioClient->deleteObject("situgu", $newNamelampiran);
                                    } else {
                                        unlink($dir . '/' . $newNamelampiran);
                                    }
                                } catch (\Throwable $th) {
                                }

                                $this->_db->transRollback();
                                $response = new \stdClass;
                                $response->status = 400;
                                $response->message = "Gagal memindahkan data usulan.";
                                return json_encode($response);
                            }
                        } else {
                            try {
                                if (strpos($newNamelampiran, '/') !== false) {
                                    $minioClient = new MinioClient();
                                    $isDeleted = $minioClient->deleteObject("situgu", $newNamelampiran);
                                } else {
                                    unlink($dir . '/' . $newNamelampiran);
                                }
                            } catch (\Throwable $th) {
                            }

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
                try {
                    if (strpos($newNamelampiran, '/') !== false) {
                        $minioClient = new MinioClient();
                        $isDeleted = $minioClient->deleteObject("situgu", $newNamelampiran);
                    } else {
                        unlink($dir . '/' . $newNamelampiran);
                    }
                } catch (\Throwable $th) {
                }

                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan data";
                return json_encode($response);
            }
        }
    }

    public function testDir()
    {
        // var_dump(PHPWORD_BASE_DIR);
        die;
    }
}
