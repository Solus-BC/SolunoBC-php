<?php

declare(strict_types=1);

namespace SolunoBC;

use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;

class SolunoBC
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var RequestFactory
     */
    private $messageFactory;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var array
     */
    private static $defaultOptions = [
        'url' => 'https://api.soluno.se/',
        'token_url' => 'https://api.unotelefoni.se/',
        'username' => '',
        'password' => '',
        'token' => '',
        'token_type' => 'Bearer',
    ];

    /**
     * @param string $token
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return string
     *
     * @throws \Exception
     * @throws \Http\Client\Exception
     */
    public function createToken()
    {
        if (!$this->options['token']) {
            $uri = $this->options['token_url'] . 'token';

            $request = $this->getMessageFactory()->createRequest('POST', $uri, [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ], http_build_query([
                'grant_type' => 'password',
                'username' => $this->options['username'],
                'password' => $this->options['password'],
            ]));

            $response = $this->httpClient->sendRequest($request);
            $json = $response->getBody()->getContents();
            $data = json_decode($json, true);
            $this->options['token'] = $data['access_token'];
        }

        return $this->options['token'];
    }

    /**
     * @param array $options
     */
    private function setOptions(array $options)
    {
        $this->options = self::$defaultOptions;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @return RequestFactory
     */
    private function getMessageFactory()
    {
        if (!$this->messageFactory) {
            $this->messageFactory = MessageFactoryDiscovery::find();
        }

        return $this->messageFactory;
    }

    /**
     * @param string $method
     * @param string $path
     * @param string $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \Exception
     * @throws \Http\Client\Exception
     */
    private function request(string $method, string $path, string $body)
    {
        $this->createToken();

        $request = call_user_func_array(
            [$this, 'buildRequestInstance'],
            [$method, $path, $body]
        );

        return $this->httpClient->sendRequest($request);
    }

    /**
     * @param string $method
     * @param string $path
     * @param string $body
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function buildRequestInstance(string $method, string $path, string $body)
    {
        $uri = $this->options['url'] . $path;

        return $this->getMessageFactory()->createRequest($method, $uri, [
            'Authorization' => sprintf(
                '%s %s',
                $this->options['token_type'],
                $this->options['token']
            ),
            'Content-Type' => 'application/json'
        ], $body);
    }

    /**
     * @param MessageInterface $message
     *
     * @return string
     *
     * @throws \Exception
     * @throws \Http\Client\Exception
     */
    public function sendSms(MessageInterface $message)
    {
        $data = [
            'from' => $message->getFromNumber(),
            'to' => $message->getToNumbers(),
            'message' => $message->getMessage(),
        ];

        $request = $this->request('POST', 'sms/skicka', json_encode($data));
        $statusCode = $request->getStatusCode();
        if ($statusCode != 200) {
            throw new \Exception();
        }

        return $request->getBody()->getContents();
    }
}
