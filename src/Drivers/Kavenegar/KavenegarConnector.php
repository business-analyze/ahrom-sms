<?php

namespace Ahrom\AhromSms\App\Drivers\Kavenegar;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Ahrom\AhromSms\App\Contracts\DriverConnector;
use Ahrom\AhromSms\App\Exceptions\ApiException;
use Ahrom\AhromSms\App\Exceptions\HttpException;
use Ahrom\AhromSms\App\Traits\HasResponse;

class KavenegarConnector implements DriverConnector
{
    use HasResponse;

    protected Client $client;

    public function __construct()
    {
        $this->client = (new Client([
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'charset' => 'utf-8',
            ],
            RequestOptions::VERIFY => false,
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::TIMEOUT => 30,
            RequestOptions::CONNECT_TIMEOUT => 0,
            // RequestOptions::TIMEOUT => config('chapaar.timeout'),
            // RequestOptions::CONNECT_TIMEOUT => config('chapaar.connect_timeout'),
        ]));
        self::$setting = (object) [
            'title' => 'chapaar::driver.kavenegar',
            'url' => 'https://api.kavenegar.com/v1/',
            'method' => 'post',
            'api_key' => '',
            'line_number' => '',
        ];
        // self::$setting = (object) config('chapaar.drivers.kavenegar');
    }

    /**
     * @param  KavenegarMessage  $message
     *
     * @throws GuzzleException
     */
    public function send($message): object
    {
        $url = self::endpoint(self::$setting->api_key, 'sms', 'send.json');

        $params = [
            'receptor' => $message->getTo(),
            'message' => $message->getContent(),
            'sender' => $message->getFrom() ?: self::$setting->line_number,
            'date' => $message->getDate() ?? null,
            'type' => $message->getType() ?? null,
            'localid' => $message->getLocalId() ?? null,
        ];

        $response = $this->performApi($url, $params);

        return $this->generateResponse($response->return->status, $response->return->message, $message->getDriver()->value, (array) $response->entries);
    }

    /**
     * @throws GuzzleException
     */
    public function verify($message): object
    {
        $url = self::endpoint(self::$setting->api_key, 'verify', 'lookup.json');

        $params = [
            'receptor' => $message->getTo(),
            'template' => $message->getTemplate(),
            'token' => $message->getTokens()[0],
            'token2' => $message->getTokens()[1] ?? null,
            'token3' => $message->getTokens()[2] ?? null,
            'token10' => $message->getTokens()[3] ?? null,
            'token20' => $message->getTokens()[4] ?? null,
            'type' => $message->getType() ?? null,
        ];

        $response = $this->performApi($url, $params);

        return $this->generateResponse($response->return->status, $response->return->message, $message->getDriver()->value, (array) $response->entries);
    }

    /**
     * @throws GuzzleException
     */
    public function account(): object
    {
        $url = self::endpoint(self::$setting->api_key, 'account', 'info.json');
        $response = $this->performApi($url);
        $entries = $response->entries;

        return $this->generateAccountResponse($entries->remaincredit, $entries->expiredate);
    }

    public function outbox($page_size = 100, $page_number = 1): object
    {
        $url = self::endpoint(self::$setting->api_key, 'sms', 'latestoutbox.json')."?pagesize=$page_size";
        $response = $this->performApi($url);

        return collect($response->entries)->map(function ($item) {
            return $this->generateReportResponse($item->message_id, $item->receptor, $item->message, $item->date, $item->sender, $item->cost);
        });
    }

    /**
     * @throws HttpException|ApiException|GuzzleException
     */
    public function performApi(string $url, array $params = []): object
    {
        $response = $this->client->post($url, [
            'form_params' => $params,
        ]);

        return $this->processApiResponse($response);
    }

    /**
     * @throws HttpException|ApiException
     */
    protected function validateResponseStatus($status_code, $json_response): void
    {
        if ($json_response === null) {
            throw new HttpException('Response is not valid JSON', $status_code);
        }
        if ($json_response->return->status !== Response::HTTP_OK) {
            throw new ApiException($json_response->return->message, $json_response->return->status);
        }
    }
}
