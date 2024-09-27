<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Apilib
{
    private $_db;
    function __construct()
    {
        helper(['text', 'session', 'cookie', 'array', 'filesystem']);
        $this->_db      = \Config\Database::connect();
    }

    private function _send_get_backbone($methode, $npsn, $sekolahId)
    {
        $urlendpoint = 'http://192.168.33.243:1992/' . $methode;
        $apiToken = '0b4e06f30dc26c36f322580591e0a07b';

        $curlHandle = curl_init($urlendpoint);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'X-API-TOKEN: ' . $apiToken,
            'X-API-NPSN: ' . $npsn,
            'X-API-SEKOLAHID: ' . $sekolahId,
            'Content-Type: application/json'
        ));
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 30);

        return $curlHandle;
    }

    private function _send_get($methode, $jwt)
    {
        $urlendpoint = getenv('be.default.url') . $methode;
        $apiToken = getenv('be.default.api_token');

        $curlHandle = curl_init($urlendpoint);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'X-API-TOKEN: ' . $apiToken,
            'Authorization: Bearer ' . $jwt,
            'Content-Type: application/json'
        ));
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 30);

        return $curlHandle;
    }

    private function _send_post($data, $methode, $jwt)
    {
        $urlendpoint = getenv('be.default.url') . $methode;
        $apiToken = getenv('be.default.api_token');

        $curlHandle = curl_init($urlendpoint);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'X-API-TOKEN: ' . $apiToken,
            'Authorization: Bearer ' . $jwt,
            'Content-Type: application/json'
        ));
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 120);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 120);


        return $curlHandle;
    }

    public function getUser()
    {
        $jwt = get_cookie('jwt');
        if ($jwt) {
            $add         = $this->_send_get('user', $jwt);
            $send_data         = curl_exec($add);

            $result = json_decode($send_data);


            if (isset($result->error)) {
                return false;
            }

            if ($result) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function syncSekolah($id, $kecamatan)
    {
        $jwt = get_cookie('jwt');
        if ($jwt) {
            $data = [
                'id' => $id,
                'kode_kecamatan' => $kecamatan,
            ];
            $add         = $this->_send_post($data, 'syncsekolahid', $jwt);
            $send_data         = curl_exec($add);

            $result = json_decode($send_data);


            if (isset($result->error)) {
                return false;
            }

            if ($result) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function syncPtk($npsn, $tw)
    {
        $jwt = get_cookie('jwt');
        if ($jwt) {
            $tmtTarikan = getTmtTarikanSync();
            if ($tmtTarikan->code !== 200) {
                return false;
            }
            $data = [
                'npsn' => $npsn,
                'tw' => $tw,
                'batas_tmt' => $tmtTarikan->data,
            ];
            $add         = $this->_send_post($data, 'syncptk', $jwt);
            $send_data         = curl_exec($add);

            $result = json_decode($send_data);


            if (isset($result->error)) {
                return false;
            }

            if ($result) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function syncPtkGetBackbone($npsn, $sekolahId)
    {
        $jwt = get_cookie('jwt');
        if ($jwt) {
            $add         = $this->_send_get_backbone('syncptkbysekolahid', $npsn, $sekolahId);
            $send_data         = curl_exec($add);

            $result = json_decode($send_data);


            if (isset($result->error)) {
                return false;
            }

            if ($result) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function syncPtkId($idPtk, $npsn, $tw)
    {
        $jwt = get_cookie('jwt');
        if ($jwt) {
            $tmtTarikan = getTmtTarikanSync();
            if ($tmtTarikan->code !== 200) {
                return false;
            }
            $data = [
                'id_ptk' => $idPtk,
                'npsn' => $npsn,
                'tw' => $tw,
                'batas_tmt' => $tmtTarikan->data,
            ];
            $add         = $this->_send_post($data, 'syncptkid', $jwt);
            $send_data         = curl_exec($add);

            $result = json_decode($send_data);


            if (isset($result->error)) {
                return false;
            }

            if ($result) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getPtkById($idPtk)
    {
        $jwt = get_cookie('jwt');
        if ($jwt) {
            $data = [
                'id' => $idPtk,
            ];
            $add         = $this->_send_post($data, 'getptkid', $jwt);
            $send_data         = curl_exec($add);

            $result = json_decode($send_data);


            if (isset($result->error)) {
                return false;
            }

            if ($result) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getPtkByNuptk($idPtk)
    {
        $jwt = get_cookie('jwt');
        if ($jwt) {
            $data = [
                'nuptk' => $idPtk,
            ];
            $add         = $this->_send_post($data, 'getptknuptk', $jwt);
            $send_data         = curl_exec($add);

            $result = json_decode($send_data);


            if (isset($result->error)) {
                return false;
            }

            if ($result) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
