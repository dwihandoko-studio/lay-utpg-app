<?php

namespace App\Controllers\Situgu\Ptk\Doc;

use App\Controllers\BaseController;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Uuid;

use App\Libraries\MinioClient;

class Master extends BaseController
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
        return redirect()->to(base_url('situgu/ptk/doc/master/data'));
    }

    public function data()
    {
        $data['title'] = 'DOKUMEN MASTER';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;
        $id = $this->_helpLib->getPtkId($user->data->id);
        $ptk = $this->_db->table('_ptk_tb')->where('id', $id)->get()->getRowObject();
        if (!$ptk) {
            return view('404', $data);
        }

        $data['ptk'] = $ptk;

        return view('situgu/ptk/doc/master/index', $data);
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
            'title' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Title tidak boleh kosong. ',
                ]
            ],
            'id_ptk' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('bulan')
                . $this->validator->getError('title')
                . $this->validator->getError('id_ptk');
            return json_encode($response);
        } else {
            $bulan = htmlspecialchars($this->request->getVar('bulan'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);
            $id_ptk = htmlspecialchars($this->request->getVar('id_ptk'), true);

            $data['bulan'] = $bulan;
            $data['title'] = $title;
            $data['id'] = $id_ptk;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ptk/doc/master/upload', $data);
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
            'id_ptk' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('bulan')
                . $this->validator->getError('title')
                . $this->validator->getError('old')
                . $this->validator->getError('id_ptk');
            return json_encode($response);
        } else {
            $bulan = htmlspecialchars($this->request->getVar('bulan'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);
            $old = htmlspecialchars($this->request->getVar('old'), true);
            $id_ptk = htmlspecialchars($this->request->getVar('id_ptk'), true);

            $data['bulan'] = $bulan;
            $data['title'] = $title;
            $data['old'] = $old;
            $data['id'] = $id_ptk;
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
                case 'doc_lainnya':
                    $data['old_url'] = base_url('upload/sekolah/doc-lainnya') . '/' . $old;
                    break;
                case 'pangkat':
                    $data['old_url'] = base_url('upload/ptk/pangkat') . '/' . $old;
                    break;
                case 'kgb':
                    $data['old_url'] = base_url('upload/ptk/kgb') . '/' . $old;
                    break;
                case 'pernyataan24':
                    $data['old_url'] = base_url('upload/ptk/pernyataanindividu') . '/' . $old;
                    break;
                case 'cuti_pensiun_kematian':
                    $data['old_url'] = base_url('upload/ptk/keterangancuti') . '/' . $old;
                    break;
                case 'attr_lainnya':
                    $data['old_url'] = base_url('upload/ptk/lainnya') . '/' . $old;
                    break;
                case 'foto':
                    $data['old_url'] = base_url('upload/ptk/foto') . '/' . $old;
                    break;
                case 'karpeg':
                    $data['old_url'] = base_url('upload/ptk/karpeg') . '/' . $old;
                    break;
                case 'ktp':
                    $data['old_url'] = base_url('upload/ptk/ktp') . '/' . $old;
                    break;
                case 'nrg':
                    $data['old_url'] = base_url('upload/ptk/nrg') . '/' . $old;
                    break;
                case 'nuptk':
                    $data['old_url'] = base_url('upload/ptk/nuptk') . '/' . $old;
                    break;
                case 'serdik':
                    $data['old_url'] = base_url('upload/ptk/serdik') . '/' . $old;
                    break;
                case 'npwp':
                    $data['old_url'] = base_url('upload/ptk/npwp') . '/' . $old;
                    break;
                case 'buku_rekening':
                    $data['old_url'] = base_url('upload/ptk/bukurekening') . '/' . $old;
                    break;
                case 'ijazah':
                    $data['old_url'] = base_url('upload/ptk/ijazah') . '/' . $old;
                    break;
                case 'inpassing':
                    $data['old_url'] = base_url('upload/ptk/impassing') . '/' . $old;
                    break;
                default:
                    $data['old_url'] = base_url('upload/sekolah/doc-lainnya') . '/' . $old;
                    break;
            }

            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/ptk/doc/master/editupload', $data);
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
            'id_ptk' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
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
                . $this->validator->getError('id_ptk')
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
            $id_ptk = htmlspecialchars($this->request->getVar('id_ptk'), true);

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $dir = "";
            $field_db = '';
            $table_db = '';

            switch ($name) {
                case 'bulan1':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen1';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'bulan2':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen2';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'bulan3':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen3';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'pembagian_tugas':
                    $dir = FCPATH . "upload/sekolah/pembagian-tugas";
                    $field_db = 'pembagian_tugas';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'slip_gaji':
                    $dir = FCPATH . "upload/sekolah/slip-gaji";
                    $field_db = 'slip_gaji';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'doc_lainnya':
                    $dir = FCPATH . "upload/sekolah/doc-lainnya";
                    $field_db = 'doc_lainnya';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'pangkat':
                    $dir = FCPATH . "upload/ptk/pangkat";
                    $field_db = 'pangkat_terakhir';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'kgb':
                    $dir = FCPATH . "upload/ptk/kgb";
                    $field_db = 'kgb_terakhir';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'pernyataan24':
                    $dir = FCPATH . "upload/ptk/pernyataanindividu";
                    $field_db = 'pernyataan_24jam';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'cuti_pensiun_kematian':
                    $dir = FCPATH . "upload/ptk/keterangancuti";
                    $field_db = 'cuti_pensiun_kematian';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'attr_lainnya':
                    $dir = FCPATH . "upload/ptk/lainnya";
                    $field_db = 'lainnya';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'foto':
                    $dir = FCPATH . "upload/ptk/foto";
                    $field_db = 'lampiran_foto';
                    $table_db = '_ptk_tb';
                    break;
                case 'karpeg':
                    $dir = FCPATH . "upload/ptk/karpeg";
                    $field_db = 'lampiran_karpeg';
                    $table_db = '_ptk_tb';
                    break;
                case 'ktp':
                    $dir = FCPATH . "upload/ptk/ktp";
                    $field_db = 'lampiran_ktp';
                    $table_db = '_ptk_tb';
                    break;
                case 'nrg':
                    $dir = FCPATH . "upload/ptk/nrg";
                    $field_db = 'lampiran_nrg';
                    $table_db = '_ptk_tb';
                    break;
                case 'nuptk':
                    $dir = FCPATH . "upload/ptk/nuptk";
                    $field_db = 'lampiran_nuptk';
                    $table_db = '_ptk_tb';
                    break;
                case 'serdik':
                    $dir = FCPATH . "upload/ptk/serdik";
                    $field_db = 'lampiran_serdik';
                    $table_db = '_ptk_tb';
                    break;
                case 'npwp':
                    $dir = FCPATH . "upload/ptk/npwp";
                    $field_db = 'lampiran_npwp';
                    $table_db = '_ptk_tb';
                    break;
                case 'buku_rekening':
                    $dir = FCPATH . "upload/ptk/bukurekening";
                    $field_db = 'lampiran_buku_rekening';
                    $table_db = '_ptk_tb';
                    break;
                case 'ijazah':
                    $dir = FCPATH . "upload/ptk/ijazah";
                    $field_db = 'lampiran_ijazah';
                    $table_db = '_ptk_tb';
                    break;
                case 'inpassing':
                    $dir = FCPATH . "upload/ptk/impassing";
                    $field_db = 'lampiran_impassing';
                    $table_db = '_ptk_tb';
                    break;
                default:
                    $dir = FCPATH . "upload/sekolah/doc-lainnya";
                    $field_db = 'doc_lainnya';
                    $table_db = '_absen_kehadiran';
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
                $this->_db->table($table_db)->where("id = '$id_ptk' AND (is_locked = 0 OR is_locked IS NULL)")->update($data);
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
                createAktifitas($user->data->id, "Mengupload lampiran data master pada lampiran $field_db", "Mengupload Lampiran Master $field_db", "upload");
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
            'old' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Old tidak boleh kosong. ',
                ]
            ],
            'id_ptk' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
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
                . $this->validator->getError('old')
                . $this->validator->getError('id_ptk')
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
            $old = htmlspecialchars($this->request->getVar('old'), true);
            $id_ptk = htmlspecialchars($this->request->getVar('id_ptk'), true);

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $dir = "";
            $field_db = '';
            $table_db = '';

            switch ($name) {
                case 'bulan1':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen1';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'bulan2':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen2';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'bulan3':
                    $dir = FCPATH . "upload/sekolah/kehadiran";
                    $field_db = 'lampiran_absen3';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'pembagian_tugas':
                    $dir = FCPATH . "upload/sekolah/pembagian-tugas";
                    $field_db = 'pembagian_tugas';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'slip_gaji':
                    $dir = FCPATH . "upload/sekolah/slip-gaji";
                    $field_db = 'slip_gaji';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'doc_lainnya':
                    $dir = FCPATH . "upload/sekolah/doc-lainnya";
                    $field_db = 'doc_lainnya';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'pangkat':
                    $dir = FCPATH . "upload/ptk/pangkat";
                    $field_db = 'pangkat_terakhir';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'kgb':
                    $dir = FCPATH . "upload/ptk/kgb";
                    $field_db = 'kgb_terakhir';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'pernyataan24':
                    $dir = FCPATH . "upload/ptk/pernyataanindividu";
                    $field_db = 'pernyataan_24jam';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'cuti_pensiun_kematian':
                    $dir = FCPATH . "upload/ptk/keterangancuti";
                    $field_db = 'cuti_pensiun_kematian';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'attr_lainnya':
                    $dir = FCPATH . "upload/ptk/lainnya";
                    $field_db = 'lainnya';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'foto':
                    $dir = FCPATH . "upload/ptk/foto";
                    $field_db = 'lampiran_foto';
                    $table_db = '_ptk_tb';
                    break;
                case 'karpeg':
                    $dir = FCPATH . "upload/ptk/karpeg";
                    $field_db = 'lampiran_karpeg';
                    $table_db = '_ptk_tb';
                    break;
                case 'ktp':
                    $dir = FCPATH . "upload/ptk/ktp";
                    $field_db = 'lampiran_ktp';
                    $table_db = '_ptk_tb';
                    break;
                case 'nrg':
                    $dir = FCPATH . "upload/ptk/nrg";
                    $field_db = 'lampiran_nrg';
                    $table_db = '_ptk_tb';
                    break;
                case 'nuptk':
                    $dir = FCPATH . "upload/ptk/nuptk";
                    $field_db = 'lampiran_nuptk';
                    $table_db = '_ptk_tb';
                    break;
                case 'serdik':
                    $dir = FCPATH . "upload/ptk/serdik";
                    $field_db = 'lampiran_serdik';
                    $table_db = '_ptk_tb';
                    break;
                case 'npwp':
                    $dir = FCPATH . "upload/ptk/npwp";
                    $field_db = 'lampiran_npwp';
                    $table_db = '_ptk_tb';
                    break;
                case 'buku_rekening':
                    $dir = FCPATH . "upload/ptk/bukurekening";
                    $field_db = 'lampiran_buku_rekening';
                    $table_db = '_ptk_tb';
                    break;
                case 'ijazah':
                    $dir = FCPATH . "upload/ptk/ijazah";
                    $field_db = 'lampiran_ijazah';
                    $table_db = '_ptk_tb';
                    break;
                case 'inpassing':
                    $dir = FCPATH . "upload/ptk/impassing";
                    $field_db = 'lampiran_impassing';
                    $table_db = '_ptk_tb';
                    break;
                default:
                    $dir = FCPATH . "upload/sekolah/doc-lainnya";
                    $field_db = 'doc_lainnya';
                    $table_db = '_absen_kehadiran';
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
                // $this->_db->table($table_db)->where(['id' => $id_ptk, 'is_locked' => 0])->update($data);
                $this->_db->table($table_db)->where("id = '$id_ptk' AND (is_locked = 0 OR is_locked IS NULL)")->update($data);
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
                createAktifitas($user->data->id, "Mengedit upload lampiran data master pada lampiran $field_db", "Mengedit Upload Lampiran Master $field_db", "edit");
                $this->_db->transCommit();
                try {
                    if (strpos($old, '/') !== false) {
                        $minioClient = new MinioClient();
                        $isDeleted = $minioClient->deleteObject("situgu", $old);
                    } else {
                        unlink($dir . '/' . $old);
                    }
                } catch (\Throwable $th) {
                }

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

    public function hapusfile()
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
            'id_ptk' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Id PTK tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('bulan')
                . $this->validator->getError('title')
                . $this->validator->getError('old')
                . $this->validator->getError('id_ptk');
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

            $bulan = htmlspecialchars($this->request->getVar('bulan'), true);
            $title = htmlspecialchars($this->request->getVar('title'), true);
            $id_ptk = htmlspecialchars($this->request->getVar('id_ptk'), true);

            switch ($bulan) {
                case 'nrg':
                    $dir = FCPATH . "upload/ptk/nrg";
                    $field_db = 'lampiran_nrg';
                    $table_db = '_ptk_tb';
                    break;
                case 'nuptk':
                    $dir = FCPATH . "upload/ptk/nuptk";
                    $field_db = 'lampiran_nuptk';
                    $table_db = '_ptk_tb';
                    break;
                case 'serdik':
                    $dir = FCPATH . "upload/ptk/serdik";
                    $field_db = 'lampiran_serdik';
                    $table_db = '_ptk_tb';
                    break;
                case 'inpassing':
                    $dir = FCPATH . "upload/ptk/impassing";
                    $field_db = 'lampiran_impassing';
                    $table_db = '_ptk_tb';
                    break;
                default:
                    $dir = "";
                    $field_db = '';
                    $table_db = '';
                    break;
            }

            $currentFile = $this->_db->table($table_db)->select("$field_db AS file, id")->where(['id' => $id_ptk, 'is_locked' => 0])->get()->getRowObject();
            if (!$currentFile) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menghapus file. Data tidak ditemukan.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table($table_db)->where(['id' => $id_ptk, 'is_locked' => 0])->update([$field_db => null, 'updated_at' => date('Y-m-d H:i:s')]);
            } catch (\Exception $e) {
                $this->_db->transRollback();

                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($e);
                $response->message = "Gagal menghapus file lampiran $title.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                createAktifitas($user->data->id, "Menghapus lampiran data master pada lampiran $field_db", "Menghapus Lampiran Master $field_db", "delete");
                $this->_db->transCommit();
                try {
                    $minioClient = new MinioClient();
                    $isDeleted = $minioClient->deleteObject("situgu", $currentFile->file);
                    unlink($dir . '/' . $currentFile->file);
                } catch (\Throwable $th) {
                }

                $response = new \stdClass;
                $response->status = 200;
                $response->message = "File lampiran $title berhasil dihapus.";
                return json_encode($response);
            } else {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menghapus file lampiran $title";
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

    // public function syncAll()
    // {
    //     if ($this->request->getMethod() != 'post') {
    //         $response = new \stdClass;
    //         $response->status = 400;
    //         $response->message = "Permintaan tidak diizinkan";
    //         return json_encode($response);
    //     }

    //     $rules = [
    //         'npsn' => [
    //             'rules' => 'required|trim',
    //             'errors' => [
    //                 'required' => 'NPSN tidak boleh kosong. ',
    //             ]
    //         ],
    //     ];

    //     if (!$this->validate($rules)) {
    //         $response = new \stdClass;
    //         $response->status = 400;
    //         $response->message = $this->validator->getError('npsn');
    //         return json_encode($response);
    //     } else {
    //         $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

    //         $apiLib = new Apilib();
    //         $result = $apiLib->syncPtk($npsn);

    //         if ($result) {
    //             if ($result->status == 200) {
    //                 $response = new \stdClass;
    //                 $response->status = 200;
    //                 $response->message = "Syncrone Data Semua PTK Berhasil Dilakukan.";
    //                 return json_encode($response);
    //             } else {
    //                 $response = new \stdClass;
    //                 $response->status = 400;
    //                 $response->message = "Gagal Syncrone Data";
    //                 return json_encode($response);
    //             }
    //         } else {
    //             $response = new \stdClass;
    //             $response->status = 400;
    //             $response->message = "Gagal Syncrone Data";
    //             return json_encode($response);
    //         }
    //     }
    // }

    // public function sync()
    // {
    //     if ($this->request->getMethod() != 'post') {
    //         $response = new \stdClass;
    //         $response->status = 400;
    //         $response->message = "Permintaan tidak diizinkan";
    //         return json_encode($response);
    //     }

    //     $rules = [
    //         'ptk_id' => [
    //             'rules' => 'required|trim',
    //             'errors' => [
    //                 'required' => 'Id PTK tidak boleh kosong. ',
    //             ]
    //         ],
    //         'nama' => [
    //             'rules' => 'required|trim',
    //             'errors' => [
    //                 'required' => 'Nama tidak boleh kosong. ',
    //             ]
    //         ],
    //         'npsn' => [
    //             'rules' => 'required|trim',
    //             'errors' => [
    //                 'required' => 'NPSN tidak boleh kosong. ',
    //             ]
    //         ],
    //     ];

    //     if (!$this->validate($rules)) {
    //         $response = new \stdClass;
    //         $response->status = 400;
    //         $response->message = $this->validator->getError('ptk_id')
    //             . $this->validator->getError('nama')
    //             . $this->validator->getError('npsn');
    //         return json_encode($response);
    //     } else {
    //         $idPtk = htmlspecialchars($this->request->getVar('ptk_id'), true);
    //         $nama = htmlspecialchars($this->request->getVar('nama'), true);
    //         $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

    //         $apiLib = new Apilib();
    //         $result = $apiLib->syncPtkId($idPtk, $npsn);

    //         if ($result) {
    //             if ($result->status == 200) {
    //                 $response = new \stdClass;
    //                 $response->status = 200;
    //                 $response->message = "Syncrone Data PTK $nama Berhasil Dilakukan.";
    //                 return json_encode($response);
    //             } else {
    //                 $response = new \stdClass;
    //                 $response->status = 400;
    //                 $response->message = "Gagal Syncrone Data";
    //                 return json_encode($response);
    //             }
    //         } else {
    //             $response = new \stdClass;
    //             $response->status = 400;
    //             $response->message = "Gagal Syncrone Data";
    //             return json_encode($response);
    //         }
    //     }
    // }

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
