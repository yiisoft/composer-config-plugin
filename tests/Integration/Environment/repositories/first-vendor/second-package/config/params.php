<?php

declare(strict_types=1);

return [
    'first-vendor/second-package' => true,
    'array parameter' => [
        'changed value' => 'first-vendor/second-package',
        'first-vendor/second-package' => true,
    ],
    'array parameter with UnsetArrayValue' => [
        'first-vendor/second-package' => true,
    ],
    'array parameter with ReplaceArrayValue' => [
        'first-vendor/second-package' => true,
    ],
    'array parameter with RemoveArrayKeys' => [
        'first-vendor/second-package' => 'first-vendor/second-package',
    ],
];
