<?php

namespace App\Controllers\Situpeng\Peng;

use App\Controllers\BaseController;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Uuid;

class Ajuan extends BaseController
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
        return redirect()->to(base_url('situpeng/peng/ajuan/data'));
    }

    public function data()
    {
        $data['title'] = 'AJUKAN USULAN TUNJANGAN';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }
        $id = $this->_helpLib->getPengawasId($user->data->id);
        $data['user'] = $user->data;
        $data['tw'] = $this->_db->table('_ref_tahun_tw')->where('is_current', 1)->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getRowObject();
        $data['data_antrian_tpg'] = $this->_db->table('_tb_usulan_detail_tpg_pengawas')->where(['id_tahun_tw' => $data['tw']->id, 'id_pengawas' => $id])->orderBy('created_at', 'desc')->get()->getRowObject();
        $data['data_antrian_tpg_transfer'] = $this->_db->table('_tb_usulan_tpg_siap_sk_pengawas')->where(['id_tahun_tw' => $data['tw']->id, 'id_pengawas' => $id])->orderBy('created_at', 'desc')->get()->getRowObject();
        $aa = $this->_db->table('_tb_temp_usulan_detail_pengawas')->where(['id_tahun_tw' => $data['tw']->id, 'id_pengawas' => $id])->orderBy('created_at', 'desc')->get()->getResult();
        if (count($aa) > 0) {
            if ($aa[0]->status_usulan == 1) {
                // var_dump($data['data_antrian_tamsil']);
                // die;
                if ($data['data_antrian_tpg'] || $data['data_antrian_tpg_transfer']) {
                    $data['data'] = false;
                } else {
                    $data['data'] = $aa[0];
                }
            } else {
                $data['data'] = $aa[0];
            }
        } else {
            $data['data'] = false;
        }

        return view('situpeng/peng/ajuan/index', $data);
    }

    public function aksiajukan()
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
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Tw tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('action')
                . $this->validator->getError('tw');
            return json_encode($response);
        } else {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session telah habis";
                $response->redirect = base_url('auth');
                return json_encode($response);
            }

            $tw = htmlspecialchars($this->request->getVar('tw'), true);

            $id = $this->_helpLib->getPengawasId($user->data->id);

            $canGrantedPengajuan = canGrantedPengajuan($id, $tw);

            if ($canGrantedPengajuan && $canGrantedPengajuan->code !== 200) {
                return json_encode($canGrantedPengajuan);
            }

            // $oldDataAbsen = $this->_db->table('_absen_kehadiran')->where(['id_ptk' => $id, 'id_tahun_tw' => $tw])->orderBy('created_at', 'desc')->get()->getRowObject();
            // if (!$oldDataAbsen) {
            //     $response = new \stdClass;
            //     $response->status = 201;
            //     $response->message = "Anda tidak dapat mengajukan Usulan Tujangan, dikarenakan Admin Sekolah belum melengkapi/mengunggah dokumen Absensi Kehadiran. Silahkan Hubungi Admin Sekolah.";
            //     return json_encode($response);
            // }

            // if (((int)$oldDataAbsen->bulan_1 + (int)$oldDataAbsen->bulan_2 + (int)$oldDataAbsen->bulan_3) < 1) {
            //     $response = new \stdClass;
            //     $response->status = 201;
            //     $response->message = "Anda tidak dapat mengajukan Usulan Tujangan, dikarenakan Admin Sekolah belum melengkapi/mengunggah dokumen Absensi Kehadiran. Silahkan Hubungi Admin Sekolah.";
            //     return json_encode($response);
            // }
            // if (!$oldDataAbsen->lampiran_absen1 || !$oldDataAbsen->lampiran_absen2 || !$oldDataAbsen->lampiran_absen3 || !$oldDataAbsen->pembagian_tugas || !$oldDataAbsen->slip_gaji) {
            //     $response = new \stdClass;
            //     $response->status = 201;
            //     $response->message = "Silahkan lengkapi dokumen absensi anda.";
            //     return json_encode($response);
            // }
            $data['tw'] = $this->_db->table('_ref_tahun_tw')->where('id', $tw)->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getRowObject();
            // $data['absen'] = $oldDataAbsen;
            $pengawas = $this->_db->table('__pengawas_tb')->where('id', $id)->get()->getRowObject();

            if (!$pengawas) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data Pengawas Tidak Ditemukan.";
                return json_encode($response);
            }
            $data['pengawas'] = $pengawas;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situpeng/peng/ajuan/pilihan', $data);
            return json_encode($response);
        }
    }

    public function getValidationAjuan()
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
                    'required' => 'Jenis tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Tw tidak boleh kosong. ',
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
            $response->message = $this->validator->getError('action')
                . $this->validator->getError('id_ptk')
                . $this->validator->getError('tw');
            return json_encode($response);
        } else {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session telah habis";
                $response->redirect = base_url('auth');
                return json_encode($response);
            }

            $tw = htmlspecialchars($this->request->getVar('tw'), true);
            $jenis_tunjangan = htmlspecialchars($this->request->getVar('id'), true);
            $id_ptk = htmlspecialchars($this->request->getVar('id_ptk'), true);

            $ptk = $this->_db->table('__pengawas_upload_data_attribut a')
                ->select("b.*, a.id_tahun_tw, a.pang_jenis, a.pang_golongan, a.pang_no, a.pang_tmt, a.pang_tgl, a.pang_tahun, a.pang_bulan, a.pangkat_terakhir as lampiran_pangkat, a.kgb_terakhir as lampiran_kgb, a.pernyataan_24jam as lampiran_pernyataan24, a.penugasan as lampiran_penugasan, a.kunjungan_binaan as lampiran_kunjungan_binaan, a.cuti as lampiran_cuti, a.pensiun as lampiran_pensiun, a.kematian as lampiran_kematian, a.lainnya as lampiran_att_lain")
                ->join('__pengawas_tb b', 'a.id_ptk = b.id')
                // ->join('_absen_kehadiran c', 'a.id_ptk = c.id_ptk AND c.id_tahun_tw = a.id_tahun_tw')
                ->where(['a.id_ptk' => $id_ptk, 'a.id_tahun_tw' => $tw])
                ->get()->getRowObject();

            if (!$ptk) {
                $response = new \stdClass;
                $response->status = 404;
                $response->message = "Atribut Dokumen Terbaru tidak ditemukan, Silahkan untuk melengkapi terlebih dahulu.";
                $response->redirrect = base_url("situpeng/peng/doc/atribut");
                return json_encode($response);
            }

            $data['tw'] = $this->_db->table('_ref_tahun_tw')->where('id', $tw)->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getRowObject();
            $data['ptk'] = $ptk;
            // $igdD = $this->_db->table('_info_gtk')->where('ptk_id', $ptk->id_ptk)->get()->getRowObject();
            // $data['igd'] = $igdD;
            $response = new \stdClass;

            if ($ptk->no_rekening === null || $ptk->no_rekening === "" || $ptk->cabang_bank === null || $ptk->cabang_bank === "") {
                $response->status = 404;
                $response->message = "No Rekening dan Cabang tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/masterdata/individu");
                return json_encode($response);
            }

            if ($ptk->lampiran_ktp === null || $ptk->lampiran_ktp === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Master KTP tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/master");
                return json_encode($response);
            }

            if ($ptk->lampiran_npwp === null || $ptk->lampiran_npwp === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Master NPWP tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/master");
                return json_encode($response);
            }

            // if ($ptk->lampiran_nrg === null || $ptk->lampiran_nrg === "") {
            //     $response->status = 404;
            //     $response->message = "Lampiran Dokumen Master NRG tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
            //     $response->redirrect = base_url("situpeng/peng/doc/master");
            //     return json_encode($response);
            // }

            if ($ptk->lampiran_nuptk === null || $ptk->lampiran_nuptk === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Master NUPTK tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/master");
                return json_encode($response);
            }

            if ($ptk->lampiran_sk80 === null || $ptk->lampiran_sk80 === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Master SK 80% tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/master");
                return json_encode($response);
            }

            if ($ptk->lampiran_sk100 === null || $ptk->lampiran_sk100 === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Master SK 100% tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/master");
                return json_encode($response);
            }

            if ($ptk->lampiran_serdik === null || $ptk->lampiran_serdik === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Master Sertifikat Pendidik tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/master");
                return json_encode($response);
            }

            if ($ptk->lampiran_serpeng === null || $ptk->lampiran_serpeng === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Master Sertifikat Pengawas tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/master");
                return json_encode($response);
            }

            if ($ptk->lampiran_buku_rekening === null || $ptk->lampiran_buku_rekening === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Master Buku Rekening tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/master");
                return json_encode($response);
            }

            if ($ptk->lampiran_penugasan === null || $ptk->lampiran_penugasan === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Atibut Penugasan tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/atribut");
                return json_encode($response);
            }

            if ($ptk->lampiran_penugasan === null || $ptk->lampiran_penugasan === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Atibut Penugasan tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/atribut");
                return json_encode($response);
            }

            if ($ptk->lampiran_kunjungan_binaan === null || $ptk->lampiran_kunjungan_binaan === "") {
                $response->status = 404;
                $response->message = "Lampiran Dokumen Atibut Kunjungan Binaan tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                $response->redirrect = base_url("situpeng/peng/doc/atribut");
                return json_encode($response);
            }

            if ($jenis_tunjangan === "tpg") {

                $canUsulTpg = canUsulTpgPengawas();

                if ($canUsulTpg && $canUsulTpg->code !== 200) {
                    return json_encode($canUsulTpg);
                }

                if ($ptk->nuptk === null || $ptk->nuptk === "" || $ptk->no_peserta === ""  || $ptk->no_peserta === null) {
                    $response->status = 400;
                    $response->message = "Untuk mendapatkan Tunjangan Sertifikasi Guru, Harus Wajib mempunyai NUPTK dan No Peserta!!";
                    return json_encode($response);
                }

                // $pendidikans = ['D4', 'S1', 'S2', 'S3'];
                // // strtoupper()
                // if (!array_search($ptk->pendidikan, $pendidikans)) {
                //     $response->status = 400;
                //     $response->message = "Untuk mendapatkan Tunjangan Sertifikasi, harus wajib memiliki Pendidikan minimal S1.";
                //     return json_encode($response);
                // }

                if ($ptk->pang_golongan == NULL || $ptk->pang_golongan == "") {
                    $response->status = 400;
                    $response->message = "Data attribut kepegawaian masih kosong.";
                    return json_encode($response);
                }

                if ($ptk->lampiran_pangkat === null || $ptk->lampiran_pangkat === "") {

                    $response->status = 400;
                    $response->message = "Lampiran Dokumen Atribut Pangkat tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!.";
                    $response->redirrect = base_url("situpeng/peng/doc/atribut");
                    return json_encode($response);
                }

                if ($ptk->pang_jenis === "kgb") {
                    if ($ptk->lampiran_kgb === null || $ptk->lampiran_kgb === "") {
                        $response->status = 400;
                        $response->message = "Lampiran Dokumen Atribut KGB tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!.";
                        $response->redirrect = base_url("situpeng/peng/doc/atribut");
                        return json_encode($response);
                    }
                }
                $response->data = view('situpeng/peng/ajuan/tpg-asn', $data);
            } else if ($jenis_tunjangan === "tamsil") {

                $canUsulTamsil = canUsulTamsil();

                if ($canUsulTamsil && $canUsulTamsil->code !== 200) {
                    return json_encode($canUsulTamsil);
                }

                if ($ptk->nuptk === null || $ptk->nuptk === "") {
                    $response->status = 400;
                    $response->message = "Untuk mendapatkan Tunjangan Penghasilan Guru PNS Non Sertifikasi (Tamsil), Harus Wajib mempunyai NUPTK!!";
                    return json_encode($response);
                }

                $pendidikans = ['D4', 'S1', 'S2', 'S3'];
                // strtoupper()
                if (!array_search($ptk->pendidikan, $pendidikans)) {
                    $response->status = 400;
                    $response->message = "Untuk mendapatkan Tunjangan Penghasilan Guru PNS Non Sertifikasi (Tamsil), harus wajib memiliki Pendidikan minimal S1.";
                    return json_encode($response);
                }

                if ($ptk->lampiran_nuptk === null || $ptk->lampiran_nuptk === "") {
                    $response->status = 404;
                    $response->message = "Lampiran Dokumen Master NUPTK tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!";
                    $response->redirrect = base_url("situpeng/peng/doc/master");
                    return json_encode($response);
                }

                if ($ptk->pang_golongan == NULL || $ptk->pang_golongan == "") {
                    $response->status = 400;
                    $response->message = "Data attribut kepegawaian masih kosong.";
                    return json_encode($response);
                }

                if ($ptk->status_kepegawaian === "PNS" || $ptk->status_kepegawaian === "PPPK" || $ptk->status_kepegawaian === "PNS Diperbantukan" || $ptk->status_kepegawaian === "PNS Depag") {
                    // if ($ptk->lampiran_pangkat === null || $ptk->lampiran_pangkat === "" || $ptk->lampiran_kgb === null || $ptk->lampiran_kgb === "") {
                    //     $response->status = 404;
                    //     $response->message = "Lampiran Dokumen Atribut Pangkat dan KGB tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!.";
                    //     $response->redirrect = base_url("situpeng/peng/doc/atribut");
                    //     return json_encode($response);
                    // }
                    if ($ptk->pang_golongan == NULL || $ptk->pang_golongan == "") {
                        $response->status = 400;
                        $response->message = "Data attribut kepegawaian masih kosong.";
                        return json_encode($response);
                    }

                    if ($ptk->lampiran_pangkat === null || $ptk->lampiran_pangkat === "") {

                        $response->status = 400;
                        $response->message = "Lampiran Dokumen Atribut Pangkat tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!.";
                        $response->redirrect = base_url("situpeng/peng/doc/atribut");
                        return json_encode($response);
                    }

                    if ($ptk->pang_jenis === "kgb") {
                        if ($ptk->lampiran_kgb === null || $ptk->lampiran_kgb === "") {
                            $response->status = 400;
                            $response->message = "Lampiran Dokumen Atribut KGB tidak boleh kosong. Silahkan untuk melengkapi terlebih dahulu!!.";
                            $response->redirrect = base_url("situpeng/peng/doc/atribut");
                            return json_encode($response);
                        }
                    }
                    $response->data = view('situpeng/peng/us/ajukan/tamsil', $data);
                } else {
                    $response->status = 400;
                    $response->message = "Tunjangan Tamsil hanya diperuntukan bagi PNS / PPPK.";
                    return json_encode($response);
                }
            } else if ($jenis_tunjangan === "pghm") {

                $canUsulPghm = canUsulPghm();

                if ($canUsulPghm && $canUsulPghm->code !== 200) {
                    return json_encode($canUsulPghm);
                }

                if ($ptk->status_kepegawaian === "Guru Honor Sekolah") {
                    // if ($ptk->nuptk === null || $ptk->nuptk === "") {
                    //     $response->status = 400;
                    //     $response->message = "Untuk mendapatkan Tunjangan PGHM, Harus Wajib mempunyai NUPTK!!";
                    //     return json_encode($response);
                    // }

                    $pendidikans = ['D4', 'S1', 'S2', 'S3'];
                    // strtoupper()
                    if (!array_search($ptk->pendidikan, $pendidikans)) {
                        $response->status = 400;
                        $response->message = "Untuk mendapatkan Tunjangan PGHM, harus wajib memiliki Pendidikan minimal S1.";
                        return json_encode($response);
                    }
                    $response->data = view('situpeng/peng/us/ajukan/pghm', $data);
                } else {
                    $response->status = 400;
                    $response->message = "Tunjangan PGHM hanya diperuntukan bagi Guru Honorer.";
                    return json_encode($response);
                }
            } else {
                $response->status = 400;
                $response->message = "Jenis tunjangan tidak tersedia.";
                return json_encode($response);
            }

            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            return json_encode($response);
        }
    }

    public function prosesajukan()
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
            'jenis' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Jenis tidak boleh kosong. ',
                ]
            ],
            'tw' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('tw')
                . $this->validator->getError('jenis');
            return json_encode($response);
        } else {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session telah habis";
                $response->redirect = base_url('auth');
                return json_encode($response);
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $jenis = htmlspecialchars($this->request->getVar('jenis'), true);
            $tw = htmlspecialchars($this->request->getVar('tw'), true);

            $ptk = $this->_db->table('__pengawas_upload_data_attribut a')
                ->select("b.*, (SELECT gaji_pokok FROM ref_gaji WHERE pangkat = a.pang_golongan AND masa_kerja = (IF(a.pang_tahun > 32, 32, a.pang_tahun)) LIMIT 1) as gajiPokok, a.id_tahun_tw, a.pang_jenis, a.pang_golongan, a.pang_no, a.pang_tmt, a.pang_tgl, a.pang_tahun, a.pang_bulan, a.pangkat_terakhir as lampiran_pangkat, a.kgb_terakhir as lampiran_kgb, a.pernyataan_24jam as lampiran_pernyataan24, a.cuti as lampiran_cuti, a.pensiun as lampiran_pensiun, a.kematian as lampiran_kematian, a.lainnya as lampiran_att_lain, a.penugasan as lampiran_penugasan, a.kunjungan_binaan as lampiran_kunjungan_binaan")
                ->join('__pengawas_tb b', 'a.id_ptk = b.id')
                // ->join('_absen_kehadiran c', 'a.id_ptk = c.id_ptk AND c.id_tahun_tw = a.id_tahun_tw')
                ->where(['a.id_ptk' => $id, 'a.id_tahun_tw' => $tw])
                ->get()->getRowObject();

            if (!$ptk) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            if ($jenis === "tpg") {

                $canUsulTpg = canUsulTpgPengawas();

                if ($canUsulTpg && $canUsulTpg->code !== 200) {
                    return json_encode($canUsulTpg);
                }

                $uuidLib = new Uuid();
                $data = [
                    'id' => $uuidLib->v4(),
                    'id_pengawas' => $id,
                    'id_tahun_tw' => $tw,
                    'jenis_tunjangan' => $jenis,
                    'us_pang_golongan' => $ptk->pang_golongan,
                    'us_pang_tmt' => $ptk->pang_tmt,
                    'us_pang_tgl' => $ptk->pang_tgl,
                    'us_pang_mk_tahun' => $ptk->pang_tahun,
                    'us_pang_mk_bulan' => $ptk->pang_bulan,
                    'us_pang_jenis' => $ptk->pang_jenis,
                    'us_gaji_pokok' => $ptk->gajiPokok ? ($ptk->gajiPokok > 0 ? $ptk->gajiPokok : 1500000) : 1500000,
                    'status_usulan' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            } else if ($jenis === "tamsil") {

                $canUsulTamsil = canUsulTamsil();

                if ($canUsulTamsil && $canUsulTamsil->code !== 200) {
                    return json_encode($canUsulTamsil);
                }

                $uuidLib = new Uuid();
                $data = [
                    'id' => $uuidLib->v4(),
                    'id_ptk' => $id,
                    'id_tahun_tw' => $tw,
                    'jenis_tunjangan' => $jenis,
                    'us_pang_golongan' => $ptk->pang_golongan,
                    'us_pang_tmt' => $ptk->pang_tmt,
                    'us_pang_tgl' => $ptk->pang_tgl,
                    'us_pang_mk_tahun' => $ptk->pang_tahun,
                    'us_pang_mk_bulan' => $ptk->pang_bulan,
                    'us_pang_jenis' => $ptk->pang_jenis,
                    'us_gaji_pokok' => $this->_helpLib->nilaiTamsil(),
                    'status_usulan' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            } else if ($jenis === "pghm") {

                $canUsulPghm = canUsulPghm();

                if ($canUsulPghm && $canUsulPghm->code !== 200) {
                    return json_encode($canUsulPghm);
                }

                $uuidLib = new Uuid();
                $data = [
                    'id' => $uuidLib->v4(),
                    'id_ptk' => $id,
                    'id_tahun_tw' => $tw,
                    'jenis_tunjangan' => $jenis,
                    'us_pang_golongan' => $ptk->pang_golongan,
                    'us_pang_tmt' => $ptk->pang_tmt,
                    'us_pang_tgl' => $ptk->pang_tgl,
                    'us_pang_mk_tahun' => $ptk->pang_tahun,
                    'us_pang_mk_bulan' => $ptk->pang_bulan,
                    'us_pang_jenis' => $ptk->pang_jenis,
                    'us_gaji_pokok' => $this->_helpLib->nilaiPghm(),
                    'status_usulan' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Jenis tunjangan tidak tersedia.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_tb_temp_usulan_detail_pengawas')->insert($data);
                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Usulan $jenis berhasil diajukan.";
                    $response->data = $data;
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengajukan uslan $jenis.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal mengajukan uslan $jenis.";
                return json_encode($response);
            }
        }
    }
}
