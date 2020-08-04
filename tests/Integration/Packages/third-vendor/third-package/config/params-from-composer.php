<?php

declare(strict_types=1);

return [
    'third-vendor/third-package-from-composer' => true,
    'array parameter' => [
        'changed value' => 'third-vendor/third-package-from-composer',
        'third-vendor/third-package-from-composer' => true,
    ],
    'array parameter with UnsetArrayValue' => [
        'third-vendor/third-package-from-composer' => true,
    ],
    'array parameter with ReplaceArrayValue' => [
        'third-vendor/third-package-from-composer' => true,
    ],
    'array parameter with RemoveArrayKeys' => [
        'third-vendor/third-package-from-composer' => 'third-vendor/third-package-from-composer',
    ],
    'array parameter with ReverseValues' => [
        'third-vendor/third-package-from-composer' => 'third-vendor/third-package-from-composer',
    ],
];
