<?php

namespace App\Libraries;

use PhpOffice\PhpWord\PhpWord;
use mPDF;
use React\EventLoop\Factory;
use React\ChildProcess\Process;

class Downloadlib
{
    private $_db;

    function __construct()
    {
        helper(['text', 'session', 'cookie', 'file', 'array', 'filesystem']);
        $this->_db      = \Config\Database::connect();
    }

    public function download($path, $name)
    {

        $phpWord = new PhpWord();
        $template = $phpWord->loadTemplate($path);

        $html = $template->getMarkup();
        $mpdf = new mPDF();
        $mpdf->WriteHTML($html);
        return $mpdf->Output($name, 'D');
    }

    public function downloaded($path, $name, $jenis_sptjm = "tamsil")
    {
        $dir = FCPATH . "upload/generate/sptjm/" . $jenis_sptjm . "/pdf2";
        sleep(3);
        try {
            // if (
            // exec('libreoffice --headless --convert-to pdf ' . $path . ' --outdir ' . $dir, $output, $retval);

            // $command = 'libreoffice --headless --convert-to pdf ' . $path . ' --outdir ' . $dir;
            // $output = shell_exec('sudo -u bejo -p bejo123 ' . $command);
            // exec('echo "bejo123" | sudo -S -u bejo && "' . $command . '"', $output, $retval);
            $loop = Factory::create();

            $command = 'libreoffice --headless --convert-to pdf ' . $path . ' --outdir ' . $dir;
            // $process = new Process('sudo -u bejo ' . $command);
            $process = new Process('sudo -u situgu -p situgu ' . $command);
            // $process = new Process('sudo -u bejo -p bejo123 ' . $command);
            // $process = new Process($command);

            $process->start($loop);

            $fileNya = $dir . '/' . $name;

            // $response = new ResponseInterface();
            $process->on('exit', function ($exitCode, $termSignal) use ($fileNya) {
                if ($exitCode === 0) {
                    if (file_exists($fileNya)) {
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment; filename="' . basename($fileNya) . '"');
                        header('Content-Length: ' . filesize($fileNya));
                        readfile($fileNya);
                        exit;
                    } else {
                        $response = new \stdClass;
                        $response->status = 404;
                        $response->message = "Gagal download SPTJM.";
                        echo json_encode($response);
                    }
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal convert file.";
                    $response->error = $termSignal;
                    $response->rr = 'exit with ' . $exitCode . PHP_EOL;
                    $response->code = $exitCode;
                    echo json_encode($response);
                }
            });

            $loop->run();
            // die;
            // ) {
            // if ($retval === 0) {
            //     $file = $dir . '/' . $name;
            //     $response = new \stdClass;
            //     $response->status = 200;
            //     $response->message = "Berhasil convert file.";
            //     $response->file = $file;
            //     return $response;
            // } else {
            //     $response = new \stdClass;
            //     $response->status = 400;
            //     $response->var_dump = var_dump($output);
            //     $response->retval = var_dump($retval);
            //     $response->error = "Error converting file to PDF: " . implode("\n", $output);
            //     $response->message = "Gagal convert file.";
            //     return $response;
            // }

        } catch (\Exception $err) {
            $response = new \stdClass;
            $response->status = 400;
            $response->error = var_dump($err);
            $response->message = "Gagal convert filee.";
            return $response;
        }
    }

    public function downloadedSpj($path, $name, $jenis_sptjm = "spjtamsil")
    {
        $dir = FCPATH . "upload/generate/sptjm/" . $jenis_sptjm . "/pdf2";
        sleep(3);
        try {
            // if (
            // exec('libreoffice --headless --convert-to pdf ' . $path . ' --outdir ' . $dir, $output, $retval);

            // $command = 'libreoffice --headless --convert-to pdf ' . $path . ' --outdir ' . $dir;
            // $output = shell_exec('sudo -u bejo -p bejo123 ' . $command);
            // exec('echo "bejo123" | sudo -S -u bejo && "' . $command . '"', $output, $retval);
            $loop = Factory::create();

            $command = 'libreoffice --headless --convert-to pdf ' . $path . ' --outdir ' . $dir;
            // $process = new Process('sudo -u bejo ' . $command);
            $process = new Process('sudo -u situgu -p situgu ' . $command);
            // $process = new Process('sudo -u bejo -p bejo123 ' . $command);
            // $process = new Process($command);

            $process->start($loop);

            $fileNya = $dir . '/' . $name;

            // $response = new ResponseInterface();
            $process->on('exit', function ($exitCode, $termSignal) use ($fileNya) {
                if ($exitCode === 0) {
                    if (file_exists($fileNya)) {
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment; filename="' . basename($fileNya) . '"');
                        header('Content-Length: ' . filesize($fileNya));
                        readfile($fileNya);
                        exit;
                    } else {
                        $response = new \stdClass;
                        $response->status = 404;
                        $response->message = "Gagal download SPTJM.";
                        echo json_encode($response);
                    }
                } else {
                    $response = new \stdClass;
                    $response->status = 400;
                    $response->message = "Gagal convert file.";
                    $response->error = $termSignal;
                    $response->rr = 'exit with ' . $exitCode . PHP_EOL;
                    $response->code = $exitCode;
                    echo json_encode($response);
                }
            });

            $loop->run();
            // die;
            // ) {
            // if ($retval === 0) {
            //     $file = $dir . '/' . $name;
            //     $response = new \stdClass;
            //     $response->status = 200;
            //     $response->message = "Berhasil convert file.";
            //     $response->file = $file;
            //     return $response;
            // } else {
            //     $response = new \stdClass;
            //     $response->status = 400;
            //     $response->var_dump = var_dump($output);
            //     $response->retval = var_dump($retval);
            //     $response->error = "Error converting file to PDF: " . implode("\n", $output);
            //     $response->message = "Gagal convert file.";
            //     return $response;
            // }

        } catch (\Exception $err) {
            $response = new \stdClass;
            $response->status = 400;
            $response->error = var_dump($err);
            $response->message = "Gagal convert filee.";
            return $response;
        }
    }
}
