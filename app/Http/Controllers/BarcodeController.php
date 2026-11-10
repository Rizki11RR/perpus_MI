<?php
namespace App\Http\Controllers;
use App\Models\BookCopy;
use App\Services\BarcodeService;
use Illuminate\Http\Response;
use Illuminate\View\View;
class BarcodeController extends Controller { public function image(BookCopy $copy, BarcodeService $barcodes): Response { return response($barcodes->png($copy->barcode))->header('Content-Type','image/png'); } public function print(BookCopy $copy): View { return view('print.barcode-labels',['copies'=>collect([$copy])]); }
    public function labels(string $ids): View { $selected = collect(explode(',', $ids))->filter(fn ($id) => ctype_digit($id))->map(fn ($id) => (int) $id); abort_unless($selected->isNotEmpty(), 422); return view('print.barcode-labels', ['copies'=>BookCopy::with('book')->whereIn('id', $selected)->get()]); } }
