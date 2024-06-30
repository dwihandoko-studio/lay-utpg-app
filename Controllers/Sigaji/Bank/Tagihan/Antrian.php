<?php

namespace App\Controllers\Sigaji\Bank\Tagihan;

use App\Controllers\BaseController;
use App\Models\Sigaji\Bank\Tagihan\AntrianModel;
use App\Models\Sigaji\Bank\Tagihan\AntriandetailModel;
use Config\Services;
use App\Libraries\Profilelib;
use App\Libraries\Sigaji\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Uuid;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Antrian
extends BaseController
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
        $datamodel = new AntrianModel($request);
        // $datamodel = new AntriandetailModel($request);
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            $output = [
                "draw" => $request->getPost('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ];
            echo json_encode($output);
            return;
        }

        $id_bank = $this->_helpLib->getIdBank($user->data->id);

        $lists = $datamodel->get_datatables($id_bank);
        $data = [];
        $no = $request->getPost("start");
        foreach ($lists as $list) {
            $no++;
            $row = [];

            $row[] = $no;
            $action = '<div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="./datadetail?d=' . $list->id_tahun . '"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
                        </div>
                    </div>';
            // $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            $row[] = $list->tahun . '-' . $list->bulan;
            $row[] = $list->jumlah_pegawai;
            $row[] = $list->jumlah_tagihan;

            $data[] = $row;
        }
        $output = [
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datamodel->count_all($id_bank),
            "recordsFiltered" => $datamodel->count_filtered($id_bank),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function getAllDetail()
    {
        $request = Services::request();
        $datamodel = new AntrianModel($request);
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            $output = [
                "draw" => $request->getPost('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ];
            echo json_encode($output);
            return;
        }

        $id_bank = $this->_helpLib->getIdBank($user->data->id);

        $lists = $datamodel->get_datatables($id_bank);
        $data = [];
        $no = $request->getPost("start");
        foreach ($lists as $list) {
            $no++;
            $row = [];

            $row[] = $no;
            $action = '<div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id_pegawai . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
                        </div>
                    </div>';
            // $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            $row[] = $list->tahun . '-' . $list->bulan;
            $row[] = $list->nama;
            $row[] = $list->nip;
            $row[] = $list->instansi;
            $row[] = $list->kecamatan;
            $row[] = $list->besar_pinjaman;
            $row[] = $list->jumlah_tagihan;
            $row[] = $list->jumlah_bulan_angsuran;
            $row[] = $list->angsuran_ke;

            $data[] = $row;
        }
        $output = [
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datamodel->count_all($id_bank),
            "recordsFiltered" => $datamodel->count_filtered($id_bank),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        return redirect()->to(base_url('sigaji/bank/tagihan/antrian/data'));
    }

    public function data()
    {
        $data['title'] = 'DATA TAGIHAN BANK';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;
        $data['tw'] = $this->_db->table('_ref_tahun_bulan')->where('is_current', 1)->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getRowObject();
        $data['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();

        return view('sigaji/bank/tagihan/antrian/index', $data);
    }

    public function datadetail()
    {
        $data['title'] = 'DATA DETAIL TAGIHAN BANK';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;
        $tw = htmlspecialchars($this->request->getGet('d'), TRUE);
        $data['tw_active'] = $tw;
        $data['tw'] = $this->_db->table('_ref_tahun_bulan')->where('id', $tw)->get()->getRowObject();
        // $data['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();
        $id_bank = $this->_helpLib->getIdBank($user->data->id);
        $data['datas'] = $this->_db->table('tb_tagihan_bank_antrian a')
            ->select("a.id, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan")
            ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
            ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
            ->where('a.dari_bank', $id_bank)
            ->where('a.tahun', $tw)
            ->countAllResults();

        // var_dump($data);
        // die; 

        return view('sigaji/bank/tagihan/antrian/index_detail', $data);
    }

    public function ambiltagihan()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                return redirect()->to(base_url('auth'));
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $d['tw_active'] = $id;
            $d['tw'] = $this->_db->table('_ref_tahun_bulan')->where('id', $id)->get()->getRowObject();
            $d['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();
            $id_bank = $this->_helpLib->getIdBank($user->data->id);
            $d['datas'] = $this->_db->table('tb_tagihan_bank a')
                ->select("a.id, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan")
                ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
                ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
                ->where('a.dari_bank', $id_bank)
                ->where('a.tahun', $id)
                ->orderBy('b.nama', 'ASC')
                ->get()->getResult();

            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('sigaji/bank/tagihan/antrian/form_ambil_tagihan', $d);
            return json_encode($response);
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function ambildatatagihan()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                return redirect()->to(base_url('auth'));
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
            $d['tw_active'] = $id;
            $d['tw'] = $this->_db->table('_ref_tahun_bulan')->where('id', $id)->get()->getRowObject();
            // $data['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();
            $id_bank = $this->_helpLib->getIdBank($user->data->id);
            $d['bank_id'] = $id_bank;
            $d['tahun_dipilih'] = $tahun;
            $d['datas'] = $this->_db->table('tb_tagihan_bank a')
                ->select("a.id, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan")
                ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
                ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
                ->where('a.dari_bank', $id_bank)
                ->where('a.tahun', $tahun)
                ->orderBy('b.nama', 'ASC')
                ->get()->getResult();

            // var_dump($d);
            // die;

            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('sigaji/bank/tagihan/antrian/content_add_ambil_tagihan', $d);
            return json_encode($response);
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function ambildataadd()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "User not authenticated.";
                return json_encode($response);
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $d['tw_active'] = $id;
            $tw = $this->_db->table('_ref_tahun_bulan')->where('id', $id)->get()->getRowObject();

            if (!$tw) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
            $d['tw'] = $tw;
            // $data['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();
            $id_bank = $this->_helpLib->getIdBank($user->data->id);
            $d['datas'] = $this->_db->table('tb_tagihan_bank_antrian a')
                ->select("a.id, a.edited, a.id_perubahan, a.status_ajuan, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan")
                ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
                ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
                ->where('a.dari_bank', $id_bank)
                ->where('a.tahun', $id)
                ->orderBy('b.nama', 'ASC')
                ->get()->getResult();
            $d['prosesed_ajuan'] = $this->_db->table('tb_tagihan_bank_antrian')->where(['dari_bank' => $id_bank, 'tahun' => $id, 'status_ajuan' => 1])->countAllResults();
            $d['prosesed_ajuan_tambahan'] = $this->_db->table('tb_tagihan_bank_antrian')->where(['dari_bank' => $id_bank, 'tahun' => $id, 'status_ajuan' => 0])->countAllResults();

            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('sigaji/bank/tagihan/antrian/content_add', $d);
            return json_encode($response);
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function tambahdatatagihanbaru()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "User not authenticated.";
                return json_encode($response);
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $d['tw_active'] = $id;
            $tw = $this->_db->table('_ref_tahun_bulan')->where('id', $id)->get()->getRowObject();

            if (!$tw) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
            $d['tw'] = $tw;
            // $data['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();
            $id_bank = $this->_helpLib->getIdBank($user->data->id);
            $d['bank_id'] = $id_bank;

            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('sigaji/bank/tagihan/antrian/content_add_new', $d);
            return json_encode($response);
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function ambildataedit()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "User not authenticated.";
                return json_encode($response);
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $oldData = $this->_db->table('tb_tagihan_bank_antrian a')
                ->select("a.id, a.edited, a.id_perubahan, a.status_ajuan, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan")
                ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
                ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
                ->where('a.id', $id)
                ->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            $d['data'] = $oldData;

            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('sigaji/bank/tagihan/antrian/content_edit', $d);
            return json_encode($response);
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function hapusdatatagihan()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "User not authenticated.";
                return json_encode($response);
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $oldData = $this->_db->table('tb_tagihan_bank_antrian a')
                ->select("a.id, a.edited, a.id_perubahan, a.status_ajuan, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan")
                ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
                ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
                ->where('a.id', $id)
                ->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            $uuidLib = new Uuid();

            $this->_db->transBegin();
            $dataRow = [
                'id' => $uuidLib->v4(),
                'user_id' => $user->data->id,
                'keterangan' => "Menghapus data tagihan dengan id $id",
                'created_at' => date('Y-m-d H:i:s'),
            ];

            try {
                $this->_db->table('riwayat_system')->insert($dataRow);
                if ($this->_db->affectedRows() > 0) {
                    $this->_db->query("INSERT INTO tb_tagihan_bank_antrian_deleted (id, tahun, id_pegawai, dari_bank, nip, instansi, kode_kecamatan, kecamatan, besar_pinjaman, jumlah_tagihan, jumlah_bulan_angsuran, angsuran_ke, status_ajuan, keterangan_penolakan, edited, id_perubahan, created_at, updated_at) SELECT id, tahun, id_pegawai, dari_bank, nip, instansi, kode_kecamatan, kecamatan, besar_pinjaman, jumlah_tagihan, jumlah_bulan_angsuran, angsuran_ke, status_ajuan, keterangan_penolakan, edited, id_perubahan, created_at, updated_at FROM tb_tagihan_bank_antrian WHERE id = '$id'");
                    if ($this->_db->affectedRows() > 0) {
                        $oldData = $this->_db->table('tb_tagihan_bank_antrian')->where('id', $id)->delete();
                        if ($this->_db->affectedRows() > 0) {
                            $this->_db->transCommit();
                            $response = new \stdClass;
                            $response->status = 200;
                            $response->message = "Data berhasil dihapus.";
                            return json_encode($response);
                        } else {
                            $this->_db->transRollback();
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Gagal menghapus data.";
                            return json_encode($response);
                        }
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal menghapus data.";
                        return json_encode($response);
                    }
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal menghapus data.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menghapus data.";
                return json_encode($response);
            }
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function editsavedatatagihan()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "User not authenticated.";
                return json_encode($response);
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $fullname = htmlspecialchars($this->request->getVar('fullname'), true);
            $nip = htmlspecialchars($this->request->getVar('nip'), true);
            $nama_instansi = htmlspecialchars($this->request->getVar('nama_instansi'), true);
            $kecamatan = htmlspecialchars($this->request->getVar('kecamatan'), true);
            $jumlah_pinjaman = htmlspecialchars($this->request->getVar('jumlah_pinjaman'), true);
            $jumlah_tagihan = htmlspecialchars($this->request->getVar('jumlah_tagihan'), true);
            $jumlah_bulan_angsuran = htmlspecialchars($this->request->getVar('jumlah_bulan_angsuran'), true);
            $angsuran_ke = htmlspecialchars($this->request->getVar('angsuran_ke'), true);

            if ($jumlah_pinjaman == "" || $jumlah_pinjaman == NULL || $jumlah_tagihan == "" || $jumlah_tagihan == NULL || $jumlah_bulan_angsuran == "" || $jumlah_bulan_angsuran == NULL || $angsuran_ke == "" || $angsuran_ke == NULL) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak valid.";
                return json_encode($response);
            }

            $oldData = $this->_db->table('tb_tagihan_bank_antrian a')
                ->select("a.id, a.edited, a.id_perubahan, a.status_ajuan, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan")
                ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
                ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
                ->where('a.id', $id)
                ->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
            $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);

            $keterangan = "Mengubah data tagihan dengan id $id dari ";
            if ((int)$jumlah_pinjaman !== (int)$oldData->besar_pinjaman) {
                $keterangan .= "Besar Pinjaman $jumlah_pinjaman --> $oldData->besar_pinjaman,";
            }
            if ((int)$jumlah_tagihan !== (int)$oldData->jumlah_tagihan) {
                $keterangan .= "Jumlah Tagihan $jumlah_tagihan --> $oldData->jumlah_tagihan,";
            }
            if ((int)$jumlah_bulan_angsuran !== (int)$oldData->jumlah_bulan_angsuran) {
                $keterangan .= "Jumlah Bulang Angsuran $jumlah_bulan_angsuran --> $oldData->jumlah_bulan_angsuran,";
            }
            if ((int)$angsuran_ke !== (int)$oldData->angsuran_ke) {
                $keterangan .= "Angsuran Ke $angsuran_ke --> $oldData->angsuran_ke,";
            }

            $uuidLib = new Uuid();

            $this->_db->transBegin();
            $dataRow = [
                'id' => $uuidLib->v4(),
                'user_id' => $user->data->id,
                'keterangan' => $keterangan,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            try {
                $this->_db->table('riwayat_system')->insert($dataRow);
                if ($this->_db->affectedRows() > 0) {

                    $oldData = $this->_db->table('tb_tagihan_bank_antrian')->where('id', $id)->update([
                        'besar_pinjaman' => $jumlah_pinjaman,
                        'jumlah_tagihan' => $jumlah_tagihan,
                        'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                        'angsuran_ke' => $angsuran_ke,
                        'id_perubahan' => $dataRow['id'],
                        'edited' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
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
                        $response->message = "Gagal mengupdate data. 1";
                        return json_encode($response);
                    }
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengupdate data. 2";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal mengupdate data. 3";
                return json_encode($response);
            }
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function addsavedatatagihan()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "User not authenticated.";
                return json_encode($response);
            }

            $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
            $pegawai_id = htmlspecialchars($this->request->getVar('fullname'), true);
            $nip = htmlspecialchars($this->request->getVar('nip'), true);
            $nama_instansi = htmlspecialchars($this->request->getVar('nama_instansi'), true);
            $kecamatan = htmlspecialchars($this->request->getVar('kecamatan'), true);
            $jumlah_pinjaman = htmlspecialchars($this->request->getVar('jumlah_pinjaman'), true);
            $jumlah_tagihan = htmlspecialchars($this->request->getVar('jumlah_tagihan'), true);
            $jumlah_bulan_angsuran = htmlspecialchars($this->request->getVar('jumlah_bulan_angsuran'), true);
            $angsuran_ke = htmlspecialchars($this->request->getVar('angsuran_ke'), true);

            $oldData = $this->_db->table('tb_tagihan_bank_antrian')->where(['tahun' => $tahun, 'id_pegawai' => $pegawai_id])->countAllResults();
            if ($oldData > 0) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tagihan sudah ada, silahkan gunakan menu edit untuk merubah data.";
                return json_encode($response);
            }

            $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
            $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);

            $keterangan = "Menambah Tagihan Baru untuk Pegawai NIP: $nip ";
            $id_bank = $this->_helpLib->getIdBank($user->data->id);

            $pegawai = getPegawaiByIdSigaji($pegawai_id);

            if (!$pegawai) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Pegawai tidak ditemukan.";
                return json_encode($response);
            }

            $uuidLib = new Uuid();

            $this->_db->transBegin();
            $dataRow = [
                'id' => $uuidLib->v4(),
                'user_id' => $user->data->id,
                'keterangan' => $keterangan,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            try {
                $this->_db->table('riwayat_system')->insert($dataRow);
                if ($this->_db->affectedRows() > 0) {

                    $this->_db->table('tb_tagihan_bank_antrian')->insert([
                        'id' => $uuidLib->v4(),
                        'tahun' => $tahun,
                        'id_pegawai' => $pegawai_id,
                        'dari_bank' => $id_bank,
                        'nip' => $pegawai->nip,
                        'instansi' => $pegawai->nama_instansi,
                        'kode_kecamatan' => $pegawai->kode_kecamatan,
                        'kecamatan' => $pegawai->nama_kecamatan,
                        'besar_pinjaman' => $jumlah_pinjaman,
                        'jumlah_tagihan' => $jumlah_tagihan,
                        'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                        'angsuran_ke' => $angsuran_ke,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    if ($this->_db->affectedRows() > 0) {
                        // try {
                        //     $this->_db->table('tb_tagihan_gagal_upload')->where(['dari_bank' => $id_bank, 'tahun' => $tahun, 'nip' => $pegawai->nip])->delete();
                        // } catch (\Throwable $th) {
                        // }
                        $this->_db->transCommit();
                        $response = new \stdClass;
                        $response->status = 200;
                        $response->message = "Data berhasil disimpan.";
                        return json_encode($response);
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal menyimpan data. 1";
                        return json_encode($response);
                    }
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal menyimpan data. 2";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal menyimpan data. 3";
                return json_encode($response);
            }
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function savetagihan()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                return redirect()->to(base_url('auth'));
            }

            $jsonData = htmlspecialchars($this->request->getVar('data'), true);
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $formData = json_decode($jsonData, true);

            if ($id === NULL || $id === "") {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Id tahun tw tidak boleh kosong.";
                return json_encode($response);
            }

            if (count($formData) !== 9) {
                if (count($formData) !== 10) {
                    if (count($formData) !== 8) {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Data yang dikirim tidak valid. Pegawai tidak ditemukan.";
                        return json_encode($response);
                    }
                }
            }

            // $id = $formData['id'];
            $nips = $formData['_filter_pegawai'];
            $instansis = $formData['instansi'];
            $kecamatans = $formData['kecamatan'];
            $jumlah_pinjamans = $formData['jumlah_pinjaman'];
            $jumlah_tagihans = $formData['jumlah_tagihan'];
            $jumlah_bulan_angsurans = $formData['jumlah_bulan_angsuran'];
            $angsuran_kes = $formData['angsuran_ke'];

            // $id = htmlspecialchars($this->request->getVar('id'), true);
            // $checks = $this->request->getVar('check');
            // $nips = $this->request->getVar('_filter_pegawai');
            // $instansis = $this->request->getVar('instansi');
            // $kecamatans = $this->request->getVar('kecamatan');
            // $jumlah_pinjamans = $this->request->getVar('jumlah_pinjaman');
            // $jumlah_tagihans = $this->request->getVar('jumlah_tagihan');
            // $jumlah_bulan_angsurans = $this->request->getVar('jumlah_bulan_angsuran');
            // $angsuran_kes = $this->request->getVar('angsuran_ke');

            $jmlData = count($nips);
            if ($jmlData === count($instansis) && $jmlData === count($kecamatans) && $jmlData === count($jumlah_pinjamans) && $jmlData === count($jumlah_tagihans) && $jmlData === count($jumlah_bulan_angsurans) && $jmlData === count($angsuran_kes)) {

                $id_bank = $this->_helpLib->getIdBank($user->data->id);
                $oldAllData = $this->_db->table('tb_tagihan_bank_antrian')->where(['tahun' => $id, 'dari_bank' => $id_bank])->countAllResults();
                if ($oldAllData > 0) {
                    $deleteAllData = $this->_db->table('tb_tagihan_bank_antrian')->where(['tahun' => $id, 'dari_bank' => $id_bank])->delete();
                    if (!($this->_db->affectedRows() > 0)) {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal memvalidasi data.";
                        return json_encode($response);
                    }
                }
                $uuidLib = new Uuid();
                $dataInserts = [];

                for ($i = 0; $i < $jmlData; $i++) {
                    $pegawai = getPegawaiByIdSigaji($nips[$i]);
                    if (!$pegawai) {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Data yang dikirim tidak valid. Pegawai tidak ditemukan.";
                        return json_encode($response);
                    }

                    $getAnyTag = $this->_db->table('tb_tagihan_bank_antrian')
                        ->where([
                            'tahun' => $id,
                            'id_pegawai' => $nips[$i],
                            'dari_bank' => $id_bank,
                        ])->countAllResults();

                    if ($getAnyTag > 0) {
                        continue;
                    }

                    $this->_db->transBegin();
                    $dataRow = [
                        'id' => $uuidLib->v4(),
                        'tahun' => $id,
                        'id_pegawai' => $nips[$i],
                        'dari_bank' => $id_bank,
                        'nip' => $pegawai->nip,
                        'instansi' => $pegawai->nama_instansi,
                        'kode_kecamatan' => $pegawai->kode_kecamatan,
                        'kecamatan' => $pegawai->nama_kecamatan,
                        'besar_pinjaman' => str_replace(".", "", $jumlah_pinjamans[$i]),
                        'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihans[$i]),
                        'jumlah_bulan_angsuran' => $jumlah_bulan_angsurans[$i],
                        'angsuran_ke' => $angsuran_kes[$i],
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    try {
                        $this->_db->table('tb_tagihan_bank_antrian')->insert($dataRow);
                        if ($this->_db->affectedRows() > 0) {
                            $this->_db->transCommit();
                            $dataInserts[] = $dataRow;
                            continue;
                        } else {
                            $this->_db->transRollback();
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Gagal menyimpan data. " . $dataRow['nip'] . " gagal disimpan.";
                            return json_encode($response);
                        }
                    } catch (\Throwable $th) {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal menyimpan data. " . $dataRow['nip'] . " gagal disimpan.";
                        return json_encode($response);
                    }
                }
                // try {
                //     $this->_db->table('tb_tagihan_bank_antrian')->insertBatch($dataInserts);
                //     if ($this->_db->affectedRows() > 0) {
                //         $this->_db->transCommit();
                //         $response = new \stdClass;
                //         $response->status = 200;
                //         $response->message = "Data berhasil disimpan.";
                //         $response->data = "Jumlah data yang disimpan adala " . count($dataInserts);
                //         return json_encode($response);
                //     } else {
                //         $this->_db->transRollback();
                //         $response = new \stdClass;
                //         $response->status = 400;
                //         $response->message = "Gagal menyimpan data.";
                //         return json_encode($response);
                //     }
                // } catch (\Throwable $th) {
                //     $this->_db->transRollback();
                //     $response = new \stdClass;
                //     $response->status = 400;
                //     $response->message = "Gagal menyimpan data.";
                //     return json_encode($response);
                // }

                // var_dump($dataInserts);
                // die;
                // $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil disimpan.";
                $response->sended_data = $jmlData;
                $response->data = "Jumlah data yang disimpan adalah " . count($dataInserts);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data yang dikirim tidak valid.";
                return json_encode($response);
            }
        } else {
            exit('Maaf tidak dapat diproses');
        }
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

        $d['bulans'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();

        $response = new \stdClass;
        $response->status = 200;
        $response->message = "Permintaan diizinkan";
        $response->data = view('sigaji/bank/tagihan/antrian/add', $d);
        return json_encode($response);
    }

    public function getPegawai()
    {
        if ($this->request->isAJAX()) {
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

                $current = $this->_db->table('tb_pegawai_')
                    ->select("id, nip, nama, nama_instansi, nama_kecamatan")
                    ->where("nip LIKE '%$keyword%' OR nama LIKE '%$keyword%' OR nama_instansi LIKE '%$keyword%'")->get()->getResult();

                if (count($current) > 0) {
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Permintaan diizinkan";
                    $response->data = $current;
                    return json_encode($response);
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Data tidak ditemukan";
                    return json_encode($response);
                }
            }
        } else {
            exit('Mohon maaf tidak dapat di proses.');
        }
    }

    public function ajukanprosestagihan()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "User is not authenticated.";
                return json_encode($response);
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
            $bulan = htmlspecialchars($this->request->getVar('bulan'), true);

            $id_bank = $this->_helpLib->getIdBank($user->data->id);

            $data = $this->_db->table('tb_tagihan_bank_antrian')
                ->select("id, tahun")
                ->where(['tahun' => $id, 'dari_bank' => $id_bank, 'status_ajuan' => 0])
                ->get()->getResult();

            $jmlData = count($data);
            if ($jmlData > 0) {
                $ids = [];
                for ($i = 0; $i < $jmlData; $i++) {
                    $ids[] = $data[$i]->id;
                }

                $this->_db->transBegin();
                try {
                    $this->_db->table('tb_tagihan_bank_antrian')->whereIn('id', $ids)->update(['status_ajuan' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->transCommit();
                        $response = new \stdClass;
                        $response->status = 200;
                        $response->url = base_url('sigaji/bank/tagihan/antrian/datadetail?d=' . $id);
                        $response->message = "Data " . count($ids) . " tagihan berhasil diproses untuk di validasi.";
                        return json_encode($response);
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal menyimpan data. " . count($ids) . " gagal disimpan.";
                        return json_encode($response);
                    }
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal menyimpan data. " . count($ids) . " gagal disimpan.";
                    return json_encode($response);
                }
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tagihan tidak ada yang akan di proses.";
                return json_encode($response);
            }
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function download()
    {
        $tw = htmlspecialchars($this->request->getGet('tb'), true);
        if ($tw == "") {
            return view('404');
        }

        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return view('404');
        }

        $id_bank = $this->_helpLib->getIdBank($user->data->id);

        try {

            $spreadsheet = new Spreadsheet();

            // $spreadsheet->getDefaultStyle()->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            // $spreadsheet->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            // Membuat objek worksheet
            $worksheet = $spreadsheet->getActiveSheet();

            // Menulis nama kolom ke dalam baris pertama worksheet
            $worksheet->fromArray(['NO', 'NAMA', 'NIP', 'INSTANSI', 'KECAMATAN', 'BESAR PINJAMAN', 'JUMLAH TAGIHAN', 'JUMLAH BULAN ANGSURAN', 'ANGSURAN KE'], NULL, 'A3');

            // Mengambil data dari database
            $dataTw = $this->_db->table('_ref_tahun_bulan')->where('id', $tw)->get()->getRowObject();
            $query = $this->_db->table('tb_tagihan_bank_antrian a')
                ->select("a.id, a.edited, a.id_perubahan, a.status_ajuan, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan")
                ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
                ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
                ->where('a.dari_bank', $id_bank)
                ->where('a.tahun', $tw)
                ->where('status_ajuan', 1)
                ->orderBy('b.nama_kecamatan', 'ASC')
                ->orderBy('b.nama_instansi', 'ASC')
                ->orderBy('b.nama', 'ASC')
                ->get();

            // Menulis data ke dalam worksheet
            $data = $query->getResult();
            $row = 4;
            if (count($data) > 0) {
                foreach ($data as $key => $item) {
                    $worksheet->getCell('A' . $row)->setValue($key + 1);
                    $worksheet->getCell('B' . $row)->setValue($item->nama);
                    $worksheet->setCellValueExplicit("C" . $row, (string)$item->nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $worksheet->getCell('D' . $row)->setValue($item->nama_instansi);
                    $worksheet->getCell('E' . $row)->setValue($item->nama_kecamatan);
                    $worksheet->setCellValueExplicit("F" . $row, $item->besar_pinjaman, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $worksheet->setCellValueExplicit("G" . $row, $item->jumlah_tagihan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $worksheet->setCellValueExplicit("H" . $row, $item->jumlah_bulan_angsuran, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $worksheet->setCellValueExplicit("I" . $row, $item->angsuran_ke, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

                    $row++;
                }
            }

            $worksheet->getStyle('F2:G' . $row) // Adjust range as needed
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            // Auto-size columns
            foreach (range('A', 'I') as $columnID) {
                $worksheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Menyiapkan objek writer untuk menulis file Excel
            $writer = new Xls($spreadsheet);

            // Menuliskan file Excel
            $filename = 'data_usulan_tagihan_' . $dataTw->tahun . '_tw_' . $dataTw->bulan_name . '.xls';
            header('Content-Type: application/vnd-ms-excel');
            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit();
            //code...
        } catch (\Throwable $th) {
            var_dump($th);
        }
    }


    public function upload()
    {
        if ($this->request->isAJAX()) {

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
                $id = htmlspecialchars($this->request->getVar('id'), true);
                $d['tahun'] = $id;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('sigaji/bank/tagihan/antrian/upload', $d);
                return json_encode($response);
            }
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function savetagihanupload()
    {
        if ($this->request->isAJAX()) {
            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                return redirect()->to(base_url('auth'));
            }

            $jsonData = htmlspecialchars($this->request->getVar('data'), true);
            $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
            $formData = json_decode($jsonData, true);

            if ($tahun === NULL || $tahun === "") {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tahun bulan tidak boleh kosong.";
                return json_encode($response);
            }

            $jmlData = count($formData);

            if ($jmlData < 1) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data yang diupload tidak valid.";
                return json_encode($response);
            }

            $id_bank = $this->_helpLib->getIdBank($user->data->id);

            $dataBerhasil = 0;
            $dataGagal = 0;
            $dataTidakDitemukan = 0;

            $uuidLib = new Uuid();
            $dataRowUpload = [
                'id' => $uuidLib->v4(),
                'tahun' => $tahun,
                'dari_bank' => $id_bank,
                'data_upload' => $jsonData,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            try {
                $this->_db->table('tb_tagihan_upload')->insert($dataRowUpload);
            } catch (\Throwable $th) {
            }

            try {
                $this->_db->table('tb_tagihan_gagal_upload')->where(['dari_bank' => $id_bank, 'tahun' => $tahun])->delete();
            } catch (\Throwable $th) {
            }

            $dataInserts = [];

            for ($i = 0; $i < $jmlData; $i++) {
                $pegawai = getPegawaiByNipImportSigaji($formData[$i]['NIP']);

                $jumlah_pinjaman = $formData[$i]['PLAFOND'];
                $jumlah_tagihan = $formData[$i]['JUMLAH_TAGIHAN'];
                $jumlah_bulan_angsuran = $formData[$i]['JUMLAH_BULAN_ANGSURAN'];
                $angsuran_ke = $formData[$i]['ANGSURAN_KE'];

                if (!$pegawai) {
                    $this->_db->transBegin();
                    $uuidLib = new Uuid();
                    $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                    $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);
                    $dataRow = [
                        'id' => $uuidLib->v4(),
                        'tahun' => $tahun,
                        'dari_bank' => $id_bank,
                        'nama' => $formData[$i]['NAMA'],
                        'nip' => $formData[$i]['NIP'],
                        'instansi' => $formData[$i]['NAMA_SEKOLAH'],
                        'kecamatan' => $formData[$i]['KECAMATAN'],
                        'besar_pinjaman' => str_replace(".", "", $jumlah_pinjaman),
                        'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihan),
                        'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                        'angsuran_ke' => $angsuran_ke,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    try {
                        $this->_db->table('tb_tagihan_gagal_upload')->insert($dataRow);
                        if ($this->_db->affectedRows() > 0) {

                            $this->_db->transCommit();
                            $dataTidakDitemukan++;
                            continue;
                        } else {
                            $this->_db->transRollback();
                            $dataTidakDitemukan++;
                            continue;
                        }
                    } catch (\Throwable $th) {
                        $this->_db->transRollback();
                        $dataTidakDitemukan++;
                        continue;
                    }

                    // $response = new \stdClass;
                    // $response->status = 400;
                    // $response->message = "Data yang dikirim tidak valid. Pegawai tidak ditemukan.";
                    // return json_encode($response);
                }

                if ($id_bank == 0) {
                    if ($jumlah_tagihan == "" || $jumlah_tagihan == NULL) {
                        $this->_db->transBegin();
                        $uuidLib = new Uuid();
                        $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                        $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);
                        $dataRow = [
                            'id' => $uuidLib->v4(),
                            'tahun' => $tahun,
                            'dari_bank' => $id_bank,
                            'nama' => $formData[$i]['NAMA'],
                            'nip' => $formData[$i]['NIP'],
                            'instansi' => $formData[$i]['NAMA_SEKOLAH'],
                            'kecamatan' => $formData[$i]['KECAMATAN'],
                            'besar_pinjaman' => str_replace(".", "", $jumlah_pinjaman),
                            'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihan),
                            'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                            'angsuran_ke' => $angsuran_ke,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];

                        try {
                            $this->_db->table('tb_tagihan_gagal_upload')->insert($dataRow);
                            if ($this->_db->affectedRows() > 0) {

                                $this->_db->transCommit();
                                $dataGagal++;
                                continue;
                            } else {
                                $this->_db->transRollback();
                                $dataGagal++;
                                continue;
                            }
                        } catch (\Throwable $th) {
                            $this->_db->transRollback();
                            $dataGagal++;
                            continue;
                        }
                    } else {
                        $jumlah_pinjaman = 0;
                        $jumlah_bulan_angsuran = 0;
                        $angsuran_ke = 0;
                    }
                } else {
                    if ($jumlah_pinjaman == "" || $jumlah_pinjaman == NULL || $jumlah_tagihan == "" || $jumlah_tagihan == NULL || $angsuran_ke == "" || $angsuran_ke == NULL) {
                        if ($jumlah_bulan_angsuran == "" || $jumlah_bulan_angsuran == NULL) {
                            $jumlah_bulan_angsuran = 0;
                        }
                        if ($jumlah_pinjaman == "" || $jumlah_pinjaman == NULL) {
                            $jumlah_pinjaman = 0;
                        }
                        $this->_db->transBegin();
                        $uuidLib = new Uuid();
                        $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                        $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);
                        $dataRow = [
                            'id' => $uuidLib->v4(),
                            'tahun' => $tahun,
                            'dari_bank' => $id_bank,
                            'nama' => $formData[$i]['NAMA'],
                            'nip' => $formData[$i]['NIP'],
                            'instansi' => $formData[$i]['NAMA_SEKOLAH'],
                            'kecamatan' => $formData[$i]['KECAMATAN'],
                            'besar_pinjaman' => str_replace(".", "", $jumlah_pinjaman),
                            'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihan),
                            'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                            'angsuran_ke' => $angsuran_ke,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];

                        try {
                            $this->_db->table('tb_tagihan_gagal_upload')->insert($dataRow);
                            if ($this->_db->affectedRows() > 0) {

                                $this->_db->transCommit();
                                $dataGagal++;
                                continue;
                            } else {
                                $this->_db->transRollback();
                                $dataGagal++;
                                continue;
                            }
                        } catch (\Throwable $th) {
                            $this->_db->transRollback();
                            $dataGagal++;
                            continue;
                        }
                    }
                }

                $oldData = $this->_db->table('tb_tagihan_bank_antrian a')
                    ->select("a.id, a.edited, a.id_perubahan, a.status_ajuan, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun as id_tahun_bulan, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan")
                    ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
                    ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
                    ->where([
                        'a.tahun' => $tahun,
                        'a.id_pegawai' => $pegawai->id,
                        'a.dari_bank' => $id_bank,
                    ])
                    ->get()->getRowObject();

                if ($oldData) {

                    $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                    $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);

                    $keterangan = "Mengubah data tagihan dengan id $oldData->id dari ";
                    if ((int)$jumlah_pinjaman !== (int)$oldData->besar_pinjaman) {
                        $keterangan .= "Besar Pinjaman $jumlah_pinjaman --> $oldData->besar_pinjaman,";
                    }
                    if ((int)$jumlah_tagihan !== (int)$oldData->jumlah_tagihan) {
                        $keterangan .= "Jumlah Tagihan $jumlah_tagihan --> $oldData->jumlah_tagihan,";
                    }
                    if ((int)$jumlah_bulan_angsuran !== (int)$oldData->jumlah_bulan_angsuran) {
                        $keterangan .= "Jumlah Bulang Angsuran $jumlah_bulan_angsuran --> $oldData->jumlah_bulan_angsuran,";
                    }
                    if ((int)$angsuran_ke !== (int)$oldData->angsuran_ke) {
                        $keterangan .= "Angsuran Ke $angsuran_ke --> $oldData->angsuran_ke,";
                    }

                    $uuidLib = new Uuid();

                    $this->_db->transBegin();
                    $dataRowRiwayat = [
                        'id' => $uuidLib->v4(),
                        'user_id' => $user->data->id,
                        'keterangan' => $keterangan,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    try {
                        $this->_db->table('riwayat_system')->insert($dataRowRiwayat);
                        if ($this->_db->affectedRows() > 0) {

                            $oldData = $this->_db->table('tb_tagihan_bank_antrian')->where('id', $oldData->id)->update([
                                'besar_pinjaman' => $jumlah_pinjaman,
                                'jumlah_tagihan' => $jumlah_tagihan,
                                'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                                'angsuran_ke' => $angsuran_ke,
                                'id_perubahan' => $dataRow['id'],
                                'edited' => 1,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                            if ($this->_db->affectedRows() > 0) {
                                $this->_db->transCommit();
                                $dataBerhasil++;
                                continue;
                            } else {
                                $this->_db->transRollback();
                                $this->_db->transBegin();
                                $uuidLib = new Uuid();
                                $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                                $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);
                                $dataRow = [
                                    'id' => $uuidLib->v4(),
                                    'tahun' => $tahun,
                                    'dari_bank' => $id_bank,
                                    'nama' => $formData[$i]['NAMA'],
                                    'nip' => $formData[$i]['NIP'],
                                    'instansi' => $formData[$i]['NAMA_SEKOLAH'],
                                    'kecamatan' => $formData[$i]['KECAMATAN'],
                                    'besar_pinjaman' => str_replace(".", "", $jumlah_pinjaman),
                                    'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihan),
                                    'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                                    'angsuran_ke' => $angsuran_ke,
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];

                                try {
                                    $this->_db->table('tb_tagihan_gagal_upload')->insert($dataRow);
                                    if ($this->_db->affectedRows() > 0) {

                                        $this->_db->transCommit();
                                        $dataGagal++;
                                        continue;
                                    } else {
                                        $this->_db->transRollback();
                                        $dataGagal++;
                                        continue;
                                    }
                                } catch (\Throwable $th) {
                                    $this->_db->transRollback();
                                    $dataGagal++;
                                    continue;
                                }
                            }
                        } else {
                            $this->_db->transRollback();
                            $this->_db->transBegin();
                            $uuidLib = new Uuid();
                            $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                            $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);
                            $dataRow = [
                                'id' => $uuidLib->v4(),
                                'tahun' => $tahun,
                                'dari_bank' => $id_bank,
                                'nama' => $formData[$i]['NAMA'],
                                'nip' => $formData[$i]['NIP'],
                                'instansi' => $formData[$i]['NAMA_SEKOLAH'],
                                'kecamatan' => $formData[$i]['KECAMATAN'],
                                'besar_pinjaman' => str_replace(".", "", $jumlah_pinjaman),
                                'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihan),
                                'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                                'angsuran_ke' => $angsuran_ke,
                                'created_at' => date('Y-m-d H:i:s'),
                            ];

                            try {
                                $this->_db->table('tb_tagihan_gagal_upload')->insert($dataRow);
                                if ($this->_db->affectedRows() > 0) {

                                    $this->_db->transCommit();
                                    $dataGagal++;
                                    continue;
                                } else {
                                    $this->_db->transRollback();
                                    $dataGagal++;
                                    continue;
                                }
                            } catch (\Throwable $th) {
                                $this->_db->transRollback();
                                $dataGagal++;
                                continue;
                            }
                        }
                    } catch (\Throwable $th) {
                        $this->_db->transRollback();
                        $this->_db->transBegin();
                        $uuidLib = new Uuid();
                        $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                        $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);
                        $dataRow = [
                            'id' => $uuidLib->v4(),
                            'tahun' => $tahun,
                            'dari_bank' => $id_bank,
                            'nama' => $formData[$i]['NAMA'],
                            'nip' => $formData[$i]['NIP'],
                            'instansi' => $formData[$i]['NAMA_SEKOLAH'],
                            'kecamatan' => $formData[$i]['KECAMATAN'],
                            'besar_pinjaman' => str_replace(".", "", $jumlah_pinjaman),
                            'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihan),
                            'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                            'angsuran_ke' => $angsuran_ke,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];

                        try {
                            $this->_db->table('tb_tagihan_gagal_upload')->insert($dataRow);
                            if ($this->_db->affectedRows() > 0) {

                                $this->_db->transCommit();
                                $dataGagal++;
                                continue;
                            } else {
                                $this->_db->transRollback();
                                $dataGagal++;
                                continue;
                            }
                        } catch (\Throwable $th) {
                            $this->_db->transRollback();
                            $dataGagal++;
                            continue;
                        }
                    }
                } else {

                    $this->_db->transBegin();
                    $uuidLib = new Uuid();
                    $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                    $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);
                    $dataRowInsert = [
                        'id' => $uuidLib->v4(),
                        'tahun' => $tahun,
                        'id_pegawai' => $pegawai->id,
                        'dari_bank' => $id_bank,
                        'nip' => $pegawai->nip,
                        'instansi' => $pegawai->nama_instansi,
                        'kode_kecamatan' => $pegawai->kode_kecamatan,
                        'kecamatan' => $pegawai->nama_kecamatan,
                        'besar_pinjaman' => str_replace(".", "", $jumlah_pinjaman),
                        'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihan),
                        'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                        'angsuran_ke' => $angsuran_ke,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    try {
                        $this->_db->table('tb_tagihan_bank_antrian')->insert($dataRowInsert);
                        if ($this->_db->affectedRows() > 0) {
                            $dataRowRiwayat = [
                                'id' => $uuidLib->v4(),
                                'user_id' => $user->data->id,
                                'keterangan' => "Menambahkan data tagihan baru untuk Pegawai $pegawai->nip",
                                'created_at' => date('Y-m-d H:i:s'),
                            ];

                            $this->_db->table('riwayat_system')->insert($dataRowRiwayat);
                            if ($this->_db->affectedRows() > 0) {

                                $this->_db->transCommit();
                                $dataBerhasil++;
                                continue;
                            } else {
                                $this->_db->transRollback();
                                $this->_db->transBegin();
                                $uuidLib = new Uuid();
                                $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                                $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);
                                $dataRow = [
                                    'id' => $uuidLib->v4(),
                                    'tahun' => $tahun,
                                    'dari_bank' => $id_bank,
                                    'nama' => $formData[$i]['NAMA'],
                                    'nip' => $formData[$i]['NIP'],
                                    'instansi' => $formData[$i]['NAMA_SEKOLAH'],
                                    'kecamatan' => $formData[$i]['KECAMATAN'],
                                    'besar_pinjaman' => str_replace(".", "", $jumlah_pinjaman),
                                    'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihan),
                                    'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                                    'angsuran_ke' => $angsuran_ke,
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];

                                try {
                                    $this->_db->table('tb_tagihan_gagal_upload')->insert($dataRow);
                                    if ($this->_db->affectedRows() > 0) {

                                        $this->_db->transCommit();
                                        $dataGagal++;
                                        continue;
                                    } else {
                                        $this->_db->transRollback();
                                        $dataGagal++;
                                        continue;
                                    }
                                } catch (\Throwable $th) {
                                    $this->_db->transRollback();
                                    $dataGagal++;
                                    continue;
                                }
                            }
                        } else {
                            $this->_db->transRollback();
                            $this->_db->transBegin();
                            $uuidLib = new Uuid();
                            $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                            $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);
                            $dataRow = [
                                'id' => $uuidLib->v4(),
                                'tahun' => $tahun,
                                'dari_bank' => $id_bank,
                                'nama' => $formData[$i]['NAMA'],
                                'nip' => $formData[$i]['NIP'],
                                'instansi' => $formData[$i]['NAMA_SEKOLAH'],
                                'kecamatan' => $formData[$i]['KECAMATAN'],
                                'besar_pinjaman' => str_replace(".", "", $jumlah_pinjaman),
                                'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihan),
                                'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                                'angsuran_ke' => $angsuran_ke,
                                'created_at' => date('Y-m-d H:i:s'),
                            ];

                            try {
                                $this->_db->table('tb_tagihan_gagal_upload')->insert($dataRow);
                                if ($this->_db->affectedRows() > 0) {

                                    $this->_db->transCommit();
                                    $dataGagal++;
                                    continue;
                                } else {
                                    $this->_db->transRollback();
                                    $dataGagal++;
                                    continue;
                                }
                            } catch (\Throwable $th) {
                                $this->_db->transRollback();
                                $dataGagal++;
                                continue;
                            }
                        }
                    } catch (\Throwable $th) {
                        $this->_db->transRollback();
                        $this->_db->transBegin();
                        $uuidLib = new Uuid();
                        $jumlah_pinjaman = str_replace(".", "", $jumlah_pinjaman);
                        $jumlah_tagihan = str_replace(".", "", $jumlah_tagihan);
                        $dataRow = [
                            'id' => $uuidLib->v4(),
                            'tahun' => $tahun,
                            'dari_bank' => $id_bank,
                            'nama' => $formData[$i]['NAMA'],
                            'nip' => $formData[$i]['NIP'],
                            'instansi' => $formData[$i]['NAMA_SEKOLAH'],
                            'kecamatan' => $formData[$i]['KECAMATAN'],
                            'besar_pinjaman' => str_replace(".", "", $jumlah_pinjaman),
                            'jumlah_tagihan' => str_replace(".", "", $jumlah_tagihan),
                            'jumlah_bulan_angsuran' => $jumlah_bulan_angsuran,
                            'angsuran_ke' => $angsuran_ke,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];

                        try {
                            $this->_db->table('tb_tagihan_gagal_upload')->insert($dataRow);
                            if ($this->_db->affectedRows() > 0) {

                                $this->_db->transCommit();
                                $dataGagal++;
                                continue;
                            } else {
                                $this->_db->transRollback();
                                $dataGagal++;
                                continue;
                            }
                        } catch (\Throwable $th) {
                            $this->_db->transRollback();
                            $dataGagal++;
                            continue;
                        }
                    }
                }
            }

            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Data berhasil diupload.";
            $response->sended_data = $jmlData;
            $response->upload_sukses = $dataBerhasil;
            $response->upload_gagal = $dataGagal;
            $response->upload_tidakditemukan = $dataTidakDitemukan;
            $response->data = "Jumlah data yang disimpan adalah Berhasil: $dataBerhasil, Gagal: $dataGagal, Pegawai Tidak Temukan: $dataTidakDitemukan.";
            return json_encode($response);
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    // public function uploadSave()
    // {
    //     if ($this->request->isAJAX()) {

    //         $rules = [
    //             'tahun' => [
    //                 'rules' => 'required',
    //                 'errors' => [
    //                     'required' => 'Tw tidak boleh kosong. ',
    //                 ]
    //             ],
    //             '_file' => [
    //                 'rules' => 'uploaded[_file]|max_size[_file,10240]|mime_in[_file,application/vnd.ms-excel,application/msexcel,application/x-msexcel,application/x-ms-excel,application/x-excel,application/x-dos_ms_excel,application/xls,application/x-xls,application/excel,application/download,application/vnd.ms-office,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/x-zip]',
    //                 'errors' => [
    //                     'uploaded' => 'Pilih file terlebih dahulu. ',
    //                     'max_size' => 'Ukuran file terlalu besar, Maximum 5Mb. ',
    //                     'mime_in' => 'Ekstensi yang anda upload harus berekstensi xls atau xlsx. '
    //                 ]
    //             ],
    //         ];

    //         if (!$this->validate($rules)) {
    //             $response = new \stdClass;
    //             $response->status = 400;
    //             $response->message = $this->validator->getError('tahun');
    //             // . $this->validator->getError('_file');
    //             return json_encode($response);
    //         } else {
    //             $Profilelib = new Profilelib();
    //             $user = $Profilelib->user();
    //             if ($user->status != 200) {
    //                 delete_cookie('jwt');
    //                 session()->destroy();
    //                 $response = new \stdClass;
    //                 $response->status = 401;
    //                 $response->message = "Session expired";
    //                 return json_encode($response);
    //             }

    //             $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
    //             $id_bank = $this->_helpLib->getIdBank($user->data->id);

    //             $lampiran = $this->request->getFile('_file');
    //             // $mimeType = $lampiran->getMimeType();

    //             // var_dump($mimeType);
    //             // die;
    //             // $extension = $lampiran->getClientExtension();
    //             // $filesNamelampiran = $lampiran->getName();
    //             // $newNamelampiran = _create_name_file_import($filesNamelampiran);
    //             $fileLocation = $lampiran->getTempName();

    //             $apiLib = new Apilib();
    //             switch ($id_bank) {
    //                 case 1:
    //                     $result = $apiLib->uploadTagihanBankEkaBandar($tahun, $fileLocation);
    //                     $namaBank = "Bank Eka Bandar Jaya";
    //                     break;
    //                 case 2:
    //                     $result = $apiLib->uploadTagihanBankEkaMetro($tahun, $fileLocation);
    //                     $namaBank = "Bank Eka Metro";
    //                     break;
    //                 case 3:
    //                     $result = $apiLib->uploadTagihanBankBpdBandar($tahun, $fileLocation);
    //                     $namaBank = "BPD Bandar Jaya";
    //                     break;
    //                 case 4:
    //                     $result = $apiLib->uploadTagihanBankBpdKoga($tahun, $fileLocation);
    //                     $namaBank = "BPD KOTA GAJAH";
    //                     break;
    //                 case 5:
    //                     $result = $apiLib->uploadTagihanBankBpdKalirejo($tahun, $fileLocation);
    //                     $namaBank = "BPD KALIREJO";
    //                     break;
    //                 case 6:
    //                     $result = $apiLib->uploadTagihanKpn($tahun, $fileLocation);
    //                     $namaBank = "KPN";
    //                     break;
    //                 case 7:
    //                     $result = $apiLib->uploadTagihanBankBri($tahun, $fileLocation);
    //                     $namaBank = "BANK BRI";
    //                     break;
    //                 case 10:
    //                     $result = $apiLib->uploadTagihanBankBpdMetro($tahun, $fileLocation);
    //                     $namaBank = "BPD METRO";
    //                     break;
    //                 case 9:
    //                     $result = $apiLib->uploadTagihanBankBni($tahun, $fileLocation);
    //                     $namaBank = "BANK BNI";
    //                     break;
    //                 case 0:
    //                     $result = $apiLib->uploadTagihanWajibKpn($tahun, $fileLocation);
    //                     $namaBank = "WAJIB KPN";
    //                     break;

    //                 default:
    //                     $response = new \stdClass;
    //                     $response->status = 400;
    //                     $response->message = "Gagal Import Data Tagihan bank.";
    //                     return json_encode($response);
    //                     break;
    //             }
    //             // $result = $apiLib->uploadPegawaiGajiSipd($tahun_bulan, $filename);

    //             if ($result) {
    //                 // var_dump($result);
    //                 // die;
    //                 if ($result->status == 200) {
    //                     $response = new \stdClass;
    //                     $response->status = 200;
    //                     $response->resss = $result;
    //                     $response->message = "Import Data Tagihan $namaBank Berhasil Dilakukan.";
    //                     return json_encode($response);
    //                 } else {
    //                     $response = new \stdClass;
    //                     $response->status = 400;
    //                     $response->error = $result;
    //                     $response->message = "Gagal Import Data Tagihan $namaBank.";
    //                     return json_encode($response);
    //                 }
    //             } else {
    //                 $response = new \stdClass;
    //                 $response->status = 400;
    //                 $response->message = "Gagal Import Data Tagihan $namaBank";
    //                 return json_encode($response);
    //             }
    //         }
    //     } else {
    //         exit('Maaf tidak dapat diproses');
    //     }
    // }
}
