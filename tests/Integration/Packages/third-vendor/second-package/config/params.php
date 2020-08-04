<?php

declare(strict_types=1);

return [
    'third-vendor/second-package' => true,
    'array parameter' => [
        'changed value' => 'third-vendor/second-package',
        'third-vendor/second-package' => true,
    ],
    'array parameter with UnsetArrayValue' => [
        'third-vendor/second-package' => true,
    ],
    'array parameter with ReplaceArrayValue' => [
        'third-vendor/second-package' => true,
    ],
    'array parameter with RemoveArrayKeys' => [
        'third-vendor/second-package' => 'third-vendor/second-package',
    ],
    'array parameter with ReverseValues' => [
        'third-vendor/second-package' => 'third-vendor/second-package',
    ],
];
