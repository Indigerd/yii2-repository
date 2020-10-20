<?php declare(strict_types=1);

namespace Indigerd\Repository\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Indigerd\Hydrator\Hydrator;
use Indigerd\Repository\Rest\Exception\ClientException;
use Indigerd\Repository\Rest\Exception\ServerException;

class RestRepository
{
    /**
     * @var Hydrator
     */
    protected $hydrator;

    /**
     * @var Hydrator
     */
    protected $collectionHydrator;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var string
     */
    protected $collectionClass;

    protected $endpoint;

    /**
     * @var array
     */
    protected $headers;

    /**
     * RestRepository constructor.
     * @param Hydrator $hydrator
     * @param Hydrator $collectionHydrator
     * @param Client $client
     * @param string $modelClass
     * @param string $collectionClass
     * @param string $endpoint
     */
    public function __construct(
        Hydrator $hydrator,
        Hydrator $collectionHydrator,
        Client $client,
        string $modelClass,
        string $collectionClass,
        string $endpoint
    ) {
        $this->hydrator = $hydrator;
        $this->collectionHydrator = $collectionHydrator;
        $this->client = $client;
        $this->modelClass = $modelClass;
        $this->collectionClass = $collectionClass;
        $this->endpoint = $endpoint;
    }

    /**
     * @param string $id
     * @param string $token
     * @return object|null
     */
    public function findOne(string $id, string $token = ''): ?object
    {
        $url = \rtrim($this->endpoint, '/') . '/' . $id;
        $this->addToken($token);
        $response = $this->request('get', $url);
        return $this->hydrator->hydrate($this->modelClass, $response['body']);
    }

    /**
     * @param array $params
     * @param string $token
     * @return Collection
     */
    public function findAll(array $params = [], string $token = '')
    {
        $url = \rtrim($this->endpoint, '/');
        $this->addToken($token);
        $response = $this->request('get', $url, $params);

        return $this->collectionHydrator->hydrate(
            $this->collectionClass,
            ['items' => $response['body']] + $this->generateHeaders($response['headers'])
        );
    }

    /**
     * @param string $header
     * @param string $value
     * @return $this
     */
    protected function addHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * @param string $token
     */
    protected function addToken(string $token)
    {
        if (!empty($token)) {
            $this->addHeader('Authorization', $token);
        }
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @return array
     */
    protected function request(string $method, string $url, array $params = []): array
    {
        $this->addHeader('Accept', 'application/json');
        $params['headers'] = $this->headers;

        try {
            /** @var \Psr\Http\Message\ResponseInterface $userRequest */
            $userRequest = $this->client->{$method}($url, $params);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $message = $this->formatResponseExceptionMessage($e);
            throw new ClientException($e->getResponse()->getStatusCode(), $message);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $message = $this->formatResponseExceptionMessage($e);
            throw new ServerException($message, $e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            $message = \sprintf('Failed to to perform request to service (%s).', $e->getMessage());
            throw new ServerException($message, $e->getCode());
        }

        $data = \json_decode($userRequest->getBody()->getContents(), true);

        return [
            'headers' => $userRequest->getHeaders(),
            'body' => $data
        ];
    }

    /**
     * @param BadResponseException $e
     * @return string
     */
    private function formatResponseExceptionMessage(BadResponseException $e): string
    {
        $message = \sprintf(
            'Service responded with error (%s - %s).',
            $e->getResponse()->getStatusCode(),
            $e->getResponse()->getReasonPhrase()
        );
        $message .= "\n" . $e->getResponse()->getBody()->getContents();

        return $message;
    }
    
     /**
     * @param array $response
     * @return array
     */
    public function generateHeaders(array $response): array
    {
        $headers = [];
        foreach ($this->headers as $header => $value) {
            if (isset($response[$header][0])) {
                $headers[$value] = $response[$header];
            }
        }
        return $headers;
    }
}
