<?php
namespace App\Services;
use Picqer\Barcode\BarcodeGeneratorPNG;
class BarcodeService { public function png(string $barcode): string { return (new BarcodeGeneratorPNG())->getBarcode($barcode, BarcodeGeneratorPNG::TYPE_CODE_128); } }
