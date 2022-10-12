<?php

namespace BinChecker;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class BinChecker
{
    /**
     * Get BIN informations
     *
     * @throws BinCheckerException If an error occurs while sending the request, or if the received data is incorrect.
     * @noinspection PhpUnused The method will be used by projects. It is not meant to be used in the library
     */
    #[ArrayShape([
        "bin" => "string", "level" => "string", "bank" => "string", "country" => "string"
    ])] public static function checkBin(string $bin): array
    {
        try {
            $response = Http::post('https://bincodes.net/ajax/bin-checker.php', [
                'bin_number' => Str::substr($bin, 0, 6),
                'action' => 'bin_ccn_generator'
            ])->throw();
        } catch (Exception $exception) {
            throw new BinCheckerException("Http request to the bin checker API failed", 0, $exception);
        }

        try {
            try {
                $data = $response->json();
            } catch (Exception $exception) {
                throw new BinCheckerException("Invalid response from the API.", 0, $exception);
            }

            $level = $data['level'];
            $bank = $data['bank_name'];
            $country = $data['country'];

            return [
                "bin" => $bin,
                "level" => $level,
                "bank" => $bank,
                "country" => $country
            ];
        } catch (Exception $exception) {
            throw new BinCheckerException("Unknown error occurred while checking.", 0, $exception);
        }
    }
}
