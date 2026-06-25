<?php

namespace App\Services;

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Config;

class DynamoDbService
{
    private DynamoDbClient $client;
    private string $tableName;

    public function __construct()
    {
        $this->tableName = Config::get('dynamodb.table_name', 'reports');
        
        $this->client = new DynamoDbClient([
            'region' => Config::get('dynamodb.region', env('AWS_DEFAULT_REGION', 'us-east-1')),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function getClient(): DynamoDbClient
    {
        return $this->client;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function putItem(array $item): array
    {
        try {
            $result = $this->client->putItem([
                'TableName' => $this->tableName,
                'Item' => $this->formatItem($item),
            ]);

            return [
                'success' => true,
                'data' => $result->toArray(),
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getItem(string $id): array
    {
        try {
            $result = $this->client->getItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'id' => ['S' => $id],
                ],
            ]);

            if (isset($result['Item'])) {
                return [
                    'success' => true,
                    'data' => $this->parseItem($result['Item']),
                ];
            }

            return [
                'success' => false,
                'error' => 'Item not found',
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function updateItem(string $id, array $updates): array
    {
        try {
            $updateExpression = 'SET ';
            $expressionAttributeValues = [];
            $expressionAttributeNames = [];
            
            $counter = 0;
            foreach ($updates as $key => $value) {
                if ($key === 'id') continue;
                
                if ($counter > 0) {
                    $updateExpression .= ', ';
                }
                
                $placeholder = ':val' . $counter;
                $expressionAttributeValues[$placeholder] = $this->formatValue($key, $value);
                $updateExpression .= "#$key = $placeholder";
                $expressionAttributeNames["#$key"] = $key;
                
                $counter++;
            }

            $params = [
                'TableName' => $this->tableName,
                'Key' => [
                    'id' => ['S' => $id],
                ],
                'UpdateExpression' => $updateExpression,
                'ExpressionAttributeValues' => $expressionAttributeValues,
                'ReturnValues' => 'ALL_NEW',
            ];

            if (!empty($expressionAttributeNames)) {
                $params['ExpressionAttributeNames'] = $expressionAttributeNames;
            }

            $result = $this->client->updateItem($params);

            return [
                'success' => true,
                'data' => $this->parseItem($result['Attributes']),
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function deleteItem(string $id): array
    {
        try {
            $this->client->deleteItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'id' => ['S' => $id],
                ],
            ]);

            return [
                'success' => true,
                'message' => 'Item deleted successfully',
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function scan(array $conditions = []): array
    {
        try {
            $params = [
                'TableName' => $this->tableName,
            ];

            $result = $this->client->scan($params);

            $items = [];
            if (isset($result['Items'])) {
                foreach ($result['Items'] as $item) {
                    $items[] = $this->parseItem($item);
                }
            }

            return [
                'success' => true,
                'data' => $items,
                'count' => count($items),
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function formatItem(array $item): array
    {
        $formatted = [];
        
        foreach ($item as $key => $value) {
            $formatted[$key] = $this->formatValue($key, $value);
        }

        return $formatted;
    }

    private function formatValue(string $key, $value): array
    {
        $attributes = Config::get('dynamodb.attributes', []);
        $type = $attributes[$key] ?? 'S';

        switch ($type) {
            case 'N':
                return ['N' => (string) $value];
            case 'SS':
                return ['SS' => is_array($value) ? $value : [$value]];
            case 'NS':
                return ['NS' => is_array($value) ? array_map('strval', $value) : [(string) $value]];
            default:
                return ['S' => (string) $value];
        }
    }

    private function parseItem(array $item): array
    {
        $parsed = [];
        
        foreach ($item as $key => $value) {
            $parsed[$key] = $this->parseValue($value);
        }

        return $parsed;
    }

    private function parseValue(array $value): mixed
    {
        $type = key($value);
        $data = $value[$type];

        switch ($type) {
            case 'S':
                return $data;
            case 'N':
                return $data;
            case 'SS':
                return $data;
            case 'NS':
                return $data;
            case 'BOOL':
                return $data;
            case 'NULL':
                return null;
            default:
                return $data;
        }
    }
}