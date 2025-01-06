<?php

namespace App\Controllers\Sigaji\Su\Masterdata;

use App\Controllers\BaseController;
use App\Models\Sigaji\Su\RefbulanModel;
use Config\Services;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Uuid;

class Refbulan extends BaseController
{
    var $folderImage = 'masterdata';
    private $_db;
    private $model;

    function __construct()
    {
        helper(['text', 'file', 'form', 'session', 'array', 'imageurl', 'web', 'filesystem']);
        $this->_db      = \Config\Database::connect('sigaji');
    }

    public function getAll()
    {
        $request = Services::request();
        $datamodel = new RefbulanModel($request);


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
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\', \'' . $list->tahun . '\', \'' . $list->bulan . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>' .
                ((int)checkIsLockedCetakSPJ($list->id) == 0 ? '' : '<a class="dropdown-item" href="javascript:actionLocked(\'' . $list->id . '\', \'' . $list->tahun . '\', \'' . $list->bulan . '\');"><i class="fas fa-lock font-size-16 align-middle"></i> &nbsp;Kunci Untuk Cetak SPJ</a>') .
                ((int)$list->is_current == 1 ? '' : '<a class="dropdown-item" href="javascript:actionActived(\'' . $list->id . '\', \'' . $list->tahun . '\', \'' . $list->bulan . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Aktifkan Tahun Bulan</a>') . '
                            <a class="dropdown-item" href="javascript:actionHapus(\'' . $list->id . '\', \'' . $list->tahun . '\', \'' . $list->bulan . '\');"><i class="bx bx-trash font-size-16 align-middle"></i> &nbsp;Hapus</a>
                        </div>
                    </div>';
            // $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama)  . '\', \'' . $list->kode_kecamatan . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace("'", "", $list->nama) . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            $row[] = $list->tahun;
            $row[] = $list->bulan;
            $row[] = $list->bulan_name;
            $row[] = (int)$list->is_current == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>';

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
        return redirect()->to(base_url('sigaji/su/masterdata/refbulan/data'));
    }

    public function data()
    {
        $data['title'] = 'Sekolah';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;

        return view('sigaji/su/masterdata/refbulan/index', $data);
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

            $id = htmlspecialchars($this->request->getVar('id'), true);

            $current = $this->_db->table('_ref_tahun_bulan')
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $data['data'] = $current;
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Permintaan diizinkan";
                $response->data = view('sigaji/su/masterdata/refbulan/detail', $data);
                return json_encode($response);
            } else {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data tidak ditemukan";
                return json_encode($response);
            }
        }
    }

    public function add()
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

            // $id = htmlspecialchars($this->request->getVar('id'), true);

            // $current = $this->_db->table('_users_tb')
            //     ->where('uid', $id)->get()->getRowObject();

            // if ($current) {
            $data['data'] = $user->data;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('sigaji/su/masterdata/refbulan/add', $data);
            return json_encode($response);
            // } else {
            //     $response = new \stdClass;
            //     $response->status = 400;
            //     $response->message = "Data tidak ditemukan";
            //     return json_encode($response);
            // }
        }
    }

    public function aktifkan()
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
            'tahun' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Tahun tidak boleh kosong. ',
                ]
            ],
            'bulan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id') .
                $this->validator->getError('tahun') .
                $this->validator->getError('bulan');
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
            $current = $this->_db->table('_ref_tahun_bulan')
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $this->_db->transBegin();
                try {
                    $this->_db->table('_ref_tahun_bulan')->where("id IS NOT NULL")->update(['is_current' => 0]);
                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->table('_ref_tahun_bulan')->where('id', $id)->update(['is_current' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

                        if ($this->_db->affectedRows() > 0) {
                            // try {
                            //     $dir = FCPATH . "uploads/user";
                            //     unlink($dir . '/' . $current->image);
                            // } catch (\Throwable $err) {
                            // }
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
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Data gagal disimpan.";
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

    public function lockedspj()
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
            'tahun' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Tahun tidak boleh kosong. ',
                ]
            ],
            'bulan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'TW tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id') .
                $this->validator->getError('tahun') .
                $this->validator->getError('bulan');
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
            $current = $this->_db->table('tb_gaji_sipd')
                ->where(['tahun' => $id, 'is_locked' => 0])->countAllResults();

            if ($current > 0) {
                $this->_db->transBegin();
                try {
                    $this->_db->table('tb_gaji_sipd')->where([['tahun' => $id, 'is_locked' => 0]])->update(['is_locked' => 1]);
                    if ($this->_db->affectedRows() > 0) {
                        $this->_db->transCommit();
                        $response = new \stdClass;
                        $response->status = 200;
                        $response->message = "Data SPJ berhasil dikunci untuk Cetak SPJ.";
                        return json_encode($response);
                    } else {
                        $this->_db->transRollback();
                        $response = new \stdClass;
                        $response->status = 400;
                        $response->message = "Data gagal dikunci.";
                        return json_encode($response);
                    }
                } catch (\Throwable $th) {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Data gagal dikunci.";
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
            $current = $this->_db->table('_ref_tahun_bulan')
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $this->_db->transBegin();
                try {
                    $this->_db->table('_ref_tahun_bulan')->where('id', $id)->delete();

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
            'tahun' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Tahun tidak boleh kosong. ',
                ]
            ],
            'bulan' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Bulan tidak boleh kosong. ',
                ]
            ],
            'bulan_name' => [
                'rules' => 'required|trim',
                'errors' => [
                    'required' => 'Bulan name tidak boleh kosong. ',
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status tidak boleh kosong. ',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('tahun')
                . $this->validator->getError('bulan')
                . $this->validator->getError('bulan_name')
                . $this->validator->getError('status');
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

            $tahun = htmlspecialchars($this->request->getVar('tahun'), true);
            $bulan = htmlspecialchars($this->request->getVar('bulan'), true);
            $bulan_name = htmlspecialchars($this->request->getVar('bulan_name'), true);
            $status = htmlspecialchars($this->request->getVar('status'), true);

            $oldData =  $this->_db->table('_ref_tahun_bulan')->where(['tahun' => $tahun, 'bulan' => $bulan])->get()->getRowObject();

            if ($oldData) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Data Tahun Bulan sudah ada dalam Database.";
                return json_encode($response);
            }

            // if (
            //     $nama === $oldData->fullname
            //     && $email === $oldData->email
            //     && $nohp === $oldData->no_hp
            //     && $nip === $oldData->nip
            //     && $alamat === $oldData->alamat
            //     && (int)$status === (int)$oldData->is_active
            // ) {
            //     if ($filenamelampiran == '') {
            //         $response = new \stdClass;
            //         $response->status = 201;
            //         $response->message = "Tidak ada perubahan data yang disimpan.";
            //         $response->redirect = base_url('a/setting/pengguna/data');
            //         return json_encode($response);
            //     }
            // }

            // if ($email !== $oldData->email) {
            //     $cekData = $this->_db->table('_users_tb')->where(['email' => $email])->get()->getRowObject();
            //     if ($cekData) {
            //         $response = new \stdClass;
            //         $response->status = 400;
            //         $response->message = "Email sudah terdaftar.";
            //         return json_encode($response);
            //     }
            // }

            $uuidLib = new Uuid();


            $data = [
                'id' => $uuidLib->v4(),
                'tahun' => $tahun,
                'bulan' => $bulan,
                'bulan_name' => $bulan_name,
                'is_current' => $status,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            // $dir = FCPATH . "uploads/user";

            // if ($filenamelampiran != '') {
            //     $lampiran = $this->request->getFile('file');
            //     $filesNamelampiran = $lampiran->getName();
            //     $newNamelampiran = _create_name_foto($filesNamelampiran);

            //     if ($lampiran->isValid() && !$lampiran->hasMoved()) {
            //         $lampiran->move($dir, $newNamelampiran);
            //         $data['image'] = $newNamelampiran;
            //     } else {
            //         $response = new \stdClass;
            //         $response->status = 400;
            //         $response->message = "Gagal mengupload gambar.";
            //         return json_encode($response);
            //     }
            // }

            $this->_db->transBegin();
            try {
                $this->_db->table('_ref_tahun_bulan')->insert($data);
            } catch (\Exception $e) {
                // unlink($dir . '/' . $newNamelampiran);
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan data baru.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                // try {
                //     unlink($dir . '/' . $oldData->image);
                // } catch (\Throwable $th) {
                // }
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil dimpan.";
                $response->redirect = base_url('sigaji/su/masterdata/refbulan');
                return json_encode($response);
            } else {
                // unlink($dir . '/' . $newNamelampiran);
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menyimpan data";
                return json_encode($response);
            }
        }
    }
}
