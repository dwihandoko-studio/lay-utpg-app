<?php

namespace App\Controllers\Situgu\Su\Upload\Tpg;

use App\Controllers\BaseController;
use App\Models\Situgu\Su\Tpg\Upload\MatchingModel;
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

class Matching extends BaseController
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
        $datamodel = new MatchingModel($request);

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
            // $action = '<div class="btn-group">
            //             <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
            //             <div class="dropdown-menu" style="">
            //                 <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama) . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
            //                 <a class="dropdown-item" href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Sync Dapodik</a>
            //             </div>
            //         </div>';
            $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . $list->filename . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                <i class="bx bxs-show font-size-16 align-middle"></i> DETAIL</button>
                </a>';
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            // $row[] = str_replace('&#039;', "`", str_replace("'", "`", $list->nama));
            $row[] = $list->filename;
            $row[] = 'Tahun ' . $list->tahun . ' - TW.' . $list->tw;
            $row[] = $list->jumlah;
            $row[] = $list->lolos;
            $row[] = $list->gagal;
            $row[] = $list->done;
            $row[] = $list->created_at;

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
        return redirect()->to(base_url('situgu/su/upload/tpg/matching/data'));
    }

    public function data()
    {
        $data['title'] = 'DATA MATCHING SIMTUN';
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
        return view('situgu/su/upload/tpg/matching/index', $data);
    }

    public function upload()
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
            $data['tw'] = $this->_db->table('_ref_tahun_tw')->where('is_current', 1)->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getRowObject();
            $data['tws'] = $this->_db->table('_ref_tahun_tw')->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getResult();
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/su/upload/tpg/matching/upload', $data);
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
            'tw' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tw tidak boleh kosong. ',
                ]
            ],
            '_file' => [
                'rules' => 'uploaded[_file]|max_size[_file,5120]|mime_in[_file,application/vnd.ms-excel,application/msexcel,application/x-msexcel,application/x-ms-excel,application/x-excel,application/x-dos_ms_excel,application/xls,application/x-xls,application/excel,application/download,application/vnd.ms-office,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/x-zip]',
                'errors' => [
                    'uploaded' => 'Pilih file terlebih dahulu. ',
                    'max_size' => 'Ukuran file terlalu besar, Maximum 5Mb. ',
                    'mime_in' => 'Ekstensi yang anda upload harus berekstensi xls atau xlsx. '
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('tw');
            // . $this->validator->getError('_file');
            return json_encode($response);
        } else {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session expired";
                return json_encode($response);
            }

            $tw = htmlspecialchars($this->request->getVar('tw'), true);

            $lampiran = $this->request->getFile('_file');
            // $mimeType = $lampiran->getMimeType();

            // var_dump($mimeType);
            // die;
            $extension = $lampiran->getClientExtension();
            $filesNamelampiran = $lampiran->getName();
            $newNamelampiran = _create_name_file($filesNamelampiran);
            $fileLocation = $lampiran->getTempName();

            if ('xls' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            $spreadsheet = $reader->load($fileLocation);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $total_line = (count($sheet) > 0) ? count($sheet) - 12 : 0;

            $dataImport = [];

            $nuptkImport = [];

            unset($sheet[0]);
            unset($sheet[1]);
            unset($sheet[2]);
            unset($sheet[3]);
            unset($sheet[4]);
            unset($sheet[5]);
            unset($sheet[6]);
            unset($sheet[7]);
            unset($sheet[8]);
            unset($sheet[9]);
            unset($sheet[10]);
            unset($sheet[11]);

            foreach ($sheet as $key => $data) {

                if ($data[1] == "" || strlen($data[1]) < 5) {
                    // if($data[1] == "") {
                    continue;
                }

                $dataInsert = [
                    'nrg' => $data[3],
                    'no_peserta' => $data[4],
                    'nuptk' => $data[5],
                    'nama' => $data[6],
                    'tgl_lahir' => $data[7],
                    'nip' => $data[11],
                    'tempat_tugas' => $data[10],
                    'jjm_sesuai' => $data[12],
                    'tugas_tambahan' => $data[13],
                    'jam_tugas_tambahan' => $data[14],
                    'total_jjm_sesuai' => $data[15],
                    'masa_kerja' => $data[16],
                    'golongan_code' => $data[17],
                    'golongan' => getCodePangkatFromMatching($data[17]),
                    'gaji_pokok' => str_replace(",", "", $data[18]),
                    'no_rekening' => $data[19],
                    'nama_bank' => $data[20],
                    'cabang_bank' => $data[21],
                    'an_rekening' => $data[22],
                ];

                $dataInsert['data_usulan'] = $this->_db->table('_tb_usulan_detail_tpg_test a')
                    ->select("a.id as id_usulan, a.us_pang_golongan, a.us_pang_mk_tahun, a.us_gaji_pokok, a.date_approve, a.kode_usulan, a.id_ptk, a.id_tahun_tw, a.status_usulan, a.date_approve_sptjm, b.nama, b.nik, b.nuptk, b.jenis_ptk, b.kecamatan, e.cuti as lampiran_cuti, e.pensiun as lampiran_pensiun, e.kematian as lampiran_kematian")
                    ->join('_ptk_tb b', 'a.id_ptk = b.id')
                    ->join('_upload_data_attribut e', 'a.id_ptk = e.id_ptk AND (a.id_tahun_tw = e.id_tahun_tw)')
                    ->where('a.status_usulan', 2)
                    ->where('a.id_tahun_tw', $tw)
                    ->where('b.nuptk', $data[5])
                    ->get()->getRowObject();

                $dataImport[] = $dataInsert;
                $nuptkImport[] = $data[5];
            }

            $dataImports = [
                'total_line' => $total_line,
                'data' => $dataImport,
            ];

            if (count($nuptkImport) < 1) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tidak ada data yang di import.";
                return json_encode($response);
            }

            // $x['import'] = $dataImports;

            $data = [
                'id_tahun_tw' => $tw,
                'jumlah' => $total_line,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $dir = FCPATH . "upload/matching";
            $field_db = 'filename';
            $table_db = 'tb_matching';

            if ($lampiran->isValid() && !$lampiran->hasMoved()) {
                $lampiran->move($dir, $newNamelampiran);
                $data[$field_db] = $newNamelampiran;
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupload file.";
                return json_encode($response);
            }

            // $dataResult['us_ptk'] = $this->_db->table('_tb_usulan_detail_tpg a')
            //     ->select("a.id as id_usulan, a.us_pang_golongan, a.us_pang_mk_tahun, a.us_gaji_pokok, a.date_approve, a.kode_usulan, a.id_ptk, a.id_tahun_tw, a.status_usulan, a.date_approve_sptjm, b.nama, b.nik, b.nuptk, b.jenis_ptk, b.kecamatan")
            //     ->join('_ptk_tb b', 'a.id_ptk = b.id')
            //     ->where('a.status_usulan', 2)
            //     ->where('a.id_tahun_tw', $tw)
            //     ->whereIn('b.nuptk', $nuptkImport)
            //     ->get()->getResult();

            $this->_db->transBegin();
            try {
                $cekCurrent = $this->_db->table($table_db)->insert($data);
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
                if (write_file($dir . '/' . $newNamelampiran . '.json', json_encode($dataImports))) {
                } else {
                    $this->_db->transRollback();

                    $response = new \stdClass;
                    $response->status = 400;
                    $response->error = "Gagal membuat file json";
                    $response->message = "Gagal menyimpan data.";
                    return json_encode($response);
                }

                // createAktifitas($user->data->id, "Mengupload matching simtun $filesNamelampiran", "Mengupload Matching Simtun filesNamelampiran", "upload", $tw);
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $x['data'] = [];
                $x['id'] = $newNamelampiran;
                $response->data = view('situgu/su/upload/tpg/matching/verifi-upload', $x);
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

    public function get_data_json()
    {
        $id = htmlspecialchars($this->request->getGet('id'), true);
        $datas = json_decode(file_get_contents(FCPATH . "upload/matching/$id.json"), true);
        $result = [];
        if (isset($datas['data']) && count($datas['data']) > 0) {
            $result['total'] = count($datas['data']);
            $response = [];
            $response_aksi = [];
            $lolos = 0;
            $gagal = 0;
            foreach ($datas['data'] as $key => $v) {
                $item = [];
                if ($v['data_usulan'] == NULL || $v['data_usulan'] == "") {
                    $item['number'] = $key + 1;
                    $item['nuptk'] = $v['nuptk'];
                    $item['nama'] = $v['nama'];
                    $item['golongan_code'] = $v['golongan_code'];
                    $item['masa_kerja'] = $v['masa_kerja'];
                    $item['gaji_pokok'] = $v['gaji_pokok'];
                    $item['total_jjm_sesuai'] = $v['total_jjm_sesuai'];
                    $item['us_nuptk'] = "";
                    $item['us_nama'] = "";
                    $item['us_golongan'] = "";
                    $item['us_masa_kerja'] = "";
                    $item['us_gaji_pokok'] = "";
                    $item['us_keterangan'] = "";
                    $item['keterangan'] = "Belum Mengusulkan";
                    $item['aksi'] = "Aksi";
                    $item['status'] = "table-info";
                    $item['id_usulan'] = $v['total_jjm_sesuai'];
                    $item['kode_usulan'] = $v['total_jjm_sesuai'];
                    $item['id_ptk'] = $v['total_jjm_sesuai'];
                    $item['id_tahun_tw'] = $v['total_jjm_sesuai'];
                    $gagal += 1;
                } else {
                    $keterangan = "";
                    if (($v['data_usulan']['lampiran_cuti'] == NULL || $v['data_usulan']['lampiran_cuti'] == "") && ($v['data_usulan']['lampiran_pensiun'] == NULL || $v['data_usulan']['lampiran_pensiun'] == "") && ($v['data_usulan']['lampiran_kematian'] == NULL || $v['data_usulan']['lampiran_kematian'] == "")) {
                        $keterangan .= "- ";
                    }

                    if (!($v['data_usulan']['lampiran_cuti'] == NULL || $v['data_usulan']['lampiran_cuti'] == "")) {
                        $keterangan .= "Cuti ";
                    }

                    if (!($v['data_usulan']['lampiran_pensiun'] == NULL || $v['data_usulan']['lampiran_pensiun'] == "")) {
                        $keterangan .= "Pensiun ";
                    }

                    if (!($v['data_usulan']['lampiran_kematian'] == NULL || $v['data_usulan']['lampiran_kematian'] == "")) {
                        $keterangan .= "Kematian ";
                    }

                    if ($v['total_jjm_sesuai'] >= 24 && $v['total_jjm_sesuai'] <= 40) {

                        if ($v['golongan'] == "" && !($v['nip'] == NULL || $v['nip'] == "")) {
                            if ("IX" == $v['data_usulan']['us_pang_golongan'] && $v['masa_kerja'] == $v['data_usulan']['us_pang_mk_tahun'] && $v['gaji_pokok'] == $v['data_usulan']['us_gaji_pokok']) {
                                $item['number'] = $key + 1;
                                $item['nuptk'] = $v['nuptk'];
                                $item['nama'] = $v['nama'];
                                $item['golongan_code'] = $v['golongan_code'];
                                $item['masa_kerja'] = $v['masa_kerja'];
                                $item['gaji_pokok'] = $v['gaji_pokok'];
                                $item['total_jjm_sesuai'] = $v['total_jjm_sesuai'];
                                $item['us_nuptk'] = $v['data_usulan']['nuptk'];
                                $item['us_nama'] = $v['data_usulan']['nama'];
                                $item['us_golongan'] = $v['data_usulan']['us_pang_golongan'];
                                $item['us_masa_kerja'] = $v['data_usulan']['us_pang_mk_tahun'];
                                $item['us_gaji_pokok'] = $v['data_usulan']['us_gaji_pokok'];
                                $item['us_keterangan'] = $keterangan;
                                $item['keterangan'] = "Siap Diusulkan SKTP";
                                $item['aksi'] = "Aksi";
                                $item['status'] = "table-success";
                                $item['id_usulan'] = $v['total_jjm_sesuai'];
                                $item['kode_usulan'] = $v['total_jjm_sesuai'];
                                $item['id_ptk'] = $v['total_jjm_sesuai'];
                                $item['id_tahun_tw'] = $v['total_jjm_sesuai'];
                                $lolos += 1;
                            } else {
                                $item['number'] = $key + 1;
                                $item['nuptk'] = $v['nuptk'];
                                $item['nama'] = $v['nama'];
                                $item['golongan_code'] = $v['golongan_code'];
                                $item['masa_kerja'] = $v['masa_kerja'];
                                $item['gaji_pokok'] = $v['gaji_pokok'];
                                $item['total_jjm_sesuai'] = $v['total_jjm_sesuai'];
                                $item['us_nuptk'] = $v['data_usulan']['nuptk'];
                                $item['us_nama'] = $v['data_usulan']['nama'];
                                $item['us_golongan'] = $v['data_usulan']['us_pang_golongan'];
                                $item['us_masa_kerja'] = $v['data_usulan']['us_pang_mk_tahun'];
                                $item['us_gaji_pokok'] = $v['data_usulan']['us_gaji_pokok'];
                                $item['us_keterangan'] = $keterangan;
                                $item['keterangan'] = "Belum Update Dapodik";
                                $item['aksi'] = "Aksi";
                                $item['status'] = "table-danger";
                                $item['id_usulan'] = $v['total_jjm_sesuai'];
                                $item['kode_usulan'] = $v['total_jjm_sesuai'];
                                $item['id_ptk'] = $v['total_jjm_sesuai'];
                                $item['id_tahun_tw'] = $v['total_jjm_sesuai'];
                                $gagal += 1;
                            }
                        } else {
                            if ($v['golongan'] == $v['data_usulan']['us_pang_golongan'] && $v['masa_kerja'] == $v['data_usulan']['us_pang_mk_tahun'] && $v['gaji_pokok'] == $v['data_usulan']['us_gaji_pokok']) {
                                $item['number'] = $key + 1;
                                $item['nuptk'] = $v['nuptk'];
                                $item['nama'] = $v['nama'];
                                $item['golongan_code'] = $v['golongan_code'];
                                $item['masa_kerja'] = $v['masa_kerja'];
                                $item['gaji_pokok'] = $v['gaji_pokok'];
                                $item['total_jjm_sesuai'] = $v['total_jjm_sesuai'];
                                $item['us_nuptk'] = $v['data_usulan']['nuptk'];
                                $item['us_nama'] = $v['data_usulan']['nama'];
                                $item['us_golongan'] = $v['data_usulan']['us_pang_golongan'];
                                $item['us_masa_kerja'] = $v['data_usulan']['us_pang_mk_tahun'];
                                $item['us_gaji_pokok'] = $v['data_usulan']['us_gaji_pokok'];
                                $item['us_keterangan'] = $keterangan;
                                $item['keterangan'] = "Siap Diusulkan SKTP";
                                $item['aksi'] = "Aksi";
                                $item['status'] = "table-success";
                                $item['id_usulan'] = $v['total_jjm_sesuai'];
                                $item['kode_usulan'] = $v['total_jjm_sesuai'];
                                $item['id_ptk'] = $v['total_jjm_sesuai'];
                                $item['id_tahun_tw'] = $v['total_jjm_sesuai'];
                                $lolos += 1;
                            } else {
                                $item['number'] = $key + 1;
                                $item['nuptk'] = $v['nuptk'];
                                $item['nama'] = $v['nama'];
                                $item['golongan_code'] = $v['golongan_code'];
                                $item['masa_kerja'] = $v['masa_kerja'];
                                $item['gaji_pokok'] = $v['gaji_pokok'];
                                $item['total_jjm_sesuai'] = $v['total_jjm_sesuai'];
                                $item['us_nuptk'] = $v['data_usulan']['nuptk'];
                                $item['us_nama'] = $v['data_usulan']['nama'];
                                $item['us_golongan'] = $v['data_usulan']['us_pang_golongan'];
                                $item['us_masa_kerja'] = $v['data_usulan']['us_pang_mk_tahun'];
                                $item['us_gaji_pokok'] = $v['data_usulan']['us_gaji_pokok'];
                                $item['us_keterangan'] = $keterangan;
                                $item['keterangan'] = "Belum Update Dapodik";
                                $item['aksi'] = "Aksi";
                                $item['status'] = "table-danger";
                                $item['id_usulan'] = $v['total_jjm_sesuai'];
                                $item['kode_usulan'] = $v['total_jjm_sesuai'];
                                $item['id_ptk'] = $v['total_jjm_sesuai'];
                                $item['id_tahun_tw'] = $v['total_jjm_sesuai'];
                                $gagal += 1;
                            }
                        }
                    } else {
                        $item['number'] = $key + 1;
                        $item['nuptk'] = $v['nuptk'];
                        $item['nama'] = $v['nama'];
                        $item['golongan_code'] = $v['golongan_code'];
                        $item['masa_kerja'] = $v['masa_kerja'];
                        $item['gaji_pokok'] = $v['gaji_pokok'];
                        $item['total_jjm_sesuai'] = $v['total_jjm_sesuai'];
                        $item['us_nuptk'] = $v['data_usulan']['nuptk'];
                        $item['us_nama'] = $v['data_usulan']['nama'];
                        $item['us_golongan'] = $v['data_usulan']['us_pang_golongan'];
                        $item['us_masa_kerja'] = $v['data_usulan']['us_pang_mk_tahun'];
                        $item['us_gaji_pokok'] = $v['data_usulan']['us_gaji_pokok'];
                        $item['us_keterangan'] = $keterangan;
                        $item['keterangan'] = "Belum Memenuhi Syarat";
                        $item['aksi'] = "Aksi";
                        $item['status'] = "table-warning";
                        $item['id_usulan'] = $v['total_jjm_sesuai'];
                        $item['kode_usulan'] = $v['total_jjm_sesuai'];
                        $item['id_ptk'] = $v['total_jjm_sesuai'];
                        $item['id_tahun_tw'] = $v['total_jjm_sesuai'];
                        $gagal += 1;
                    }
                    $response_aksi[] = $item;
                }

                $response[] = $item;
            }
            $result['lolos'] = $lolos;
            $result['gagal'] = $gagal;
            $result['data'] = $response;
            $result['aksi'] = $response_aksi;
        } else {
            $result['total'] = 0;
            $result['lolos'] = 0;
            $result['gagal'] = 0;
            $result['data'] = [];
        }

        return json_encode($result);
    }

    public function prosesmatching()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        var_dump($this->request->getVar());
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

            $current = $this->_db->table('v_lolosberkas_usulan_tpg a')
                ->select("a.*, b.kecamatan as kecamatan_sekolah, c.lampiran_sptjm, d.gaji_pokok as gaji_pokok_referensi, e.fullname as verifikator")
                ->join('ref_sekolah b', 'a.npsn = b.npsn')
                ->join('_tb_sptjm c', 'a.kode_usulan = c.kode_usulan')
                ->join('ref_gaji d', 'a.us_pang_golongan = d.pangkat AND (a.us_pang_mk_tahun = d.masa_kerja)', 'LEFT')
                ->join('_profil_users_tb e', 'a.admin_approve = e.id', 'LEFT')
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
                $response->data = view('situgu/su/upload/tpg/matching/detail', $data);
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
            $worksheet->fromArray(['NO', 'NUPTK', 'NAMA', 'TEMPAT TUGAS', 'NIP', 'GOL', 'MASA KERJA TAHUN', 'GAJI POKOK PP.15', 'JML. BLN', 'JML.UANG', 'IURAN BPJS 1%', 'PPH.21', 'JML. DITERIMA', 'NO REKENING', 'NPSN', 'KECAMATAN', 'JENJANG SEKOLAH', 'KETERANGAN', 'VERIFIKATOR'], NULL, 'A3');

            // Mengambil data dari database
            $dataTw = $this->_db->table('_ref_tahun_tw')->where('id', $tw)->get()->getRowObject();
            $query = $this->_db->table('_tb_usulan_detail_tpg a')
                ->select("a.id as id_usulan, a.us_pang_golongan, a.us_pang_mk_tahun, a.us_pang_mk_bulan, a.us_gaji_pokok, a.date_approve, a.kode_usulan, a.id_ptk, a.id_tahun_tw, a.status_usulan, a.date_approve_sptjm, b.nama, b.nik, CONCAT(b.nip) as nip, b.tempat_tugas, b.npsn, CONCAT(b.no_rekening) as no_rekening, CONCAT(b.nuptk) as nuptk, b.jenis_ptk, c.kecamatan, c.bentuk_pendidikan, d.fullname as verifikator, e.cuti as lampiran_cuti, e.pensiun as lampiran_pensiun, e.kematian as lampiran_kematian")
                ->join('_ptk_tb b', 'a.id_ptk = b.id')
                ->join('ref_sekolah c', 'b.npsn = c.npsn')
                ->join('_profil_users_tb d', 'a.admin_approve = d.id')
                ->join('_upload_data_attribut e', 'a.id_ptk = e.id_ptk AND (a.id_tahun_tw = e.id_tahun_tw)')
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

                    $itemCreate = [
                        $key + 1,
                        substr($item->nuptk, 0),
                        $item->nama,
                        $item->tempat_tugas,
                        substr($item->nip, 0),
                        $item->us_pang_golongan,
                        $item->us_pang_mk_tahun,
                        $item->us_gaji_pokok,
                        3,
                        $item->us_gaji_pokok * 3,
                        ($item->us_gaji_pokok * 3) * 0.01,
                        ($item->us_gaji_pokok * 3) * $pph21,
                        ($item->us_gaji_pokok * 3) - (($item->us_gaji_pokok * 3) * 0.01) - (($item->us_gaji_pokok * 3) * $pph21),
                        substr($item->no_rekening, 0),
                        $item->npsn,
                        $item->kecamatan,
                        $item->bentuk_pendidikan,
                        $keterangan,
                        $item->verifikator,
                    ];
                    // $worksheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // $worksheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // $worksheet->getStyle('N' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    // $worksheet->getStyle('O' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $worksheet->fromArray($itemCreate, NULL, 'A' . $row);
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
            $filename = 'data_lolosberkas_usulan_tpg_tahun_' . $dataTw->tahun . '_tw_' . $dataTw->tw . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit();
            //code...
        } catch (\Throwable $th) {
            var_dump($th);
        }
    }

    public function test()
    {
        $util = \CodeIgniter\Database\Config::utils();

        // Get the data from the MySQL table
        $query = $this->_db->table('_ptk_tb')->limit(10);

        // Format the column as text
        // foreach ($query as $row) {
        //     $data[] = array(
        //         'nama' => "\t" . $row->nama,
        //         'nik' => "\t" . $row->nik,
        //         'nuptk' => "\t" . $row->nuptk,
        //         'nip' => "\t" . $row->nip,
        //         'no_rekening' => "\t" . $row->no_rekening,
        //         'cabang_bank' => "\t" . $row->cabang_bank
        //     );
        // }

        // Create an Excel file
        $xls = $util->getXMLFromResult($query->get());
        // $xls = $util->getXMLFromResult($data, "\t");

        // Set the headers to download the file
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="mytable.xls"');
        header('Cache-Control: max-age=0');

        // Output the Excel file
        echo $xls;

        // $this->load->dbutil();
        // $this->load->helper('download');

        // // Query the database to retrieve the data
        // $query = $this->db->query("SELECT * FROM your_table");
        // $data = $this->dbutil->csv_from_result($query);

        // // Modify the data to include formatting column as text
        // $data = "\t" . str_replace("\n", "\n\t", $data);

        // // Set the appropriate headers for the Excel file, and force the browser to download the file
        // $filename = 'your_file.xls';
        // header('Content-Type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment; filename="' . $filename . '"');
        // echo $data;
        // exit;
    }
}
