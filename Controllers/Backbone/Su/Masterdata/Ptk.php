<?php

namespace App\Controllers\Backbone\Su\Masterdata;

use App\Controllers\BaseController;
use App\Models\Backbone\Su\PtkModel;
use Config\Services;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;

class Ptk extends BaseController
{
    var $folderImage = 'masterdata';
    private $_db;
    private $model;
    private $_helpLib;

    function __construct()
    {
        helper(['text', 'file', 'form', 'session', 'array', 'imageurl', 'web', 'filesystem']);
        $this->_db      = \Config\Database::connect('backbone');
        $this->_helpLib = new Helplib();
    }

    public function getAll()
    {
        $request = Services::request();
        $datamodel = new PtkModel($request);


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
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->ptk_terdaftar_id . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
                        
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
            $row[] = $list->nama_sekolah;
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
        return redirect()->to(base_url('backbone/su/masterdata/ptk/data'));
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

        return view('backbone/su/masterdata/ptk/index', $data);
    }

    public function detailbackbone()
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
            'nuptk' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'NUPTK tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('nama')
                . $this->validator->getError('nuptk');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nuptk = htmlspecialchars($this->request->getVar('nuptk'), true);

            $current = $this->_db->table('_ptk_tb')->select("id_ptk, nuptk")
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $apiLib = new Apilib();
                if ($current->id_ptk !== null) {
                    $result = $apiLib->getPtkById($current->id_ptk);

                    $ptk = $result;
                } else {
                    $result = $apiLib->getPtkByNuptk($current->nuptk);

                    $ptk = $result;
                }

                $data['data'] = $ptk;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('backbone/su/masterdata/ptk/get_detail_backbone', $data);
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

            $current = $this->_db->table('_tbl_gtk a')
                ->select("a.*, b.nama as nama_sekolah, b.bentuk_pendidikan, b.status_sekolah, b.kecamatan")
                ->join('_tbl_sekolah b', 'a.npsn = b.npsn', 'left')
                ->where('a.ptk_terdaftar_id', $id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $data['pangkats'] = $this->_db->table('_tbl_rwy_kepangkatan a')
                    ->select("a.*")
                    ->where('a.ptk_id', $current->ptk_id)
                    ->orderBy('a.tanggal_sk', 'DESC')->get()->getResult();
                $data['pendidikans'] = $this->_db->table('_tbl_rwy_pendidikan_formal a')
                    ->select("a.*")
                    ->where('a.ptk_id', $current->ptk_id)
                    ->orderBy('a.tahun_lulus', 'DESC')->get()->getResult();
                $data['pembelajarans'] = $this->_db->table('_tbl_pembelajaran a')
                    ->select("a.*")
                    ->where('a.ptk_id', $current->ptk_id)
                    ->orderBy('a.mata_pelajaran_id', 'ASC')->get()->getResult();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('backbone/su/masterdata/ptk/detail', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function unlockallptk()
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
                $response->message = "Session telah habis.";
                return json_encode($response);
            }

            $currents = $this->_db->table('_ptk_tb')
                ->select("id, nuptk, nama, is_locked")
                ->where("is_locked = 1")->get()->getResult();

            $tw = $this->_db->table('_ref_tahun_tw')->where('is_current', 1)->orderBy('tahun', 'desc')->orderBy('tw', 'desc')->get()->getRowObject();

            if (!$tw) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tidak ada tahun TW aktif.";
                return json_encode($response);
            }
            if (count($currents) > 0) {
                $total = count($currents);
                $sukses = 0;
                $gagal = 0;
                foreach ($currents as $key => $ptk) {

                    $canGrantedPengajuan = canGrantedPengajuan($ptk->id, $tw->id);

                    if ($canGrantedPengajuan && $canGrantedPengajuan->code !== 200) {
                        $gagal++;
                        continue;
                    }
                    $this->_db->transBegin();
                    try {
                        $this->_db->table('_ptk_tb')->where(['id' => $ptk->id, 'is_locked' => 1])->update([
                            'is_locked' => 0,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    } catch (\Exception $e) {
                        $this->_db->transRollback();
                        $gagal++;
                        continue;
                    }

                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->transCommit();

                        createAktifitas($user->data->id, "Mengunlock dapa PTK $ptk->nama dengan NUPTK: $ptk->nuptk", "Mengunlock data PTK", "edit");

                        $sukses++;
                        continue;
                    } else {
                        $gagal++;
                        continue;
                    }
                }
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data PTK berhasil di unlock dengan jumlah TOTAL: $total, SUKSES: $sukses, GAGAL: $gagal.";
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tidak ada data PTK yang perlu di unlock.";
                return json_encode($response);
            }
        }
    }

    public function unlockdataptk()
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
            'ptk_id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'PTK Id tidak boleh kosong. ',
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
                . $this->validator->getError('npsn')
                . $this->validator->getError('ptk_id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $ptk_id = htmlspecialchars($this->request->getVar('ptk_id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session telah habis.";
                return json_encode($response);
            }

            $current = $this->_db->table('_ptk_tb')
                ->where(['id' => $id, 'id_ptk' => $ptk_id, 'npsn' => $npsn])->get()->getRowObject();

            if ($current) {
                if ((int)$current->is_locked == 0) {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Status Unlock PTK sudah terbuka.";
                    return json_encode($response);
                }
                $this->_db->transBegin();
                try {
                    $this->_db->table('_ptk_tb')->where(['id' => $current->id, 'is_locked' => 1])->update([
                        'is_locked' => 0,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } catch (\Exception $e) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengunlock data ptk.";
                    return json_encode($response);
                }

                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();

                    createAktifitas($user->data->id, "Mengunlock dapa PTK $nama dengan NUPTK: $current->nuptk", "Mengunlock data PTK", "edit");

                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Data PTK berhasil diunlock.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengunlock data PTK.";
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

    public function editdefaulinpassing()
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
            'ptk_id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'PTK Id tidak boleh kosong. ',
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
                . $this->validator->getError('npsn')
                . $this->validator->getError('ptk_id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $ptk_id = htmlspecialchars($this->request->getVar('ptk_id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $Profilelib = new Profilelib();
            $user = $Profilelib->user();
            if ($user->status != 200) {
                delete_cookie('jwt');
                session()->destroy();
                $response = new \stdClass;
                $response->status = 401;
                $response->message = "Session telah habis.";
                return json_encode($response);
            }

            $current = $this->_db->table('_ptk_tb')
                ->where(['id' => $id, 'id_ptk' => $ptk_id, 'npsn' => $npsn])->get()->getRowObject();

            if ($current) {
                if ((int)$current->is_locked == 1) {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mendefault data inpassing. Status PTK terkunci, silahkan unlock terlebih dahulu.";
                    return json_encode($response);
                }
                $this->_db->transBegin();
                try {
                    $this->_db->table('_ptk_tb')->where(['id' => $current->id, 'is_locked' => 0])->update([
                        'nomor_sk_impassing' => NULL,
                        'tgl_sk_impassing' => NULL,
                        'tmt_sk_impassing' => NULL,
                        'jabatan_angka_kredit' => NULL,
                        'pangkat_golongan_ruang' => NULL,
                        'masa_kerja_tahun_impassing' => NULL,
                        'masa_kerja_bulan_impassing' => NULL,
                        'jumlah_tunjangan_pokok_impassing' => NULL,
                        'lampiran_impassing' => NULL,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } catch (\Exception $e) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->error = var_dump($e);
                    $response->message = "Gagal mendefault data inpassing.";
                    return json_encode($response);
                }

                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    createAktifitas($user->data->id, "Mendafaultkan data Inpassing PTK $nama dengan NUPTK: $current->nuptk", "Mendefaultkan Inpassing data PTK", "edit");

                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Data inpassing berhasil didefaulkan.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->error = "tidak ada perubahan data";
                    $response->status = 400;
                    $response->message = "Gagal mendefault data inpassing.";
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

    public function editdefaulpendidikan()
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
            'ptk_id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'PTK Id tidak boleh kosong. ',
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
                . $this->validator->getError('npsn')
                . $this->validator->getError('ptk_id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $ptk_id = htmlspecialchars($this->request->getVar('ptk_id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $current = $this->_db->table('_ptk_tb')
                ->where(['id' => $id, 'id_ptk' => $ptk_id, 'npsn' => $npsn])->get()->getRowObject();

            if ($current) {
                $this->_db->transBegin();
                try {
                    $this->_db->table('_ptk_tb')->where(['id' => $current->id, 'is_locked' => 0])->update(['pendidikan' => NULL, 'updated_at' => date('Y-m-d H:i:s')]);
                } catch (\Exception $e) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mendefault data pendidikan.";
                    return json_encode($response);
                }

                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Data pendidikan berhasil didefaulkan.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mendefault data pendidikan.";
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
            'ptk_id' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'PTK Id tidak boleh kosong. ',
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
                . $this->validator->getError('npsn')
                . $this->validator->getError('ptk_id');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $ptk_id = htmlspecialchars($this->request->getVar('ptk_id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);

            $current = $this->_db->table('_ptk_tb')
                ->where(['id' => $id, 'id_ptk' => $ptk_id, 'npsn' => $npsn])->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('backbone/su/masterdata/ptk/edit', $data);
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
        if (!(grantTarikDataBackbone())) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = "Tarik data dari backbone masih dalam normalisasi system.";
            return json_encode($response);
        }

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
            'kecamatan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Kecamatan tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('nama')
                . $this->validator->getError('kecamatan');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $kecamatan = htmlspecialchars($this->request->getVar('kecamatan'), true);

            $apiLib = new Apilib();
            $result = $apiLib->syncSekolah($id, $kecamatan);

            if ($result) {
                if ($result->status == 200) {
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Syncrone Data Sekolah Berhasil Dilakukan.";
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
        // if (!(grantTarikDataBackbone())) {
        //     $response = new \stdClass;
        //     $response->status = 400;
        //     $response->message = "Tarik data dari backbone masih dalam normalisasi system.";
        //     return json_encode($response);
        // }

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

            $tw = $this->_helpLib->getCurrentTw();
            if (!$tw) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tahun Triwulan Active tidak ditemukan.";
                return json_encode($response);
            }

            $sekolahId = $this->_helpLib->getSekolahId($npsn);
            if (!$sekolahId) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Sekolah id tidak ditemukan.";
                return json_encode($response);
            }

            $checkAnySynToday = $this->_helpLib->checkAnySyncToday($npsn);
            $apiLib = new Apilib();
            if (!$checkAnySynToday) {
                $resultBack = $apiLib->syncPtkGetBackbone($npsn, $sekolahId);
                $insertAnySynToday = $this->_helpLib->insertSyncToday($npsn);
                if (!$insertAnySynToday) {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Data syncrone tidak ditemukan.";
                    return json_encode($response);
                }
            }

            if ($checkAnySynToday == date('Y-m-d')) {
                $resultBack = true;
            } else {
                $resultBack = $apiLib->syncPtkGetBackbone($npsn, $sekolahId);
            }

            if ($resultBack) {
                $result = $apiLib->syncPtkId($idPtk, $npsn, $tw);

                if ($result) {
                    // var_dump($result);
                    // die;
                    if ($result->status == 200) {
                        $response = new \stdClass;
                        $response->status = 200;
                        $response->message = "Tarik Data PTK $nama Berhasil Dilakukan.";
                        return json_encode($response);
                    } else {
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->error = $result;
                        $response->message = "Gagal Tarik Data.";
                        return json_encode($response);
                    }
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal Tarik Data";
                    return json_encode($response);
                }
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal Tarik Data";
                return json_encode($response);
            }
        }
    }

    public function syncpembenahan()
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

            $tw = $this->_helpLib->getCurrentTw();
            if (!$tw) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tahun Triwulan Active tidak ditemukan.";
                return json_encode($response);
            }

            $ptk = $this->_db->table('_ptk_tb')->where('id_ptk', $idPtk)->get()->getRowObject();

            if (!$ptk) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data PTK tidak ditemukan.";
                return json_encode($response);
            }

            $ptkAttr = $this->_db->table('_upload_data_attribut')->where(['id_ptk' => $ptk->id, 'id_tahun_tw' => $tw])->orderBy('created_at', 'DESC')->get()->getRowObject();
            if (!$ptkAttr) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data Atribut PTK tidak ditemukan.";
                return json_encode($response);
            }
            if ($ptkAttr->is_locked == 1) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data Atribut PTK Sudah Terkunci.";
                return json_encode($response);
            }

            $tgl = "";
            $golongan = "";
            $tmt = "";
            $tgl = "";
            $no = "";
            $jenis = "";
            $tahun = "";
            $bulan = "";

            if ($ptk->tmt_sk_kgb > $ptk->tmt_pangkat) {
                $tgl = $ptk->tgl_sk_kgb;
                $golongan = $ptk->pangkat_golongan_kgb;
                $tmt = $ptk->tmt_sk_kgb;
                $no = $ptk->sk_kgb;
                $jenis = "kgb";
                $tahun = $ptk->masa_kerja_tahun_kgb;
                $bulan = $ptk->masa_kerja_bulan_kgb;
            } else {
                $tgl = $ptk->tgl_sk_pangkat;
                $golongan = $ptk->pangkat_golongan;
                $tmt = $ptk->tmt_pangkat;
                $no = $ptk->nomor_sk_pangkat;
                $jenis = "pangkat";
                $tahun = $ptk->masa_kerja_tahun;
                $bulan = $ptk->masa_kerja_bulan;
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_upload_data_attribut')->where('id', $ptkAttr->id)->update([
                    'pang_golongan' => $golongan,
                    'pang_jenis' => $jenis,
                    'pang_no' => $no,
                    'pang_tmt' => $tmt,
                    'pang_tgl' => $tgl,
                    'pang_tahun' => $tahun,
                    'pang_bulan' => $bulan,
                ]);
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal melakukan pembaharuan data attribut.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Pembaharuan Data Atribut PTK $nama Berhasil Dilakukan.";
                return json_encode($response);
            } else {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal Melakukan Pembaharuan Data Atribut PTK";
                return json_encode($response);
            }
        }
    }

    public function unlockuploadspj()
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

            $ptk = $this->_db->table('_ptk_tb')->where('id_ptk', $idPtk)->get()->getRowObject();

            if (!$ptk) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data PTK tidak ditemukan.";
                return json_encode($response);
            }

            $spjTpg = $this->_db->table('_tb_spj_tpg')->select("id, id_ptk")->where('id_ptk', $ptk->id)->whereIn('status_usulan', [0, 3])->get()->getResult();
            $spjTamsil = $this->_db->table('_tb_spj_tamsil')->select("id, id_ptk")->where('id_ptk', $ptk->id)->whereIn('status_usulan', [0, 3])->get()->getResult();
            if (count($spjTpg) > 0 || count($spjTamsil) > 0) {
                $idPtknya = NULL;
                if (count($spjTpg) > 0) {
                    $idsSpj = [];
                    foreach ($spjTpg as $key => $value) {
                        $idsSpj[] = $value->id;
                        $idPtknya = $value->id_ptk;
                    }

                    $this->_db->table('_tb_spj_tpg')->whereIn('id', $idsSpj)->update(['lock_upload_spj' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                }
                if (count($spjTamsil) > 0) {
                    $idsSpjTamsil = [];
                    foreach ($spjTamsil as $key => $value) {
                        $idsSpjTamsil[] = $value->id;
                        $idPtknya = $value->id_ptk;
                    }

                    $this->_db->table('_tb_spj_tamsil')->whereIn('id', $idsSpjTamsil)->update(['lock_upload_spj' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                }

                try {
                    $this->_db->table('granted_upload_spj')->insert(['ptk_id' => $idPtknya, 'created_at' => date('Y-m-d H:i:s')]);
                } catch (\Throwable $th) {
                    //throw $th;
                }
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Upload SPJ untuk PTK $nama berhasil dilakukan.";
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Tidak ada SPJ yang perlu di unlock, karena sudah terverifikasi.";
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

            $current = $this->_db->table('_ptk_tb')
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $this->_db->transBegin();
                try {
                    $date = date('Y-m-d H:i:s');
                    $this->_db->query("INSERT INTO _ptk_tb_hapus(id, id_ptk, email, nama, gelar_depan, gelar_belakang, nik, nuptk, nip, nrg, no_peserta, npwp, no_rekening, cabang_bank, jenis_kelamin, tempat_lahir, tgl_lahir, status_tugas, tempat_tugas, npsn, kecamatan, id_kecamatan, no_hp, sk_cpns, tgl_cpns, sk_pengangkatan, tmt_pengangkatan, jenis_ptk, pendidikan, bidang_studi_pendidikan, bidang_studi_sertifikasi, status_kepegawaian, mapel_diajarkan, jam_mengajar_perminggu, jabatan_kepsek, jabatan_ks_plt, pangkat_golongan, nomor_sk_pangkat, tgl_sk_pangkat, tmt_pangkat, masa_kerja_tahun, masa_kerja_bulan, gaji_pokok, sk_kgb, tgl_sk_kgb, tmt_sk_kgb, masa_kerja_tahun_kgb, masa_kerja_bulan_kgb, gaji_pokok_kgb, mengajar_lain_satmikal, nomor_sk_impassing, tgl_sk_impassing, tmt_sk_impassing, jabatan_angka_kredit, pangkat_golongan_ruang, masa_kerja_tahun_impassing, masa_kerja_bulan_impassing, jumlah_tunjangan_pokok_impassing, lampiran_impassing, lampiran_foto, lampiran_karpeg, lampiran_ktp, lampiran_nrg, lampiran_nuptk, lampiran_serdik, lampiran_npwp, lampiran_buku_rekening, lampiran_ijazah, jenis_tunjangan, is_locked, created_at, updated_at, last_sync, created_ajuan)
                                        SELECT id, id_ptk, email, nama, gelar_depan, gelar_belakang, nik, nuptk, nip, nrg, no_peserta, npwp, no_rekening, cabang_bank, jenis_kelamin, tempat_lahir, tgl_lahir, status_tugas, tempat_tugas, npsn, kecamatan, id_kecamatan, no_hp, sk_cpns, tgl_cpns, sk_pengangkatan, tmt_pengangkatan, jenis_ptk, pendidikan, bidang_studi_pendidikan, bidang_studi_sertifikasi, status_kepegawaian, mapel_diajarkan, jam_mengajar_perminggu, jabatan_kepsek, jabatan_ks_plt, pangkat_golongan, nomor_sk_pangkat, tgl_sk_pangkat, tmt_pangkat, masa_kerja_tahun, masa_kerja_bulan, gaji_pokok, sk_kgb, tgl_sk_kgb, tmt_sk_kgb, masa_kerja_tahun_kgb, masa_kerja_bulan_kgb, gaji_pokok_kgb, mengajar_lain_satmikal, nomor_sk_impassing, tgl_sk_impassing, tmt_sk_impassing, jabatan_angka_kredit, pangkat_golongan_ruang, masa_kerja_tahun_impassing, masa_kerja_bulan_impassing, jumlah_tunjangan_pokok_impassing, lampiran_impassing, lampiran_foto, lampiran_karpeg, lampiran_ktp, lampiran_nrg, lampiran_nuptk, lampiran_serdik, lampiran_npwp, lampiran_buku_rekening, lampiran_ijazah, jenis_tunjangan, is_locked, created_at, updated_at, last_sync, CONCAT('$date', '') AS created_ajuan
                                        FROM _ptk_tb
                                        WHERE id = '$id'");

                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->table('_ptk_tb')->where('id', $id)->delete();
                        if ($this->_db->affectedRows() > 0) {
                            $this->_db->transCommit();
                            $response = new \stdClass;
                            $response->status = 200;
                            $response->message = "Berhasil hapus data.";
                            return json_encode($response);
                        } else {
                            $this->_db->transRollback();
                            $response = new \stdClass;
                            $response->status = 400;
                            $response->message = "Gagal hapus data.";
                            return json_encode($response);
                        }
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Gagal mengajukan hapus data.";
                        return json_encode($response);
                    }
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengajukan hapus data.";
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
                    'required' => 'Id PTK tidak boleh kosong. ',
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
                $response->message = "Permintaan diizinkan";
                return json_encode($response);
            }

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nrg = htmlspecialchars($this->request->getVar('nrg'), true);
            $no_peserta = htmlspecialchars($this->request->getVar('no_peserta'), true);
            $pendidikan = htmlspecialchars($this->request->getVar('pendidikan'), true);
            $bidang_studi_sertifikasi = htmlspecialchars($this->request->getVar('bidang_studi_sertifikasi'), true);
            $pangkat = htmlspecialchars($this->request->getVar('pangkat'), true);
            $no_sk_pangkat = htmlspecialchars($this->request->getVar('no_sk_pangkat'), true);
            $tgl_pangkat = htmlspecialchars($this->request->getVar('tgl_pangkat'), true);
            $tmt_pangkat = htmlspecialchars($this->request->getVar('tmt_pangkat'), true);
            $mkt_pangkat = htmlspecialchars($this->request->getVar('mkt_pangkat'), true);
            $mkb_pangkat = htmlspecialchars($this->request->getVar('mkb_pangkat'), true);
            $kgb = htmlspecialchars($this->request->getVar('kgb'), true);
            $no_sk_kgb = htmlspecialchars($this->request->getVar('no_sk_kgb'), true);
            $tgl_kgb = htmlspecialchars($this->request->getVar('tgl_kgb'), true);
            $tmt_kgb = htmlspecialchars($this->request->getVar('tmt_kgb'), true);
            $mkt_kgb = htmlspecialchars($this->request->getVar('mkt_kgb'), true);
            $mkb_kgb = htmlspecialchars($this->request->getVar('mkb_kgb'), true);

            $oldData =  $this->_db->table('_ptk_tb')->where('id', $id)->get()->getRowObject();

            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan.";
                return json_encode($response);
            }

            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($nrg !== "") {
                $data['nrg'] = $nrg;
            }
            if ($no_peserta !== "") {
                $data['no_peserta'] = $no_peserta;
            }
            if ($pendidikan !== "") {
                $data['pendidikan'] = $pendidikan;
            }
            if ($bidang_studi_sertifikasi !== "") {
                $data['bidang_studi_sertifikasi'] = $bidang_studi_sertifikasi;
            }
            if ($pangkat !== "") {
                $data['pangkat_golongan'] = $pangkat;
            }
            if ($no_sk_pangkat !== "") {
                $data['nomor_sk_pangkat'] = $no_sk_pangkat;
            }
            if ($tgl_pangkat !== "") {
                $data['tgl_sk_pangkat'] = $tgl_pangkat;
            }
            if ($tmt_pangkat !== "") {
                $data['tmt_pangkat'] = $tmt_pangkat;
            }
            if ($mkt_pangkat !== "") {
                $data['masa_kerja_tahun'] = $mkt_pangkat;
            }
            if ($mkb_pangkat !== "") {
                $data['masa_kerja_bulan'] = $mkb_pangkat;
            }
            if ($kgb !== "") {
                $data['pangkat_golongan_kgb'] = $kgb;
            }
            if ($no_sk_kgb !== "") {
                $data['sk_kgb'] = $no_sk_kgb;
            }
            if ($tgl_kgb !== "") {
                $data['tgl_sk_kgb'] = $tgl_kgb;
            }
            if ($tmt_kgb !== "") {
                $data['tmt_sk_kgb'] = $tmt_kgb;
            }
            if ($mkt_kgb !== "") {
                $data['masa_kerja_tahun_kgb'] = $mkt_kgb;
            }
            if ($mkb_kgb !== "") {
                $data['masa_kerja_bulan_kgb'] = $mkb_kgb;
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('_ptk_tb')->where('id', $oldData->id)->update($data);
            } catch (\Exception $e) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan gambar baru.";
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
        }
    }
}
