<?php

declare(strict_types=1);

function famous_thoughts_file_path(): string
{
    return app_data_root_path() . '/database/famous-thoughts.txt';
}

function load_famous_thoughts(): array
{
    $candidateFiles = [
        famous_thoughts_file_path(),
        project_root_path() . '/storage/database/famous-thoughts.txt',
    ];

    $file = null;
    foreach ($candidateFiles as $candidateFile) {
        if (is_file($candidateFile)) {
            $file = $candidateFile;
            break;
        }
    }

    if ($file === null) {
        return [];
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $thoughts = [];

    foreach ($lines as $line) {
        [$author, $quote, $category] = array_pad(array_map('trim', explode('|', $line)), 3, '');
        if ($author === '' || $quote === '') {
            continue;
        }

        $thoughts[] = [
            'author' => trim($author, '"'),
            'quote' => trim($quote, '"'),
            'category' => trim($category, '"'),
        ];
    }

    return $thoughts;
}
