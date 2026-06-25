<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TestS3Controller extends Controller
{
    /**
     * Test S3 connection and configuration
     */
    public function testConnection(): JsonResponse
    {
        try {
            $config = config('filesystems.disks.s3');
            
            // Test 1: Check configuration
            $testResults = [
                'config_loaded' => !empty($config),
                'driver' => $config['driver'] ?? 'not set',
                'bucket' => $config['bucket'] ?? 'not set',
                'region' => $config['region'] ?? 'not set',
                'key_configured' => !empty($config['key']),
                'secret_configured' => !empty($config['secret']),
            ];
            
            // Test 2: Try to list files (this will fail if credentials are wrong)
            try {
                $files = Storage::disk('s3')->files('test');
                $testResults['list_files'] = true;
                $testResults['files_count'] = count($files);
            } catch (\Exception $e) {
                $testResults['list_files'] = false;
                $testResults['list_error'] = $e->getMessage();
            }
            
            // Test 3: Try to write a test file
            try {
                $testContent = 'Test connection at ' . now();
                $result = Storage::disk('s3')->put('test/connection.txt', $testContent, 'public');
                $testResults['write_test'] = $result;
                
                if ($result) {
                    // Try to get the URL
                    $url = Storage::disk('s3')->url('test/connection.txt');
                    $testResults['test_url'] = $url;
                    
                    // Clean up
                    Storage::disk('s3')->delete('test/connection.txt');
                    $testResults['cleanup'] = true;
                }
            } catch (\Exception $e) {
                $testResults['write_test'] = false;
                $testResults['write_error'] = $e->getMessage();
                $testResults['write_trace'] = $e->getTraceAsString();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'S3 Connection Test',
                'data' => $testResults
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('S3 Test Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'S3 Test Failed',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}