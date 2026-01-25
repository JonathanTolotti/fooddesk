<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableQrCodeController extends Controller
{
    /**
     * Display all tables with QR codes
     */
    public function index(): View
    {
        $tables = Table::where('is_active', true)
            ->orderBy('number')
            ->get()
            ->map(function ($table) {
                $table->menu_url = $this->getMenuUrl($table);
                $table->qr_code_svg = $this->generateQrCode($table->menu_url, 150);

                return $table;
            });

        return view('tables.qrcodes', compact('tables'));
    }

    /**
     * Display single table QR code for printing
     */
    public function show(Table $table): View
    {
        $table->menu_url = $this->getMenuUrl($table);
        $table->qr_code_svg = $this->generateQrCode($table->menu_url, 300);

        return view('tables.qrcode-print', compact('table'));
    }

    /**
     * Download QR code as SVG
     */
    public function download(Table $table)
    {
        $menuUrl = $this->getMenuUrl($table);
        $svg = $this->generateQrCode($menuUrl, 400);

        $filename = "mesa-{$table->number}-qrcode.svg";

        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Generate menu URL for table
     */
    private function getMenuUrl(Table $table): string
    {
        return url("/menu/{$table->uuid}");
    }

    /**
     * Generate QR Code SVG
     */
    private function generateQrCode(string $data, int $size = 200): string
    {
        return QrCode::size($size)
            ->margin(1)
            ->generate($data);
    }
}