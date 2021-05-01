<?php

namespace App\Services;

use SplFileObject;

class ListService
{
    const length = 65535; // 4096, 32768, 65535
    private string $path;
    private $fp;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->fp = fopen($path, "r");
    }

    public function __destruct()
    {
        fclose($this->fp);
    }

    public function get_total_lines(): int
    {
        $total = 0;

        while (!feof($this->fp)) {
            $line = stream_get_line($this->fp, self::length);
            $total = $total + substr_count($line, PHP_EOL); // If not working in your OS, use "\r\n" instead.
        }

        return $total + 1;
    }

    public function find_delimiter($checkLines = 50): ?string
    {
        $file = new SplFileObject($this->path);
        $delimiters = [
            ',' => 'Comma',
            ';' => 'Semicolon',
            ':' => 'Colon',
            '\t' => 'Tab',
        ];
        $results = [];
        $i = 0;

        while ($file->valid() && $i <= $checkLines) {
            $line = $file->fgets();
            foreach ($delimiters as $delimiter => $caption) {
                $regExp = '/[' . $delimiter . ']/';
                $fields = preg_split($regExp, $line);
                if (count($fields) > 1) {
                    if (!empty($results[$delimiter])) {
                        $results[$delimiter]++;
                    } else {
                        $results[$delimiter] = 1;
                    }
                }
            }
            $i++;
        }
        $results = !empty($results) ? array_keys($results, max($results)) : null;

        return !empty($results) ? $delimiters[$results[0]] : null;
    }
}
