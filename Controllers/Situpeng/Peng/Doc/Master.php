<?php

namespace App\Controllers\Situpeng\Peng\Doc;

use App\Controllers\BaseController;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Uuid;

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
        return redirect()->to(base_url('situpeng/peng/doc/master/data'));
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
        $ptk = $this->_db->table('__pengawas_tb')->where('id', $user->data->ptk_id)->get()->getRowObject();
        if (!$ptk) {
            return view('404', $data);
        }

        $data['ptk'] = $ptk;

        return view('situpeng/peng/doc/master/index', $data);
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
            $response->data = view('situpeng/peng/doc/master/upload', $data);
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
                    $data['old_url'] = base_url('upload/pengawas/kehadiran') . '/' . $old;
                    break;
                case 'bulan2':
                    $data['old_url'] = base_url('upload/pengawas/kehadiran') . '/' . $old;
                    break;
                case 'bulan3':
                    $data['old_url'] = base_url('upload/pengawas/kehadiran') . '/' . $old;
                    break;
                case 'pembagian_tugas':
                    $data['old_url'] = base_url('upload/pengawas/pembagian-tugas') . '/' . $old;
                    break;
                case 'slip_gaji':
                    $data['old_url'] = base_url('upload/pengawas/slip-gaji') . '/' . $old;
                    break;
                case 'doc_lainnya':
                    $data['old_url'] = base_url('upload/pengawas/doc-lainnya') . '/' . $old;
                    break;
                case 'pangkat':
                    $data['old_url'] = base_url('upload/pengawas/pangkat') . '/' . $old;
                    break;
                case 'kgb':
                    $data['old_url'] = base_url('upload/pengawas/kgb') . '/' . $old;
                    break;
                case 'pernyataan24':
                    $data['old_url'] = base_url('upload/pengawas/pernyataanindividu') . '/' . $old;
                    break;
                case 'cuti_pensiun_kematian':
                    $data['old_url'] = base_url('upload/pengawas/keterangancuti') . '/' . $old;
                    break;
                case 'attr_lainnya':
                    $data['old_url'] = base_url('upload/pengawas/lainnya') . '/' . $old;
                    break;
                case 'foto':
                    $data['old_url'] = base_url('upload/pengawas/foto') . '/' . $old;
                    break;
                case 'karpeg':
                    $data['old_url'] = base_url('upload/pengawas/karpeg') . '/' . $old;
                    break;
                case 'ktp':
                    $data['old_url'] = base_url('upload/pengawas/ktp') . '/' . $old;
                    break;
                case 'nrg':
                    $data['old_url'] = base_url('upload/pengawas/nrg') . '/' . $old;
                    break;
                case 'nuptk':
                    $data['old_url'] = base_url('upload/pengawas/nuptk') . '/' . $old;
                    break;
                case 'serdik':
                    $data['old_url'] = base_url('upload/pengawas/serdik') . '/' . $old;
                    break;
                case 'serpeng':
                    $data['old_url'] = base_url('upload/pengawas/serpeng') . '/' . $old;
                    break;
                case 'sk80':
                    $data['old_url'] = base_url('upload/pengawas/sk80') . '/' . $old;
                    break;
                case 'sk100':
                    $data['old_url'] = base_url('upload/pengawas/sk100') . '/' . $old;
                    break;
                case 'npwp':
                    $data['old_url'] = base_url('upload/pengawas/npwp') . '/' . $old;
                    break;
                case 'buku_rekening':
                    $data['old_url'] = base_url('upload/pengawas/bukurekening') . '/' . $old;
                    break;
                case 'sk_jabfung':
                    $data['old_url'] = base_url('upload/pengawas/skjabfung') . '/' . $old;
                    break;
                case 'ijazah':
                    $data['old_url'] = base_url('upload/pengawas/ijazah') . '/' . $old;
                    break;
                case 'inpassing':
                    $data['old_url'] = base_url('upload/pengawas/impassing') . '/' . $old;
                    break;
                default:
                    $data['old_url'] = base_url('upload/pengawas/doc-lainnya') . '/' . $old;
                    break;
            }

            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situpeng/peng/doc/master/editupload', $data);
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
                    $dir = FCPATH . "upload/pengawas/kehadiran";
                    $field_db = 'lampiran_absen1';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'bulan2':
                    $dir = FCPATH . "upload/pengawas/kehadiran";
                    $field_db = 'lampiran_absen2';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'bulan3':
                    $dir = FCPATH . "upload/pengawas/kehadiran";
                    $field_db = 'lampiran_absen3';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'pembagian_tugas':
                    $dir = FCPATH . "upload/pengawas/pembagian-tugas";
                    $field_db = 'pembagian_tugas';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'slip_gaji':
                    $dir = FCPATH . "upload/pengawas/slip-gaji";
                    $field_db = 'slip_gaji';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'doc_lainnya':
                    $dir = FCPATH . "upload/pengawas/doc-lainnya";
                    $field_db = 'doc_lainnya';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'pangkat':
                    $dir = FCPATH . "upload/pengawas/pangkat";
                    $field_db = 'pangkat_terakhir';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'kgb':
                    $dir = FCPATH . "upload/pengawas/kgb";
                    $field_db = 'kgb_terakhir';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'pernyataan24':
                    $dir = FCPATH . "upload/pengawas/pernyataanindividu";
                    $field_db = 'pernyataan_24jam';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'cuti_pensiun_kematian':
                    $dir = FCPATH . "upload/pengawas/keterangancuti";
                    $field_db = 'cuti_pensiun_kematian';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'attr_lainnya':
                    $dir = FCPATH . "upload/pengawas/lainnya";
                    $field_db = 'lainnya';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'foto':
                    $dir = FCPATH . "upload/pengawas/foto";
                    $field_db = 'lampiran_foto';
                    $table_db = '__pengawas_tb';
                    break;
                case 'karpeg':
                    $dir = FCPATH . "upload/pengawas/karpeg";
                    $field_db = 'lampiran_karpeg';
                    $table_db = '__pengawas_tb';
                    break;
                case 'ktp':
                    $dir = FCPATH . "upload/pengawas/ktp";
                    $field_db = 'lampiran_ktp';
                    $table_db = '__pengawas_tb';
                    break;
                case 'nrg':
                    $dir = FCPATH . "upload/pengawas/nrg";
                    $field_db = 'lampiran_nrg';
                    $table_db = '__pengawas_tb';
                    break;
                case 'nuptk':
                    $dir = FCPATH . "upload/pengawas/nuptk";
                    $field_db = 'lampiran_nuptk';
                    $table_db = '__pengawas_tb';
                    break;
                case 'serdik':
                    $dir = FCPATH . "upload/pengawas/serdik";
                    $field_db = 'lampiran_serdik';
                    $table_db = '__pengawas_tb';
                    break;
                case 'serpeng':
                    $dir = FCPATH . "upload/pengawas/serpeng";
                    $field_db = 'lampiran_serpeng';
                    $table_db = '__pengawas_tb';
                    break;
                case 'sk80':
                    $dir = FCPATH . "upload/pengawas/sk80";
                    $field_db = 'lampiran_sk80';
                    $table_db = '__pengawas_tb';
                    break;
                case 'sk100':
                    $dir = FCPATH . "upload/pengawas/sk100";
                    $field_db = 'lampiran_sk100';
                    $table_db = '__pengawas_tb';
                    break;
                case 'npwp':
                    $dir = FCPATH . "upload/pengawas/npwp";
                    $field_db = 'lampiran_npwp';
                    $table_db = '__pengawas_tb';
                    break;
                case 'buku_rekening':
                    $dir = FCPATH . "upload/pengawas/bukurekening";
                    $field_db = 'lampiran_buku_rekening';
                    $table_db = '__pengawas_tb';
                    break;
                case 'sk_jabfung':
                    $dir = FCPATH . "upload/pengawas/skjabfung";
                    $field_db = 'lampiran_sk_jabfung';
                    $table_db = '__pengawas_tb';
                    break;
                case 'ijazah':
                    $dir = FCPATH . "upload/pengawas/ijazah";
                    $field_db = 'lampiran_ijazah';
                    $table_db = '__pengawas_tb';
                    break;
                case 'inpassing':
                    $dir = FCPATH . "upload/pengawas/impassing";
                    $field_db = 'lampiran_impassing';
                    $table_db = '__pengawas_tb';
                    break;
                default:
                    $dir = FCPATH . "upload/pengawas/doc-lainnya";
                    $field_db = 'doc_lainnya';
                    $table_db = '_absen_kehadiran';
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
                $this->_db->table($table_db)->where("id = '$id_ptk' AND (is_locked = 0 OR is_locked IS NULL)")->update($data);
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
                    $dir = FCPATH . "upload/pengawas/kehadiran";
                    $field_db = 'lampiran_absen1';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'bulan2':
                    $dir = FCPATH . "upload/pengawas/kehadiran";
                    $field_db = 'lampiran_absen2';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'bulan3':
                    $dir = FCPATH . "upload/pengawas/kehadiran";
                    $field_db = 'lampiran_absen3';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'pembagian_tugas':
                    $dir = FCPATH . "upload/pengawas/pembagian-tugas";
                    $field_db = 'pembagian_tugas';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'slip_gaji':
                    $dir = FCPATH . "upload/pengawas/slip-gaji";
                    $field_db = 'slip_gaji';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'doc_lainnya':
                    $dir = FCPATH . "upload/pengawas/doc-lainnya";
                    $field_db = 'doc_lainnya';
                    $table_db = '_absen_kehadiran';
                    break;
                case 'pangkat':
                    $dir = FCPATH . "upload/pengawas/pangkat";
                    $field_db = 'pangkat_terakhir';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'kgb':
                    $dir = FCPATH . "upload/pengawas/kgb";
                    $field_db = 'kgb_terakhir';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'pernyataan24':
                    $dir = FCPATH . "upload/pengawas/pernyataanindividu";
                    $field_db = 'pernyataan_24jam';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'cuti_pensiun_kematian':
                    $dir = FCPATH . "upload/pengawas/keterangancuti";
                    $field_db = 'cuti_pensiun_kematian';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'attr_lainnya':
                    $dir = FCPATH . "upload/pengawas/lainnya";
                    $field_db = 'lainnya';
                    $table_db = '_upload_data_attribut';
                    break;
                case 'foto':
                    $dir = FCPATH . "upload/pengawas/foto";
                    $field_db = 'lampiran_foto';
                    $table_db = '__pengawas_tb';
                    break;
                case 'karpeg':
                    $dir = FCPATH . "upload/pengawas/karpeg";
                    $field_db = 'lampiran_karpeg';
                    $table_db = '__pengawas_tb';
                    break;
                case 'ktp':
                    $dir = FCPATH . "upload/pengawas/ktp";
                    $field_db = 'lampiran_ktp';
                    $table_db = '__pengawas_tb';
                    break;
                case 'nrg':
                    $dir = FCPATH . "upload/pengawas/nrg";
                    $field_db = 'lampiran_nrg';
                    $table_db = '__pengawas_tb';
                    break;
                case 'nuptk':
                    $dir = FCPATH . "upload/pengawas/nuptk";
                    $field_db = 'lampiran_nuptk';
                    $table_db = '__pengawas_tb';
                    break;
                case 'serdik':
                    $dir = FCPATH . "upload/pengawas/serdik";
                    $field_db = 'lampiran_serdik';
                    $table_db = '__pengawas_tb';
                    break;
                case 'serpeng':
                    $dir = FCPATH . "upload/pengawas/serpeng";
                    $field_db = 'lampiran_serpeng';
                    $table_db = '__pengawas_tb';
                    break;
                case 'sk80':
                    $dir = FCPATH . "upload/pengawas/sk80";
                    $field_db = 'lampiran_sk80';
                    $table_db = '__pengawas_tb';
                    break;
                case 'sk100':
                    $dir = FCPATH . "upload/pengawas/sk100";
                    $field_db = 'lampiran_sk100';
                    $table_db = '__pengawas_tb';
                    break;
                case 'npwp':
                    $dir = FCPATH . "upload/pengawas/npwp";
                    $field_db = 'lampiran_npwp';
                    $table_db = '__pengawas_tb';
                    break;
                case 'buku_rekening':
                    $dir = FCPATH . "upload/pengawas/bukurekening";
                    $field_db = 'lampiran_buku_rekening';
                    $table_db = '__pengawas_tb';
                    break;
                case 'sk_jabfung':
                    $dir = FCPATH . "upload/pengawas/skjabfung";
                    $field_db = 'lampiran_sk_jabfung';
                    $table_db = '__pengawas_tb';
                    break;
                case 'ijazah':
                    $dir = FCPATH . "upload/pengawas/ijazah";
                    $field_db = 'lampiran_ijazah';
                    $table_db = '__pengawas_tb';
                    break;
                case 'inpassing':
                    $dir = FCPATH . "upload/pengawas/impassing";
                    $field_db = 'lampiran_impassing';
                    $table_db = '__pengawas_tb';
                    break;
                default:
                    $dir = FCPATH . "upload/pengawas/doc-lainnya";
                    $field_db = 'doc_lainnya';
                    $table_db = '_absen_kehadiran';
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
                // $this->_db->table($table_db)->where(['id' => $id_ptk, 'is_locked' => 0])->update($data);
                $this->_db->table($table_db)->where("id = '$id_ptk' AND (is_locked = 0 OR is_locked IS NULL)")->update($data);
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
                    $dir = FCPATH . "upload/pengawas/nrg";
                    $field_db = 'lampiran_nrg';
                    $table_db = '__pengawas_tb';
                    break;
                case 'nuptk':
                    $dir = FCPATH . "upload/pengawas/nuptk";
                    $field_db = 'lampiran_nuptk';
                    $table_db = '__pengawas_tb';
                    break;
                case 'serdik':
                    $dir = FCPATH . "upload/pengawas/serdik";
                    $field_db = 'lampiran_serdik';
                    $table_db = '__pengawas_tb';
                    break;
                case 'serpeng':
                    $dir = FCPATH . "upload/pengawas/serpeng";
                    $field_db = 'lampiran_serpeng';
                    $table_db = '__pengawas_tb';
                    break;
                case 'sk80':
                    $dir = FCPATH . "upload/pengawas/sk80";
                    $field_db = 'lampiran_sk80';
                    $table_db = '__pengawas_tb';
                    break;
                case 'sk100':
                    $dir = FCPATH . "upload/pengawas/sk100";
                    $field_db = 'lampiran_sk100';
                    $table_db = '__pengawas_tb';
                    break;
                case 'sk_jabfung':
                    $dir = FCPATH . "upload/pengawas/skjabfung";
                    $field_db = 'lampiran_sk_jabfung';
                    $table_db = '__pengawas_tb';
                    break;
                case 'inpassing':
                    $dir = FCPATH . "upload/pengawas/impassing";
                    $field_db = 'lampiran_impassing';
                    $table_db = '__pengawas_tb';
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
                $this->_db->transCommit();
                try {
                    unlink($dir . '/' . $currentFile->file);
                } catch (\Throwable $th) {
                    //throw $th;
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
}
