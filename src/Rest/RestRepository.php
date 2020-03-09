<?php declare(strict_types=1);

namespace Indigerd\Repository\Rest;

use GuzzleHttp\Client;
use Indigerd\Hydrator\Hydrator;
use Indigerd\Repository\Rest\Exception\ClientException;
use Indigerd\Repository\Rest\Exception\ServerException;

class RestRepository
{
    protected $hydrator;

    protected $collectionHydrator;

    protected $client;

    protected $modelClass;

    protected $collectionClass;

    protected $endpoint;

    protected $headers;

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

    public function findOne(string $id, string $token = ''): ?object
    {
        $url = \rtrim($this->endpoint, '/') . '/' . $id;
        $this->addToken($token);
        $response = $this->request('get', $url);
        return $this->hydrator->hydrate($this->modelClass, $response['body']);
    }

    public function findAll(array $params = [], string $token = ''): Collection
    {
        $url = \rtrim($this->endpoint, '/');
        $this->addToken($token);
        $response = $this->request('get', $url, $params);
        return $this->collectionHydrator->hydrate(
            $this->collectionClass,
            ['items' => $response['body']] + $this->generateHeaders($response['headers'])
        );
    }

    protected function addHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    protected function addToken(string $token)
    {
        if (!empty($token)) {
            $this->addHeader('Authorization', $token);
        }
    }

    protected function request(string $method, string $url, array $params = []): array
    {
        $this->addHeader('Accept', 'application/json');
        $params['headers'] = $this->headers;

        try {
            /** @var \Psr\Http\Message\ResponseInterface $userRequest */
            $userRequest = $this->client->{$method}($url, $params);
        } catch (\Exception $e) {
            $message = \sprintf('Failed to to perform request to service orders (%s).', $e->getMessage());
            throw new ServerException($message);
        }

        if (400 <= $userRequest->getStatusCode()) {
            $message = \sprintf(
                'Service orders responded with error (%s - %s).',
                $userRequest->getStatusCode(),
                $userRequest->getReasonPhrase()
            );
            $message .= "\n" . $userRequest->getBody();
            if (500 <= $userRequest->getStatusCode()) {
                throw new ServerException($message);
            }
            throw new ClientException($userRequest->getStatusCode(), $message);
        }

        $data = \json_decode($userRequest->getBody(), true);
        return [
            'headers' => $userRequest->getHeaders(),
            'body' => $data
        ];
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
