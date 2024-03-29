<?php

namespace App\Controllers\Sigaji;

use App\Controllers\BaseController;
use App\Libraries\Profilelib;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\Mtlib;

class Maintenance extends BaseController
{
    private $_db;

    function __construct()
    {
        helper(['text', 'file', 'form', 'array', 'imageurl', 'web', 'filesystem']);
        $this->_db      = \Config\Database::connect();
    }
    public function index()
    {
        $mtLib = new Mtlib();
        if (!$mtLib->get(3, '_tb_maintenance_gaji')) {
            return redirect()->to(base_url('sigaji'));
        }

        $data['title'] = "MAINTENANCE";
        $template = $this->_db->table('_tb_maintenance_gaji')->where(['id' => 3, 'template_active' => 1])->get()->getRowObject();
        if ($template) {
            if ($template->template !== null) {
                $activeTemplate = $template->template;
            } else {
                $activeTemplate = 'index';
            }
        } else {
            $activeTemplate = 'index';
        }
        return view('sigaji/mt/' . $activeTemplate, $data);
    }
}
