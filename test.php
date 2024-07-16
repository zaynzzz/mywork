<?php

require 'vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;

putenv('PATH=' . getenv('PATH') . PATH_SEPARATOR . 'C:\Program Files\Tesseract-OCR');
putenv('TESSDATA_PREFIX=C:\Program Files\Tesseract-OCR');
class KTPInformation
{
    public $nik;
    public $nama;
    public $tanggal_lahir;
    public $tempat_lahir;
    public $jenis_kelamin;
    public $golongan_darah;
    public $alamat;
    public $rt;
    public $rw;
    // ... tambahkan properti lain sesuai kebutuhan 
}

class KTPOCR
{
    private $imagePath;
    private $result;

    public function __construct($imagePath)
    {
        $this->imagePath = $imagePath;
        $this->result = new KTPInformation();
        $this->masterProcess();
    }

    public function process()
    {
        $rawExtractedText = (new TesseractOCR($this->imagePath))
            ->lang('ind')
            ->run();
        return $rawExtractedText;
    }

    public function wordToNumberConverter($word)
    {
        $wordDict = [
            '|' => "1"
            // ... tambahkan item lain sesuai kebutuhan 
        ];
        $res = strtr($word, $wordDict);
        return $res;
    }

    public function nikExtract($word)
    {
        $wordDict = [
            'b' => "6",
            'e' => "2"
            // ... tambahkan item lain sesuai kebutuhan 
        ];
        $res = strtr($word, $wordDict);
        return $res;
    }

    public function extract($extractedResult)
    {
        $lines = explode("\n", $extractedResult);
        foreach ($lines as $word) {
            if (strpos($word, "NIK") !== false) {
                $word = explode(':', $word);
                $this->result->nik = $this->nikExtract(trim(str_replace(" ", "", end($word))));
                continue;
            }

            if (strpos($word, "Nama") !== false) {
                $word = explode(':', $word);
                $this->result->nama = trim(str_replace('Nama', '', end($word)));
                continue;
            }

            // ... tambahkan logika ekstraksi lainnya sesuai kebutuhan 
        }
    }

    public function masterProcess()
    {
        $rawText = $this->process();
        $this->extract($rawText);
    }

    public function toJson()
    {
        return json_encode($this->result, JSON_PRETTY_PRINT);
    }
}

// Contoh penggunaan: 
$imagePath = './uploads/ktp.jpeg';
$ktpOcr = new KTPOCR($imagePath);
echo $ktpOcr->toJson();
