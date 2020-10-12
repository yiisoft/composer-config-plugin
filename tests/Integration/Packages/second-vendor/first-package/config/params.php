<?php

declare(strict_types=1);

return [
    'second-vendor/first-package' => true,
    'array parameter' => [
        'changed value' => 'second-vendor/first-package',
        'second-vendor/first-package' => true,
    ],
    'array parameter with UnsetArrayValue' => [
        'second-vendor/first-package' => true,
    ],
    'array parameter with ReplaceArrayValue' => [
        'second-vendor/first-package' => true,
    ],
    'array parameter with RemoveArrayKeys' => [
        'second-vendor/first-package' => 'second-vendor/first-package',
    ],
    'array parameter with ReverseValues' => [
        'second-vendor/first-package' => 'second-vendor/first-package',
    ],
];
