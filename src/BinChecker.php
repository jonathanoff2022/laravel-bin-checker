<?php

namespace BinChecker;

use Exception;
use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\ArrayShape;

class BinChecker
{
    private const DESCRIPTION_PATTERN = '<meta property="og:description" content="This number: ([0-9]{6}) is a valid BIN number ([A-Z ]+) issued by ([A-Za-z0-9 ]+) in ([A-Z ]+)">';
    private const LEVEL_PATTERN = '<tr>\s*<td\s+width="[0-9]{1,3}%"\s+class="p-2 font-medium">\s*Card Level\s*</td>\s*<td width="[0-9]{1,3}%" class="p-2">\s*([A-Z]+)\s*</td>\s*</tr>';

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
            $response = Http::get('https://bincheck.io/details/'.$bin)->throw();
        } catch (Exception $exception) {
            throw new BinCheckerException("Http request to the bin checker API failed", 0, $exception);
        }

        try {
            if (!preg_match(self::DESCRIPTION_PATTERN, $response->body(), $descriptionMatch)) {
                throw new BinCheckerException("Failed to match description.");
            }
            if (!preg_match(self::LEVEL_PATTERN, $response->body(), $levelMatch)) {
                throw new BinCheckerException("Failed to match level.");
            }

            $level = $levelMatch[1];
            $bank = $descriptionMatch[3];
            $country = $descriptionMatch[4];

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
