<?php

namespace App\Controllers\Situgu\Ks;

use App\Controllers\BaseController;
// use App\Models\Situgu\Ptk\PtkModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Emaillib;
use App\Libraries\Helplib;

use App\Libraries\MinioClient;

class Profil extends BaseController
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

    public function index()
    {
        return redirect()->to(base_url('situgu/ks/profil/data'));
    }

    public function data()
    {
        $data['title'] = 'PENGGUNA';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;
        $current = $this->_db->table('_ptk_tb a')
            ->select("b.*, a.lampiran_foto, a.nama, a.nuptk, a.nip, a.nik, a.email as email_dapodik, a.no_hp as nohp_dapodik, a.chat_id_telegram, c.role")
            ->join('v_user b', 'a.id_ptk = b.ptk_id', 'left')
            ->join('_role_user c', 'b.role_user = c.id', 'left')
            ->where('a.id_ptk', $user->data->ptk_id)->get()->getRowObject();

        if ($current) {
            $data['data'] = $current;

            return view('situgu/ks/profil/index', $data);
        } else {
            return view('situgu/ks/404', $data);
        }
    }

    public function act()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'action' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Action tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('action');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('action'), true);

            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                return redirect()->to(base_url('auth'));
            }

            $current = $this->_db->table('_ptk_tb a')
                ->select("b.*, a.lampiran_foto, a.nama, a.nuptk, a.nip, a.nik, a.email as email_dapodik, a.no_hp as nohp_dapodik, a.chat_id_telegram, c.role")
                ->join('v_user b', 'a.id_ptk = b.ptk_id', 'left')
                ->join('_role_user c', 'b.role_user = c.id', 'left')
                ->where('a.id_ptk', $user->data->ptk_id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                switch ($id) {
                    case 'password':
                        $response->data = view('situgu/ks/profil/password', $data);
                        break;
                    case 'foto':
                        $response->data = view('situgu/ks/profil/foto', $data);
                        break;
                    case 'aktivasi_email':
                        $response->data = view('situgu/ks/profil/aktivasi_email', $data);
                        break;
                    case 'aktivasi_wa':
                        $response->data = view('situgu/ks/profil/aktivasi_wa', $data);
                        break;
                    case 'aktivasi_telegram':
                        $response->data = view('situgu/ks/profil/aktivasi_telegram', $data);
                        break;

                    default:
                        $response->data = view('situgu/ks/profil/aktivasi_wa', $data);
                        break;
                }

                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function actubah()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'action' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Action tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('action');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('action'), true);

            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                return redirect()->to(base_url('auth'));
            }

            $current = $this->_db->table('_ptk_tb a')
                ->select("b.*, a.lampiran_foto, a.nama, a.nuptk, a.nip, a.nik, a.email as email_dapodik, a.no_hp as nohp_dapodik, a.chat_id_telegram, c.role")
                ->join('v_user b', 'a.id_ptk = b.ptk_id', 'left')
                ->join('_role_user c', 'b.role_user = c.id', 'left')
                ->where('a.id_ptk', $user->data->ptk_id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                switch ($id) {
                    case 'password':
                        $response->data = view('situgu/ks/profil/password', $data);
                        break;
                    case 'foto':
                        $response->data = view('situgu/ks/profil/foto', $data);
                        break;
                    case 'aktivasi_email':
                        $response->data = view('situgu/ks/profil/aktivasi_email', $data);
                        break;
                    case 'aktivasi_wa':
                        $response->data = view('situgu/ks/profil/aktivasi_wa', $data);
                        break;
                    case 'aktivasi_telegram':
                        $response->data = view('situgu/ks/profil/ubah_aktivasi_telegram', $data);
                        break;

                    default:
                        $response->data = view('situgu/ks/profil/aktivasi_wa', $data);
                        break;
                }

                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
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
            'action' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Action tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('action');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('action'), true);

            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                return redirect()->to(base_url('auth'));
            }

            $current = $this->_db->table('_ptk_tb a')
                ->select("b.*, a.lampiran_foto, a.nama, a.nuptk, a.nip, a.nik, a.email as email_dapodik, a.no_hp as nohp_dapodik, c.role")
                ->join('v_user b', 'a.id_ptk = b.ptk_id', 'left')
                ->join('_role_user c', 'b.role_user = c.id', 'left')
                ->where('a.id_ptk', $user->data->ptk_id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                switch ($id) {
                    case 'password':
                        $response->data = view('situgu/ks/profil/password', $data);
                        break;
                    case 'foto':
                        $response->data = view('situgu/ks/profil/foto', $data);
                        break;
                    case 'aktivasi_email':
                        $response->data = view('situgu/ks/profil/aktivasi_email', $data);
                        break;

                    default:
                        $response->data = view('situgu/ks/profil/edit', $data);
                        break;
                }

                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function savePassword()
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
            'old_password' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Kata sandi lama tidak boleh kosong. ',
                ]
            ],
            'new_password' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Kata sandi baru tidak boleh kosong. ',
                ]
            ],
            're_new_password' => [
                'rules' => 'required|trim|matches[new_password]',
                'errors' => [
                    'required' => 'Ulangi kata sandi baru tidak boleh kosong. ',
                    'matches' => 'Ulangi kata sandi baru tidak sama dengan kata sandi baru. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('old_password')
                . $this->validator->getError('id')
                . $this->validator->getError('new_password')
                . $this->validator->getError('re_new_password');
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
            $old_password = htmlspecialchars($this->request->getVar('old_password'), true);
            $new_password = htmlspecialchars($this->request->getVar('new_password'), true);
            $re_new_password = htmlspecialchars($this->request->getVar('re_new_password'), true);

            $oldData =  $this->_db->table('_users_tb')->where('id', $user->data->id)->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            if (password_verify($old_password, $oldData->password) == false) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Password lama tidak sama.";
                return json_encode($response);
            }

            $data = [
                'password' => password_hash($new_password, PASSWORD_DEFAULT),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $this->_db->transBegin();
            try {
                $this->_db->table('_users_tb')->where('id', $oldData->id)->update($data);
            } catch (\Exception $e) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupdate password.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Password berhasil diupdate.";
                return json_encode($response);
            } else {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupate password";
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
                'rules' => 'required|trim|valid_email',
                'errors' => [
                    'required' => 'Email tidak boleh kosong. ',
                    'valid_email' => 'Email tidak valid. ',
                ]
            ],
            'nohp' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'No Handphone tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('nama')
                . $this->validator->getError('id')
                . $this->validator->getError('email')
                . $this->validator->getError('nohp');
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

            $oldData =  $this->_db->table('v_user')->where('id', $user->data->id)->get()->getRowObject();

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
            ) {
                $response = new \stdClass;
                $response->status = 201;
                $response->message = "Tidak ada perubahan data yang disimpan.";
                return json_encode($response);
            }

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $dataUser = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($nama !== $oldData->fullname) {
                $dataUser['fullname'] = $nama;
            }

            if ($email !== $oldData->email) {
                $data['email'] = $email;
                $data['email_verified'] = 0;
                $dataUser['email'] = $email;
            }

            if ($nohp !== $oldData->no_hp) {
                $dataUser['no_hp'] = $nohp;
                $data['wa_verified'] = 0;
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_users_tb')->where('id', $oldData->id)->update($data);
            } catch (\Exception $e) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupdate data.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                try {
                    $this->_db->table('_profil_users_tb')->where('id', $oldData->id)->update($dataUser);
                } catch (\Exception $e) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengupdate data.";
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
                    $response->message = "Gagal mengupate data";
                    return json_encode($response);
                }
            } else {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupate data";
                return json_encode($response);
            }
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
            'id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Id tidak boleh kosong. ',
                ]
            ],
            '_file' => [
                'rules' => 'uploaded[_file]|max_size[_file,512]|mime_in[_file,image/jpeg,image/jpg,image/png]',
                'errors' => [
                    'uploaded' => 'Pilih file terlebih dahulu. ',
                    'max_size' => 'Ukuran file terlalu besar, Maximum 500 Kb. ',
                    'mime_in' => 'Ekstensi yang anda upload harus berekstensi gambar. '
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
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

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $dir = FCPATH . "upload/user";
            $field_db = 'profile_picture';
            $table_db = '_profil_users_tb';

            $lampiran = $this->request->getFile('_file');
            $filesNamelampiran = $lampiran->getName();
            $newNamelampiran = $field_db . '/' . _create_name_file($filesNamelampiran);

            if ($lampiran->isValid() && !$lampiran->hasMoved()) {
                $minioClient = new MinioClient();

                // $filesNamelampiran = $lampiran->getName();

                // Contoh penamaan file unik di MinIO
                // $newNamelampiran = time() . '_' . $filesNamelampiran;

                // --- Konfigurasi MinIO ---
                $bucketName = 'situgu';
                // $field_db = 'file_path'; // Nama field di DB Anda

                // 3. Ambil Path Sementara File
                // MinIO SDK perlu path file sementara (temp file) untuk di-upload
                $tempFilePath = $lampiran->getTempName();

                // 4. Proses Upload ke MinIO
                // Panggil metode upload dari Minio_client
                $uploadResult = $minioClient->uploadFile(
                    $bucketName,
                    $newNamelampiran, // objectName
                    $tempFilePath,    // sourceFilePath
                    [
                        'x-amz-meta-uploader' => 'situgu-app',
                        'Content-Type' => $lampiran->getMimeType()
                    ]
                );

                if ($uploadResult) {
                    // Upload berhasil. $uploadResult berisi URL atau path object
                    $data[$field_db] = $newNamelampiran; // Simpan nama object MinIO di database

                    // Lanjutkan ke proses penyimpanan data di database

                    // $response = new \stdClass;
                    // $response->status = 200;
                    // $response->message = "File berhasil diupload ke MinIO.";
                    // $response->filePath = getDokumentPreviewStorage("situgu", $field_db . '/' . $newNamelampiran);
                    // return json_encode($response);
                } else {
                    // Gagal upload ke MinIO
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengupload file ke Storage.";
                    return json_encode($response);
                }

                // $lampiran->move($dir, $newNamelampiran);
                // $data[$field_db] = $newNamelampiran;
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupload file.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table($table_db)->where(['id' => $id])->update($data);
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

    public function getAktivasiEmail()
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

            if (!$user || $user->status !== 200) {
                session()->destroy();
                delete_cookie('jwt');
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session expired.";
                return json_encode($response);
            }

            if ($id == "wa") {
                $x['user'] = $user->data;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/ks/profil/wa', $x);
                return json_encode($response);
            } else if ($id == "email") {
                $x['user'] = $user->data;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/ks/profil/email', $x);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function kirimAktivasiEmail()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'email' => [
                'rules' => 'required|valid_email|trim',
                'errors' => [
                    'required' => 'Email tidak boleh kosong. ',
                    'valid_email' => 'Email tidak valid. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('email');
            return json_encode($response);
        } else {
            $email = htmlspecialchars($this->request->getVar('email'), true);

            $Profilelib = new Profilelib();
            $user = $Profilelib->user();

            if (!$user || $user->status !== 200) {
                session()->destroy();
                delete_cookie('jwt');
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session expired.";
                return json_encode($response);
            }

            $kode = rand(1000, 9999);

            $emailLib = new Emaillib();
            $sendEmail = $emailLib->sendActivation($email, $kode);

            if ($sendEmail->code == 200) {
                $x['user'] = $user->data;
                $x['email'] = $email;
                $x['kode_aktivasi'] = $kode;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/ks/profil/kode_email', $x);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengirim kode aktivasi.";
                return json_encode($response);
            }
        }
    }

    public function verifiAktivasiEmail()
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
            'email' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Email tidak boleh kosong. ',
                ]
            ],
            'kode' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Kode tidak boleh kosong. ',
                ]
            ],
            'fth' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Kode tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('email')
                . $this->validator->getError('kode')
                . $this->validator->getError('fth');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $email = htmlspecialchars($this->request->getVar('email'), true);
            $kode = htmlspecialchars($this->request->getVar('kode'), true);
            $fth = htmlspecialchars($this->request->getVar('fth'), true);

            $Profilelib = new Profilelib();
            $user = $Profilelib->user();

            if (!$user || $user->status !== 200) {
                session()->destroy();
                delete_cookie('jwt');
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session expired.";
                return json_encode($response);
            }

            if ($kode === $fth) {
                $this->_db->transBegin();
                try {
                    $date = date('Y-m-d H:i:s');
                    $this->_db->table('_profil_users_tb')->where('id', $user->data->id)->update([
                        'email' => $email,
                        'updated_at' => $date
                    ]);

                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->table('_users_tb')->where('id', $user->data->id)->update([
                            'email_verified' => 1,
                            'updated_at' => $date
                        ]);
                        if ($this->_db->affectedRows() > 0) {
                            $this->_db->transCommit();
                            $response = new \stdClass;
                            $response->status = 200;
                            $response->message = "Berhasil memverifikasi email.";
                            return json_encode($response);
                        } else {
                            $this->_db->transRollback();
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Gagal menautkan email.";
                            return json_encode($response);
                        }
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal menautkan email.";
                        return json_encode($response);
                    }
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal menautkan email.";
                    return json_encode($response);
                }
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Kode verifikasi salah.";
                return json_encode($response);
            }
        }
    }
}
