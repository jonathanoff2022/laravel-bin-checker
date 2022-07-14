<?php

namespace BinChecker;

use Exception;
use Illuminate\Support\Facades\Http;
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
        "bin" => "string", "vendor" => "string", "type" => "string", "level" => "string", "bank" => "string",
        "country" => "string", "country_emoji" => "string"
    ])] public static function checkBin(string $bin): array
    {
        try {
            $response = Http::get('https://bin-check-dr4g.herokuapp.com/api/'.$bin)->throw();
        } catch (Exception $exception) {
            throw new BinCheckerException("Http request to the bin checker API failed", 0, $exception);
        }

        try {
            $result = $response->json();
            if (!$result["success"]) {
                throw new BinCheckerException("Bin checker API failed. Response : `".$response->body()."`");
            }

            if ($result["data"]["bin"] != $bin) {
                throw new BinCheckerException("Returned bin is not the same. Returned=".$result["data"]["bin"].", expected=".$bin);
            }

            return [
                "bin" => $bin,
                "vendor" => $result["data"]["vendor"],
                "type" => $result["data"]["type"],
                "level" => $result["data"]["level"],
                "bank" => $result["data"]["bank"],
                "country" => $result["data"]["country"],
                "country_emoji" => $result["data"]["countryInfo"]["emoji"],
            ];
        } catch (Exception $exception) {
            throw new BinCheckerException("Unknown error occurred while checking.", 0, $exception);
        }
    }
}
