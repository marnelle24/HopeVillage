<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Generate a unique QR code string for a user
     */
    public function generateUniqueCode(): string
    {
        return 'HV-' . Str::upper(Str::random(12));
    }

    /**
     * Generate QR code image as data URI
     */
    public function generateQrCodeImage(string $data, int $size = 300): string
    {
        try {
            $builder = new Builder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: $size,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin
            );

            $result = $builder->build();

            // Convert to data URI
            return $result->getDataUri();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('QR code generation exception', [
                'error' => $e->getMessage(),
                'data' => $data,
                'size' => $size,
            ]);
            throw $e;
        }
    }

    /**
     * Generate QR code image as base64 string
     */
    public function generateQrCodeBase64(string $data, int $size = 300): string
    {
        try {
            $builder = new Builder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: $size,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin
            );

            $result = $builder->build();

            return base64_encode($result->getString());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('QR code base64 generation exception', [
                'error' => $e->getMessage(),
                'data' => $data,
                'size' => $size,
            ]);
            throw $e;
        }
    }
}

