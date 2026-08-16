<?php

declare(strict_types=1);

/** Escape a value for safe HTML output. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

/** Renders a view file with the given data, returning the HTML as a string. */
function render(string $view, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    require dirname(__DIR__) . "/views/{$view}.view.php";
    return (string) ob_get_clean();
}
