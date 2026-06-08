<?php

return [
    'api_url' => env('PYTHON_API_URL', 'http://localhost:8001/api'),
    'timeout' => (int) env('GA_TIMEOUT', 1800),
];