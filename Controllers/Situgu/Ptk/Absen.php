<?php

namespace App\Controllers\Situgu\Ptk;

use App\Controllers\BaseController;
use App\Models\Situgu\Ptk\AbsenModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Situgu\Kehadiranptklib;

use App\Libraries\MinioClient;

class Absen extends BaseController
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
        $datamodel = new AbsenModel($request);

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

            $row[] = $list->tahun;
            $row[] = $list->tw;
            $row[] = $list->bulan_1;
            $row[] = $list->bulan_2;
            $row[] = $list->bulan_3;
            switch ($list->is_locked) {
                case 1:
                    $row[] = $list->lampiran_absen1 ? '<a href="' . (strpos($list->lampiran_absen1, '/') !== false ? getDokumentPreviewStorage('situgu', $list->lampiran_absen1) : base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen1) . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Absen 1</span></a>' : '-';
                    $row[] = $list->lampiran_absen2 ? '<a href="' . (strpos($list->lampiran_absen2, '/') !== false ? getDokumentPreviewStorage('situgu', $list->lampiran_absen2) : base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen2) . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Absen 2</span></a>' : '-';
                    $row[] = $list->lampiran_absen3 ? '<a href="' . (strpos($list->lampiran_absen3, '/') !== false ? getDokumentPreviewStorage('situgu', $list->lampiran_absen3) : base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen3) . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Absen 3</span></a>' : '-';
                    $row[] = $list->pembagian_tugas ? '<a href="' . (strpos($list->pembagian_tugas, '/') !== false ? getDokumentPreviewStorage('situgu', $list->pembagian_tugas) : base_url('upload/sekolah/pembagian-tugas') . '/' . $list->pembagian_tugas) . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Pembagian Tugas</span></a>' : '-';
                    $row[] = $list->slip_gaji ? '<a href="' . (strpos($list->slip_gaji, '/') !== false ? getDokumentPreviewStorage('situgu', $list->slip_gaji) : base_url('upload/sekolah/slip-gaji') . '/' . $list->slip_gaji) . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Slip Gaji</span></a>' : '-';
                    $row[] = $list->doc_lainnya ? '<a href="' . (strpos($list->doc_lainnya, '/') !== false ? getDokumentPreviewStorage('situgu', $list->doc_lainnya) : base_url('upload/sekolah/lainnya') . '/' . $list->doc_lainnya) . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Doc Lainnya</span></a>' : '-';
                    $row[] = '<div class="text-center">
                    <span class="badge rounded-pill badge-soft-success font-size-11">Terkunci</span>
                    </div>';
                    break;
                default:
                    $row[] = $list->lampiran_absen1 ? '<a target="_blank" href="' . (strpos($list->lampiran_absen1, '/') !== false ? getDokumentPreviewStorage('situgu', $list->lampiran_absen1) : base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen1) . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Absen Bulan 1\',\'bulan1\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\',\'' . $list->lampiran_absen1 . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Absen Bulan 1\',\'bulan1\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = $list->lampiran_absen2 ? '<a target="_blank" href="' . (strpos($list->lampiran_absen2, '/') !== false ? getDokumentPreviewStorage('situgu', $list->lampiran_absen2) : base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen2) . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Absen Bulan 2\',\'bulan2\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\',\'' . $list->lampiran_absen2 . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Absen Bulan 2\',\'bulan2\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = $list->lampiran_absen3 ? '<a target="_blank" href="' . (strpos($list->lampiran_absen3, '/') !== false ? getDokumentPreviewStorage('situgu', $list->lampiran_absen3) : base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen3) . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Absen Bulan 3\',\'bulan3\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\',\'' . $list->lampiran_absen3 . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Absen Bulan 3\',\'bulan3\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = $list->pembagian_tugas ? '<a target="_blank" href="' . (strpos($list->pembagian_tugas, '/') !== false ? getDokumentPreviewStorage('situgu', $list->pembagian_tugas) : base_url('upload/sekolah/pembagian-tugas') . '/' . $list->pembagian_tugas) . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Pembagian Tugas\',\'pembagian_tugas\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\',\'' . $list->pembagian_tugas . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Pembagian Tugas\',\'pembagian_tugas\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = $list->slip_gaji ? '<a target="_blank" href="' . (strpos($list->slip_gaji, '/') !== false ? getDokumentPreviewStorage('situgu', $list->slip_gaji) : base_url('upload/sekolah/slip-gaji') . '/' . $list->slip_gaji) . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Slip Gaji\',\'slip_gaji\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\',\'' . $list->slip_gaji . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Slip Gaji\',\'slip_gaji\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = $list->doc_lainnya ? '<a target="_blank" href="' . (strpos($list->doc_lainnya, '/') !== false ? getDokumentPreviewStorage('situgu', $list->doc_lainnya) : base_url('upload/sekolah/doc-lainnya') . '/' . $list->doc_lainnya) . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Dokumen Lainnya\',\'doc_lainnya\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\',\'' . $list->doc_lainnya . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Dokumen Lainnya\',\'doc_lainnya\',\'' . $list->id_tahun_tw . '\',\'' . $list->id_ptk . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = '<div class="text-center">
                        <span class="badge rounded-pill badge-soft-danger font-size-11">Terbuka</span>
                    </div>';
                    break;
            }

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
        return redirect()->to(base_url('situgu/ptk/absen/data'));
    }

    public function data()
    {
        $data['title'] = 'Absen';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;
        $data['data'] = $this->_db->table('_ref_tahun_tw')->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getResult();

        return view('situgu/ptk/absen/index', $data);
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
            'bulan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Bulan tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            'id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id tidak boleh kosong. ',
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
            $response->message = $this->validator->getError('bulan')
                . $this->validator->getError('tw')
                . $this->validator->getError('title')
                . $this->validator->getError('id');
            return json_encode($response);
        } else {
            $bulan = htmlspecialchars($this->request->getVar('bulan'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);

            $data['bulan'] = $bulan;
            $data['tw'] = $tw;
            $data['id'] = $id;
            $data['title'] = $title;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ptk/absen/upload', $data);
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
            'bulan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Bulan tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
            'id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id tidak boleh kosong. ',
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
            $response->message = $this->validator->getError('bulan')
                . $this->validator->getError('tw')
                . $this->validator->getError('title')
                . $this->validator->getError('old')
                . $this->validator->getError('id');
            return json_encode($response);
        } else {
            $bulan = htmlspecialchars($this->request->getVar('bulan'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);
            $old = htmlspecialchars($this->request->getVar('old'), true);

            $data['bulan'] = $bulan;
            $data['tw'] = $tw;
            $data['id'] = $id;
            $data['title'] = $title;
            $data['old'] = $old;
            switch ($bulan) {
                case 'bulan1':
                    $data['old_url'] = base_url('upload/sekolah/kehadiran') . '/' . $old;
                    break;
                case 'bulan2':
                    $data['old_url'] = base_url('upload/sekolah/kehadiran') . '/' . $old;
                    break;
                case 'bulan3':
                    $data['old_url'] = base_url('upload/sekolah/kehadiran') . '/' . $old;
                    break;
                case 'pembagian_tugas':
                    $data['old_url'] = base_url('upload/sekolah/pembagian-tugas') . '/' . $old;
                    break;
                case 'slip_gaji':
                    $data['old_url'] = base_url('upload/sekolah/slip-gaji') . '/' . $old;
                    break;
                default:
                    $data['old_url'] = base_url('upload/sekolah/doc-lainnya') . '/' . $old;
                    break;
            }

            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ptk/absen/editupload', $data);
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
            'name' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Name tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tw tidak boleh kosong. ',
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
            $response->message = $this->validator->getError('name')
                . $this->validator->getError('tw')
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

            $name = htmlspecialchars($this->request->getVar('name'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $id = htmlspecialchars($this->request->getVar('id'), true);

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $dir = "";
            $field_db = '';

            switch ($name) {
                case 'bulan1':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen1';
                    break;
                case 'bulan2':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen2';
                    break;
                case 'bulan3':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen3';
                    break;
                case 'pembagian_tugas':
                    $dir = FCPATH . "upload/sekolah/pembagian-tugas";
                    $field_db = 'pembagian_tugas';
                    break;
                case 'slip_gaji':
                    $dir = FCPATH . "upload/sekolah/slip-gaji";
                    $field_db = 'slip_gaji';
                    break;
                default:
                    $dir = FCPATH . "upload/sekolah/doc-lainnya";
                    $field_db = 'doc_lainnya';
                    break;
            }

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
                $this->_db->table('_absen_kehadiran')->where(['id_tahun_tw' => $tw, 'id_ptk' => $id, 'is_locked' => 0])->update($data);
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

    public function editUploadSave()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'name' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Name tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tw tidak boleh kosong. ',
                ]
            ],
            'id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Id tidak boleh kosong. ',
                ]
            ],
            'old' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Old tidak boleh kosong. ',
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
            $response->message = $this->validator->getError('name')
                . $this->validator->getError('tw')
                . $this->validator->getError('id')
                . $this->validator->getError('old')
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

            $name = htmlspecialchars($this->request->getVar('name'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $old = htmlspecialchars($this->request->getVar('old'), true);

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $dir = "";
            $field_db = '';

            switch ($name) {
                case 'bulan1':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen1';
                    break;
                case 'bulan2':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen2';
                    break;
                case 'bulan3':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen3';
                    break;
                case 'pembagian_tugas':
                    $dir = FCPATH . "upload/sekolah/pembagian-tugas";
                    $field_db = 'pembagian_tugas';
                    break;
                case 'slip_gaji':
                    $dir = FCPATH . "upload/sekolah/slip-gaji";
                    $field_db = 'slip_gaji';
                    break;
                default:
                    $dir = FCPATH . "upload/sekolah/doc-lainnya";
                    $field_db = 'doc_lainnya';
                    break;
            }

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
                $this->_db->table('_absen_kehadiran')->where(['id_tahun_tw' => $tw, 'id_ptk' => $id, 'is_locked' => 0])->update($data);
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
                $this->_db->transCommit();

                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil diupdate.";
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
}
