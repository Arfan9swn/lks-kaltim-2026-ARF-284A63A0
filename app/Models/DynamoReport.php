<?php

namespace App\Models;

use App\Services\DynamoDbService;

class DynamoReport
{
    private DynamoDbService $dynamoDb;
    private string $tableName;

    public function __construct()
    {
        $this->dynamoDb = new DynamoDbService();
        $this->tableName = $this->dynamoDb->getTableName();
    }

    public function create(array $data): array
    {
        $item = [
            'id' => $data['id'] ?? uniqid(),
            'category' => $data['category'] ?? [],
            'description' => $data['description'] ?? '',
            'image_url' => $data['image_url'] ?? '',
            'location' => $data['location'] ?? '',
            'status' => $data['status'] ?? ['open'],
            'title' => $data['title'] ?? '',
            'user_id' => $data['user_id'] ?? 0,
        ];

        return $this->dynamoDb->putItem($item);
    }

    public function find(string $id): ?array
    {
        $result = $this->dynamoDb->getItem($id);
        
        if ($result['success']) {
            return $result['data'];
        }

        return null;
    }

    public function update(string $id, array $data): array
    {
        return $this->dynamoDb->updateItem($id, $data);
    }

    public function delete(string $id): array
    {
        return $this->dynamoDb->deleteItem($id);
    }

    public function getAll(): array
    {
        $result = $this->dynamoDb->scan();
        
        return $result['success'] ? $result['data'] : [];
    }

    public function findByUserId(string|int $userId): array
    {
        $allReports = $this->getAll();
        
        return array_filter($allReports, function ($report) use ($userId) {
            return $report['user_id'] == $userId;
        });
    }

    public function findByStatus(string $status): array
    {
        $allReports = $this->getAll();
        
        return array_filter($allReports, function ($report) use ($status) {
            return in_array($status, (array) $report['status']);
        });
    }

    public function findByCategory(string $category): array
    {
        $allReports = $this->getAll();
        
        return array_filter($allReports, function ($report) use ($category) {
            return in_array($category, (array) $report['category']);
        });
    }
}