<?php

declare(strict_types=1);

return [
    'second-vendor/second-package' => true,
    'array parameter' => [
        'changed value' => 'second-vendor/second-package',
        'second-vendor/second-package' => true,
    ],
    'array parameter with UnsetArrayValue' => [
        'second-vendor/second-package' => true,
    ],
    'array parameter with ReplaceArrayValue' => [
        'second-vendor/second-package' => true,
    ],
    'array parameter with RemoveArrayKeys' => [
        'second-vendor/second-package' => 'second-vendor/second-package',
    ],
];
