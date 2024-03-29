<?php

namespace App\Libraries;

class Mtlib
{
    private $_db;
    function __construct()
    {
        helper(['text', 'session', 'cookie', 'array', 'filesystem']);
        $this->_db      = \Config\Database::connect();
    }

    public function get($id = 1, $table = '_tb_maintenance')
    {

        $user = $this->_db->table($table)
            ->where(['id' => $id, 'status' => 1])
            ->countAllResults();

        if ($user > 0) {
            return true;
        }

        return false;
    }

    public function getAccess($userId)
    {

        $granted = $this->_db->table('granted_mt')
            ->where('id', $userId)
            ->countAllResults();

        if ($granted > 0) {
            return true;
        }

        return false;
    }
}
