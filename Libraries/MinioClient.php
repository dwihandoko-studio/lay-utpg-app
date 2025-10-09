<?php

// Pastikan autoloading Composer sudah dimuat (biasanya di index.php atau boot-up)
// require 'vendor/autoload.php'; 
namespace App\Libraries;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class MinioClient
{
    protected $client;
    protected $region;

    public function __construct()
    {
        // Ambil konfigurasi (sesuaikan cara pengambilan config di CI3/CI4)
        $endpoint  = getenv('minio.endpoint') ?: 'http://127.0.0.1:9000'; // Contoh CI4 getenv
        $accessKey = getenv('minio.accessKey') ?: 'Q3AM3UQ867SPQQA43P2F';
        $secretKey = getenv('minio.secretKey') ?: 'zuf+tfteSlswRu7BJ86wekitnifILbZam1KYY3TG';
        $useSSL    = getenv('minio.useSSL') == 'true';
        $this->region = getenv('minio.region') ?: 'us-east-1';

        try {
            // Inisialisasi S3 Client yang akan berbicara dengan MinIO
            $this->client = new S3Client([
                'endpoint' => $endpoint,
                'version' => 'latest', // Atau '2006-03-01'
                'region'  => $this->region,
                'suppress_php_deprecation_warning' => true,
                'use_path_style_endpoint' => true, // Wajib untuk MinIO
                'credentials' => [
                    'key'    => $accessKey,
                    'secret' => $secretKey,
                ],
                'scheme' => $useSSL ? 'https' : 'http',
            ]);
        } catch (Exception $e) {
            log_message('error', 'Gagal inisialisasi MinIO Client: ' . $e->getMessage());
            // Atau throw exception
        }
    }

    /**
     * Membuat bucket jika belum ada
     * @param string $bucketName
     */
    public function makeBucket(string $bucketName)
    {
        if ($this->client->doesBucketExist($bucketName)) {
            echo "Bucket {$bucketName} sudah ada.\n";
            return true;
        }

        try {
            $this->client->createBucket([
                'Bucket' => $bucketName,
                'LocationConstraint' => $this->region,
            ]);
            echo "Bucket {$bucketName} berhasil dibuat.\n";
            return true;
        } catch (S3Exception $e) {
            echo "Gagal membuat bucket: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Mengunggah file lokal ke MinIO
     * @param string $bucketName
     * @param string $objectName
     * @param string $sourceFilePath
     * @param array $metaData Metadata opsional
     */
    public function uploadFile(string $bucketName, string $objectName, string $sourceFilePath, array $metaData = [])
    {
        try {
            $result = $this->client->putObject([
                'Bucket'     => $bucketName,
                'Key'        => $objectName,
                'SourceFile' => $sourceFilePath, // Menggunakan SourceFile untuk file lokal
                'Metadata'   => $metaData,
                'ContentType' => mime_content_type($sourceFilePath), // Ambil tipe konten otomatis
            ]);

            // echo "File {$sourceFilePath} berhasil diunggah sebagai {$objectName}.\n";
            // Kembalikan URL publik jika diperlukan
            return $result['ObjectURL'] ?? null;
        } catch (S3Exception $e) {
            // echo "Gagal mengunggah file: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Menghapus objek dari MinIO.
     * @param string $bucketName Nama bucket.
     * @param string $objectName Key/path lengkap objek (contoh: 'skp_pkg/nama_file.pdf').
     * @return bool True jika berhasil, False jika gagal.
     */
    public function deleteObject(string $bucketName, string $objectName): bool
    {
        try {
            $this->client->deleteObject([
                'Bucket' => $bucketName,
                'Key'    => $objectName,
            ]);

            // Asumsi jika tidak ada exception, penghapusan berhasil
            log_message('info', "Objek {$objectName} berhasil dihapus dari bucket {$bucketName}.");
            return true;
        } catch (S3Exception $e) {
            log_message('error', "Gagal menghapus objek {$objectName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Membuat URL Pre-signed untuk akses objek sementara.
     * @param string $bucketName
     * @param string $objectName
     * @param int $expirySeconds Waktu kadaluarsa dalam detik (mis. 300 detik = 5 menit)
     * @return string|false URL Pre-signed
     */
    public function getPresignedUrl(string $bucketName, string $objectName, int $expirySeconds = 300)
    {
        try {
            $cmd = $this->client->getCommand('GetObject', [
                'Bucket' => $bucketName,
                'Key'    => $objectName
            ]);

            $request = $this->client->createPresignedRequest($cmd, "+{$expirySeconds} seconds");

            return (string) $request->getUri();
        } catch (S3Exception $e) {
            log_message('error', 'Gagal membuat Pre-signed URL: ' . $e->getMessage());
            return false;
        }
    }
}
