<?php

namespace App\Services;

use Exception;
use Illuminate\Support\LazyCollection;

class CsvProcessor
{
    /**
     * Process CSV file to array with various options
     *
     * @param string $filePath
     * @param array $options
     * @return array|LazyCollection
     * @throws Exception
     */
    public function process(string $filePath, array $options = [])
    {
        $options = array_merge([
            'header' => null,       // Custom headers
            'delimiter' => ',',    // Field delimiter
            'length' => 1000,       // Max line length
            'skipHeader' => false,  // Skip first row
            'chunkSize' => null,    // Chunk processing size
        ], $options);

        // Deteksi encoding file
        $fileContent = file_get_contents($filePath);
        $encoding = mb_detect_encoding($fileContent, 'UTF-8, ISO-8859-1', true);

        if ($encoding !== 'UTF-8') {
            $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
            file_put_contents($filePath, $fileContent);
        }

        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception("CSV file does not exist or is not readable");
        }

        if ($options['chunkSize']) {
            return $this->processInChunks($filePath, $options);
        }

        return $this->processAll($filePath, $options);
    }

    /**
     * Process entire file at once
     */
    protected function processAll(string $filePath, array $options): array
    {
        $data = [];
        $header = $options['header'];
        $skipHeader = $options['skipHeader'];
        $firstRow = true;

        if (($handle = fopen($filePath, 'r')) === false) {
            throw new Exception("Could not open CSV file");
        }

        try {
            while (($row = fgetcsv($handle, $options['length'], $options['delimiter'])) !== false) {
                if ($firstRow && $skipHeader) {
                    $firstRow = false;
                    continue;
                }

                if ($header === null) {
                    if ($firstRow) {
                        $header = $row;
                        $firstRow = false;
                        continue;
                    }
                    $header = array_map(fn($i) => "column_$i", range(0, count($row) - 1));
                }

                if (count($header) !== count($row)) {
                    throw new Exception("Header count does not match column count");
                }

                $data[] = array_combine($header, $row);
                $firstRow = false;
            }
        } finally {
            fclose($handle);
        }

        return $data;
    }

    /**
     * Process file in chunks using LazyCollection
     */
    protected function processInChunks(string $filePath, array $options): LazyCollection
    {
        return LazyCollection::make(function () use ($filePath, $options) {
            $handle = fopen($filePath, 'r');
            $header = $options['header'];
            $skipHeader = $options['skipHeader'];
            $firstRow = true;
            $chunk = [];
            $chunkSize = $options['chunkSize'];

            try {
                while (($row = fgetcsv($handle, $options['length'], $options['delimiter'])) !== false) {
                    if ($firstRow && $skipHeader) {
                        $firstRow = false;
                        continue;
                    }

                    if ($header === null) {
                        if ($firstRow) {
                            $header = $row;
                            $firstRow = false;
                            continue;
                        }
                        $header = array_map(fn($i) => "column_$i", range(0, count($row) - 1));
                    }

                    if (count($header) !== count($row)) {
                        throw new Exception("Header count does not match column count");
                    }

                    $chunk[] = array_combine($header, $row);
                    $firstRow = false;

                    if (count($chunk) >= $chunkSize) {
                        yield $chunk;
                        $chunk = [];
                    }
                }

                if (!empty($chunk)) {
                    yield $chunk;
                }
            } finally {
                fclose($handle);
            }
        });
    }
}
