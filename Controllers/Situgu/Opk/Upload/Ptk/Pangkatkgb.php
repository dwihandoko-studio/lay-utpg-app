<?php

namespace App\Controllers\Situgu\Opk\Upload\Ptk;

use App\Controllers\BaseController;
use App\Models\Situgu\Opk\Ptk\Upload\PangkatkgbModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Situgu\NotificationLib;
use App\Libraries\Uuid;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Pangkatkgb extends BaseController
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
        $datamodel = new PangkatkgbModel($request);

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

        $lists = $datamodel->get_datatables($userId);
        $data = [];
        $no = $request->getPost("start");
        foreach ($lists as $list) {
            $no++;
            $row = [];

            $row[] = $no;
            $action = '<div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\', \'' . $list->filename . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
                            <a class="dropdown-item" href="javascript:actionHapus(\'' . $list->id . '\', \'' . $list->filename . '\');"><i class="bx bx-trash font-size-16 align-middle"></i> &nbsp;Delete</a>
                        </div>
                    </div>';
            // $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . $list->filename . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i> DETAIL</button>
            //     </a>';
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            // $row[] = str_replace('&#039;', "`", str_replace("'", "`", $list->nama));
            $row[] = $list->filename;
            $row[] = $list->jumlah;
            $row[] = $list->lolos;
            $row[] = $list->gagal;
            $row[] = $list->done;
            $row[] = $list->created_at;

            $data[] = $row;
        }
        $output = [
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datamodel->count_all($userId),
            "recordsFiltered" => $datamodel->count_filtered($userId),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }
        if (!(grantUploadPangkat($user->data->id))) {
            return view("405");
        }

        return redirect()->to(base_url('situgu/opk/upload/ptk/pangkatkgb/data'));
    }

    public function data()
    {
        $data['title'] = 'DATA UPLOAD PANGKAT KGB PTK';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }
        if (!(grantUploadPangkat($user->data->id))) {
            return view("405");
        }
        // $id = $this->_helpLib->getPtkId($user->data->id);
        $data['user'] = $user->data;
        return view('situgu/opk/upload/ptk/pangkatkgb/index', $data);
    }

    public function upload()
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
            $response->message = "Akses tidak diizinkan";
            return json_encode($response);
        }
        if (!(grantUploadPangkat($user->data->id))) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Akses tidak diizinkan";
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
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/opk/upload/ptk/pangkatkgb/upload');
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
            '_file' => [
                'rules' => 'uploaded[_file]|max_size[_file,10240]|mime_in[_file,application/vnd.ms-excel,application/msexcel,application/x-msexcel,application/x-ms-excel,application/x-excel,application/x-dos_ms_excel,application/xls,application/x-xls,application/excel,application/download,application/vnd.ms-office,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/x-zip]',
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
            $response->message =
                // $this->validator->getError('tw');
                $this->validator->getError('_file');
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

            if (!(grantUploadPangkat($user->data->id))) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Akses tidak diizinkan";
                return json_encode($response);
            }

            $lampiran = $this->request->getFile('_file');
            // $mimeType = $lampiran->getMimeType();

            // var_dump($mimeType);
            // die;
            $extension = $lampiran->getClientExtension();
            $filesNamelampiran = $lampiran->getName();
            $newNamelampiran = _create_name_file_import($filesNamelampiran);
            $fileLocation = $lampiran->getTempName();

            if ('xls' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            $spreadsheet = $reader->load($fileLocation);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $total_line = (count($sheet) > 0) ? count($sheet) - 4 : 0;

            $dataImport = [];

            $nuptkImport = [];

            // var_dump($ketSimtunDokumen);
            // die;

            unset($sheet[0]);
            unset($sheet[1]);
            unset($sheet[2]);
            // unset($sheet[3]);
            // unset($sheet[4]);

            foreach ($sheet as $key => $data) {

                if ($data[2] == "" || strlen($data[2]) < 16) {
                    // if($data[1] == "") {
                    continue;
                }

                $dataInsert = [
                    'nuptk' => str_replace("'", "", $data[2]),
                    'nama' => $data[1],
                    'nik' => $data[3],
                    'npsn' => $data[4],
                    'jam_mengajar_perminggu' => $data[5],
                    'npsn_noninduk' => $data[6],
                    'jam_mengajar_perminggu_noninduk' => $data[7],
                    'jam_mengajar_perminggu_noninduk' => $data[7],
                    'nomor_sk_pangkat' => $data[8],
                    'pangkat_golongan' => $data[9],
                    'tgl_sk_pangkat' => $data[10],
                    'tmt_pangkat' => $data[11],
                    'masa_kerja_tahun' => $data[12],
                    'masa_kerja_bulan' => $data[13],
                    'sk_kgb' => $data[14],
                    'pangkat_golongan_kgb' => $data[15],
                    'tgl_sk_kgb' => $data[16],
                    'tmt_sk_kgb' => $data[17],
                    'masa_kerja_tahun_kgb' => $data[18],
                    'masa_kerja_bulan_kgb' => $data[19],
                ];

                $dataInsert['data_ptk'] = $this->_db->table('_ptk_tb a')
                    ->select("a.id_ptk, a.nuptk, a.nip, a.nama, a.pangkat_golongan, a.nomor_sk_pangkat, a.tgl_sk_pangkat, a.tmt_pangkat, a.masa_kerja_tahun, a.masa_kerja_bulan, a.pangkat_golongan_kgb, a.sk_kgb, a.tgl_sk_kgb, a.tmt_sk_kgb, a.masa_kerja_tahun_kgb, a.masa_kerja_bulan_kgb, a.jam_mengajar_perminggu, a.mengajar_lain_satmikal")
                    ->where('a.nuptk', str_replace("'", "", $data[2]))
                    ->get()->getRowObject();

                $dataImport[] = $dataInsert;
                $nuptkImport[] = str_replace("'", "", $data[2]);
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
                'user_id' => $user->data->id,
                'jumlah' => $total_line,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $dir = FCPATH . "upload/ptk-pangkat-kgb";
            $field_db = 'filename';
            $table_db = 'tb_ptk_upload_pangkat_kgb';

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
                $response->data = view('situgu/opk/upload/ptk/pangkatkgb/verifi-upload', $x);
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
        $datas = json_decode(file_get_contents(FCPATH . "upload/ptk-pangkat-kgb/$id.json"), true);

        // var_dump($datas);
        // die;
        $result = [];
        if (isset($datas['data']) && count($datas['data']) > 0) {
            $result['total'] = count($datas['data']);
            $response = [];
            $response_aksi = [];
            $lolos = 0;
            $gagal = 0;
            $belumusul = 0;
            foreach ($datas['data'] as $key => $v) {
                $item = [];
                // $tgl_lahir = explode("-", $v['tgl_lahir']);
                // $tgl_lhr = $tgl_lahir[2] . $tgl_lahir[1] . $tgl_lahir[0];
                if ($v['data_ptk'] == NULL || $v['data_ptk'] == "") {
                    $item['number'] = $key + 1;
                    $item['nama_up'] = $v['nama'];
                    $item['nuptk_up'] = $v['nuptk'];
                    $item['pangkat_golongan_up'] = $v['pangkat_golongan'];
                    $item['nomor_sk_pangkat_up'] = $v['nomor_sk_pangkat'];
                    $item['tgl_sk_pangkat_up'] = $v['tgl_sk_pangkat'];
                    $item['tmt_pangkat_up'] = $v['tmt_pangkat'];
                    $item['masa_kerja_tahun_up'] = $v['masa_kerja_tahun'];
                    $item['masa_kerja_bulan_up'] = $v['masa_kerja_bulan'];
                    $item['pangkat_golongan_kgb_up'] = $v['pangkat_golongan_kgb'];
                    $item['sk_kgb_up'] = $v['sk_kgb'];
                    $item['tgl_sk_kgb_up'] = $v['tgl_sk_kgb'];
                    $item['tmt_sk_kgb_up'] = $v['tmt_sk_kgb'];
                    $item['masa_kerja_tahun_kgb_up'] = $v['masa_kerja_tahun_kgb'];
                    $item['masa_kerja_bulan_kgb_up'] = $v['masa_kerja_bulan_kgb'];
                    $item['jam_mengajar_perminggu_up'] = $v['jam_mengajar_perminggu'];
                    $item['mengajar_lain_satmikal_up'] = $v['npsn_noninduk'];
                    $item['nama'] = "";
                    $item['nuptk'] = "";
                    $item['pangkat_golongan'] = "";
                    $item['nomor_sk_pangkat'] = "";
                    $item['keterangan'] = "PTK tidak ditemukan";
                    $item['aksi'] = "Aksi";
                    $item['status'] = "table-info";
                    $item['id_ptk'] = "";
                    $item['sort'] = "99";
                    $belumusul += 1;
                } else {
                    $item['number'] = $key + 1;
                    $item['nama_up'] = $v['nama'];
                    $item['nuptk_up'] = $v['nuptk'];
                    $item['pangkat_golongan_up'] = $v['pangkat_golongan'];
                    $item['nomor_sk_pangkat_up'] = $v['nomor_sk_pangkat'];
                    $item['tgl_sk_pangkat_up'] = $v['tgl_sk_pangkat'];
                    $item['tmt_pangkat_up'] = $v['tmt_pangkat'];
                    $item['masa_kerja_tahun_up'] = $v['masa_kerja_tahun'];
                    $item['masa_kerja_bulan_up'] = $v['masa_kerja_bulan'];
                    $item['pangkat_golongan_kgb_up'] = $v['pangkat_golongan_kgb'];
                    $item['sk_kgb_up'] = $v['sk_kgb'];
                    $item['tgl_sk_kgb_up'] = $v['tgl_sk_kgb'];
                    $item['tmt_sk_kgb_up'] = $v['tmt_sk_kgb'];
                    $item['masa_kerja_tahun_kgb_up'] = $v['masa_kerja_tahun_kgb'];
                    $item['masa_kerja_bulan_kgb_up'] = $v['masa_kerja_bulan_kgb'];
                    $item['jam_mengajar_perminggu_up'] = $v['jam_mengajar_perminggu'];
                    $item['mengajar_lain_satmikal_up'] = $v['npsn_noninduk'];
                    $item['nama'] = $v['data_ptk']['nama'];
                    $item['nuptk'] = $v['data_ptk']['nuptk'];
                    $item['pangkat_golongan'] = $v['data_ptk']['pangkat_golongan'];
                    $item['nomor_sk_pangkat'] = $v['data_ptk']['nomor_sk_pangkat'];
                    $item['keterangan'] = "PTK Ditemukan";
                    $item['aksi'] = "Aksi";
                    $item['status'] = "table-success";
                    $item['id_ptk'] = $v['data_ptk']['id_ptk'];
                    $item['sort'] = "88";
                    $lolos += 1;

                    $response_aksi[] = $item;
                }

                $response[] = $item;
            }
            usort($response, function ($a, $b) {
                return $a['sort'] - $b['sort'];
            });

            $result['lolos'] = $lolos;
            $result['gagal'] = $gagal;
            $result['belumusul'] = $belumusul;
            $result['data'] = $response;
            $result['aksi'] = $response_aksi;
        } else {
            $result['total'] = 0;
            $result['lolos'] = 0;
            $result['gagal'] = 0;
            $result['belumusul'] = 0;
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

        $rules = [
            'id_ptk' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
            'status' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Status tidak boleh kosong. ',
                ]
            ],
            'nuptk' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nuptk tidak boleh kosong. ',
                ]
            ],
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
            'pangkat_golongan_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Pangkat Golongan tidak boleh kosong. ',
                ]
            ],
            'nomor_sk_pangkat_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'No SK tidak boleh kosong. ',
                ]
            ],
            'tgl_sk_pangkat_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TGL SK up tidak boleh kosong. ',
                ]
            ],
            'tmt_pangkat_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TMT SK up tidak boleh kosong. ',
                ]
            ],
            'masa_kerja_tahun_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'MKT up tidak boleh kosong. ',
                ]
            ],
            'masa_kerja_bulan_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'MKB up tidak boleh kosong. ',
                ]
            ],
            'jam_mengajar_perminggu_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'JJM tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id_ptk')
                . $this->validator->getError('status')
                . $this->validator->getError('nama_up')
                . $this->validator->getError('nuptk_up')
                . $this->validator->getError('pangkat_golongan_up')
                . $this->validator->getError('tgl_sk_pangkat_up')
                . $this->validator->getError('tmt_pangkat_up')
                . $this->validator->getError('masa_kerja_tahun_up')
                . $this->validator->getError('masa_kerja_bulan_up')
                . $this->validator->getError('jam_mengajar_perminggu_up');
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

            if (!(grantUploadPangkat($user->data->id))) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Akses tidak diizinkan";
                return json_encode($response);
            }

            $status = htmlspecialchars($this->request->getVar('status'), true);
            $id_ptk = htmlspecialchars($this->request->getVar('id_ptk'), true);
            $nuptk_up = htmlspecialchars($this->request->getVar('nuptk'), true);
            $nama_up = htmlspecialchars($this->request->getVar('nama'), true);
            $pangkat_golongan_up = htmlspecialchars($this->request->getVar('pangkat_golongan_up'), true);
            $nomor_sk_pangkat_up = htmlspecialchars($this->request->getVar('nomor_sk_pangkat_up'), true);
            $tgl_sk_pangkat_up = htmlspecialchars($this->request->getVar('tgl_sk_pangkat_up'), true);
            $tmt_pangkat_up = htmlspecialchars($this->request->getVar('tmt_pangkat_up'), true);
            $masa_kerja_tahun_up = htmlspecialchars($this->request->getVar('masa_kerja_tahun_up'), true);
            $masa_kerja_bulan_up = htmlspecialchars($this->request->getVar('masa_kerja_bulan_up'), true);
            $jam_mengajar_perminggu_up = htmlspecialchars($this->request->getVar('jam_mengajar_perminggu_up'), true);
            $pangkat_golongan_kgb_up = htmlspecialchars($this->request->getVar('pangkat_golongan_kgb_up'), true);
            $sk_kgb_up = htmlspecialchars($this->request->getVar('sk_kgb_up'), true);
            $tgl_sk_kgb_up = htmlspecialchars($this->request->getVar('tgl_sk_kgb_up'), true);
            $tmt_sk_kgb_up = htmlspecialchars($this->request->getVar('tmt_sk_kgb_up'), true);
            $masa_kerja_tahun_kgb_up = htmlspecialchars($this->request->getVar('masa_kerja_tahun_kgb_up'), true);
            $masa_kerja_bulan_kgb_up = htmlspecialchars($this->request->getVar('masa_kerja_bulan_kgb_up'), true);
            $jam_mengajar_perminggu_up = htmlspecialchars($this->request->getVar('jam_mengajar_perminggu_up'), true);

            $current = $this->_db->table('_ptk_tb a')
                ->select("a.id_ptk, a.nuptk, a.nip, a.nama, a.pangkat_golongan, a.nomor_sk_pangkat, a.tgl_sk_pangkat, a.tmt_pangkat, a.masa_kerja_tahun, a.masa_kerja_bulan, a.pangkat_golongan_kgb, a.sk_kgb, a.tgl_sk_kgb, a.tmt_sk_kgb, a.masa_kerja_tahun_kgb, a.masa_kerja_bulan_kgb, a.jam_mengajar_perminggu, a.mengajar_lain_satmikal")
                ->where('a.id_ptk', $id_ptk)
                ->get()->getRowObject();

            if ($current) {

                $dataUpdate = [
                    'pangkat_golongan' => $pangkat_golongan_up,
                    'nomor_sk_pangkat' => $nomor_sk_pangkat_up,
                    'tgl_sk_pangkat' => $tgl_sk_pangkat_up,
                    'tmt_pangkat' => $tmt_pangkat_up,
                    'masa_kerja_tahun' => $masa_kerja_tahun_up,
                    'masa_kerja_bulan' => $masa_kerja_bulan_up,
                    'jam_mengajar_perminggu' => $jam_mengajar_perminggu_up,
                    'pangkat_golongan_kgb' => $jam_mengajar_perminggu_up,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if (!($pangkat_golongan_kgb_up == "" || $pangkat_golongan_kgb_up == NULL)) {
                    $dataUpdate['pangkat_golongan_kgb'] = $pangkat_golongan_kgb_up;
                }

                if (!($sk_kgb_up == "" || $sk_kgb_up == NULL)) {
                    $dataUpdate['sk_kgb'] = $sk_kgb_up;
                }

                if (!($tgl_sk_kgb_up == "" || $tgl_sk_kgb_up == NULL)) {
                    $dataUpdate['tgl_sk_kgb'] = $tgl_sk_kgb_up;
                }

                if (!($tmt_sk_kgb_up == "" || $tmt_sk_kgb_up == NULL)) {
                    $dataUpdate['tmt_sk_kgb'] = $tmt_sk_kgb_up;
                }

                if (!($masa_kerja_tahun_kgb_up == "" || $masa_kerja_tahun_kgb_up == NULL)) {
                    $dataUpdate['masa_kerja_tahun_kgb'] = $masa_kerja_tahun_kgb_up;
                }

                if (!($masa_kerja_bulan_kgb_up == "" || $masa_kerja_bulan_kgb_up == NULL)) {
                    $dataUpdate['masa_kerja_bulan_kgb'] = $masa_kerja_bulan_kgb_up;
                }

                $this->_db->transBegin();

                if ($status == "table-success") {
                    $this->_db->table('_ptk_tb')->where('id', $current->id)->update(
                        $dataUpdate
                    );
                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->transCommit();

                        // $dataNotif = [
                        //     "SKTP Telah Terbit", "Usulan " . $ptk->kode_usulan . " telah Terbit dengan No SK: " . $no_sktp . " No Urut: " . $no_urut, "success", $user->data->id, $ptk->id_ptk, base_url('situgu/ptk/us/tpg/skterbit')
                        // ];

                        // try {
                        //     $notifLib = new NotificationLib();
                        //     $notifLib->create("Pembaharuan No Rekening", "No Rekening Anda " . $current->no_rekening . " telah diperbaharui ke no rekening $no_rekening_up, silahkan mengecek data pembaharuan.", "success", $user->data->id, $current->id, base_url('situgu/ptk/masterdata/dapodik'));
                        //     $getChatIdName = getChatIdTelegramPTKName($current->id);
                        //     if ($getChatIdName) {
                        //         // $admin = $user->data;
                        //         $tokenTele = "6504819187:AAEtykjIx2Gjd229nUgDHRlwJ5xGNTMjO0A";
                        //         $message = "Hallo <b>$getChatIdName->nama ($getChatIdName->nuptk)</b>....!!!\n______________________________________________________\n\n<b>UPDATA DATA PEMBAHARUAN</b> pada <b>SI-TUGU</b> dengan No Rekening : \n<b>$current->no_rekening</b>\ntelah diperbaharui ke no rekening $no_rekening_up, silahkan mengecek data pembaharuan.\n\n\nPesan otomatis dari <b>SI-TUGU Kab. Lampung Tengah</b>\n_________________________________________________";
                        //         try {

                        //             $dataReq = [
                        //                 'chat_id' => $getChatIdName->chat_id_telegram,
                        //                 "parse_mode" => "HTML",
                        //                 'text' => $message,
                        //             ];

                        //             $ch = curl_init("https://api.telegram.org/bot$tokenTele/sendMessage");
                        //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        //             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataReq));
                        //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        //             curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        //                 'Content-Type: application/json'
                        //             ));
                        //             curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                        //             curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

                        //             $server_output = curl_exec($ch);
                        //             curl_close($ch);

                        //             // var_dump($server_output);
                        //         } catch (\Throwable $th) {
                        //             // var_dump($th);
                        //         }
                        //     }
                        // } catch (\Throwable $th) {
                        //     //throw $th;
                        // }
                        $response = new \stdClass;
                        $response->status = 200;
                        $response->message = "Data berhasil disimpan.";
                        // $response->suce = $dataNotif;
                        return json_encode($response);
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal mengupdate data ptk.";
                        return json_encode($response);
                    }
                } else {
                    // $this->_db->table('_tb_usulan_detail_tpg')->where('id', $current->id_usulan)->update(['status_usulan' => 4, 'updated_at' => date('Y-m-d H:i:s'), 'date_matching' => date('Y-m-d H:i:s'), 'admin_matching' => $user->data->id, 'keterangan_reject' => $keterangan]);
                    // if ($this->_db->affectedRows() > 0) {
                    //     $this->_db->transCommit();
                    //     try {
                    //         $notifLib = new NotificationLib();
                    //         $notifLib->create("Gagal Matching Simtun", "Usulan " . $current->kode_usulan . " gagal untuk lolos matching simtun dengan keterangan: " . $keterangan, "danger", $user->data->id, $current->id_ptk, base_url('situgu/ptk/us/tpg/siapsk'));
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //     }
                    //     $response = new \stdClass;
                    //     $response->status = 200;
                    //     $response->message = "Data berhasil disimpan.";
                    //     return json_encode($response);
                    // } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal menyimpan data ptk.";
                    return json_encode($response);
                    // }
                }
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
            'filename' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Filename tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('filename');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $filename = htmlspecialchars($this->request->getVar('filename'), true);

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

            $current = $this->_db->table('tb_ptk_upload_pangkat_kgb')
                ->where('id', $id)
                ->get()->getRowObject();

            if ($current) {

                $this->_db->transBegin();
                try {
                    $this->_db->table('tb_ptk_upload_pangkat_kgb')->where('id', $current->id)->delete();
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->error = var_dump($th);
                    $response->message = "Data Upload Pangkat KGB gagal dihapus.";
                    return json_encode($response);
                }

                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    try {
                        $file = $current->filename;
                        unlink(FCPATH . "upload/ptk-pangkat-kgb/$file.json");
                        unlink(FCPATH . "upload/ptk-pangkat-kgb/$file");
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Data Upload Pangkat KGB berhasil dihapus.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Data Upload Pangkat KGB gagal dihapus.";
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
}
