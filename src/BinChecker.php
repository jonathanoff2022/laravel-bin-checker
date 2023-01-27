<?php

namespace BinChecker;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\ArrayShape;

class BinChecker
{
    private const BIN_PATTERN = '#<tr><td>([0-9]{6})</td><td>([A-Z]{0,2})</td><td>([^<]*)</td><td>([A-Z]*)</td><td>([^<]*)</td><td>([^<]*)</td></tr>#';
    private const BIN_GROUP_COUNTRY = 2;
    private const BIN_GROUP_LEVEL = 5;
    private const BIN_GROUP_BANK = 6;

    /**
     * Get BIN information
     *
     * @throws BinCheckerException If an error occurs while sending the request, or if the received data is incorrect.
     * @noinspection PhpUnused The method will be used by projects. It is not meant to be used in the library
     */
    #[ArrayShape([
        "bin" => "string", "level" => "string", "bank" => "string", "country" => "string"
    ])] public static function checkBin(string $bin): array
    {
        try {
            $client = new Client();

            $response = $client->post('http://bins.su/', [
                'form_params' => [
                    'bins' => substr($bin, 0, 6),
                    'action' => 'searchbins',
                    'bank' => '',
                    'country' => ''
                ]
            ]);
        } catch (GuzzleException $exception) {
            throw new BinCheckerException("Http request to the bin checker API failed", 0, $exception);
        }

        try {
            try {
                $htmlContent = $response->getBody()->getContents();
            } catch (Exception $exception) {
                throw new BinCheckerException("Failed to read response content", 0, $exception);
            }

            if (!preg_match(self::BIN_PATTERN, $htmlContent, $matches)) {
                throw new BinCheckerException("Failed to find bin details from response content");
            }

            return [
                "bin" => $bin,
                "level" => $matches[self::BIN_GROUP_LEVEL],
                "bank" => $matches[self::BIN_GROUP_BANK],
                "country" => $matches[self::BIN_GROUP_COUNTRY]
            ];
        } catch (Exception $exception) {
            throw new BinCheckerException("Unknown error occurred while checking.", 0, $exception);
        }
    }
}
