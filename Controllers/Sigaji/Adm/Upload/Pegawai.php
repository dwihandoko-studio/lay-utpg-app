<?php

namespace App\Controllers\Sigaji\Adm\Upload;

use App\Controllers\BaseController;
use App\Models\Sigaji\Adm\Upload\PegawaiModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Sigaji\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Situgu\NotificationLib;
use App\Libraries\Uuid;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Pegawai extends BaseController
{
    var $folderImage = 'masterdata';
    private $_db;
    private $model;
    private $_helpLib;

    function __construct()
    {
        helper(['text', 'file', 'form', 'session', 'array', 'imageurl', 'web', 'filesystem']);
        $this->_db      = \Config\Database::connect('sigaji');
        $this->_helpLib = new Helplib();
    }

    public function getAll()
    {
        $request = Services::request();
        $datamodel = new PegawaiModel($request);

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
            $row[] = 'Tahun ' . $list->tahun . ' - Bulan.' . $list->bulan;
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
        return redirect()->to(base_url('sigaji/adm/upload/pegawai/data'));
    }

    public function data()
    {
        $data['title'] = 'UPLOAD DATA MASTERDATA PEGAWAI';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }
        // $id = $this->_helpLib->getPtkId($user->data->id);
        $data['user'] = $user->data;
        $data['tw'] = $this->_db->table('_ref_tahun_bulan')->where('is_current', 1)->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getRowObject();
        $data['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();
        return view('sigaji/adm/upload/masterdata/pegawai/index', $data);
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
            $data['tw'] = $this->_db->table('_ref_tahun_bulan')->where('is_current', 1)->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getRowObject();
            $data['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('sigaji/adm/upload/masterdata/pegawai/upload', $data);
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
            $newNamelampiran = _create_name_file_import_pegawai($filesNamelampiran);
            $fileLocation = $lampiran->getTempName();

            if ('xls' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            $spreadsheet = $reader->load($fileLocation);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $total_line = (count($sheet) > 0) ? count($sheet) - 1 : 0;

            // $dataImport = [];

            // $nuptkImport = [];
            // unset($sheet[0]);

            // foreach ($sheet as $key => $data) {

            //     if ($data[0] == "" || strlen($data[0]) < 5) {
            //         continue;
            //     }

            //     $nip = $data[0];
            //     $nip = str_replace("'", "", $nip);
            //     $nip = str_replace("\u200c", "", $nip);

            //     $nama = $data[1];
            //     $nama = str_replace("'", "", $nama);
            //     $nama = str_replace("\u200c", "", $nama);

            //     $nik = $data[2];
            //     $nik = str_replace("'", "", $nik);
            //     $nik = str_replace("\u200c", "", $nik);

            //     $dataInsert = [
            //         'nip' => $nip,
            //         'nama' => $nama,
            //         'nik' => $nik,
            //         'npwp' => $data[3],
            //         'tgl_lahir' => $data[4],
            //         'tipe_jabatan' => $data[5],
            //         'nama_jabatan' => $data[6],
            //         'eselon' => $data[7],
            //         'status_asn' => $data[8],
            //         'golongan' => $data[9],
            //         'mk_golongan' => $data[10],
            //         'alamat' => $data[11],
            //         'status_pernikahan' => $data[12],
            //         'jumlah_istri_suami' => $data[13],
            //         'jumlah_anak' => $data[14],
            //         'jumlah_tanggungan' => $data[15],
            //         'pasangan_pns' => $data[16],
            //         'nip_pasangan' => $data[17],
            //         'kode_bank' => $data[18],
            //         'nama_bank' => $data[19],
            //         'no_rekening_bank' => $data[20],
            //         'gaji_pokok' => $data[21],
            //         'perhitungan_suami_istri' => $data[22],
            //         'perhitungan_anak' => $data[23],
            //         'tunjangan_keluarga' => $data[24],
            //         'tunjangan_jabatan' => $data[25],
            //         'tunjangan_fungsional' => $data[26],
            //         'tunjangan_fungsional_umum' => $data[27],
            //         'tunjangan_beras' => $data[28],
            //         'tunjangan_pph' => $data[29],
            //         'pembulatan_gaji' => $data[30],
            //         'iuran_jaminan_kesehatan' => $data[31],
            //         'iuran_jaminan_kecelakaan_kerja' => $data[32],
            //         'iuran_jaminan_kematian' => $data[33],
            //         'iuran_simpanan_tapera' => $data[34],
            //         'iuran_pensiun' => $data[35],
            //         'tunjangan_khusus_papua' => $data[36],
            //         'tunjangan_jaminan_hari_tua' => $data[37],
            //         'potongan_iwp' => $data[38],
            //         'potongan_pph21' => $data[39],
            //         'potongan_zakat' => $data[40],
            //         'potongan_bulog' => $data[41],
            //         'jumlah_gaji_dan_tunjangan' => $data[42],
            //         'jumlah_potongan' => $data[43],
            //         'jumlah_ditransfer' => $data[44],
            //         'nama_kecamatan' => $data[45],
            //         'nama_instansi' => $data[46],
            //         'kode_instansi' => $data[47],
            //         'id_tahun_tw' => $tw,
            //     ];

            //     $dataInsert['data_pegawai'] = $this->_db->table('tb_pegawai_ a')
            //         ->select("a.id, a.nip, a.nik, a.nama, a.golongan, a.mk_golongan, a.status_asn, a.no_rekening_bank, a.nama_bank")
            //         ->where('a.nip', $nip)
            //         ->get()->getRowObject();

            //     $dataImport[] = $dataInsert;
            //     $nuptkImport[] = $nip;
            // }

            // $dataImports = [
            //     'total_line' => $total_line,
            //     'data' => $dataImport,
            // ];

            // if (count($nuptkImport) < 1) {
            //     $response = new \stdClass;
            //     $response->status = 400;
            //     $response->message = "Tidak ada data yang di import.";
            //     return json_encode($response);
            // }

            $data = [
                'id_tahun_tw' => $tw,
                'jumlah' => $total_line,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $dir = FCPATH . "uploads/api/pegawai";
            $field_db = 'filename';
            $table_db = 'tb_up_masterdata_pegawai';

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
                // if (write_file($dir . '/' . $newNamelampiran . '.json', json_encode($dataImports))) {
                // } else {
                //     $this->_db->transRollback();

                //     $response = new \stdClass;
                //     $response->status = 400;
                //     $response->error = "Gagal membuat file json";
                //     $response->message = "Gagal menyimpan data.";
                //     return json_encode($response);
                // }

                // createAktifitas($user->data->id, "Mengupload matching simtun $filesNamelampiran", "Mengupload Matching Simtun filesNamelampiran", "upload", $tw);
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $x['data'] = [];
                $x['id'] = $newNamelampiran;
                $x['tahun_bulan'] = $tw;
                $x['jumlah'] = $total_line;
                $response->data = view('sigaji/adm/upload/masterdata/pegawai/verifi-upload', $x);
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
        $datas = json_decode(file_get_contents(FCPATH . "uploads/api/pegawai/$id.json"), true);

        // var_dump($datas);
        // die;
        $result = [];
        if (isset($datas['data']) && count($datas['data']) > 0) {
            $result['total'] = count($datas['data']);
            $response = [];
            $response_aksi = [];
            $update = 0;
            $insert = 0;
            $sama = 0;
            foreach ($datas['data'] as $key => $v) {
                $item = [];
                $item['number'] = $key + 1;
                $item['nip'] = $v['nip'];
                $item['nama'] = $v['nama'];
                $item['nik'] = $v['nik'];
                $item['npwp'] = $v['npwp'];
                $item['tgl_lahir'] = $v['tgl_lahir'];
                $item['tipe_jabatan'] = $v['tipe_jabatan'];
                $item['nama_jabatan'] = $v['nama_jabatan'];
                $item['eselon'] = $v['eselon'];
                $item['status_asn'] = $v['status_asn'];
                $item['golongan'] = $v['golongan'];
                $item['mk_golongan'] = $v['mk_golongan'];
                $item['alamat'] = $v['alamat'];
                $item['status_pernikahan'] = $v['status_pernikahan'];
                $item['jumlah_istri_suami'] = $v['jumlah_istri_suami'];
                $item['jumlah_anak'] = $v['jumlah_anak'];
                $item['jumlah_tanggungan'] = $v['jumlah_tanggungan'];
                $item['pasangan_pns'] = $v['pasangan_pns'];
                $item['nip_pasangan'] = $v['nip_pasangan'];
                $item['kode_bank'] = $v['kode_bank'];
                $item['nama_bank'] = $v['nama_bank'];
                $item['no_rekening_bank'] = $v['no_rekening_bank'];
                $item['gaji_pokok'] = $v['gaji_pokok'];
                $item['perhitungan_suami_istri'] = $v['perhitungan_suami_istri'];
                $item['perhitungan_anak'] = $v['perhitungan_anak'];
                $item['tunjangan_keluarga'] = $v['tunjangan_keluarga'];
                $item['tunjangan_jabatan'] = $v['tunjangan_jabatan'];
                $item['tunjangan_fungsional'] = $v['tunjangan_fungsional'];
                $item['tunjangan_fungsional_umum'] = $v['tunjangan_fungsional_umum'];
                $item['tunjangan_beras'] = $v['tunjangan_beras'];
                $item['tunjangan_pph'] = $v['tunjangan_pph'];
                $item['pembulatan_gaji'] = $v['pembulatan_gaji'];
                $item['pembulatan_gaji'] = $v['pembulatan_gaji'];
                $item['iuran_jaminan_kesehatan'] = $v['iuran_jaminan_kesehatan'];
                $item['iuran_jaminan_kecelakaan_kerja'] = $v['iuran_jaminan_kecelakaan_kerja'];
                $item['iuran_jaminan_kematian'] = $v['iuran_jaminan_kematian'];
                $item['iuran_simpanan_tapera'] = $v['iuran_simpanan_tapera'];
                $item['iuran_pensiun'] = $v['iuran_pensiun'];
                $item['tunjangan_khusus_papua'] = $v['tunjangan_khusus_papua'];
                $item['tunjangan_jaminan_hari_tua'] = $v['tunjangan_jaminan_hari_tua'];
                $item['potongan_iwp'] = $v['potongan_iwp'];
                $item['potongan_pph21'] = $v['potongan_pph21'];
                $item['potongan_zakat'] = $v['potongan_zakat'];
                $item['potongan_bulog'] = $v['potongan_bulog'];
                $item['jumlah_gaji_dan_tunjangan'] = $v['jumlah_gaji_dan_tunjangan'];
                $item['jumlah_potongan'] = $v['jumlah_potongan'];
                $item['jumlah_ditransfer'] = $v['jumlah_ditransfer'];
                $item['kode_instansi'] = $v['kode_instansi'];
                $item['nama_instansi'] = $v['nama_instansi'];
                $item['nama_kecamatan'] = $v['nama_kecamatan'];
                $item['id_tahun_tw'] = $v['id_tahun_tw'];
                if ($v['data_pegawai'] == NULL || $v['data_pegawai'] == "") {
                    $item['aksi'] = "Aksi";
                    $item['status'] = "table-success";
                    $item['sort'] = "99";
                    $item['nip_db'] = "";
                    $item['nik_db'] = "";
                    $item['nama_db'] = "";
                    $item['golongan_db'] = "";
                    $item['mk_golongan_db'] = "";
                    $item['no_rekening_bank_db'] = "";
                    $response_aksi[] = $item;
                    $insert += 1;
                } else {

                    $nik = $v['nik'];
                    $nik = str_replace("'", "", $nik);
                    $nik = str_replace("\u200c", "", $nik);

                    $no_reg = $v['no_rekening_bank'];
                    $no_reg = str_replace("'", "", $no_reg);
                    $no_reg = str_replace("\u200c", "", $no_reg);

                    $nama = $v['nama'];
                    $nama = str_replace("'", " ", $nama);
                    $nama = str_replace("\u200c", " ", $nama);

                    $nikdb = $v['data_pegawai']['nik'];
                    $nikdb = str_replace("'", "", $nikdb);
                    $nikdb = str_replace("\u200c", "", $nikdb);

                    $no_reg_db = $v['data_pegawai']['no_rekening_bank'];
                    $no_reg_db = str_replace("'", "", $no_reg_db);
                    $no_reg_db = str_replace("\u200c", "", $no_reg_db);

                    $nama_db = $v['data_pegawai']['nama'];
                    $nama_db = str_replace("'", " ", $nama_db);
                    $nama_db = str_replace("\u200c", " ", $nama_db);

                    // if ($v['nama'] == $v['data_pegawai']['nama'] && $v['nik'] == $v['data_pegawai']['nik'] && $v['golongan'] == $v['data_pegawai']['golongan'] && $v['mk_golongan'] == $v['data_pegawai']['mk_golongan'] && $v['status_asn'] == $v['data_pegawai']['status_asn'] && $v['no_rekening_bank'] == $v['data_pegawai']['no_rekening_bank']) {
                    if ($nama === $nama_db && $nik === $nikdb && $no_reg === $no_reg_db && $v['golongan'] === $v['data_pegawai']['golongan'] && (int)$v['mk_golongan'] === (int)$v['data_pegawai']['mk_golongan'] && (int)$v['status_asn'] === (int)$v['data_pegawai']['status_asn']) {
                        $item['aksi'] = "Aksi";
                        $item['status'] = "table-info";
                        $item['sort'] = "88";
                        $sama += 1;
                    } else {
                        $item['aksi'] = "Aksi";
                        $item['status'] = "table-warning";
                        $item['sort'] = "99";
                        $item['nip_db'] = $v['data_pegawai']['nip'];
                        $item['nik_db'] = $v['data_pegawai']['nik'];
                        $item['nama_db'] = $v['data_pegawai']['nama'];
                        $item['golongan_db'] = $v['data_pegawai']['golongan'];
                        $item['mk_golongan_db'] = $v['data_pegawai']['mk_golongan'];
                        $item['no_rekening_bank_db'] = $v['data_pegawai']['no_rekening_bank'];
                        $response_aksi[] = $item;
                        $update += 1;
                    }
                }
                // }

                $response[] = $item;
            }
            usort($response, function ($a, $b) {
                return $a['sort'] - $b['sort'];
            });

            $result['update'] = $update;
            $result['insert'] = $insert;
            $result['sama'] = $sama;
            $result['data'] = $response;
            $result['aksi'] = $response_aksi;
        } else {
            $result['total'] = 0;
            $result['update'] = 0;
            $result['insert'] = 0;
            $result['sama'] = 0;
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
            'tahun_bulan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Tahun bulan tidak boleh kosong. ',
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
            $response->message =
                $this->validator->getError('filename')
                . $this->validator->getError('tahun_bulan');
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

            $tahun_bulan = htmlspecialchars($this->request->getVar('tahun_bulan'), true);
            $filename = htmlspecialchars($this->request->getVar('filename'), true);

            $apiLib = new Apilib();
            $result = $apiLib->uploadPegawaiGajiSipd($tahun_bulan, $filename);

            if ($result) {
                // var_dump($result);
                // die;
                if ($result->status == 200) {
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->resss = $result;
                    $response->message = "Import Data Pegawi dan Gaji SIPD Berhasil Dilakukan.";
                    return json_encode($response);
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->error = $result;
                    $response->message = "Gagal Import Data Pegawi dan Gaji SIPD.";
                    return json_encode($response);
                }
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal Import Data Pegawi dan Gaji SIPD";
                return json_encode($response);
            }

            // $nama_instansi = htmlspecialchars($this->request->getVar('nama_instansi'), true);
            // $nama_kecamatan = htmlspecialchars($this->request->getVar('nama_kecamatan'), true);
            // $kode_kecamatan = htmlspecialchars($this->request->getVar('kode_kecamatan'), true);
            // $nip = htmlspecialchars($this->request->getVar('nip'), true);
            // $nama = htmlspecialchars($this->request->getVar('nama'), true);
            // $nik = htmlspecialchars($this->request->getVar('nik'), true);
            // $npwp = htmlspecialchars($this->request->getVar('npwp'), true);
            // $tgl_lahir = htmlspecialchars($this->request->getVar('tgl_lahir'), true);
            // $tipe_jabatan = htmlspecialchars($this->request->getVar('tipe_jabatan'), true);
            // $nama_jabatan = htmlspecialchars($this->request->getVar('nama_jabatan'), true);
            // $eselon = htmlspecialchars($this->request->getVar('eselon'), true);
            // $status_asn = htmlspecialchars($this->request->getVar('status_asn'), true);
            // $golongan = htmlspecialchars($this->request->getVar('golongan'), true);
            // $mk_golongan = htmlspecialchars($this->request->getVar('mk_golongan'), true);
            // $alamat = htmlspecialchars($this->request->getVar('alamat'), true);
            // $status_pernikahan = htmlspecialchars($this->request->getVar('status_pernikahan'), true);
            // $jumlah_istri_suami = htmlspecialchars($this->request->getVar('jumlah_istri_suami'), true);
            // $jumlah_anak = htmlspecialchars($this->request->getVar('jumlah_anak'), true);
            // $jumlah_tanggungan = htmlspecialchars($this->request->getVar('jumlah_tanggungan'), true);
            // $pasangan_pns = htmlspecialchars($this->request->getVar('pasangan_pns'), true);
            // $nip_pasangan = htmlspecialchars($this->request->getVar('nip_pasangan'), true);
            // $kode_bank = htmlspecialchars($this->request->getVar('kode_bank'), true);
            // $nama_bank = htmlspecialchars($this->request->getVar('nama_bank'), true);
            // $no_rekening_bank = htmlspecialchars($this->request->getVar('no_rekening_bank'), true);
            // $gaji_pokok = htmlspecialchars($this->request->getVar('gaji_pokok'), true);
            // $perhitungan_suami_istri = htmlspecialchars($this->request->getVar('perhitungan_suami_istri'), true);
            // $perhitungan_anak = htmlspecialchars($this->request->getVar('perhitungan_anak'), true);
            // $tunjangan_keluarga = htmlspecialchars($this->request->getVar('tunjangan_keluarga'), true);
            // $tunjangan_jabatan = htmlspecialchars($this->request->getVar('tunjangan_jabatan'), true);
            // $tunjangan_fungsional = htmlspecialchars($this->request->getVar('tunjangan_fungsional'), true);
            // $tunjangan_fungsional_umum = htmlspecialchars($this->request->getVar('tunjangan_fungsional_umum'), true);
            // $tunjangan_beras = htmlspecialchars($this->request->getVar('tunjangan_beras'), true);
            // $tunjangan_pph = htmlspecialchars($this->request->getVar('tunjangan_pph'), true);
            // $pembulatan_gaji = htmlspecialchars($this->request->getVar('pembulatan_gaji'), true);
            // $iuran_jaminan_kesehatan = htmlspecialchars($this->request->getVar('iuran_jaminan_kesehatan'), true);
            // $iuran_jaminan_kecelakaan_kerja = htmlspecialchars($this->request->getVar('iuran_jaminan_kecelakaan_kerja'), true);
            // $iuran_jaminan_kematian = htmlspecialchars($this->request->getVar('iuran_jaminan_kematian'), true);
            // $iuran_simpanan_tapera = htmlspecialchars($this->request->getVar('iuran_simpanan_tapera'), true);
            // $iuran_pensiun = htmlspecialchars($this->request->getVar('iuran_pensiun'), true);
            // $tunjangan_khusus_papua = htmlspecialchars($this->request->getVar('tunjangan_khusus_papua'), true);
            // $tunjangan_jaminan_hari_tua = htmlspecialchars($this->request->getVar('tunjangan_jaminan_hari_tua'), true);
            // $potongan_iwp = htmlspecialchars($this->request->getVar('potongan_iwp'), true);
            // $potongan_pph21 = htmlspecialchars($this->request->getVar('potongan_pph21'), true);
            // $potongan_zakat = htmlspecialchars($this->request->getVar('potongan_zakat'), true);
            // $potongan_bulog = htmlspecialchars($this->request->getVar('potongan_bulog'), true);
            // $jumlah_gaji_dan_tunjangan = htmlspecialchars($this->request->getVar('jumlah_gaji_dan_tunjangan'), true);
            // $jumlah_potongan = htmlspecialchars($this->request->getVar('jumlah_potongan'), true);
            // $jumlah_ditransfer = htmlspecialchars($this->request->getVar('jumlah_ditransfer'), true);

            // $status = htmlspecialchars($this->request->getVar('status'), true);

            // // $current = $this->_db->table('_tb_usulan_detail_tpg a')
            // //     ->select("a.id as id_usulan, a.us_pang_golongan, a.us_pang_mk_tahun, a.us_gaji_pokok, a.date_approve, a.kode_usulan, a.id_ptk, a.id_tahun_tw, a.status_usulan, a.date_approve_sptjm, b.nama, b.nik, b.nuptk, b.jenis_ptk, b.kecamatan, e.cuti as lampiran_cuti, e.pensiun as lampiran_pensiun, e.kematian as lampiran_kematian")
            // //     ->join('_ptk_tb b', 'a.id_ptk = b.id')
            // //     ->join('_upload_data_attribut e', 'a.id_ptk = e.id_ptk AND (a.id_tahun_tw = e.id_tahun_tw)')
            // //     ->where('a.id', $id_usulan)
            // //     ->where('a.status_usulan', 2)
            // //     ->where('a.id_tahun_tw', $tw)
            // //     ->get()->getRowObject();

            // // if ($current) {
            // $this->_db->transBegin();

            // if ($status == "table-success") {
            //     $uuidLib = new Uuid();
            //     try {
            //         $y = substr($nip, 0, 4);
            //         $m = substr($nip, 4, 2);
            //         $d = substr($nip, 6, 2);

            //         // Menggabungkan menjadi format tanggal yang diinginkan
            //         $ttl = $y . "-" . $m . "-" . $d;

            //         $dataInsert = [
            //             'id' => $uuidLib->v4(),
            //             'nip' => $nip,
            //             'nik' => $nik,
            //             'nama' => $nama,
            //             'npwp' => $npwp,
            //             'tgl_lahir' => $ttl,
            //             'tipe_jabatan' => $tipe_jabatan,
            //             'nama_jabatan' => $nama_jabatan,
            //             'eselon' => $eselon,
            //             'status_asn' => $status_asn,
            //             'golongan' => $golongan,
            //             'mk_golongan' => $mk_golongan,
            //             'alamat' => $alamat,
            //             'nama_kecamatan' => $nama_kecamatan == "" ? NULL : $nama_kecamatan,
            //             'kode_kecamatan' => $kode_kecamatan == "" ? NULL : $kode_kecamatan,
            //             'kode_instansi' => $kode_instansi == "" ? NULL : $kode_instansi,
            //             'nama_instansi' => $nama_instansi == "" ? NULL : $nama_instansi,
            //             'status_kawin' => $status_pernikahan,
            //             'jumlah_istri' => $jumlah_istri_suami,
            //             'jumlah_anak' => $jumlah_anak,
            //             'jumlah_tanggungan' => $jumlah_tanggungan,
            //             'pasangan_pns' => $pasangan_pns,
            //             'nip_pasangan' => $nip_pasangan,
            //             'kode_bank' => $kode_bank,
            //             'nama_bank' => $nama_bank,
            //             'no_rekening_bank' => $no_rekening_bank,
            //             'status_active' => 1,
            //             'created_at' => date('Y-m-d H:i:s'),
            //         ];

            //         $this->_db->table('tb_pegawai_')->insert($dataInsert);
            //         if ($this->_db->affectedRows() > 0) {
            //             $this->_db->table('tb_gaji_sipd')->insert([
            //                 'id' => $uuidLib->v4(),
            //                 'id_pegawai' => $dataInsert['id'],
            //                 'tahun' => $tahun_bulan,
            //                 'gaji_pokok' => $gaji_pokok,
            //                 'perhitungan_suami_istri' => $perhitungan_suami_istri,
            //                 'perhitungan_anak' => $perhitungan_anak,
            //                 'tunjangan_keluarga' => $tunjangan_keluarga,
            //                 'tunjangan_jabatan' => $tunjangan_jabatan,
            //                 'tunjangan_fungsional' => $tunjangan_fungsional,
            //                 'tunjangan_fungsional_umum' => $tunjangan_fungsional_umum,
            //                 'tunjangan_beras' => $tunjangan_beras,
            //                 'tunjangan_pph' => $tunjangan_pph,
            //                 'pembulatan_gaji' => $pembulatan_gaji,
            //                 'iuran_jaminan_kesehatan' => $iuran_jaminan_kesehatan,
            //                 'iuran_jaminan_kecelakaan_kerja' => $iuran_jaminan_kecelakaan_kerja,
            //                 'iuran_jaminan_kematian' => $iuran_jaminan_kematian,
            //                 'iuran_simpanan_tapera' => $iuran_simpanan_tapera,
            //                 'iuran_pensiun' => $iuran_pensiun,
            //                 'tunjangan_jaminan_hari_tua' => $tunjangan_jaminan_hari_tua,
            //                 'potongan_iwp' => $potongan_iwp,
            //                 'potongan_pph_21' => $potongan_pph21,
            //                 'zakat' => $potongan_zakat,
            //                 'bulog' => $potongan_bulog,
            //                 'jumlah_gaji_dan_tunjangan' => $jumlah_gaji_dan_tunjangan,
            //                 'jumlah_potongan' => $jumlah_potongan,
            //                 'jumlah_transfer' => $jumlah_ditransfer,
            //                 'created_at' => $dataInsert['created_at'],
            //             ]);
            //             if ($this->_db->affectedRows() > 0) {

            //                 $this->_db->transCommit();

            //                 // try {
            //                 //     $notifLib = new NotificationLib();
            //                 //     $notifLib->create("Lolos Matching Simtun", "Usulan " . $ptk->kode_usulan . " telah lolos matching simtun.", "success", $user->data->id, $ptk->id_ptk, base_url('situgu/ptk/us/tpg/siapsk'));
            //                 // } catch (\Throwable $th) {
            //                 //     //throw $th;
            //                 // }
            //                 $response = new \stdClass;
            //                 $response->status = 200;
            //                 $response->message = "Data berhasil disimpan.";
            //                 return json_encode($response);
            //             } else {
            //                 $this->_db->transRollback();
            //                 $response = new \stdClass;
            //                 $response->status = 400;
            //                 $response->message = "Gagal menyimpan data import pada gaji sipd.";
            //                 return json_encode($response);
            //             }
            //         } else {
            //             $this->_db->transRollback();
            //             $response = new \stdClass;
            //             $response->status = 400;
            //             $response->message = "Gagal menyimpan data import pada data pegawai.";
            //             return json_encode($response);
            //         }
            //     } catch (\Throwable $th) {
            //         $this->_db->transRollback();
            //         $response = new \stdClass;
            //         $response->status = 200;
            //         $response->error = var_dump($th);
            //         $response->message = "Gagal menyimpan data import. kesalahan";
            //         return json_encode($response);
            //     }
            // } else if ($status == "table-warning") {
            //     $dataOldPegawai = $this->_db->table('tb_pegawai_')->select("id, nama_kecamatan, kode_kecamatan, kode_instansi, nama_instansi")->where('nip', $nip)->get()->getRowObject();
            //     $uuidLib = new Uuid();
            //     try {
            //         // $y = substr($nip, 0, 4);
            //         // $m = substr($nip, 4, 2);
            //         // $d = substr($nip, 6, 2);

            //         // // Menggabungkan menjadi format tanggal yang diinginkan
            //         // $ttl = $y . "-" . $m . "-" . $d;

            //         $dataUpdate = [
            //             'nik' => $nik,
            //             'nama' => $nama,
            //             'npwp' => $npwp,
            //             'tipe_jabatan' => $tipe_jabatan,
            //             'nama_jabatan' => $nama_jabatan,
            //             'eselon' => $eselon,
            //             'status_asn' => $status_asn,
            //             'golongan' => $golongan,
            //             'mk_golongan' => $mk_golongan,
            //             'alamat' => $alamat,
            //             'nama_kecamatan' => $nama_kecamatan == "" ? $dataOldPegawai->nama_kecamatan : $nama_kecamatan,
            //             'kode_kecamatan' => $kode_kecamatan == "" ? $dataOldPegawai->kode_kecamatan : $kode_kecamatan,
            //             'kode_instansi' => $kode_instansi == "" ? $dataOldPegawai->kode_instansi : $kode_instansi,
            //             'nama_instansi' => $nama_instansi == "" ? $dataOldPegawai->nama_instansi : $nama_instansi,
            //             'status_kawin' => $status_pernikahan,
            //             'jumlah_istri' => $jumlah_istri_suami,
            //             'jumlah_anak' => $jumlah_anak,
            //             'jumlah_tanggungan' => $jumlah_tanggungan,
            //             'pasangan_pns' => $pasangan_pns,
            //             'nip_pasangan' => $nip_pasangan,
            //             'kode_bank' => $kode_bank,
            //             'nama_bank' => $nama_bank,
            //             'no_rekening_bank' => $no_rekening_bank,
            //             'status_active' => 1,
            //             'updated_at' => date('Y-m-d H:i:s'),
            //         ];

            //         $this->_db->table('tb_pegawai_')->where('id', $dataOldPegawai->id)->update($dataUpdate);
            //         if ($this->_db->affectedRows() > 0) {
            //             $cekAnyData = $this->_db->table('tb_gaji_sipd')->where(['id_pegawai' => $dataOldPegawai->id, 'tahun' => $tahun_bulan])->countAllResults();
            //             if ($cekAnyData > 0) {
            //                 $this->_db->table('tb_gaji_sipd')->where(['id_pegawai' => $dataOldPegawai->id, 'tahun' => $tahun_bulan])->update([
            //                     'gaji_pokok' => $gaji_pokok,
            //                     'perhitungan_suami_istri' => $perhitungan_suami_istri,
            //                     'perhitungan_anak' => $perhitungan_anak,
            //                     'tunjangan_keluarga' => $tunjangan_keluarga,
            //                     'tunjangan_jabatan' => $tunjangan_jabatan,
            //                     'tunjangan_fungsional' => $tunjangan_fungsional,
            //                     'tunjangan_fungsional_umum' => $tunjangan_fungsional_umum,
            //                     'tunjangan_beras' => $tunjangan_beras,
            //                     'tunjangan_pph' => $tunjangan_pph,
            //                     'pembulatan_gaji' => $pembulatan_gaji,
            //                     'iuran_jaminan_kesehatan' => $iuran_jaminan_kesehatan,
            //                     'iuran_jaminan_kecelakaan_kerja' => $iuran_jaminan_kecelakaan_kerja,
            //                     'iuran_jaminan_kematian' => $iuran_jaminan_kematian,
            //                     'iuran_simpanan_tapera' => $iuran_simpanan_tapera,
            //                     'iuran_pensiun' => $iuran_pensiun,
            //                     'tunjangan_jaminan_hari_tua' => $tunjangan_jaminan_hari_tua,
            //                     'potongan_iwp' => $potongan_iwp,
            //                     'potongan_pph_21' => $potongan_pph21,
            //                     'zakat' => $potongan_zakat,
            //                     'bulog' => $potongan_bulog,
            //                     'jumlah_gaji_dan_tunjangan' => $jumlah_gaji_dan_tunjangan,
            //                     'jumlah_potongan' => $jumlah_potongan,
            //                     'jumlah_transfer' => $jumlah_ditransfer,
            //                     'updated_at' => $dataUpdate['created_at'],
            //                 ]);
            //             } else {
            //                 $this->_db->table('tb_gaji_sipd')->insert([
            //                     'id' => $uuidLib->v4(),
            //                     'id_pegawai' => $dataOldPegawai->id,
            //                     'tahun' => $tahun_bulan,
            //                     'gaji_pokok' => $gaji_pokok,
            //                     'perhitungan_suami_istri' => $perhitungan_suami_istri,
            //                     'perhitungan_anak' => $perhitungan_anak,
            //                     'tunjangan_keluarga' => $tunjangan_keluarga,
            //                     'tunjangan_jabatan' => $tunjangan_jabatan,
            //                     'tunjangan_fungsional' => $tunjangan_fungsional,
            //                     'tunjangan_fungsional_umum' => $tunjangan_fungsional_umum,
            //                     'tunjangan_beras' => $tunjangan_beras,
            //                     'tunjangan_pph' => $tunjangan_pph,
            //                     'pembulatan_gaji' => $pembulatan_gaji,
            //                     'iuran_jaminan_kesehatan' => $iuran_jaminan_kesehatan,
            //                     'iuran_jaminan_kecelakaan_kerja' => $iuran_jaminan_kecelakaan_kerja,
            //                     'iuran_jaminan_kematian' => $iuran_jaminan_kematian,
            //                     'iuran_simpanan_tapera' => $iuran_simpanan_tapera,
            //                     'iuran_pensiun' => $iuran_pensiun,
            //                     'tunjangan_jaminan_hari_tua' => $tunjangan_jaminan_hari_tua,
            //                     'potongan_iwp' => $potongan_iwp,
            //                     'potongan_pph_21' => $potongan_pph21,
            //                     'zakat' => $potongan_zakat,
            //                     'bulog' => $potongan_bulog,
            //                     'jumlah_gaji_dan_tunjangan' => $jumlah_gaji_dan_tunjangan,
            //                     'jumlah_potongan' => $jumlah_potongan,
            //                     'jumlah_transfer' => $jumlah_ditransfer,
            //                     'created_at' => $dataUpdate['created_at'],
            //                 ]);
            //             }
            //             if ($this->_db->affectedRows() > 0) {

            //                 $this->_db->transCommit();

            //                 // try {
            //                 //     $notifLib = new NotificationLib();
            //                 //     $notifLib->create("Lolos Matching Simtun", "Usulan " . $ptk->kode_usulan . " telah lolos matching simtun.", "success", $user->data->id, $ptk->id_ptk, base_url('situgu/ptk/us/tpg/siapsk'));
            //                 // } catch (\Throwable $th) {
            //                 //     //throw $th;
            //                 // }
            //                 $response = new \stdClass;
            //                 $response->status = 200;
            //                 $response->message = "Data berhasil disimpan.";
            //                 return json_encode($response);
            //             } else {
            //                 $this->_db->transRollback();
            //                 $response = new \stdClass;
            //                 $response->status = 400;
            //                 $response->message = "Gagal menyimpan data import pada gaji sipd.";
            //                 return json_encode($response);
            //             }
            //         } else {
            //             $this->_db->transRollback();
            //             $response = new \stdClass;
            //             $response->status = 400;
            //             $response->message = "Gagal menyimpan data import pada data pegawai.";
            //             return json_encode($response);
            //         }
            //     } catch (\Throwable $th) {
            //         $this->_db->transRollback();
            //         $response = new \stdClass;
            //         $response->status = 200;
            //         $response->error = var_dump($th);
            //         $response->message = "Gagal menyimpan data import. kesalahan";
            //         return json_encode($response);
            //     }
            // } else {

            //     $this->_db->transRollback();
            //     $response = new \stdClass;
            //     $response->status = 400;
            //     $response->message = "Gagal menyimpan data usulan.";
            //     return json_encode($response);
            // }
            // } else {
            //     $response = new \stdClass;
            //     $response->status = 400;
            //     $response->message = "Data tidak ditemukan";
            //     return json_encode($response);
            // }
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

            $current = $this->_db->table('tb_up_masterdata_pegawai')
                ->where('id', $id)
                ->get()->getRowObject();

            if ($current) {

                $this->_db->transBegin();
                try {
                    $this->_db->table('tb_up_masterdata_pegawai')->where('id', $current->id)->delete();
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->error = var_dump($th);
                    $response->message = "Data import pegawai gagal dihapus.";
                    return json_encode($response);
                }

                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    try {
                        $file = $current->filename;
                        unlink(FCPATH . "uploads/api/pegawai/$file.json");
                        unlink(FCPATH . "uploads/api/pegawai/$file");
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Data import pegawai berhasil dihapus.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Data import pegawai gagal dihapus.";
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
