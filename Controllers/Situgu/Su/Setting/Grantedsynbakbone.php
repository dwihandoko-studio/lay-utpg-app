<?php

namespace App\Controllers\Situgu\Su\Setting;

use App\Controllers\BaseController;
use Config\Services;
use App\Libraries\Profilelib;
use App\Libraries\Apilib;

class Grantedsynbakbone extends BaseController
{
    var $folderImage = 'masterdata';
    private $_db;
    private $model;

    function __construct()
    {
        helper(['text', 'file', 'form', 'session', 'array', 'imageurl', 'web', 'filesystem']);
        $this->_db      = \Config\Database::connect();
    }

    public function index()
    {
        $data['title'] = 'GRANTED SYNCRONE BACKBONE';
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();
        if ($user->status != 200) {
            delete_cookie('jwt');
            session()->destroy();
            return redirect()->to(base_url('auth'));
        }

        $data['status_syn'] = $this->_db->table('granted_syncrone_backbone')->where(['id' => 1, 'status' => 1])->countAllResults();
        $data['status_syn_local'] = $this->_db->table('granted_syncrone_backbone')->where(['id' => 2, 'status' => 1])->countAllResults();
        $data['status_edit_kgb'] = $this->_db->table('granted_syncrone_backbone')->where(['id' => 3, 'status' => 1])->countAllResults();

        $data['user'] = $user->data;

        return view('situgu/su/setting/synbackbone/index', $data);
    }

    public function disabled()
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
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('jenis');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $jenis = htmlspecialchars($this->request->getVar('jenis'), true);

            $this->_db->transBegin();
            try {
                $this->_db->table('granted_syncrone_backbone')->where(['id' => $jenis, 'status' => 1])->update(['status' => 0]);

                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Granted $id Berhasil Dinonaktifkan.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal menonaktifkan Granted $id.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal menonaktifkan Granted $id.";
                return json_encode($response);
            }
        }
    }

    public function active()
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
        ];

        if (!$this->validate($rules)) {
            $response = new \stdClass;
            $response->status = 400;
            $response->message = $this->validator->getError('id')
                . $this->validator->getError('jenis');
            return json_encode($response);
        } else {
            $id = htmlspecialchars($this->request->getVar('id'), true);
            $jenis = htmlspecialchars($this->request->getVar('jenis'), true);

            $this->_db->transBegin();
            try {
                $this->_db->table('granted_syncrone_backbone')->where(['id' => $jenis, 'status' => 0])->update(['status' => 1]);

                if ($this->_db->affectedRows() > 0) {
                    $this->_db->transCommit();
                    $response = new \stdClass;
                    $response->status = 200;
                    $response->message = "Granted $id Berhasil Diaktifkan.";
                    return json_encode($response);
                } else {
                    $this->_db->transRollback();
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal mengaktifkan Granted $id.";
                    return json_encode($response);
                }
            } catch (\Throwable $th) {
                $this->_db->transRollback();
                $response = new \stdClass;
                $response->status = 400;
                $response->message = "Gagal mengaktifkan Granted $id.";
                return json_encode($response);
            }
        }
    }
}
