<?php

namespace App\Controllers\Situgu\Adm\Masterdata;

use App\Controllers\BaseController;
use App\Models\Situgu\Adm\PtkterhapusModel;
use Config\Services;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;

class Ptkterhapus extends BaseController
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
        $datamodel = new PtkterhapusModel($request);


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
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
                            <a class="dropdown-item" href="javascript:actionMutasikan(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Mutasikan PTK</a>
                            <a class="dropdown-item" href="javascript:actionKembalikan(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><i class="bx bx-transfer-alt font-size-16 align-middle"></i> &nbsp;Kembalikan PTK</a>
                        </div>
                    </div>';
            // $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            $row[] = $list->nama;
            $row[] = $list->nik;
            $row[] = $list->nuptk;
            $row[] = $list->npsn;
            $row[] = $list->tempat_tugas;
            $row[] = $list->kecamatan;
            $row[] = $list->jenis_ptk;

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
        return redirect()->to(base_url('situgu/adm/masterdata/ptkterhapus/data'));
    }

    public function data()
    {
        $data['title'] = 'PTK TERHAPUS';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;

        return view('situgu/adm/masterdata/ptkterhapus/index', $data);
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

            $current = $this->_db->table('_ptk_tb a')
                ->select("a.*, b.no_hp as nohpAkun, b.email as emailAkun, b.wa_verified, b.image, c.kecamatan as kecamatan_sekolah")
                ->join('v_user b', 'a.id_ptk = b.ptk_id', 'left')
                ->join('ref_sekolah c', 'a.npsn = c.npsn', 'left')
                ->where('a.id', $id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $data['penugasans'] = $this->_db->table('_ptk_tb_dapodik a')
                    ->select("a.*, b.npsn, b.nama as namaSekolah, b.kecamatan as kecamatan_sekolah, (SELECT SUM(jam_mengajar_per_minggu) FROM _pembelajaran_dapodik WHERE ptk_id = a.ptk_id AND sekolah_id = a.sekolah_id AND semester_id = a.semester_id) as jumlah_total_jam_mengajar_perminggu")
                    ->join('ref_sekolah b', 'a.sekolah_id = b.id')
                    ->where('a.ptk_id', $current->id_ptk)
                    ->where("a.jenis_keluar IS NULL")
                    ->orderBy('a.ptk_induk', 'DESC')->get()->getResult();
                $data['igd'] = $this->_db->table('_info_gtk')->where('ptk_id', $current->id_ptk)->get()->getRowObject();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('situgu/adm/masterdata/ptk/detail', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function formmutasi()
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
            'nama' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Nama tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('nama');
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
            // $canUsulTamsil = canUsulTamsil();

            // if ($canUsulTamsil && $canUsulTamsil->code !== 200) {
            //     return json_encode($canUsulTamsil);
            // }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);

            $data['id'] = $id;
            $data['nama'] = $nama;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/adm/masterdata/ptkterhapus/mutasi', $data);
            return json_encode($response);
        }
    }

    public function mutasi()
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
            'sekolah_tujuan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Sekolah Tujuan tidak boleh kosong. ',
                ]
            ],
            'keterangan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Keterangan tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('sekolah_tujuan')
                . $this->validator->getError('keterangan');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $sekolah_tujuan = htmlspecialchars($this->request->getVar('sekolah_tujuan'), true);
            $keterangan = htmlspecialchars($this->request->getVar('keterangan'), true);

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

            $current = $this->_db->table('_ptk_tb_hapus')
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $tujuan = $this->_db->table('ref_sekolah')
                    ->where('id', $sekolah_tujuan)->get()->getRowObject();

                if (!$tujuan) {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Sekolah tujuan tidak ditemukan.";
                    return json_encode($response);
                }

                $npsnTujuan = $tujuan->npsn;
                $namaTujuan = $tujuan->nama;
                $namaKecamatanTujuan = $tujuan->kecamatan;
                $idKecamatanTujuan = $tujuan->kode_kecamatan;

                $this->_db->transBegin();
                try {
                    $date = date('Y-m-d H:i:s');
                    $this->_db->query("INSERT INTO _ptk_tb(id, id_ptk, email, nama, gelar_depan, gelar_belakang, nik, nuptk, nip, nrg, no_peserta, npwp, no_rekening, cabang_bank, jenis_kelamin, tempat_lahir, tgl_lahir, status_tugas, tempat_tugas, npsn, kecamatan, id_kecamatan, no_hp, sk_cpns, tgl_cpns, sk_pengangkatan, tmt_pengangkatan, jenis_ptk, pendidikan, bidang_studi_pendidikan, bidang_studi_sertifikasi, status_kepegawaian, mapel_diajarkan, jam_mengajar_perminggu, jabatan_kepsek, jabatan_ks_plt, pangkat_golongan, nomor_sk_pangkat, tgl_sk_pangkat, tmt_pangkat, masa_kerja_tahun, masa_kerja_bulan, gaji_pokok, sk_kgb, tgl_sk_kgb, tmt_sk_kgb, masa_kerja_tahun_kgb, masa_kerja_bulan_kgb, gaji_pokok_kgb, mengajar_lain_satmikal, nomor_sk_impassing, tgl_sk_impassing, tmt_sk_impassing, jabatan_angka_kredit, pangkat_golongan_ruang, masa_kerja_tahun_impassing, masa_kerja_bulan_impassing, jumlah_tunjangan_pokok_impassing, lampiran_impassing, lampiran_foto, lampiran_karpeg, lampiran_ktp, lampiran_nrg, lampiran_nuptk, lampiran_serdik, lampiran_npwp, lampiran_buku_rekening, lampiran_ijazah, jenis_tunjangan, is_locked, created_at, updated_at, last_sync)
                                        SELECT id, id_ptk, email, nama, gelar_depan, gelar_belakang, nik, nuptk, nip, nrg, no_peserta, npwp, no_rekening, cabang_bank, jenis_kelamin, tempat_lahir, tgl_lahir, status_tugas, CONCAT('$namaTujuan', '') AS tempat_tugas, CONCAT('$npsnTujuan', '') AS npsn, CONCAT('$namaKecamatanTujuan', '') AS kecamatan, CONCAT('$idKecamatanTujuan', '') AS id_kecamatan, no_hp, sk_cpns, tgl_cpns, sk_pengangkatan, tmt_pengangkatan, jenis_ptk, pendidikan, bidang_studi_pendidikan, bidang_studi_sertifikasi, status_kepegawaian, mapel_diajarkan, jam_mengajar_perminggu, jabatan_kepsek, jabatan_ks_plt, pangkat_golongan, nomor_sk_pangkat, tgl_sk_pangkat, tmt_pangkat, masa_kerja_tahun, masa_kerja_bulan, gaji_pokok, sk_kgb, tgl_sk_kgb, tmt_sk_kgb, masa_kerja_tahun_kgb, masa_kerja_bulan_kgb, gaji_pokok_kgb, mengajar_lain_satmikal, nomor_sk_impassing, tgl_sk_impassing, tmt_sk_impassing, jabatan_angka_kredit, pangkat_golongan_ruang, masa_kerja_tahun_impassing, masa_kerja_bulan_impassing, jumlah_tunjangan_pokok_impassing, lampiran_impassing, lampiran_foto, lampiran_karpeg, lampiran_ktp, lampiran_nrg, lampiran_nuptk, lampiran_serdik, lampiran_npwp, lampiran_buku_rekening, lampiran_ijazah, jenis_tunjangan, is_locked, created_at, CONCAT('$date', '') AS updated_at, last_sync
                                        FROM _ptk_tb_hapus
                                        WHERE id = '$id' AND status_ajuan = 2");

                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->table('_ptk_tb_hapus')->where('id', $id)->delete();
                        if ($this->_db->affectedRows() > 0) {
                            $this->_db->transCommit();
                            $response = new \stdClass;
                            $response->status = 200;
                            $response->message = "Berhasil memutasikan data ptk $current->nama.";
                            return json_encode($response);
                        } else {
                            $this->_db->transRollback();
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Gagal memutasikan data.";
                            return json_encode($response);
                        }
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal memutasikan data.";
                        return json_encode($response);
                    }
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal memutasikan data.";
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

    public function kembalikan()
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
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('nama')
                . $this->validator->getError('npsn');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

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
            $oldData = $this->_db->table('_ptk_tb_hapus')->where(['id' => $id])->get()->getRowObject();
            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 201;
                $response->message = "Usulan tidak ditemukan.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->query("INSERT INTO _ptk_tb(id, id_ptk, email, nama, gelar_depan, gelar_belakang, nik, nuptk, nip, nrg, no_peserta, npwp, no_rekening, cabang_bank, jenis_kelamin, tempat_lahir, tgl_lahir, status_tugas, tempat_tugas, npsn, kecamatan, id_kecamatan, no_hp, sk_cpns, tgl_cpns, sk_pengangkatan, tmt_pengangkatan, jenis_ptk, pendidikan, bidang_studi_pendidikan, bidang_studi_sertifikasi, status_kepegawaian, mapel_diajarkan, jam_mengajar_perminggu, jabatan_kepsek, jabatan_ks_plt, pangkat_golongan, nomor_sk_pangkat, tgl_sk_pangkat, tmt_pangkat, masa_kerja_tahun, masa_kerja_bulan, gaji_pokok, sk_kgb, tgl_sk_kgb, tmt_sk_kgb, masa_kerja_tahun_kgb, masa_kerja_bulan_kgb, gaji_pokok_kgb, mengajar_lain_satmikal, nomor_sk_impassing, tgl_sk_impassing, tmt_sk_impassing, jabatan_angka_kredit, pangkat_golongan_ruang, masa_kerja_tahun_impassing, masa_kerja_bulan_impassing, jumlah_tunjangan_pokok_impassing, lampiran_impassing, lampiran_foto, lampiran_karpeg, lampiran_ktp, lampiran_nrg, lampiran_nuptk, lampiran_serdik, lampiran_npwp, lampiran_buku_rekening, lampiran_ijazah, jenis_tunjangan, is_locked, created_at, updated_at, last_sync)
                                        SELECT id, id_ptk, email, nama, gelar_depan, gelar_belakang, nik, nuptk, nip, nrg, no_peserta, npwp, no_rekening, cabang_bank, jenis_kelamin, tempat_lahir, tgl_lahir, status_tugas, tempat_tugas, npsn, kecamatan, id_kecamatan, no_hp, sk_cpns, tgl_cpns, sk_pengangkatan, tmt_pengangkatan, jenis_ptk, pendidikan, bidang_studi_pendidikan, bidang_studi_sertifikasi, status_kepegawaian, mapel_diajarkan, jam_mengajar_perminggu, jabatan_kepsek, jabatan_ks_plt, pangkat_golongan, nomor_sk_pangkat, tgl_sk_pangkat, tmt_pangkat, masa_kerja_tahun, masa_kerja_bulan, gaji_pokok, sk_kgb, tgl_sk_kgb, tmt_sk_kgb, masa_kerja_tahun_kgb, masa_kerja_bulan_kgb, gaji_pokok_kgb, mengajar_lain_satmikal, nomor_sk_impassing, tgl_sk_impassing, tmt_sk_impassing, jabatan_angka_kredit, pangkat_golongan_ruang, masa_kerja_tahun_impassing, masa_kerja_bulan_impassing, jumlah_tunjangan_pokok_impassing, lampiran_impassing, lampiran_foto, lampiran_karpeg, lampiran_ktp, lampiran_nrg, lampiran_nuptk, lampiran_serdik, lampiran_npwp, lampiran_buku_rekening, lampiran_ijazah, jenis_tunjangan, is_locked, created_at, updated_at, last_sync
                                        FROM _ptk_tb_hapus
                                        WHERE id = '$id'");
                if ($this->_db->affectedRows() > 0) {
                    $this->_db->table('_ptk_tb_hapus')->where('id', $oldData->id)->delete();
                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->transCommit();

                        $response = new \stdClass;
                        $response->status = 200;
                        $response->message = "Data PTK Terhapus $nama berhasil dikembalikan.";
                        return json_encode($response);
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal mengembalikan PTK Terhapus $nama.";
                        return json_encode($response);
                    }
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengembalikan PTK Terhapus $nama.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal mengembalikan PTK Terhapus $nama.";
                return json_encode($response);
            }
        }
    }

    public function getSekolahMutasi()
    {
        if ($this->request->getMethod() != 'post') {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Permintaan tidak diizinkan";
            return json_encode($response);
        }

        $rules = [
            'keyword' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Keyword tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('keyword');
            return json_encode($response);
        } else {
            $keyword = htmlspecialchars($this->request->getVar('keyword'), true);

            $current = $this->_db->table('ref_sekolah')->select("id, nama, npsn, bentuk_pendidikan, kecamatan")
                ->where("npsn = '$keyword' OR nama LIKE '%$keyword%'")->get()->getResult();

            if (count($current) > 0) {
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = $current;
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->keyword = $keyword;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }
}
