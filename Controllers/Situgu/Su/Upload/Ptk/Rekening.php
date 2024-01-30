<?php

namespace App\Controllers\Situgu\Su\Upload\Ptk;

use App\Controllers\BaseController;
use App\Models\Situgu\Su\Ptk\Upload\RekeningModel;
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

class Rekening extends BaseController
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
        $datamodel = new RekeningModel($request);

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
            "recordsTotal" => $datamodel->count_all(),
            "recordsFiltered" => $datamodel->count_filtered(),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        return redirect()->to(base_url('situgu/su/upload/ptk/rekening/data'));
    }

    public function data()
    {
        $data['title'] = 'DATA REKENING PTK';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }
        $id = $this->_helpLib->getPtkId($user->data->id);
        $data['user'] = $user->data;
        return view('situgu/su/upload/ptk/rekening/index', $data);
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
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/su/upload/ptk/rekening/upload');
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
            unset($sheet[3]);
            // unset($sheet[4]);

            foreach ($sheet as $key => $data) {

                if ($data[1] == "" || strlen($data[1]) < 5) {
                    // if($data[1] == "") {
                    continue;
                }

                $dataInsert = [
                    'nuptk' => str_replace("'", "", $data[1]),
                    'nama' => $data[2],
                    'status' => $data[3],
                    'nip' => $data[6],
                    'no_rekening' => $data[8],
                    'cabang_bank' => $data[9],
                ];

                $dataInsert['data_ptk'] = $this->_db->table('_ptk_tb a')
                    ->select("a.id_ptk, a.nuptk, a.nip, a.nama, a.no_rekening, a.cabang_bank")
                    ->where('a.nuptk', str_replace("'", "", $data[1]))
                    ->get()->getRowObject();

                $dataImport[] = $dataInsert;
                $nuptkImport[] = str_replace("'", "", $data[1]);
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
                'jumlah' => $total_line,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $dir = FCPATH . "upload/ptk-rekening";
            $field_db = 'filename';
            $table_db = 'tb_ptk_rekening';

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
                $response->data = view('situgu/su/upload/ptk/rekening/verifi-upload', $x);
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
        $datas = json_decode(file_get_contents(FCPATH . "upload/ptk-rekening/$id.json"), true);

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
                    $item['no_rekening_up'] = $v['no_rekening'];
                    $item['cabang_bank_up'] = $v['cabang_bank'];
                    $item['nama'] = "";
                    $item['nuptk'] = "";
                    $item['no_rekening'] = "";
                    $item['cabang_bank'] = "";
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
                    $item['no_rekening_up'] = $v['no_rekening'];
                    $item['cabang_bank_up'] = $v['cabang_bank'];
                    $item['nama'] = $v['data_ptk']['nama'];
                    $item['nuptk'] = $v['data_ptk']['nuptk'];
                    $item['no_rekening'] = $v['data_ptk']['no_rekening'];
                    $item['cabang_bank'] = $v['data_ptk']['cabang_bank'];
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
            'cabang_bank' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Cabang bank tidak boleh kosong. ',
                ]
            ],
            'no_rekening' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'No Rekening tidak boleh kosong. ',
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
            'cabang_bank_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Cabang bank up tidak boleh kosong. ',
                ]
            ],
            'no_rekening_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'No Rekening up tidak boleh kosong. ',
                ]
            ],
            'nuptk_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nuptk up tidak boleh kosong. ',
                ]
            ],
            'nama_up' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama up tidak boleh kosong. ',
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
                . $this->validator->getError('cabang_bank_up')
                . $this->validator->getError('no_rekening_up')
                . $this->validator->getError('nama')
                . $this->validator->getError('nuptk')
                . $this->validator->getError('cabang_bank')
                . $this->validator->getError('no_rekening');
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

            $status = htmlspecialchars($this->request->getVar('status'), true);
            $id_ptk = htmlspecialchars($this->request->getVar('id_ptk'), true);
            $nuptk_up = htmlspecialchars($this->request->getVar('nuptk_up'), true);
            $nama_up = htmlspecialchars($this->request->getVar('nama_up'), true);
            $no_rekening_up = htmlspecialchars($this->request->getVar('no_rekening_up'), true);
            $cabang_bank_up = htmlspecialchars($this->request->getVar('cabang_bank_up'), true);
            $nuptk = htmlspecialchars($this->request->getVar('nuptk'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $no_rekening = htmlspecialchars($this->request->getVar('no_rekening'), true);
            $cabang_bank = htmlspecialchars($this->request->getVar('cabang_bank'), true);

            $current = $this->_db->table('_ptk_tb a')
                ->select("a.id, a.id_ptk, a.no_rekening, a.cabang_bank")
                ->where('a.id_ptk', $id_ptk)
                ->get()->getRowObject();

            if ($current) {
                $this->_db->transBegin();

                if ($status == "table-success") {
                    $this->_db->table('_ptk_tb')->where('id', $current->id)->update(['no_rekening' => $no_rekening_up, 'cabang_bank' => $cabang_bank_up, 'updated_at' => date('Y-m-d H:i:s')]);
                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->transCommit();

                        // $dataNotif = [
                        //     "SKTP Telah Terbit", "Usulan " . $ptk->kode_usulan . " telah Terbit dengan No SK: " . $no_sktp . " No Urut: " . $no_urut, "success", $user->data->id, $ptk->id_ptk, base_url('situgu/ptk/us/tpg/skterbit')
                        // ];

                        try {
                            $notifLib = new NotificationLib();
                            $notifLib->create("Pembaharuan No Rekening", "No Rekening Anda " . $current->no_rekening . " telah diperbaharui ke no rekening $no_rekening_up, silahkan mengecek data pembaharuan.", "success", $user->data->id, $current->id, base_url('situgu/ptk/masterdata/dapodik'));
                            $getChatIdName = getChatIdTelegramPTKName($current->id);
                            if ($getChatIdName) {
                                // $admin = $user->data;
                                $tokenTele = "6504819187:AAEtykjIx2Gjd229nUgDHRlwJ5xGNTMjO0A";
                                $message = "Hallo <b>$getChatIdName->nama ($getChatIdName->nuptk)</b>....!!!\n______________________________________________________\n\n<b>UPDATA DATA PEMBAHARUAN</b> pada <b>SI-TUGU</b> dengan No Rekening : \n<b>$current->no_rekening</b>\ntelah diperbaharui ke no rekening $no_rekening_up, silahkan mengecek data pembaharuan.\n\n\nPesan otomatis dari <b>SI-TUGU Kab. Lampung Tengah</b>\n_________________________________________________";
                                try {

                                    $dataReq = [
                                        'chat_id' => $getChatIdName->chat_id_telegram,
                                        "parse_mode" => "HTML",
                                        'text' => $message,
                                    ];

                                    $ch = curl_init("https://api.telegram.org/bot$tokenTele/sendMessage");
                                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataReq));
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                        'Content-Type: application/json'
                                    ));
                                    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

                                    $server_output = curl_exec($ch);
                                    curl_close($ch);

                                    // var_dump($server_output);
                                } catch (\Throwable $th) {
                                    // var_dump($th);
                                }
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
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

            $current = $this->_db->table('tb_matching_prosestransfer')
                ->where('id', $id)
                ->get()->getRowObject();

            if ($current) {

                $this->_db->transBegin();
                try {
                    $this->_db->table('tb_matching_prosestransfer')->where('id', $current->id)->delete();
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->error = var_dump($th);
                    $response->message = "Data matching gagal dihapus.";
                    return json_encode($response);
                }

                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    try {
                        $file = $current->filename;
                        unlink(FCPATH . "upload/matching-prosestransfer/$file.json");
                        unlink(FCPATH . "upload/matching-prosestransfer/$file");
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Data matching berhasil dihapus.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Data matching gagal dihapus.";
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
