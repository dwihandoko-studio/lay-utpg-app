<?php

namespace App\Controllers\Situgu\Ptk\Masterdata;

use App\Controllers\BaseController;
// use App\Models\Situgu\Ptk\PtkModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;

class Dapodik extends BaseController
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
        return redirect()->to(base_url('situgu/ptk/masterdata/dapodik/data'));
    }

    public function data()
    {
        $data['title'] = 'PTK';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;
        $current = $this->_db->table('_ptk_tb a')
            ->select("a.*, b.no_hp as nohpAkun, b.email as emailAkun, b.wa_verified, b.image, c.kecamatan as kecamatan_sekolah")
            ->join('v_user b', 'a.id_ptk = b.ptk_id', 'left')
            ->join('ref_sekolah c', 'a.npsn = c.npsn')
            ->where('a.id_ptk', $user->data->ptk_id)->get()->getRowObject();

        if ($current) {
            $data['data'] = $current;
            $data['penugasans'] = $this->_db->table('_ptk_tb_dapodik a')
                ->select("a.*, b.npsn, b.nama as namaSekolah, b.kecamatan as kecamatan_sekolah, (SELECT SUM(jam_mengajar_per_minggu) FROM _pembelajaran_dapodik WHERE ptk_id = a.ptk_id AND sekolah_id = a.sekolah_id AND semester_id = a.semester_id) as jumlah_total_jam_mengajar_perminggu")
                ->join('ref_sekolah b', 'a.sekolah_id = b.id')
                ->where('a.ptk_id', $current->id_ptk)
                ->where("a.jenis_keluar IS NULL")
                ->orderBy('a.ptk_induk', 'DESC')->get()->getResult();
            return view('situgu/ptk/masterdata/dapodik/index', $data);
        } else {
            return view('situgu/ptk/404', $data);
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
                ->select("a.*, b.no_hp as nohpAkun, b.email as emailAkun, b.wa_verified, b.image")
                ->join('v_user b', 'a.id_ptk = b.ptk_id', 'left')
                ->where('a.id_ptk', $user->data->ptk_id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $data['ref_serti'] = ['Pendidikan Guru PAUD', 'Pendidikan Guru Sekolah Dasar', 'Pendidikan Luar Biasa', 'Seni Budaya', 'Pendidikan Jasmani dan Kesehatan', 'Bahasa Jawa', 'Bahasa Madura', 'Bahasa Sunda', 'Bahasa Bali', 'Bahasa Inggris', 'Ilmu Pengetahuan Sosial (IPS)', 'Ilmu Pengetahuan Alam (IPA)', 'Pendidikan Pancasila dan Kewarganegaraan (PKn)', 'Bahasa Indonesia', 'Matematika', 'Bimbingan Konseling', 'Geografi', 'Ekonomi', 'Sosiologi', 'Antropologi', 'Bahasa Jerman', 'Bahasa Perancis', 'Bahasa Arab', 'Bahasa Jepang', 'Bahasa Mandarin', 'Fisika', 'Kimia', 'Biologi', 'Sejarah', 'Teknologi Konstruksi dan Properti', 'Teknik Geomatika dan Geospasial', 'Teknik Ketenagalistrikan', 'Teknik Mesin', 'Teknologi Pesawat Udara', 'Teknik Grafika', 'Teknik Instrumentasi Industri', 'Teknik Industri', 'Teknologi Tekstil', 'Teknik Kimia', 'Teknik Otomotif', 'Teknik Perkapalan ', 'Teknik Elektronika', 'Teknik Perminyakan', 'Geologi Pertambangan', 'Teknik Energi Terbarukan', 'Teknik Komputer dan Informatika', 'Teknik Telekomunikasi', 'Keperawatan', 'Kesehatan Gigi', 'Teknologi Laboratorium Medik ', 'Farmasi', 'Pekerjaan Sosial', 'Agribisnis Tanaman', 'Agribisnis Ternak', 'Kesehatan Hewan', 'Agribisnis Pengolahan Hasil Pertanian', 'Teknik Pertanian', 'Kehutanan', 'Pelayaran Kapal Penangkapan Ikan', 'Pelayaran Kapal Niaga Perikanan', 'Pengolahan Hasil Perikanan', 'Bisnis dan Pemasaran', 'Manajemen Perkantoran', 'Akuntansi dan Keuangan', 'Logistik', 'Perhotelan dan Jasa Pariwisata', 'Kuliner', 'Tata Kecantikan', 'Tata Busana', 'Seni Rupa', 'Desain dan Produk Kreatif Kriya', 'Seni Musik', 'Seni Tari', 'Seni Karawitan', 'Seni Pedalangan', 'Seni Teater', 'Seni Broadcasting dan Film', 'Teknik Otomotif', 'Informatika/Teknik Komputer dan Informasi', 'Teknik Broadcasting', 'Prakarya/Desain dan Produk Kreatif Kriya'];
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/ptk/masterdata/dapodik/edit', $data);
                return json_encode($response);
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
            'nrg' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NRG tidak boleh kosong. ',
                ]
            ],
            'no_peserta' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'No Peserta tidak boleh kosong. ',
                ]
            ],
            'npwp' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NPWP tidak boleh kosong. ',
                ]
            ],
            'no_rekening' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'No Rekening tidak boleh kosong. ',
                ]
            ],
            'cabang_bank' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Cabang bank tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('nrg')
                . $this->validator->getError('id')
                . $this->validator->getError('no_peserta')
                . $this->validator->getError('npwp')
                . $this->validator->getError('no_rekening')
                . $this->validator->getError('cabang_bank');
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
            $nrg = htmlspecialchars($this->request->getVar('nrg'), true);
            $no_peserta = htmlspecialchars($this->request->getVar('no_peserta'), true);
            $npwp = htmlspecialchars($this->request->getVar('npwp'), true);
            $no_rekening = htmlspecialchars($this->request->getVar('no_rekening'), true);
            $cabang_bank = htmlspecialchars($this->request->getVar('cabang_bank'), true);
            $bidang_studi_sertifikasi = htmlspecialchars($this->request->getVar('bidang_studi_sertifikasi'), true);
            $pendidikan = htmlspecialchars($this->request->getVar('pendidikan'), true);

            $oldData =  $this->_db->table('_ptk_tb')->where('id_ptk', $id)->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            if ($bidang_studi_sertifikasi == NULL || $bidang_studi_sertifikasi == "") {
                if (
                    $nrg === $oldData->nrg
                    && $no_peserta === $oldData->no_peserta
                    && $npwp === $oldData->npwp
                    && $no_rekening === $oldData->no_rekening
                    && $cabang_bank === $oldData->cabang_bank
                ) {
                    $response = new \stdClass;
                    $response->status = 201;
                    $response->message = "Tidak ada perubahan data yang disimpan.";
                    return json_encode($response);
                }
            } else {
                if ($pendidikan == NULL || $pendidikan == "") {
                    if (
                        $nrg === $oldData->nrg
                        && $no_peserta === $oldData->no_peserta
                        && $npwp === $oldData->npwp
                        && $no_rekening === $oldData->no_rekening
                        && $cabang_bank === $oldData->cabang_bank
                        && $bidang_studi_sertifikasi === $oldData->bidang_studi_sertifikasi
                    ) {
                        $response = new \stdClass;
                        $response->status = 201;
                        $response->message = "Tidak ada perubahan data yang disimpan.";
                        return json_encode($response);
                    }
                } else {
                    if (
                        $nrg === $oldData->nrg
                        && $no_peserta === $oldData->no_peserta
                        && $npwp === $oldData->npwp
                        && $no_rekening === $oldData->no_rekening
                        && $cabang_bank === $oldData->cabang_bank
                        && $bidang_studi_sertifikasi === $oldData->bidang_studi_sertifikasi
                        && $pendidikan === $oldData->pendidikan
                    ) {
                        $response = new \stdClass;
                        $response->status = 201;
                        $response->message = "Tidak ada perubahan data yang disimpan.";
                        return json_encode($response);
                    }
                }
            }

            $data = [
                'nrg' => $nrg,
                'no_peserta' => $no_peserta,
                'npwp' => $npwp,
                'no_rekening' => $no_rekening,
                'cabang_bank' => $cabang_bank,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($bidang_studi_sertifikasi == NULL || $bidang_studi_sertifikasi == "") {
            } else {
                $data['bidang_studi_sertifikasi'] = $bidang_studi_sertifikasi;
            }

            if ($pendidikan == NULL || $pendidikan == "") {
            } else {
                $data['pendidikan'] = $pendidikan;
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_ptk_tb')->where('id_ptk', $oldData->id_ptk)->update($data);
            } catch (\Exception $e) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengupdate data.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                $this->_db->transCommit();
                createAktifitas($user->data->id, "Mengedit data PTK", "Mengedit Data PTK", "edit");
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
        }
    }
}
