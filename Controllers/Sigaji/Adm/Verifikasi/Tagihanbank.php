<?php

namespace App\Controllers\Sigaji\Adm\Verifikasi;

use App\Controllers\BaseController;
use App\Models\Sigaji\Adm\Verifikasi\TagihanbankModel;
use Config\Services;
use App\Libraries\Profilelib;
use App\Libraries\Sigaji\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Uuid;

class Tagihanbank
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
        $datamodel = new TagihanbankModel($request);
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
                            <a class="dropdown-item" href="./datadetail?d=' . $list->id_tahun . '&b=' . $list->dari_bank . '"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
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
            $row[] = $list->nama_bank;
            $row[] = $list->tahun . '-' . $list->bulan;
            $row[] = $list->jumlah_pegawai;
            $row[] = $list->jumlah_tagihan;

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
        return redirect()->to(base_url('sigaji/adm/verifikasi/tagihanbank/data'));
    }

    public function data()
    {
        $data['title'] = 'DATA VERIFIKASI TAGIHAN BANK';
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

        return view('sigaji/adm/verifikasi/tagihanbank/index', $data);
    }

    public function datadetail()
    {
        $data['title'] = 'DATA DETAIL VERIFIKASI TAGIHAN BANK';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;
        $tw = htmlspecialchars($this->request->getGet('d'), TRUE);
        $id_bank = htmlspecialchars($this->request->getGet('b'), TRUE);
        $data['id_bank'] = $id_bank;
        $data['tw_active'] = $tw;
        $data['tw'] = $this->_db->table('_ref_tahun_bulan')->where('id', $tw)->get()->getRowObject();
        // $data['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();
        $data['datas'] = $this->_db->table('tb_tagihan_bank_antrian a')
            ->select("a.id, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan")
            ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
            ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
            ->where('a.dari_bank', $id_bank)
            ->where('a.tahun', $tw)
            ->where('a.status_ajuan', 1)
            ->countAllResults();

        // var_dump($data['datas']);
        // die;

        return view('sigaji/adm/verifikasi/tagihanbank/index_detail', $data);
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
            $id_bank = htmlspecialchars($this->request->getVar('bank'), true);
            $d['tw_active'] = $id;
            $d['id_bank'] = $id_bank;
            $tw = $this->_db->table('_ref_tahun_bulan')->where('id', $id)->get()->getRowObject();

            if (!$tw) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
            $d['tw'] = $tw;
            // $data['tws'] = $this->_db->table('_ref_tahun_bulan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get()->getResult();
            $d['datas'] = $this->_db->table('tb_tagihan_bank_antrian a')
                ->select("a.id, a.status_ajuan, a.id_pegawai, a.instansi, a.kecamatan, a.besar_pinjaman, a.jumlah_tagihan, a.jumlah_bulan_angsuran, a.angsuran_ke, a.tahun, b.nama, b.nip, b.golongan, b.no_rekening_bank, b.kode_instansi, b.nama_instansi, b.nama_kecamatan, c.tahun, c.bulan, d.jumlah_transfer, (e.bank_eka_bandar_jaya + e.bank_eka_metro + e.bpd_bandar_jaya + e.bpd_koga + e.bpd_metro + e.bpd_kalirejo + e.wajib_kpn + e.kpn + e.bri + e.btn + e.bni + e.dharma_wanita + e.korpri + e.zakat_profesi + e.infak + e.shodaqoh + e.zakat_fitrah) as jumlah_potongan")
                ->join('_ref_tahun_bulan c', 'a.tahun = c.id')
                ->join('tb_pegawai_ b', 'a.id_pegawai = b.id')
                ->join('tb_gaji_sipd d', 'a.id_pegawai = d.id_pegawai AND a.tahun = d.tahun', 'LEFT')
                ->join('tb_potongan_ e', 'a.id_pegawai = e.id_pegawai AND a.tahun = e.tahun')
                ->where('a.dari_bank', $id_bank)
                ->where('a.tahun', $id)
                ->where('a.status_ajuan', 1)
                ->orderBy('b.nama', 'ASC')
                ->get()->getResult();
            $d['prosesed_ajuan'] = $this->_db->table('tb_tagihan_bank_antrian')->where(['dari_bank' => $id_bank, 'tahun' => $id, 'status_ajuan' => 1])->countAllResults();

            // var_dump($d['datas']);
            // die;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('sigaji/adm/verifikasi/tagihanbank/content_add', $d);
            return json_encode($response);
        } else {
            exit('Maaf tidak dapat diproses');
        }
    }

    public function verifikasitagihan()
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
            $formData = json_decode($jsonData, true);

            $id = $formData['id'];
            $id_bank = $formData['bank'];
            $id_tags = $formData['id_tag'];

            $jmlData = count($id_tags);
            if ($jmlData > 0) {
                // $uuidLib = new Uuid();
                $dataInserts = [];

                for ($i = 0; $i < $jmlData; $i++) {
                    $tagihan = $this->_db->table('tb_tagihan_bank_antrian')
                        ->where([
                            'id' => $id_tags[$i],
                            'dari_bank' => $id_bank,
                            'tahun' => $id,
                            'status_ajuan' => 1,
                        ])->get()->getRowObject();
                    if (!$tagihan) {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Data yang dikirim tidak valid. Tagihan tidak ditemukan.";
                        return json_encode($response);
                    }

                    $this->_db->transBegin();
                    $dateUpdate = date('Y-m-d H:i:s');
                    $getAnyTag = $this->_db->table('tb_tagihan_bank_antrian')
                        ->where([
                            'id' => $id_tags[$i],
                            'dari_bank' => $id_bank,
                            'tahun' => $id,
                            'status_ajuan' => 1,
                        ])->update(['status_ajuan' => 2, 'updated_at' => $dateUpdate]);

                    if ($this->_db->affectedRows() > 0) {
                        $idTagihan = $id_tags[$i];
                        $this->_db->query("INSERT INTO tb_tagihan_bank (id, tahun, id_pegawai, dari_bank, nip, instansi, kode_kecamatan, kecamatan, besar_pinjaman, jumlah_tagihan, jumlah_bulan_angsuran, angsuran_ke, created_at, updated_at) SELECT id, tahun, id_pegawai, dari_bank, nip, instansi, kode_kecamatan, kecamatan, besar_pinjaman, jumlah_tagihan, jumlah_bulan_angsuran, angsuran_ke, created_at, updated_at FROM tb_tagihan_bank_antrian WHERE id = '$idTagihan'");
                        if ($this->_db->affectedRows() > 0) {
                            switch ((int)$id_bank) {
                                case 0:
                                    $pot_bang = 'wajib_kpn';
                                    break;
                                case 1:
                                    $pot_bang = 'bank_eka_bandar_jaya';
                                    break;
                                case 2:
                                    $pot_bang = 'bank_eka_metro';
                                    break;
                                case 3:
                                    $pot_bang = 'bpd_bandar_jaya';
                                    break;
                                case 4:
                                    $pot_bang = 'bpd_koga';
                                    break;
                                case 5:
                                    $pot_bang = 'bpd_kalirejo';
                                    break;
                                case 6:
                                    $pot_bang = 'kpn';
                                    break;
                                case 7:
                                    $pot_bang = 'bri';
                                    break;
                                case 8:
                                    $pot_bang = 'btn';
                                    break;
                                case 9:
                                    $pot_bang = 'bni';
                                    break;
                                case 10:
                                    $pot_bang = 'bpd_metro';
                                    break;

                                default:
                                    $pot_bang = 'wajib_kpn';
                                    break;
                            }
                            $this->_db->query("UPDATE tb_potongan_ SET $pot_bang = $tagihan->jumlah_tagihan, updated_at = '$dateUpdate' WHERE tahun = '$tagihan->tahun' AND id_pegawai = '$tagihan->id_pegawai'");
                            if ($this->_db->affectedRows() > 0) {
                                $uuidLib = new Uuid();
                                $dataRow = [
                                    'id' => $uuidLib->v4(),
                                    'user_id' => $user->data->id,
                                    'keterangan' => "Memverifikasi pengajuan data tagihan dengan id $idTagihan",
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                                $this->_db->table('riwayat_system')->insert($dataRow);
                                if ($this->_db->affectedRows() > 0) {

                                    $this->_db->transCommit();
                                    $dataInserts[] = $id_tags[$i];
                                    continue;
                                } else {
                                    $this->_db->transRollback();
                                    $response = new \stdClass;
                                    $response->status = 400;
                                    $response->message = "Gagal memverifikasi data. " . $id_tags[$i] . ".";
                                    return json_encode($response);
                                }
                            } else {
                                $this->_db->transRollback();
                                $response = new \stdClass;
                                $response->status = 400;
                                $response->message = "Gagal memverifikasi data. " . $id_tags[$i] . ".";
                                return json_encode($response);
                            }
                        } else {
                            $this->_db->transRollback();
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Gagal memverifikasi data. " . $id_tags[$i] . ".";
                            return json_encode($response);
                        }
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal memverifikasi data. " . $id_tags[$i] . ".";
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
                $response->message = "Data berhasil diverifikasi.";
                $response->sended_data = $jmlData;

                $response->data = "Jumlah data yang diverifikasi adalah " . count($dataInserts);
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

    public function tolakverifikasitagihan()
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
            $formData = json_decode($jsonData, true);

            $id = $formData['id'];
            $id_bank = $formData['bank'];
            $id_tags = $formData['id_tag'];
            $keterangans = $formData['keterangan'];

            $jmlData = count($id_tags);
            if ($jmlData > 0) {
                // $uuidLib = new Uuid();
                $dataInserts = [];

                for ($i = 0; $i < $jmlData; $i++) {
                    $tagihan = $this->_db->table('tb_tagihan_bank_antrian')
                        ->where([
                            'id' => $id_tags[$i],
                            'dari_bank' => $id_bank,
                            'tahun' => $id,
                            'status_ajuan' => 1,
                        ])->get()->getRowObject();
                    if (!$tagihan) {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Data yang dikirim tidak valid. Tagihan tidak ditemukan.";
                        return json_encode($response);
                    }

                    $this->_db->transBegin();
                    $getAnyTag = $this->_db->table('tb_tagihan_bank_antrian')
                        ->where([
                            'id' => $id_tags[$i],
                            'dari_bank' => $id_bank,
                            'tahun' => $id,
                            'status_ajuan' => 1,
                        ])->update(['status_ajuan' => 3, 'keterangan_penolakan' => $keterangans[$i], 'updated_at' => date('Y-m-d H:i:s')]);

                    if ($this->_db->affectedRows() > 0) {
                        $uuidLib = new Uuid();
                        $dataRow = [
                            'id' => $uuidLib->v4(),
                            'user_id' => $user->data->id,
                            'keterangan' => "Menolak verifikasi pengajuan data tagihan dengan id " . $id_tags[$i] . " dengan keterangan: " . $keterangans[$i],
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                        $this->_db->table('riwayat_system')->insert($dataRow);
                        if ($this->_db->affectedRows() > 0) {
                            $this->_db->transCommit();
                            $dataInserts[] = $id_tags[$i];
                            continue;
                        } else {
                            $this->_db->transRollback();
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Gagal memverifikasi data. " . $id_tags[$i] . ".";
                            return json_encode($response);
                        }
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal memverifikasi data. " . $id_tags[$i] . ".";
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
                $response->message = "Data berhasil ditolak verifikasi.";
                $response->sended_data = $jmlData;

                $response->data = "Jumlah data yang ditolak verifikasi adalah " . count($dataInserts);
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
}
