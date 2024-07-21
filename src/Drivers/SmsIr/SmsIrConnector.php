<?php

namespace Ahrom\AhromSms\App\Drivers\SmsIr;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Ahrom\AhromSms\App\Contracts\DriverConnector;
use Ahrom\AhromSms\App\Exceptions\ApiException;
use Ahrom\AhromSms\App\Exceptions\HttpException;
use Ahrom\AhromSms\App\Traits\HasResponse;

class SmsIrConnector implements DriverConnector
{
    use HasResponse;

    protected Client $client;

    public function __construct()
    {
        self::$setting = (object) [
            'title' => 'chapaar::driver.smsir',
            'url' => 'https://api.sms.ir/v1/',
            'api_key' => 'qqwMZ4ncx09xRBo4SfKFBYPMkVz5PR4siytPV2eOGLJwIcScCI8kh4ugmAp4g986',
            'line_number' => '',
        ];
        // self::$setting = (object) config('chapaar.drivers.smsir');
        $this->client = new Client([
            RequestOptions::HEADERS => [
                'x-api-key' => self::$setting->api_key,
                'Accept' => 'text/plain',
                'Content-Type' => 'application/json',
            ],
            RequestOptions::TIMEOUT => 30,
            RequestOptions::CONNECT_TIMEOUT => 0,
            'verify' => false
            // RequestOptions::TIMEOUT => config('chapaar.timeout'),
            // RequestOptions::CONNECT_TIMEOUT => config('chapaar.connect_timeout'),
        ]);

    }

    /**
     * @param  SmsIrMessage  $message
     *
     * @throws GuzzleException|HttpException|ApiException
     */
    public function send($message): object
    {
        $url = self::endpoint('send', 'bulk');
        $params = [
            'lineNumber' => $message->getFrom() ?: self::$setting->line_number,
            'MessageText' => $message->getContent(),
            'Mobiles' => (array) $message->getTo(),
            'SendDateTime' => $message->getDate() ?? null,
        ];

        $response = $this->performApi($url, $params);

        return $this->generateResponse($response->status, $response->message, $message->getDriver()->value, (array) $response->data);
    }

    /**
     * @param  SmsIrMessage  $message
     *
     * @throws GuzzleException
     */
    public function verify($message): object
    {
        $receiver = $message->getTo();
        $url = self::endpoint('send', 'verify');
        $params = [
            'mobile' => $receiver,
            'templateId' => $message->getTemplate(),
            'parameters' => $message->getTokens(),
        ];

        $response = $this->performApi($url, $params);

        return $this->generateResponse($response->status, $response->message, $message->getDriver()->value, (array) $response->data);

    }

    /**
     * @throws GuzzleException
     */
    public function account(): object
    {
        $url = self::endpoint('credit');

        //todo:: use performApi method by passing request type to the method
        $response = $this->client->get($url);
        $response = $this->processApiResponse($response);

        return $this->generateAccountResponse($response->data, 0);
    }

    /**
     * @throws GuzzleException
     */
    public function outbox($page_size = 100, $page_number = 1): object
    {
        $url = self::endpoint('send', 'live')."?PageSize=$page_size&PageNumber=$page_number";

        $response = $this->client->get($url);
        $response = $this->processApiResponse($response);

        return collect($response->data)->map(function ($item) {
            return $this->generateReportResponse($item->messageId, $item->mobile, $item->messageText, $item->sendDateTime, $item->lineNumber, $item->cost);
        });
    }

    /**
     * @throws GuzzleException
     */
    public function performApi(string $url, array $params = []): object
    {
        $response = $this->client->post($url, [
            'json' => $params,
        ]);

        return $this->processApiResponse($response);
    }

    /**
     * @throws HttpException | ApiException
     */
    protected function validateResponseStatus($status_code, $json_response): void
    {
        if ($json_response === null) {
            throw new HttpException('Response is not valid JSON', $status_code);
        }

        if ($json_response->status !== 1) {
            throw new ApiException($json_response->message, $json_response->status);
        }
    }
}
