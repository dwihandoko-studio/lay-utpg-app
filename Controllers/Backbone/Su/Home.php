<?php

namespace App\Controllers\Backbone\Su;

use App\Controllers\BaseController;
use App\Libraries\Profilelib;

// header("Access-Control-Allow-Origin: * ");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: POST");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
class Home extends BaseController
{
    var $folderImage = 'masterdata';
    private $_db;

    function __construct()
    {
        helper(['text', 'file', 'form', 'session', 'array', 'imageurl', 'web', 'filesystem']);
        $this->_db      = \Config\Database::connect('backbone');
    }

    public function index()
    {
        $Profilelib = new Profilelib();
        $user = $Profilelib->user();

        if (!$user || $user->status !== 200) {
            session()->destroy();
            delete_cookie('jwt');
            return redirect()->to(base_url('auth'));
        }

        $data['user'] = $user->data;
        $data['title'] = 'Dashboard';
        $data['admin'] = true;

        return view('backbone/su/home/index', $data);
    }

    public function testingtele()
    {
        $nama = "BEJO";
        $oldData = new \stdClass;
        $oldData->id_ptk = "3b3054a4-9711-4fc0-bf90-06d54da07cc4";
        $oldData->kode_usulan = "TPG-2023-1-10801864-1686904037";
        $oldData->admin = "TESTER";

        $getChatId = getChatIdTelegramPTK($oldData->id_ptk);
        if ($getChatId) {
            $tokenTele = "6504819187:AAEtykjIx2Gjd229nUgDHRlwJ5xGNTMjO0A";
            $message = "Hallo <b>$nama</b>....!!!\n______________________________________________________\n\n<b>USULAN ANDA</b> pada <b>SI-TUGU</b> dengan kode usulan : \n<b>$oldData->kode_usulan</b>\nTelah disetujui oleh Admin Verifikator:\n<b>$oldData->admin</b>\n\n\nPesan otomatis dari <b>SI-TUGU Kab. Lampung Tengah</b>\n_________________________________________________";
            try {

                $dataReq = [
                    'chat_id' => $getChatId,
                    "parse_mode" => "HTML",
                    'text' => $message,
                ];

                $ch = curl_init("https://api.telegram.org/bot$tokenTele/sendMessage");
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

                var_dump($server_output);
            } catch (\Throwable $th) {
                var_dump($th);
            }
        }
    }
}
