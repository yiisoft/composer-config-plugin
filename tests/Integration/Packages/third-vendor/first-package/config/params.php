<?php

declare(strict_types=1);

return [
    'third-vendor/first-package' => true,
    'array parameter' => [
        'changed value' => 'third-vendor/first-package',
        'third-vendor/first-package' => true,
    ],
    'array parameter with UnsetArrayValue' => [
        'third-vendor/first-package' => true,
    ],
    'array parameter with ReplaceArrayValue' => [
        'third-vendor/first-package' => true,
    ],
    'array parameter with RemoveArrayKeys' => [
        'third-vendor/first-package' => 'third-vendor/first-package',
    ],
    'array parameter with ReverseValues' => [
        'third-vendor/first-package' => 'third-vendor/first-package',
    ],
];
