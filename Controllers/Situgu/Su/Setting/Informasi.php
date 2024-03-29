<?php

namespace App\Controllers\Situgu\Su\Setting;

use App\Controllers\BaseController;
use App\Models\Situgu\Su\InformasiModel;
use Config\Services;
use App\Libraries\Profilelib;
use App\Libraries\Uuid;

class Informasi extends BaseController
{
    var $folderImage = 'masterdata';
    private $_db;
    private $model;

    function __construct()
    {
        helper(['text', 'file', 'form', 'session', 'array', 'imageurl', 'web', 'filesystem']);
        $this->_db      = \Config\Database::connect();
    }

    public function getAll()
    {
        $request = Services::request();
        $datamodel = new InformasiModel($request);


        $lists = $datamodel->get_datatables();
        // $lists = [];
        $data = [];
        $no = $request->getPost("start");
        foreach ($lists as $list) {
            $no++;
            $row = [];

            $row[] = $no;
            $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace("'", "", $list->judul) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bxs-show font-size-16 align-middle"></i></button>
                        </a>
                        <!--<a href="javascript:actionEdit(\'' . $list->id . '\', \'' . str_replace("'", "", $list->judul) . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bx-edit font-size-16 align-middle"></i></button>
                        </a>-->
                        <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->judul) . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
                        <i class="bx bx-trash font-size-16 align-middle"></i></button>
                        </a>';
            if ($list->image !== null) {
                $image = '<img alt="Image placeholder" src="' . base_url() . '/uploads/pengumuman/' . $list->image . '" width="80px" height="50px">';
            } else {
                $image = "-";
            }
            if ($list->lampiran !== null) {
                $lampiran = '<a target="_blank" href="' . base_url() . '/uploads/pengumuman/' . $list->lampiran . '" class="badge badge-pill badge-soft-success">Lampiran</a>';
            } else {
                $lampiran = "-";
            }
            $row[] = $action;
            switch ((int)$list->status) {
                case 1:
                    $row[] = '<span class="badge badge-pill badge-soft-success">Terpublish</span>';
                    break;
                default:
                    $row[] = '<span class="badge badge-pill badge-soft-danger">Tidak Terpublish</span>';
                    break;
            }
            $row[] = $list->judul;
            $row[] = $image;
            $row[] = $lampiran;

            $data[] = $row;
        }
        $output = [
            "draw" => $request->getPost('draw'),
            // "recordsTotal" => 0,
            // "recordsFiltered" => 0,
            "recordsTotal" => $datamodel->count_all(),
            "recordsFiltered" => $datamodel->count_filtered(),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        return redirect()->to(base_url('situgu/su/setting/informasi/data'));
    }

    public function data()
    {
        $data['title'] = 'INFORMASI';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;

        return view('situgu/su/setting/informasi/index', $data);
    }

    public function add()
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

        $data['user'] = $user->data;
        $data['roles'] = $this->_db->table('_role_user')->whereNotIn('id', [1, 8])->get()->getResult();

        $response = new \stdClass;
        $response->status = 200;
        $response->message = "Permintaan diizinkan";
        $response->data = view('situgu/su/setting/informasi/add', $data);
        return json_encode($response);
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

            $current = $this->_db->table('_tb_pengumuman')
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/su/setting/informasi/edit', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
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
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);

            $current = $this->_db->table('_tb_infopop')
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/su/setting/informasi/detail', $data);
                return json_encode($response);
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
            $current = $this->_db->table('_tb_infopop')
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $this->_db->transBegin();
                try {
                    $this->_db->table('_tb_infopop')->where('id', $id)->delete();

                    if ($this->_db->affectedRows() > 0) {
                        if ($current->image !== null) {
                            try {
                                $dir = FCPATH . "uploads/pengumuman";
                                unlink($dir . '/' . $current->image);
                            } catch (\Throwable $err) {
                            }
                        }
                        if ($current->lampiran !== null) {
                            try {
                                $dir = FCPATH . "uploads/pengumuman";
                                unlink($dir . '/' . $current->lampiran);
                            } catch (\Throwable $err) {
                            }
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

    public function addSave()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'judul' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Judul pengumuman tidak boleh kosong. ',
                ]
            ],
            'isi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Deskripsi pengumuman tidak boleh kosong. ',
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status tidak boleh kosong. ',
                ]
            ],
            'status_web' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status Web tidak boleh kosong. ',
                ]
            ],
            'status_tele' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status Tele tidak boleh kosong. ',
                ]
            ],
        ];

        $filenamelampiran = dot_array_search('_file.name', $_FILES);
        if ($filenamelampiran != '') {
            $lampiranVal = [
                '_file' => [
                    'rules' => 'uploaded[_file]|max_size[_file,1024]|mime_in[_file,image/jpeg,image/jpg,image/png]',
                    'errors' => [
                        'uploaded' => 'Pilih gambar pengumuman terlebih dahulu. ',
                        'max_size' => 'Ukuran gambar pengumuman terlalu besar. ',
                        'mime_in' => 'Ekstensi yang anda upload harus berekstensi gambar. '
                    ]
                ],
            ];
            $rules = array_merge($rules, $lampiranVal);
        }

        $filenamelampiranFile = dot_array_search('_file_lampiran.name', $_FILES);
        if ($filenamelampiranFile != '') {
            $lampiranValFile = [
                '_file_lampiran' => [
                    'rules' => 'uploaded[_file_lampiran]|max_size[_file_lampiran,5148]|mime_in[_file_lampiran,image/jpeg,image/jpg,image/png,application/pdf]',
                    'errors' => [
                        'uploaded' => 'Pilih file pengumuman terlebih dahulu. ',
                        'max_size' => 'Ukuran file pengumuman terlalu besar. ',
                        'mime_in' => 'Ekstensi yang anda upload harus berekstensi gambar/pdf. '
                    ]
                ],
            ];
            $rules = array_merge($rules, $lampiranValFile);
        }

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('judul')
                . $this->validator->getError('isi')
                . $this->validator->getError('status')
                . $this->validator->getError('status_web')
                . $this->validator->getError('status_tele')
                . $this->validator->getError('_file_lampiran')
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

            $judul = htmlspecialchars($this->request->getVar('judul'), true);
            $isi = $this->request->getVar('isi');
            $status = htmlspecialchars($this->request->getVar('status'), true);
            $status_web = htmlspecialchars($this->request->getVar('status_web'), true);
            $status_tele = htmlspecialchars($this->request->getVar('status_tele'), true);

            $roles = $this->request->getVar('roles');

            if ((int)$status_web == 1) {

                $slug = generateSlug($judul);

                $cekData = $this->_db->table('_tb_infopop')->where(['url' => $slug . '.html'])->get()->getRowObject();

                if ($cekData) {
                    $slug = $slug . "-" . date('Y-m-d');
                }

                $isi = str_replace('<img src=', '<img style="max-width: 100%;" src=', $isi);

                if ($roles == "" || $roles == " ") {
                    $roles = "ALL";
                }

                $uuidLib = new Uuid();

                $data = [
                    'id' => $uuidLib->v4(),
                    'judul' => $judul,
                    'status' => $status,
                    'tampil' => $status,
                    'tujuan_role' => $roles,
                    'url' => $slug . '.html',
                    'isi' => $isi,
                    'uploader' => $user->data->id,
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $dir = FCPATH . "uploads/pengumuman";

                if ($filenamelampiran != '') {
                    $lampiran = $this->request->getFile('_file');
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

                if ($filenamelampiranFile != '') {
                    $lampiranFile = $this->request->getFile('_file_lampiran');
                    $filesNamelampiranFile = $lampiranFile->getName();
                    $newNamelampiranFile = _create_name_foto($filesNamelampiranFile);

                    if ($lampiranFile->isValid() && !$lampiranFile->hasMoved()) {
                        $lampiranFile->move($dir, $newNamelampiranFile);
                        $data['lampiran'] = $newNamelampiranFile;
                    } else {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal mengupload file.";
                        return json_encode($response);
                    }
                }

                $this->_db->transBegin();
                try {
                    $this->_db->table('_tb_infopop')->insert($data);
                } catch (\Exception $e) {
                    if ($filenamelampiran != '') {
                        unlink($dir . '/' . $newNamelampiran);
                    }
                    if ($filenamelampiranFile != '') {
                        unlink($dir . '/' . $newNamelampiranFile);
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
                    if ((int)$status_tele == 1) {
                        $tokenTele = "6504819187:AAEtykjIx2Gjd229nUgDHRlwJ5xGNTMjO0A";
                        $isis = str_replace("<p>", "\n", $isi);
                        $isis = str_replace("</p>", "\n", $isis);
                        $isis = str_replace("<strong>", "", $isis);
                        $isis = str_replace("</strong>", "", $isis);
                        $isis = str_replace("<br>", "\n", $isis);
                        $juduls = strtoupper($judul);
                        $message = "<b>$juduls</b>\n______________________________________________________\n\n$isis\n\n\nPesan otomatis dari <b>SI-TUGU Kab. Lampung Tengah</b>\n_________________________________________________";
                        try {

                            $dataReq = [
                                'chat_id' => "-1001704311879",
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
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Data berhasil disimpan.";
                    $response->redirect = base_url('situgu/su/setting/informasi/data');
                    return json_encode($response);
                } else {
                    if ($filenamelampiran != '') {
                        unlink($dir . '/' . $newNamelampiran);
                    }
                    if ($filenamelampiranFile != '') {
                        unlink($dir . '/' . $newNamelampiranFile);
                    }
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal menyimpan data";
                    return json_encode($response);
                }
            } else {
                if ((int)$status_tele == 1) {
                    $tokenTele = "6504819187:AAEtykjIx2Gjd229nUgDHRlwJ5xGNTMjO0A";
                    $isis = str_replace("<p>", "\n", $isi);
                    $isis = str_replace("</p>", "\n", $isis);
                    $isis = str_replace("<strong>", "", $isis);
                    $isis = str_replace("</strong>", "", $isis);
                    $isis = str_replace("<br>", "\n", $isis);
                    $juduls = strtoupper($judul);
                    $message = "<b>$juduls</b>\n______________________________________________________\n\n$isis\n\n\nPesan otomatis dari <b>SI-TUGU Kab. Lampung Tengah</b>\n_________________________________________________";
                    try {

                        $dataReq = [
                            'chat_id' => "-1001704311879",
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
                        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);

                        if ($http_status == 200 || $http_status == 201) {
                        } else {
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Informasi gagal dikirim ke group telegram.";
                            return json_encode($response);
                        }

                        // var_dump($server_output);
                    } catch (\Throwable $th) {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->error = var_dump($th);
                        $response->message = "Informasi gagal dikirim ke group telegram.";
                        // $response->redirect = base_url('situgu/su/setting/informasi/data');
                        return json_encode($response);
                    }
                }

                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Informasi berhasil dikirim ke group telegram.";
                $response->redirect = base_url('situgu/su/setting/informasi/data');
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
                    'required' => 'Id tidak boleh kosong. ',
                ]
            ],
            'judul' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Judul berita tidak boleh kosong. ',
                ]
            ],
            'isi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Deskripsi berita tidak boleh kosong. ',
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status tidak boleh kosong. ',
                ]
            ],
        ];

        $filenamelampiran = dot_array_search('_file.name', $_FILES);
        if ($filenamelampiran != '') {
            $lampiranVal = [
                '_file' => [
                    'rules' => 'uploaded[_file]|max_size[_file,1024]|mime_in[_file,image/jpeg,image/jpg,image/png]',
                    'errors' => [
                        'uploaded' => 'Pilih gambar pengumuman terlebih dahulu. ',
                        'max_size' => 'Ukuran gambar pengumuman terlalu besar. ',
                        'mime_in' => 'Ekstensi yang anda upload harus berekstensi gambar. '
                    ]
                ],
            ];
            $rules = array_merge($rules, $lampiranVal);
        }

        $filenamelampiranFile = dot_array_search('_file_lampiran.name', $_FILES);
        if ($filenamelampiranFile != '') {
            $lampiranValFile = [
                '_file_lampiran' => [
                    'rules' => 'uploaded[_file_lampiran]|max_size[_file_lampiran,5148]|mime_in[_file_lampiran,image/jpeg,image/jpg,image/png,application/pdf]',
                    'errors' => [
                        'uploaded' => 'Pilih file pengumuman terlebih dahulu. ',
                        'max_size' => 'Ukuran file pengumuman terlalu besar. ',
                        'mime_in' => 'Ekstensi yang anda upload harus berekstensi gambar/pdf. '
                    ]
                ],
            ];
            $rules = array_merge($rules, $lampiranValFile);
        }

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('judul')
                . $this->validator->getError('isi')
                . $this->validator->getError('status')
                . $this->validator->getError('_file_lampiran')
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

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $judul = htmlspecialchars($this->request->getVar('judul'), true);
            $isi = $this->request->getVar('isi');
            $status = htmlspecialchars($this->request->getVar('status'), true);

            $oldData =  $this->_db->table('_tb_pengumuman')->where('id', $id)->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            $isi = str_replace('<img src=', '<img style="max-width: 100%;" src=', $isi);

            $data = [
                'judul' => $judul,
                'status' => $status,
                'deskripsi' => $isi,
                'user_updated' => $user->data->uid,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($judul !== $oldData->judul) {
                $slug = generateSlug($judul);
                $cekData = $this->_db->table('_tb_pengumuman')->where(['url' => $slug . '.html'])->get()->getRowObject();

                if ($cekData) {
                    $slug = $slug . "-" . date('Y-m-d');
                }

                $data['url'] = $slug . '.html';
            }

            if (
                (int)$status === (int)$oldData->status
                && $judul === $oldData->judul
                && $isi === $oldData->deskripsi
            ) {
                if ($filenamelampiran == '') {
                    if ($filenamelampiranFile == '') {
                        $response = new \stdClass;
                        $response->status = 201;
                        $response->message = "Tidak ada perubahan data yang disimpan.";
                        $response->redirect = base_url('a/informasi/pengumuman/data');
                        return json_encode($response);
                    }
                }
                if ($filenamelampiranFile == '') {
                    if ($filenamelampiran == '') {
                        $response = new \stdClass;
                        $response->status = 201;
                        $response->message = "Tidak ada perubahan data yang disimpan.";
                        $response->redirect = base_url('a/informasi/pengumuman/data');
                        return json_encode($response);
                    }
                }
            }

            $dir = FCPATH . "uploads/pengumuman";

            if ($filenamelampiran != '') {
                $lampiran = $this->request->getFile('_file');
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

            if ($filenamelampiranFile != '') {
                $lampiranFile = $this->request->getFile('_file_lampiran');
                $filesNamelampiranFile = $lampiranFile->getName();
                $newNamelampiranFile = _create_name_foto($filesNamelampiranFile);

                if ($lampiranFile->isValid() && !$lampiranFile->hasMoved()) {
                    $lampiranFile->move($dir, $newNamelampiranFile);
                    $data['lampiran'] = $newNamelampiranFile;
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengupload file.";
                    return json_encode($response);
                }
            }
            $this->_db->transBegin();
            try {
                $this->_db->table('_tb_pengumuman')->where('id', $oldData->id)->update($data);
            } catch (\Exception $e) {
                if ($filenamelampiran != '') {
                    unlink($dir . '/' . $newNamelampiran);
                }
                if ($filenamelampiranFile != '') {
                    unlink($dir . '/' . $newNamelampiranFile);
                }
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data gagal disimpan.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                if ($filenamelampiran != '') {
                    if ($oldData->image !== null) {
                        try {
                            unlink($dir . '/' . $oldData->image);
                        } catch (\Throwable $th) {
                        }
                    }
                }
                if ($filenamelampiranFile != '') {
                    if ($oldData->lampiran !== null) {
                        try {
                            unlink($dir . '/' . $oldData->lampiran);
                        } catch (\Throwable $th) {
                        }
                    }
                }
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil diupdate.";
                $response->redirect = base_url('a/informasi/pengumuman/data');
                return json_encode($response);
            } else {
                if ($filenamelampiran != '') {
                    unlink($dir . '/' . $newNamelampiran);
                }
                if ($filenamelampiranFile != '') {
                    unlink($dir . '/' . $newNamelampiranFile);
                }
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupate data";
                return json_encode($response);
            }
        }
    }

    public function uploadImage()
    {
        $validated = $this->validate([
            'upload' => [
                'uploaded[upload]',
                'max_size[upload, 1024]',
                'is_image[upload]',
            ],
        ]);

        if ($validated) {
            $lampiran = $this->request->getFile('upload');
            $filesNamelampiran = $lampiran->getName();
            $newNamelampiran = _create_name_foto($filesNamelampiran);
            $writePath = FCPATH . "uploads/pengumuman/widget";

            if ($lampiran->isValid() && !$lampiran->hasMoved()) {
                $lampiran->move($writePath, $newNamelampiran);
                $data = [
                    "uploaded" => true,
                    "url" => base_url('uploads/pengumuman/widget/' . $newNamelampiran),
                ];
            } else {
                $data = [
                    "uploaded" => false,
                    "error" => [
                        "message" => $lampiran
                    ],
                ];
            }
        } else {
            $data = [
                "uploaded" => false,
                "error" => [
                    "message" => $this->validator->getError('upload')
                ],
            ];
        }
        return $this->response->setJSON($data);
    }
}
