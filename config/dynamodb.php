<?php

return [
    'table_name' => env('DYNAMO_TABLE_NAME', 'reports'),
    
    'attributes' => [
        'id' => 'S',
        'category' => 'SS',
        'description' => 'S',
        'image_url' => 'S',
        'location' => 'S',
        'status' => 'SS',
        'title' => 'S',
        'user_id' => 'N',
    ],
];