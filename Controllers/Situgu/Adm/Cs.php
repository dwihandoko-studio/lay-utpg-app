<?php

namespace App\Controllers\Situgu\Adm;

use App\Controllers\BaseController;
use App\Models\Situgu\Adm\AduanModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;
use App\Libraries\Helplib;
use App\Libraries\Emaillib;

class Cs extends BaseController
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
    public function getAllAntrian()
    {
        $request = Services::request();
        $datamodel = new AduanModel($request);

        $jwt = get_cookie('jwt');
        $token_jwt = getenv('token_jwt.default.key');
        if ($jwt) {
            try {
                $decoded = JWT::decode($jwt, new Key($token_jwt, 'HS256'));
                if ($decoded) {
                    $userId = $decoded->id;
                    $level = $decoded->level;
                } else {
                    $output = [
                        "draw" => $request->getPost('draw'),
                        "recordsTotal" => 0,
                        "recordsFiltered" => 0,
                        "data" => []
                    ];
                    echo json_encode($output);
                    return;
                }
            } catch (\Exception $e) {
                $output = [
                    "draw" => $request->getPost('draw'),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => []
                ];
                echo json_encode($output);
                return;
            }
        }

        $lists = $datamodel->get_datatables(0);
        $data = [];
        $no = $request->getPost("start");
        foreach ($lists as $list) {
            $no++;
            $row = [];

            $row[] = $no;
            if ($list->status_ajuan == 2) {
                $action = '<div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
                        </div>
                    </div>';
            } else {
                $action = '<div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action <i class="mdi mdi-chevron-down"></i></button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="javascript:actionDetail(\'' . $list->id . '\');"><i class="bx bxs-show font-size-16 align-middle"></i> &nbsp;Detail</a>
                            <a class="dropdown-item" href="javascript:actionHapus(\'' . $list->id . '\');"><i class="bx bx-trash font-size-16 align-middle"></i> &nbsp;Hapus</a>
                        </div>
                    </div>';
            }
            // $action = '<a href="javascript:actionDetail(\'' . $list->id . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama)) . '\');"><button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bxs-show font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionSync(\'' . $list->id . '\', \'' . $list->id_ptk . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk  . '\', \'' . $list->npsn . '\');"><button type="button" class="btn btn-secondary btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-transfer-alt font-size-16 align-middle"></i></button>
            //     </a>
            //     <a href="javascript:actionHapus(\'' . $list->id . '\', \'' . str_replace('&#039;', "`", str_replace("'", "`", $list->nama))  . '\', \'' . $list->nuptk . '\');" class="delete" id="delete"><button type="button" class="btn btn-danger btn-sm btn-rounded waves-effect waves-light mr-2 mb-1">
            //     <i class="bx bx-trash font-size-16 align-middle"></i></button>
            //     </a>';
            $row[] = $action;
            switch ($list->status_ajuan) {
                case 1:
                    $row[] = '<div class="text-center">
                            <span class="badge rounded-pill badge-soft-danger font-size-11">Ditolak</span>
                        </div>';
                    break;
                case 2:
                    $row[] = '<div class="text-center">
                            <span class="badge rounded-pill badge-soft-success font-size-11">Selesai</span>
                        </div>';
                    break;
                default:
                    $row[] = '<div class="text-center">
                        <span class="badge rounded-pill badge-soft-info font-size-11">Menunggu</span>
                    </div>';
                    break;
            }
            $row[] = $list->created_at;
            $row[] = $list->jenis;
            $row[] = $list->kategori;
            $row[] = $list->npsn;
            $row[] = $list->isi;
            switch ($list->status) {
                case 1:
                    $row[] = '<div class="text-center">
                            <span class="badge rounded-pill badge-soft-danger font-size-11">Urgent</span>
                        </div>';
                    break;
                default:
                    $row[] = '<div class="text-center">
                        <span class="badge rounded-pill badge-soft-warning font-size-11">Tidak Urgent</span>
                    </div>';
                    break;
            }

            $data[] = $row;
        }
        $output = [
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datamodel->count_all(0),
            "recordsFiltered" => $datamodel->count_filtered(0),
            "data" => $data
        ];
        echo json_encode($output);
    }

    public function index()
    {
        return redirect()->to(base_url('situgu/adm/cs/antrian'));
    }

    public function antrian()
    {
        $data['title'] = 'PENGADUAN';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;

        $data['data'] = $user->data;

        return view('situgu/adm/cs/antrian', $data);
        // return view('situgu/ops/404', $data);
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
                    'required' => 'Action tidak boleh kosong. ',
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
                $response->message = "Session telah habis";
            }

            $current = $this->_db->table('aduan_tb a')
                ->select("a.*, b.fullname, c.nama")
                ->join('v_user b', 'a.user_id = b.id')
                ->join('ref_sekolah c', 'a.npsn = c.npsn')
                ->where('a.id', $id)->get()->getRowObject();

            if (!$current) {
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Aduan tidak ditemukan.";
            }
            $ptks = explode(",", $current->ptks);
            if (count($ptks) > 0) {
                $data['ptks'] = $this->_db->table('_ptk_tb')
                    ->select("id, nuptk, nama, nip, status_kepegawaian")
                    ->whereIn('id', $ptks)
                    ->get()->getResult();
            }
            $data['data'] = $current;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";

            $response->data = view('situgu/adm/cs/detail', $data);

            return json_encode($response);
        }
    }

    public function approve()
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
                . $this->validator->getError('nama')
                . $this->validator->getError('keterangan');
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
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $keterangan = htmlspecialchars($this->request->getVar('keterangan'), true);

            $oldData = $this->_db->table('aduan_tb')->where(['id' => $id])->get()->getRowObject();
            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 201;
                $response->message = "Aduan tidak ditemukan.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('aduan_tb')->where('id', $oldData->id)->update(['keterangan' => $keterangan, 'date_approve' => date('Y-m-d H:i:s'), 'status_ajuan' => 2, 'admin_eksekusi' => $user->data->id]);
                if ($this->_db->affectedRows() > 0) {

                    $pengadu = $this->_db->table('v_user')->where('id', $oldData->user_id)->get()->getRowObject();
                    if ($pengadu) {
                        if ($pengadu->wa_verified == 1) {
                            try {
                                $nomor = $pengadu->no_hp;
                                if (substr($nomor, 0, 1) == 0) {
                                    $nomor = "+62" . substr($nomor, 1);
                                }

                                if (substr($nomor, 0, 1) == 8) {
                                    $nomor = "+62" . substr($nomor, 0);
                                }

                                if (substr($nomor, 0, 2) == 62) {
                                    $nomor = "+62" . substr($nomor, 2);
                                }

                                $nama = $user->data->fullname;
                                $message = "Hallo *$nama*....!!!\n______________________________________________________\n\n*PENGADUAN ANDA* pada *SI-TUGU* mengenai : \n$oldData->isi\nBerhasil diproses dengan keterangan:\n*$keterangan*\n\n\nPesan otomatis dari *SI-TUGU Kab. Lampung Tengah*\n_________________________________________________";

                                $dataReq = [
                                    'number' => (string)$nomor,
                                    'message' => $message,
                                ];

                                $ch = curl_init("https://whapi.kntechline.id/send-message");
                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataReq));
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                    'Content-Type: application/json'
                                ));
                                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

                                $server_output = curl_exec($ch);
                                curl_close($ch);
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                        }
                        if ($pengadu->email_verified == 1) {
                            try {
                                $message = '<p style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;color:#74787e;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">Pengaduan anda mengenai ' . $oldData->isi . ' telah diproses admin dengan keterangan: ';
                                $message .= $keterangan;
                                $message .= '</p>';
                                $emailLib = new Emaillib();
                                $sendEmail = $emailLib->sendNotifikasi($pengadu->email, "Aduan mengenai " . $oldData->jenis . " diproses", $message);

                                if ($sendEmail->code == 200) {
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                        }
                    }
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Aduan admin sekolah $nama berhasil diproses.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal memproses aduan admin sekolah $nama.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal memproses aduan admin sekolah $nama.";
                return json_encode($response);
            }
        }
    }

    public function formapprove()
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

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);

            $data['id'] = $id;
            $data['nama'] = $nama;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/adm/cs/solved', $data);
            return json_encode($response);
        }
    }

    public function formtolak()
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

            $id = htmlspecialchars($this->request->getVar('id'), true);
            $nama = htmlspecialchars($this->request->getVar('nama'), true);

            $data['id'] = $id;
            $data['nama'] = $nama;
            $response = new \stdClass;
            $response->status = 200;
            $response->message = "Permintaan diizinkan";
            $response->data = view('situgu/adm/cs/tolak', $data);
            return json_encode($response);
        }
    }

    public function tolak()
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
                . $this->validator->getError('nama')
                . $this->validator->getError('keterangan');
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
            $nama = htmlspecialchars($this->request->getVar('nama'), true);
            $keterangan = htmlspecialchars($this->request->getVar('keterangan'), true);

            $oldData = $this->_db->table('aduan_tb')->where(['id' => $id])->get()->getRowObject();
            if (!$oldData) {
                $response = new \stdClass;
                $response->status = 201;
                $response->message = "Aduan tidak ditemukan.";
                return json_encode($response);
            }

            $this->_db->transBegin();
            try {
                $this->_db->table('aduan_tb')->where('id', $oldData->id)->update(['status_ajuan' => 1, 'keterangan' => $keterangan, 'admin_eksekusi' => $user->data->id, 'date_reject' => date('Y-m-d H:i:s')]);
                if ($this->_db->affectedRows() > 0) {
                    $pengadu = $this->_db->table('v_user')->where('id', $oldData->user_id)->get()->getRowObject();
                    if ($pengadu) {
                        if ($pengadu->wa_verified == 1) {
                            try {
                                $nomor = $pengadu->no_hp;
                                if (substr($nomor, 0, 1) == 0) {
                                    $nomor = "+62" . substr($nomor, 1);
                                }

                                if (substr($nomor, 0, 1) == 8) {
                                    $nomor = "+62" . substr($nomor, 0);
                                }

                                if (substr($nomor, 0, 2) == 62) {
                                    $nomor = "+62" . substr($nomor, 2);
                                }

                                $nama = $user->data->fullname;
                                $message = "Hallo *$nama*....!!!\n______________________________________________________\n\n*PENGADUAN ANDA* pada *SI-TUGU* mengenai : \n$oldData->isi\nDitolak dengan keterangan:\n*$keterangan*\n\n\nPesan otomatis dari *SI-TUGU Kab. Lampung Tengah*\n_________________________________________________";

                                $dataReq = [
                                    'number' => (string)$nomor,
                                    'message' => $message,
                                ];

                                $ch = curl_init("https://whapi.kntechline.id/send-message");
                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataReq));
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                    'Content-Type: application/json'
                                ));
                                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

                                $server_output = curl_exec($ch);
                                curl_close($ch);
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                        }
                        if ($pengadu->email_verified == 1) {
                            try {
                                $message = '<p style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;color:#74787e;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">Pengaduan anda mengenai ' . $oldData->isi . ' telah ditolak admin dengan keterangan: ';
                                $message .= $keterangan;
                                $message .= '</p>';
                                $emailLib = new Emaillib();
                                $sendEmail = $emailLib->sendNotifikasi($pengadu->email, "Aduan mengenai " . $oldData->jenis . " ditolak", $message);

                                if ($sendEmail->code == 200) {
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                        }
                    }
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Aduan admin sekolah $nama berhasil ditolak.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal menolak aduan admin sekolah $nama.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($th);
                $response->message = "Gagal menolak aduan admin sekolah $nama.";
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
            'jenis' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis tidak boleh kosong. ',
                ]
            ],
            'npsn' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NPSN tidak boleh kosong. ',
                ]
            ],
            'isi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Deskripsi tidak boleh kosong. ',
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status tidak boleh kosong. ',
                ]
            ],
        ];

        $filenamelampiran = dot_array_search('_file.name', $_FILES);
        if ($filenamelampiran != '') {
            $lampiranVal = [
                '_file' => [
                    'rules' => 'uploaded[_file]|max_size[_file,1024]|mime_in[_file,image/jpeg,image/jpg,image/png]',
                    'errors' => [
                        'uploaded' => 'Pilih gambar berita terlebih dahulu. ',
                        'max_size' => 'Ukuran gambar berita terlalu besar. ',
                        'mime_in' => 'Ekstensi yang anda upload harus berekstensi gambar. '
                    ]
                ],
            ];
            $rules = array_merge($rules, $lampiranVal);
        }

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('npsn')
                . $this->validator->getError('isi')
                . $this->validator->getError('status')
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
                $response->message = "Session telah habis.";
                return json_encode($response);
            }

            $jenis = htmlspecialchars($this->request->getVar('jenis'), true);
            $npsn = htmlspecialchars($this->request->getVar('npsn'), true);
            $ptks = $this->request->getVar('ptks');
            $isi = htmlspecialchars($this->request->getVar('isi'), true);
            $status = htmlspecialchars($this->request->getVar('status'), true);

            // if (count($ptks) < 1) {
            //     $id_ptks = null;
            // } else {
            //     $id_ptks = implode(",", $ptks);
            // }

            $data = [
                'user_id' => $user->data->id,
                'jenis' => $jenis,
                'npsn' => $npsn,
                'ptks' => $ptks,
                'isi' => $isi,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $dir = FCPATH . "upload/aduan";
            $field_db = 'lampiran';
            $table_db = 'aduan_tb';
            if ($filenamelampiran != '') {
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
            }
            $this->_db->transBegin();
            try {
                $this->_db->table($table_db)->insert($data);
            } catch (\Exception $e) {
                if ($filenamelampiran != '') {
                    unlink($dir . '/' . $newNamelampiran);
                }
                $this->_db->transRollback();

                $response = new \stdClass;
                $response->status = 400;
                $response->error = var_dump($e);
                $response->message = "Gagal mengirim data.";
                return json_encode($response);
            }

            if ($this->_db->affectedRows() > 0) {
                $this->_db->transCommit();
                $response = new \stdClass;
                $response->status = 200;
                $response->message = "Data berhasil dikirm.";
                return json_encode($response);
            } else {
                if ($filenamelampiran != '') {
                    unlink($dir . '/' . $newNamelampiran);
                }
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengirim data";
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

            $current = $this->_db->table('aduan_tb')
                ->where('id', $id)->get()->getRowObject();

            if ($current) {
                $this->_db->transBegin();
                try {
                    $this->_db->table('aduan_tb')->where('id', $id)->delete();

                    if ($this->_db->affectedRows() > 0) {
                        if ($current->lampiran !== null) {

                            try {
                                $dir = FCPATH . "uploads/aduan";
                                unlink($dir . '/' . $current->lampiran);
                            } catch (\Throwable $err) {
                            }
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
}
