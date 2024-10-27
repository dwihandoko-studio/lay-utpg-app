<?php

namespace App\Controllers\Situgu\Ops\Doc;

use App\Controllers\BaseController;
use App\Models\Situgu\Ops\UpkelengkapanModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;

class Upkelengkapan extends BaseController
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
        $datamodel = new UpkelengkapanModel($request);

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
            //                 <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\', \'' . $list->id_tahun_tw . '\', \'' . $list->npsn . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
            //                 <a class="dropdown-item" href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_tahun_tw . '\', \'' . $list->npsn . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Sync Dapodik</a>
            //             </div>
            //         </div>';
            // $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            // $row[] = $action;
            $row[] = $list->tahun;
            $row[] = $list->tw;
            switch ($list->is_locked) {
                case 1:
                    $row[] = $list->lampiran_absen1 ? '<a href="' . base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen1 . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Absen 1</span></a>' : '-';
                    $row[] = $list->lampiran_absen2 ? '<a href="' . base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen2 . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Absen 2</span></a>' : '-';
                    $row[] = $list->lampiran_absen3 ? '<a href="' . base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen3 . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Absen 3</span></a>' : '-';
                    $row[] = $list->pembagian_tugas ? '<a href="' . base_url('upload/sekolah/pembagian-tugas') . '/' . $list->pembagian_tugas . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Pembagian Tugas</span></a>' : '-';
                    $row[] = $list->slip_gaji ? '<a href="' . base_url('upload/sekolah/slip-gaji') . '/' . $list->slip_gaji . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Slip Gaji</span></a>' : '-';
                    $row[] = $list->doc_lainnya ? '<a href="' . base_url('upload/sekolah/lainnya') . '/' . $list->doc_lainnya . '" target="_blank"><span class="badge rounded-pill badge-soft-primary font-size-11">Lampiran Doc Lainnya</span></a>' : '-';
                    $row[] = '<div class="text-center">
                    <span class="badge rounded-pill badge-soft-success font-size-11">Terkunci</span>
                    </div>';
                    break;
                default:
                    $row[] = $list->lampiran_absen1 ? '<a target="_blank" href="' . base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen1 . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Absen Bulan 1\',\'bulan1\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\',\'' . $list->lampiran_absen1 . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Absen Bulan 1\',\'bulan1\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = $list->lampiran_absen2 ? '<a target="_blank" href="' . base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen2 . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Absen Bulan 2\',\'bulan2\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\',\'' . $list->lampiran_absen2 . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Absen Bulan 2\',\'bulan2\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = $list->lampiran_absen3 ? '<a target="_blank" href="' . base_url('upload/sekolah/kehadiran') . '/' . $list->lampiran_absen3 . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Absen Bulan 3\',\'bulan3\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\',\'' . $list->lampiran_absen3 . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Absen Bulan 3\',\'bulan3\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = $list->pembagian_tugas ? '<a target="_blank" href="' . base_url('upload/sekolah/pembagian-tugas') . '/' . $list->pembagian_tugas . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Pembagian Tugas\',\'pembagian_tugas\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\',\'' . $list->pembagian_tugas . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Pembagian Tugas\',\'pembagian_tugas\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = $list->slip_gaji ? '<a target="_blank" href="' . base_url('upload/sekolah/slip-gaji') . '/' . $list->slip_gaji . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Slip Gaji\',\'slip_gaji\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\',\'' . $list->slip_gaji . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Slip Gaji\',\'slip_gaji\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\')" class="btn btn-primary waves-effect waves-light">
                        <i class="bx bx-upload font-size-16 align-middle me-2"></i> Upload
                    </a>';
                    $row[] = $list->doc_lainnya ? '<a target="_blank" href="' . base_url('upload/sekolah/doc-lainnya') . '/' . $list->doc_lainnya . '"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                    </a>
                    <a href="javascript:actionEditFile(\'Dokumen Lainnya\',\'doc_lainnya\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\',\'' . $list->doc_lainnya . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-edit-alt font-size-16 align-middle"></i></button>
                    </a>' :
                        '<a href="javascript:actionUpload(\'Dokumen Lainnya\',\'doc_lainnya\',\'' . $list->id_tahun_tw . '\',\'' . $list->npsn . '\')" class="btn btn-primary waves-effect waves-light">
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
            "recordsTotal" => $datamodel->count_all($npsn),
            "recordsFiltered" => $datamodel->count_filtered($npsn),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        return redirect()->to(base_url('situgu/ops/doc/upkelengkapan/data'));
    }

    public function data()
    {
        $data['title'] = 'KELENGKAPAN DOC';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;
        $data['tw'] = $this->_db->table('_ref_tahun_tw')->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getResult();

        return view('situgu/ops/doc/upkelengkapan/index', $data);
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
            'npsn' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
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
                . $this->validator->getError('npsn');
            return json_encode($response);
        } else {
            $bulan = htmlspecialchars($this->request->getVar('bulan'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);

            $data['bulan'] = $bulan;
            $data['tw'] = $tw;
            $data['npsn'] = $npsn;
            $data['title'] = $title;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ops/doc/upkelengkapan/upload', $data);
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
            'npsn' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
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
                . $this->validator->getError('npsn');
            return json_encode($response);
        } else {
            $bulan = htmlspecialchars($this->request->getVar('bulan'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);
            $old = htmlspecialchars($this->request->getVar('old'), true);

            $data['bulan'] = $bulan;
            $data['tw'] = $tw;
            $data['npsn'] = $npsn;
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
            $response->data = view('situgu/ops/doc/upkelengkapan/editupload', $data);
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
            'npsn' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
                ]
            ],
            '_file' => [
                'rules' => 'uploaded[_file]|max_size[_file,5120]|mime_in[_file,image/jpeg,image/jpg,image/png,application/pdf]',
                'errors' => [
                    'uploaded' => 'Pilih file terlebih dahulu. ',
                    'max_size' => 'Ukuran file terlalu besar, Maximum 5Mb. ',
                    'mime_in' => 'Ekstensi yang anda upload harus berekstensi gambar dan pdf. '
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('name')
                . $this->validator->getError('tw')
                . $this->validator->getError('npsn')
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
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

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
                $this->_db->table('_absen_kehadiran')->where(['id_tahun_tw' => $tw, 'npsn' => $npsn, 'is_locked' => 0])->update($data);
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
            'npsn' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
                ]
            ],
            'old' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Old tidak boleh kosong. ',
                ]
            ],
            '_file' => [
                'rules' => 'uploaded[_file]|max_size[_file,3072]|mime_in[_file,image/jpeg,image/jpg,image/png,application/pdf]',
                'errors' => [
                    'uploaded' => 'Pilih file terlebih dahulu. ',
                    'max_size' => 'Ukuran file terlalu besar, Maximum 3Mb. ',
                    'mime_in' => 'Ekstensi yang anda upload harus berekstensi gambar dan pdf. '
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('name')
                . $this->validator->getError('tw')
                . $this->validator->getError('npsn')
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
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);
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
                $this->_db->table('_absen_kehadiran')->where(['id_tahun_tw' => $tw, 'npsn' => $npsn, 'is_locked' => 0])->update($data);
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
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);

            $current = $this->_db->table('_users_tb')
                ->where('uid', $id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('a/setting/pengguna/edit', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function syncAll()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'npsn' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('npsn');
            return json_encode($response);
        } else {
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $apiLib = new Apilib();
            $result = $apiLib->syncPtk($npsn);

            if ($result) {
                if ($result->status == 200) {
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Syncrone Data Semua PTK Berhasil Dilakukan.";
                    return json_encode($response);
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal Syncrone Data";
                    return json_encode($response);
                }
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal Syncrone Data";
                return json_encode($response);
            }
        }
    }

    public function sync()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'ptk_id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
            'npsn' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('ptk_id')
                . $this->validator->getError('nama')
                . $this->validator->getError('npsn');
            return json_encode($response);
        } else {
            $idPtk = htmlspecialchars($this->request->getVar('ptk_id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $apiLib = new Apilib();
            $result = $apiLib->syncPtkId($idPtk, $npsn);

            if ($result) {
                if ($result->status == 200) {
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Syncrone Data PTK $nama Berhasil Dilakukan.";
                    return json_encode($response);
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal Syncrone Data";
                    return json_encode($response);
                }
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal Syncrone Data";
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
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id');
            return json_encode($response);
        } else {
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
            $current = $this->_db->table('_users_tb')
                ->where('uid', $id)->get()->getRowObject();

            if ($current) {
                $this->_db->transBegin();
                try {
                    $this->_db->table('_users_tb')->where('uid', $id)->delete();

                    if ($this->_db->affectedRows() > 0) {
                        try {
                            $dir = FCPATH . "uploads/user";
                            unlink($dir . '/' . $current->image);
                        } catch (\Throwable $err) {
                        }
                        $this->_db->transCommit();
                        $response = new \stdClass;
                        $response->status = 200;
                        $response->message = "Data berhasil dihapus.";
                        return json_encode($response);
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Data gagal dihapus.";
                        return json_encode($response);
                    }
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Data gagal dihapus.";
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

    public function editSave()
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
                    'required' => 'Id buku tidak boleh kosong. ',
                ]
            ],
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
            'email' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Email tidak boleh kosong. ',
                ]
            ],
            'nohp' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'No handphone tidak boleh kosong. ',
                ]
            ],
            'nip' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NIP tidak boleh kosong. ',
                ]
            ],
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat tidak boleh kosong. ',
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status tidak boleh kosong. ',
                ]
            ],
        ];

        $filenamelampiran = dot_array_search('file.name', $_FILES);
        if ($filenamelampiran != '') {
            $lampiranVal = [
                'file' => [
                    'rules' => 'uploaded[file]|max_size[file,512]|is_image[file]',
                    'errors' => [
                        'uploaded' => 'Pilih gambar profil terlebih dahulu. ',
                        'max_size' => 'Ukuran gambar profil terlalu besar. ',
                        'is_image' => 'Ekstensi yang anda upload harus berekstensi gambar. '
                    ]
                ],
            ];
            $rules = array_merge($rules, $lampiranVal);
        }

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('nama')
                . $this->validator->getError('id')
                . $this->validator->getError('email')
                . $this->validator->getError('nohp')
                . $this->validator->getError('nip')
                . $this->validator->getError('alamat')
                . $this->validator->getError('status')
                . $this->validator->getError('file');
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

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $email = htmlspecialchars($this->request->getVar('email'), true);
            $nohp = htmlspecialchars($this->request->getVar('nohp'), true);
            $nip = htmlspecialchars($this->request->getVar('nip'), true);
            $alamat = htmlspecialchars($this->request->getVar('alamat'), true);
            $status = htmlspecialchars($this->request->getVar('status'), true);

            $oldData =  $this->_db->table('_users_tb')->where('uid', $id)->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            if (
                $nama === $oldData->fullname
                && $email === $oldData->email
                && $nohp === $oldData->no_hp
                && $nip === $oldData->nip
                && $alamat === $oldData->alamat
                && (int)$status === (int)$oldData->is_active
            ) {
                if ($filenamelampiran == '') {
                    $response = new \stdClass;
                    $response->status = 201;
                    $response->message = "Tidak ada perubahan data yang disimpan.";
                    $response->redirect = base_url('a/setting/pengguna/data');
                    return json_encode($response);
                }
            }

            if ($email !== $oldData->email) {
                $cekData = $this->_db->table('_users_tb')->where(['email' => $email])->get()->getRowObject();
                if ($cekData) {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Email sudah terdaftar.";
                    return json_encode($response);
                }
            }

            $data = [
                'email' => $email,
                'fullname' => $nama,
                'no_hp' => $nohp,
                'nip' => $nip,
                'alamat' => $alamat,
                'is_active' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $dir = FCPATH . "uploads/user";

            if ($filenamelampiran != '') {
                $lampiran = $this->request->getFile('file');
                $filesNamelampiran = $lampiran->getName();
                $newNamelampiran = _create_name_foto($filesNamelampiran);

                if ($lampiran->isValid() && !$lampiran->hasMoved()) {
                    $lampiran->move($dir, $newNamelampiran);
                    $data['image'] = $newNamelampiran;
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengupload gambar.";
                    return json_encode($response);
                }
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_users_tb')->where('uid', $oldData->uid)->update($data);
            } catch (\Exception $e) {
                unlink($dir . '/' . $newNamelampiran);
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan gambar baru.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                try {
                    unlink($dir . '/' . $oldData->image);
                } catch (\Throwable $th) {
                }
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil diupdate.";
                $response->redirect = base_url('a/setting/pengguna/data');
                return json_encode($response);
            } else {
                unlink($dir . '/' . $newNamelampiran);
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupate data";
                return json_encode($response);
            }
        }
    }
}
