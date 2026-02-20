<?php

declare(strict_types=1);

$output = new Output();
$jsonFormatter = new JsonFormatter($output);

$opts = getopt("", ['env::', 'shopware:', 'admin', 'storefront']);

if (!$opts) {
    $output->error('No options set. Option "env" is required.');
    return;
}

// Get selected env
switch ($opts['env']) {
    case 'dev':
    case 'develop':
    case 'development':
        $env = 'development';
        $require = 'require-dev';
        break;
    case 'prod':
    case 'production':
        $env = 'production';
        $require = 'require';
        break;
}

if (!isset($require)) {
    $output->error('Env needs to be one of: dev, develop, development, prod, production');
    return;
}

// Get minimum Shopware version
$shopware = '*';

if (isset($opts['shopware'])) {
    $shopware = (string)$opts['shopware'];
}

// Should admin package be added to require
$requireAdmin = array_key_exists('admin', $opts);

// Should storefront package be added to require
$requireStorefront = array_key_exists('storefront', $opts);

try {
    $composerContent = $jsonFormatter->read(__DIR__ . '/composer.json');

    unset($composerContent['require']['shopware/core']);
    unset($composerContent['require']['shopware/administration']);
    unset($composerContent['require']['shopware/storefront']);
    unset($composerContent['require-dev']['shopware/core']);
    unset($composerContent['require-dev']['shopware/administration']);
    unset($composerContent['require-dev']['shopware/storefront']);

    if (empty($composerContent['require'])) {
        unset($composerContent['require']);
    }

    if (empty($composerContent['require-dev'])) {
        unset($composerContent['require-dev']);
    }

    $composerContent[$require]['shopware/core'] = $shopware;

    if ($requireAdmin) {
        $composerContent[$require]['shopware/administration'] = "*";
    }

    if ($requireStorefront) {
        $composerContent[$require]['shopware/storefront'] = "*";
    }

    $jsonFormatter->write(
        __DIR__ . '/composer.json',
        $jsonFormatter->sort(
            $composerContent,
            [
                "name",
                "description",
                "version",
                "type",
                "license",
                "authors",
                "extra",
                "autoload",
                "autoload-dev",
                "require",
                "require-dev",
                "scripts",
                "config",
            ]
        )
    );

    $output->success(sprintf('Switched composer.json to %s requiring Shopware version %s', $env, $shopware));
}
catch (\Exception $e) {
    $output->error($e->getMessage());
}

class JsonFormatter
{
    /** @var Output */
    private $output;

    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    public function read(string $path)
    {
        $json = file_get_contents($path);
        if (empty($json)) {
            throw new \Exception(sprintf('Something went wrong reading %s', $path));
        }

        $json = json_decode($this->fixEncoding($json), true);
        if (empty($json)) {
            throw new \Exception(sprintf('Something went wrong decoding %s', $path));
        }

        return $json;
    }

    public function write(string $path, array $json): bool
    {
        return (bool)file_put_contents($path, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    public function fixEncoding(string $json): string
    {
        $currentEncoding = mb_detect_encoding($json, ['UTF-8', 'ISO-8859-1'], true);

        switch ($currentEncoding) {
            case 'UTF-8': // Already UTF-8, do nothing
                break;
            case 'ISO-8859-1':
                $this->output->warn('Detected ISO-8859-1 encoding. Attempting to switch to UTF-8');

                $json = mb_convert_encoding($json, 'UTF-8', 'ISO-8859-1');
                break;
            default: // Unknown encoding, warn user they should convert manually.
                throw new \Exception('Unable to detect current json file encoding. Please convert to UTF-8 manually.');
        }

        return $json;
    }

    public function sort(array $json, array $keyOrder): array
    {
        $sortedArray = [];

        foreach ($keyOrder as $key) {
            if (isset($json[$key])) {
                $sortedArray[$key] = $json[$key];
                unset($json[$key]);
            }
        }

        if (!empty($json)) {
            $sortedArray += $json;
        }

        return $sortedArray;
    }
}

class Output
{
    const COLORS = [
        'black'   => 0,
        'red'     => 1,
        'green'   => 2,
        'yellow'  => 3,
        'blue'    => 4,
        'magenta' => 5,
        'cyan'    => 6,
        'white'   => 7,
        'default' => 9,
    ];

    public function info($text)
    {
        $this->writeLn($this->createBlock([$text]), self::COLORS['black'], self::COLORS['cyan']);
        $this->writeLn();
    }

    public function success($text)
    {
        $this->writeLn($this->createBlock([$text]), self::COLORS['black'], self::COLORS['green']);
        $this->writeLn();
    }

    public function warn($text)
    {
        $this->writeLn($this->createBlock([$text]), self::COLORS['black'], self::COLORS['yellow']);
        $this->writeLn();
    }

    public function error($text)
    {
        $this->writeLn($this->createBlock([$text]), self::COLORS['white'], self::COLORS['red']);
        $this->writeLn();
    }

    public function writeLn($messages = "", $fg = self::COLORS['default'], $bg = self::COLORS['default'])
    {
        if (!is_iterable($messages)) {
            $messages = [$messages];
        }

        $fg = '3' . $fg;
        $bg = '4' . $bg;

        foreach ($messages as $message) {
            echo sprintf("\033[%s;%sm%s\033[0m%s", $fg, $bg, $message, PHP_EOL);
        }
    }

    public function createBlock(iterable $messages, int $indentLength = 2)
    {
        $lines = [];
        $lineLength = 80;

        $lineIndentation = str_repeat(' ', $indentLength);

        foreach ($messages as $message) {
            $messageLineLength = $lineLength - ($indentLength * 2);
            $messageLines = explode(\PHP_EOL, wordwrap($message, $messageLineLength, \PHP_EOL, true));

            foreach ($messageLines as $messageLine) {
                $lines[] = $messageLine;
            }
        }

        array_unshift($lines, '');
        $lines[] = '';

        foreach ($lines as &$line) {
            $line = $lineIndentation . $line;
            $line .= str_repeat(' ', max($lineLength - strlen($line), 0));
        }

        return $lines;
    }
}
