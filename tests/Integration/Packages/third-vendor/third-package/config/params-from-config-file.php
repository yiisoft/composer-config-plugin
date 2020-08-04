<?php

declare(strict_types=1);

return [
    'third-vendor/third-package' => true,
    'array parameter' => [
        'changed value' => 'third-vendor/third-package',
        'third-vendor/third-package' => true,
    ],
    'array parameter with UnsetArrayValue' => [
        'third-vendor/third-package' => true,
    ],
    'array parameter with ReplaceArrayValue' => [
        'third-vendor/third-package' => true,
    ],
    'array parameter with RemoveArrayKeys' => [
        'third-vendor/third-package' => 'third-vendor/third-package',
    ],
    'array parameter with ReverseValues' => [
        'third-vendor/third-package' => 'third-vendor/third-package',
    ],
];
