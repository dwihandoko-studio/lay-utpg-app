<?php

namespace App\Controllers\Situpeng\Adm\Us\Tpg;

use App\Controllers\BaseController;
use App\Models\Situpeng\Adm\Tpg\LolosberkasModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Situgu\Kehadiranptklib;
use App\Libraries\Uuid;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Lolosberkas extends BaseController
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
        $datamodel = new LolosberkasModel($request);

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
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id_usulan . '\', \'' . $list->id_pengawas . '\', \'' . $list->id_tahun_tw . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\',\'' . $list->nuptk . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
                            <a class="dropdown-item" href="javascript:actionEdit(\'' . $list->id_usulan . '\', \'' . $list->id_pengawas . '\', \'' . $list->id_tahun_tw . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\',\'' . $list->nuptk . '\');"><i class="bx bx bx-edit-alt font-size-16 align-middle"></i> &nbsp;Edit Data Pembenahan</a>
                        </div>
                    </div>';
            // $action = '<a href="javascript:actionDetail(\'' . $list->id_usulan . '\', \'' . $list->id_pengawas . '\', \'' . $list->id_tahun_tw . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i> DETAIL</button>
            //     </a>';
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_pengawas . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            $row[] = str_replace('&#039;', "`", str_replace("'", "`", $list->nama));
            $row[] = $list->nik;
            $row[] = $list->nuptk;
            $row[] = $list->jenjang_pengawas;
            $row[] = $list->jenis_pengawas;
            $row[] = $list->date_approve;

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
        return redirect()->to(base_url('situpeng/adm/us/tpg/lolosberkas/data'));
    }

    public function data()
    {
        $data['title'] = 'USULAN LOLOS BERKAS TUNJANGAN PROFESI GURU';
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
        return view('situpeng/adm/us/tpg/lolosberkas/index', $data);
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
            'id_pengawas' => [
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
            'nuptk' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nuptk tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('id_pengawas')
                . $this->validator->getError('tw')
                . $this->validator->getError('nuptk')
                . $this->validator->getError('nama');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $id_pengawas = htmlspecialchars($this->request->getVar('id_pengawas'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $nuptk = htmlspecialchars($this->request->getVar('nuptk'), true);

            $current = $this->_db->table('_tb_usulan_detail_tpg_pengawas a')
                ->select("a.id as id_usulan, a.us_pang_golongan, a.us_pang_tmt, a.us_pang_tgl, a.us_pang_jenis, a.us_pang_mk_tahun, a.us_pang_mk_bulan, a.us_gaji_pokok, a.date_approve, a.kode_usulan, a.id_pengawas, a.id_tahun_tw, a.status_usulan, a.date_approve_sptjm, b.nama, b.nik, b.nuptk, b.jenis_ptk, b.kecamatan, e.cuti as lampiran_cuti, e.pensiun as lampiran_pensiun, e.kematian as lampiran_kematian, e.pang_golongan as attr_pang_golongan, e.pang_jenis as attr_pang_jenis, e.pang_no as attr_pang_no, e.pang_tmt as attr_pang_tmt, e.pang_tgl as attr_pang_tgl, e.pang_tahun as attr_pang_mk_tahun, e.pang_bulan as attr_pang_mk_bulan")
                ->join('__pengawas_tb b', 'a.id_pengawas = b.id')
                ->join('__pengawas_upload_data_attribut e', 'a.id_pengawas = e.id_ptk AND (a.id_tahun_tw = e.id_tahun_tw)')
                ->where('a.status_usulan', 2)
                ->where(['a.id' => $id, 'a.id_tahun_tw' => $tw])
                ->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $data['penugasans'] = $this->_db->table('_ptk_tb_dapodik a')
                    ->select("a.*, b.npsn, b.nama as namaSekolah, b.kecamatan as kecamatan_sekolah, (SELECT SUM(jam_mengajar_per_minggu) FROM _pembelajaran_dapodik WHERE ptk_id = a.ptk_id AND sekolah_id = a.sekolah_id AND semester_id = a.semester_id) as jumlah_total_jam_mengajar_perminggu")
                    ->join('ref_sekolah b', 'a.sekolah_id = b.id')
                    ->where('a.ptk_id', $current->id_pengawas)
                    ->where("a.jenis_keluar IS NULL")
                    ->orderBy('a.ptk_induk', 'DESC')->get()->getResult();
                $data['igd'] = $this->_db->table('_info_gtk')->where('ptk_id', $current->id_pengawas)->get()->getRowObject();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situpeng/adm/us/tpg/lolosberkas/edit-lolos', $data);
                return json_encode($response);
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
            'id_usulan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id Usulan tidak boleh kosong. ',
                ]
            ],
            'id_pengawas' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
            'id_tahun_tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id Tahun TW tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id_usulan')
                . $this->validator->getError('id_tahun_tw')
                . $this->validator->getError('id_pengawas');
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

            $id_usulan = htmlspecialchars($this->request->getVar('id_usulan'), true);
            $id_pengawas = htmlspecialchars($this->request->getVar('id_pengawas'), true);
            $id_tahun_tw = htmlspecialchars($this->request->getVar('id_tahun_tw'), true);

            $us_pang_jenis = htmlspecialchars($this->request->getVar('us_pang_jenis'), true);
            $us_pang_golongan = htmlspecialchars($this->request->getVar('us_pang_golongan'), true);
            $us_pang_tmt = htmlspecialchars($this->request->getVar('us_pang_tmt'), true);
            $us_pang_tgl = htmlspecialchars($this->request->getVar('us_pang_tgl'), true);
            $us_pang_mk_tahun = htmlspecialchars($this->request->getVar('us_pang_mk_tahun'), true);
            $us_pang_mk_bulan = htmlspecialchars($this->request->getVar('us_pang_mk_bulan'), true);
            $us_gaji_pokok = htmlspecialchars($this->request->getVar('us_gaji_pokok'), true);

            $attr_pang_jenis = htmlspecialchars($this->request->getVar('attr_pang_jenis'), true);
            $attr_pang_golongan = htmlspecialchars($this->request->getVar('attr_pang_golongan'), true);
            $attr_pang_no = htmlspecialchars($this->request->getVar('attr_pang_no'), true);
            $attr_pang_tmt = htmlspecialchars($this->request->getVar('attr_pang_tmt'), true);
            $attr_pang_tgl = htmlspecialchars($this->request->getVar('attr_pang_tgl'), true);
            $attr_pang_mk_tahun = htmlspecialchars($this->request->getVar('attr_pang_mk_tahun'), true);
            $attr_pang_mk_bulan = htmlspecialchars($this->request->getVar('attr_pang_mk_bulan'), true);

            $oldData =  $this->_db->table('_tb_usulan_detail_tpg')->where('id', $id_usulan)->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($us_pang_jenis !== "") {
                $data['us_pang_jenis'] = $us_pang_jenis;
            }
            if ($us_pang_golongan !== "") {
                $data['us_pang_golongan'] = $us_pang_golongan;
            } else {
                $data['us_pang_golongan'] = NULL;
            }
            if ($us_pang_tmt !== "") {
                $data['us_pang_tmt'] = $us_pang_tmt;
            } else {
                $data['us_pang_tmt'] = null;
            }
            if ($us_pang_tgl !== "") {
                $data['us_pang_tgl'] = $us_pang_tgl;
            } else {
                $data['us_pang_tgl'] = NULL;
            }
            if ($us_pang_mk_tahun !== "") {
                $data['us_pang_mk_tahun'] = $us_pang_mk_tahun;
            } else {
                $data['us_pang_mk_tahun'] = 0;
            }
            if ($us_pang_mk_bulan !== "") {
                $data['us_pang_mk_bulan'] = $us_pang_mk_bulan;
            } else {
                $data['us_pang_mk_bulan'] = 0;
            }
            if ($us_gaji_pokok !== "") {
                $data['us_gaji_pokok'] = $us_gaji_pokok;
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_tb_usulan_detail_tpg')->where('id', $oldData->id)->update($data);
            } catch (\Exception $e) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($e);
                $response->message = "Gagal mengubah data usulan.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {

                $dataAttr = [
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if ($attr_pang_jenis !== "") {
                    $dataAttr['pang_jenis'] = $attr_pang_jenis;
                }
                if ($attr_pang_golongan !== "") {
                    $dataAttr['pang_golongan'] = $attr_pang_golongan;
                } else {
                    $dataAttr['pang_golongan'] = NULL;
                }
                if ($attr_pang_no !== "") {
                    $dataAttr['pang_no'] = $attr_pang_no;
                } else {
                    $dataAttr['pang_no'] = NULL;
                }
                if ($attr_pang_tmt !== "") {
                    $dataAttr['pang_tmt'] = $attr_pang_tmt;
                } else {
                    $dataAttr['pang_tmt'] = NULL;
                }
                if ($attr_pang_tgl !== "") {
                    $dataAttr['pang_tgl'] = $attr_pang_tgl;
                } else {
                    $dataAttr['pang_tgl'] = NULL;
                }
                if ($attr_pang_mk_tahun !== "") {
                    $dataAttr['pang_tahun'] = $attr_pang_mk_tahun;
                } else {
                    $dataAttr['pang_tahun'] = 0;
                }
                if ($attr_pang_mk_bulan !== "") {
                    $dataAttr['pang_bulan'] = $attr_pang_mk_bulan;
                } else {
                    $dataAttr['pang_bulan'] = 0;
                }

                try {
                    $this->_db->table('__pengawas_upload_data_attribut')->where(['id_ptk' => $id_pengawas, 'id_tahun_tw' => $id_tahun_tw])->update($dataAttr);
                } catch (\Exception $e) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->error = var_dump($e);
                    $response->message = "Gagal mengubah data baru pada atribut.";
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
                    $response->message = "Gagal mengupate data pada atribut";
                    return json_encode($response);
                }
            } else {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupate data usulan";
                return json_encode($response);
            }
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
            'id_pengawas' => [
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
                . $this->validator->getError('id_pengawas')
                . $this->validator->getError('tw')
                . $this->validator->getError('nama');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $id_pengawas = htmlspecialchars($this->request->getVar('id_pengawas'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);

            $current = $this->_db->table('_tb_usulan_detail_tpg_pengawas a')
                ->select("b.*, a.id as id_usulan, a.id_tahun_tw, a.us_pang_golongan, a.us_pang_tmt, a.us_pang_tgl, a.us_pang_mk_tahun, a.us_pang_mk_bulan, a.us_pang_jenis, a.us_gaji_pokok, a.status_usulan, c.gaji_pokok as gaji_pokok_referensi, d.pang_no, d.pangkat_terakhir as lampiran_pangkat, d.kgb_terakhir as lampiran_kgb, d.pernyataan_24jam as lampiran_pernyataan, d.penugasan as lampiran_penugasan, d.kunjungan_binaan as lampiran_kunjungan_binaan, d.cuti as lampiran_cuti, d.pensiun as lampiran_pensiun, d.kematian as lampiran_kematian, d.lainnya as lampiran_attr_lainnya, e.fullname as verifikator")
                ->join('__pengawas_tb b', 'a.id_pengawas = b.id')
                ->join('__pengawas_upload_data_attribut d', 'a.id_pengawas = d.id_ptk AND (a.id_tahun_tw = d.id_tahun_tw)')
                ->join('ref_gaji c', 'a.us_pang_golongan = c.pangkat AND (c.masa_kerja = (IF(a.us_pang_mk_tahun > 32, 32, a.us_pang_mk_tahun)))', 'LEFT')
                ->join('_profil_users_tb e', 'a.admin_approve = e.id')
                ->where(['a.id' => $id, 'a.id_tahun_tw' => $tw])->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situpeng/adm/us/tpg/detail-lolos', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function download()
    {
        $tw = htmlspecialchars($this->request->getGet('tw'), true);
        if ($tw == "") {
            return view('404');
        }

        try {

            $spreadsheet = new Spreadsheet();

            // $spreadsheet->getDefaultStyle()->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            // $spreadsheet->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            // Membuat objek worksheet
            $worksheet = $spreadsheet->getActiveSheet();

            // Menulis nama kolom ke dalam baris pertama worksheet
            $worksheet->fromArray(['NO', 'NUPTK', 'NAMA', 'TEMPAT TUGAS', 'NIP', 'GOL', 'MASA KERJA TAHUN', 'GAJI POKOK PP.15', 'JML. BLN', 'JML.UANG', 'IURAN BPJS 1%', 'PPH.21', 'JML. DITERIMA', 'NO REKENING', 'JENIS PENGAWAS', 'JENJANG PENGAWAS', 'KETERANGAN', 'VERIFIKATOR'], NULL, 'A3');

            // Mengambil data dari database
            $dataTw = $this->_db->table('_ref_tahun_tw')->where('id', $tw)->get()->getRowObject();
            $query = $this->_db->table('_tb_usulan_detail_tpg_pengawas a')
                ->select("a.id as id_usulan, a.us_pang_golongan, a.us_pang_mk_tahun, a.us_pang_mk_bulan, a.us_gaji_pokok, a.date_approve, a.kode_usulan, a.id_pengawas, a.id_tahun_tw, a.status_usulan, a.date_approve_sptjm, b.nama, b.nik, CONCAT(b.nip) as nip, b.tempat_tugas, CONCAT(b.no_rekening) as no_rekening, CONCAT(b.nuptk) as nuptk, b.jenis_pengawas, b.jenjang_pengawas, d.fullname as verifikator, e.cuti as lampiran_cuti, e.pensiun as lampiran_pensiun, e.kematian as lampiran_kematian")
                ->join('__pengawas_tb b', 'a.id_pengawas = b.id')
                ->join('_profil_users_tb d', 'a.admin_approve = d.id')
                ->join('__pengawas_upload_data_attribut e', 'a.id_pengawas = e.id_ptk AND (a.id_tahun_tw = e.id_tahun_tw)')
                ->where('a.status_usulan', 2)
                ->where('a.id_tahun_tw', $tw)
                ->get();

            // Menulis data ke dalam worksheet
            $data = $query->getResult();
            $row = 4;
            if (count($data) > 0) {
                foreach ($data as $key => $item) {
                    $keterangan = "";
                    $pph = "0%";
                    $pph21 = 0;
                    if ($item->us_pang_golongan == NULL || $item->us_pang_golongan == "") {
                    } else {
                        $pang = explode("/", $item->us_pang_golongan);
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

                    if (($item->lampiran_cuti == NULL || $item->lampiran_cuti == "") && ($item->lampiran_pensiun == NULL || $item->lampiran_pensiun == "") && ($item->lampiran_kematian == NULL || $item->lampiran_kematian == "")) {
                        $keterangan .= "- ";
                    }

                    if (!($item->lampiran_cuti == NULL || $item->lampiran_cuti == "")) {
                        $keterangan .= "Cuti ";
                    }

                    if (!($item->lampiran_pensiun == NULL || $item->lampiran_pensiun == "")) {
                        $keterangan .= "Pensiun ";
                    }

                    if (!($item->lampiran_kematian == NULL || $item->lampiran_kematian == "")) {
                        $keterangan .= "Kematian ";
                    }

                    // $itemCreate = [
                    //     $key + 1,
                    //     substr($item->nuptk, 0),
                    //     $item->nama,
                    //     $item->tempat_tugas,
                    //     substr($item->nip, 0),
                    //     $item->us_pang_golongan,
                    //     $item->us_pang_mk_tahun,
                    //     $item->us_gaji_pokok,
                    //     3,
                    //     $item->us_gaji_pokok * 3,
                    //     ($item->us_gaji_pokok * 3) * 0.01,
                    //     ($item->us_gaji_pokok * 3) * $pph21,
                    //     ($item->us_gaji_pokok * 3) - (($item->us_gaji_pokok * 3) * 0.01) - (($item->us_gaji_pokok * 3) * $pph21),
                    //     substr($item->no_rekening, 0),
                    //     $item->jenis_pengawas,
                    //     $item->jenjang_pengawas,
                    //     $keterangan,
                    //     $item->verifikator,
                    // ];

                    $worksheet->getCell('A' . $row)->setValue($key + 1);
                    $worksheet->setCellValueExplicit("B" . $row, (string)$item->nuptk, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $worksheet->getCell('C' . $row)->setValue($item->nama);
                    $worksheet->getCell('D' . $row)->setValue($item->tempat_tugas);
                    $worksheet->setCellValueExplicit("E" . $row, (string)$item->nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $worksheet->getCell('F' . $row)->setValue($item->us_pang_golongan);
                    $worksheet->setCellValueExplicit("G" . $row, $item->us_pang_mk_tahun, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $worksheet->setCellValueExplicit("H" . $row, $item->us_gaji_pokok, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $worksheet->setCellValueExplicit("I" . $row, 3, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $worksheet->setCellValueExplicit("J" . $row, ($item->us_gaji_pokok * 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $worksheet->setCellValueExplicit("K" . $row, (($item->us_gaji_pokok * 3) * 0.01), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $worksheet->setCellValueExplicit("L" . $row, (($item->us_gaji_pokok * 3) * $pph21), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $worksheet->setCellValueExplicit("M" . $row, (($item->us_gaji_pokok * 3) - (($item->us_gaji_pokok * 3) * 0.01) - (($item->us_gaji_pokok * 3) * $pph21)), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $worksheet->setCellValueExplicit("N" . $row, (string)$item->no_rekening, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $worksheet->getCell('O' . $row)->setValue($item->jenis_pengawas);
                    $worksheet->getCell('P' . $row)->setValue($item->jenjang_pengawas);
                    $worksheet->getCell('Q' . $row)->setValue($keterangan);
                    $worksheet->getCell('R' . $row)->setValue($item->verifikator);


                    // $worksheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // $worksheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // $worksheet->getStyle('N' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // $worksheet->getStyle('O' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // $worksheet->fromArray($itemCreate, NULL, 'A' . $row);
                    // $worksheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // $worksheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // $worksheet->getStyle('N' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // $worksheet->getStyle('O' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $row++;
                }
            }

            // Menyiapkan objek writer untuk menulis file Excel
            $writer = new Xls($spreadsheet);

            // Menuliskan file Excel
            $filename = 'data_lolosberkas_usulan_tpg_pengawas_tahun_' . $dataTw->tahun . '_tw_' . $dataTw->tw . '.xls';
            header('Content-Type: application/vnd-ms-excel');
            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit();
            //code...
        } catch (\Throwable $th) {
            var_dump($th);
        }
    }
}
