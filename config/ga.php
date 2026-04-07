<?php

return [
    'api_url' => env('PYTHON_API_URL', 'http://localhost:8001/api'),
    
    'presets' => [
        'small' => [
            'population_size' => (int) env('GA_PRESET_SMALL_POPULATION', 20),
            'generations' => (int) env('GA_PRESET_SMALL_GENERATIONS', 30),
            'crossover_rate' => (float) env('GA_PRESET_SMALL_CROSSOVER', 0.8),
            'mutation_rate' => (float) env('GA_PRESET_SMALL_MUTATION', 0.2),
            'max_packages' => 10,
        ],
        'medium' => [
            'population_size' => (int) env('GA_PRESET_MEDIUM_POPULATION', 50),
            'generations' => (int) env('GA_PRESET_MEDIUM_GENERATIONS', 50),
            'crossover_rate' => (float) env('GA_PRESET_MEDIUM_CROSSOVER', 0.8),
            'mutation_rate' => (float) env('GA_PRESET_MEDIUM_MUTATION', 0.15),
            'max_packages' => 30,
        ],
        'large' => [
            'population_size' => (int) env('GA_PRESET_LARGE_POPULATION', 100),
            'generations' => (int) env('GA_PRESET_LARGE_GENERATIONS', 100),
            'crossover_rate' => (float) env('GA_PRESET_LARGE_CROSSOVER', 0.85),
            'mutation_rate' => (float) env('GA_PRESET_LARGE_MUTATION', 0.1),
            'max_packages' => PHP_INT_MAX,
        ],
    ],
    
    'timeout' => (int) env('GA_TIMEOUT', 60),
];