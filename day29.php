<?php
// structure de code 
$structure = [
    'app' => [
        'Controllers' => [],
        'Models'      => [],
        'Views'       => [],
        'Middleware'  => [],
        'Services'    => [],
        'Helpers'     => [],
    ],
    'config'  => [],
    'public'  => [
        'css' => [],
        'js'  => [],
        'img' => [],
    ],
    'routes'   => [],
    'storage'  => [
        'logs'  => [],
        'cache' => [],
    ],
    'tests'    => [],
    'vendor'   => [],
];

function createStructure(string $base, array $tree): void {
    foreach ($tree as $name => $children) {
        $path = $base . DIRECTORY_SEPARATOR . $name;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        if (!empty($children)) {
            createStructure($path, $children);
        }
    }
}

$root = __DIR__ . DIRECTORY_SEPARATOR . 'my_project';

createStructure($root, $structure);

function printStructure(string $base, array $tree, int $level = 0): void {
    foreach ($tree as $name => $children) {
        echo str_repeat("    ", $level) . $name . "\n";
        if (!empty($children)) {
            printStructure($base, $children, $level + 1);
        }
    }
}

echo "my_project\n";
printStructure($root, $structure, 1);
echo "\nDone.\n";
?>
