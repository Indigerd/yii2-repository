<?php declare(strict_types=1);

namespace Indigerd\Repository\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Indigerd\Hydrator\Hydrator;
use Indigerd\Repository\RepositoryInterface;
use Indigerd\Repository\Rest\Exception\ClientException;
use Indigerd\Repository\Rest\Exception\ServerException;

class RestRepository implements RepositoryInterface
{
    /**
     * @var Hydrator
     */
    protected $hydrator;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $modelClass;

    protected $endpoint;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $uniqidPrefix;

    /**
     * RestRepository constructor.
     * @param Hydrator $hydrator
     * @param Client $client
     * @param string $modelClass
     * @param string $collectionClass
     * @param string $endpoint
     */
    public function __construct(
        Hydrator $hydrator,
        Client $client,
        string $modelClass,
        string $endpoint,
        string $uniqidPrefix = ''
    ) {
        $this->hydrator = $hydrator;
        $this->client = $client;
        $this->modelClass = $modelClass;
        $this->endpoint = $endpoint;
        $this->uniqidPrefix = $uniqidPrefix;
    }

    public function findOne(array $conditions = [], array $with = []): object
    {
        if (!isset($conditions['id'])) {
            throw new \InvalidArgumentException('No id provided');
        }
        $url = \rtrim($this->endpoint, '/') . '/' . $conditions['id'];
        $response = $this->request('get', $url);
        return $this->hydrator->hydrate($this->modelClass, $response['body']);
    }

    public function findAll(
        array $conditions = [],
        array $order = [],
        int $limit = 0,
        int $offset = 0,
        array $with = []
    ): array {
        $url = \rtrim($this->endpoint, '/');
        $response = $this->request('get', $url, [RequestOptions::QUERY => $conditions]);
        $result = [];
        foreach ($response['body'] as $item) {
            $result[] = $this->hydrator->hydrate($this->modelClass, $item);
        }
        return $result;
    }

    /**
     * @param string $expression
     * @param array $conditions
     * @return string
     */
    public function aggregate(string $expression, array $conditions): string
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param string $field
     * @param array $conditions
     * @return string
     */
    public function aggregateCount(string $field = '', array $conditions = []): string
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param string $field
     * @param array $conditions
     * @return string
     */
    public function aggregateSum(string $field, array $conditions): string
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param string $field
     * @param array $conditions
     * @return string
     */
    public function aggregateAverage(string $field, array $conditions): string
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param string $field
     * @param array $conditions
     * @return string
     */
    public function aggregateMin(string $field, array $conditions): string
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param string $field
     * @param array $conditions
     * @return string
     */
    public function aggregateMax(string $field, array $conditions): string
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param array $data
     * @return object
     */
    public function create(array $data= []): object
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param object $entity
     * @param array $data
     */
    public function populate(object $entity, array $data): void
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param object $entity
     */
    public function insert(object $entity): void
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param object $entity
     */
    public function update(object $entity): void
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param object $entity
     */
    public function delete(object $entity): void
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param array $data
     * @param array $conditions
     * @param array $options
     * @return int
     */
    public function updateAll(array $data, array $conditions, array $options = []): int
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param array $conditions
     * @return int
     */
    public function deleteAll(array $conditions): int
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param string $header
     * @param string $value
     * @return $this
     */
    public function addHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * @param string $token
     */
    public function addToken(string $token)
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
        $this->addXRequestIdHeader();
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
        return $e->getResponse()->getBody()->getContents();
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

    private function addXRequestIdHeader(): void
    {
        if (!array_key_exists('X-Request-Id',$this->headers)) {
            $this->addHeader('X-Request-Id', uniqid($this->uniqidPrefix, true));
        }
    }
}
