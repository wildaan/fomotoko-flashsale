<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HiddenItemGame extends Command
{
    protected $signature = 'game:hidden-item';
    protected $description = 'Find all probable hidden item coordinates based on movement rules';

    private array $grid;
    private int $height;
    private int $width;

    public function handle()
    {
        $this->initializeGrid();

        $startPos = $this->findStartingPosition();
        if (!$startPos) {
            $this->error('Starting position X not found in the grid.');
            return;
        }

        [$startX, $startY] = $startPos;
        $validEndpoints = $this->calculateProbableLocations($startX, $startY);

        $this->displayResults($validEndpoints);
    }

    /**
     * Inisialisasi map/grid 2D
     */
    private function initializeGrid(): void
    {
        $gridStr = [
            "########",
            "#......#",
            "#.###..#",
            "#...#.##",
            "#X#....#",
            "########",
        ];

        $this->grid = array_map('str_split', $gridStr);
        $this->height = count($this->grid);
        $this->width = count($this->grid[0]);
    }

    /**
     * Mencari koordinat awal (X)
     */
    private function findStartingPosition(): ?array
    {
        for ($row = 0; $row < $this->height; $row++) {
            for ($col = 0; $col < $this->width; $col++) {
                if ($this->grid[$row][$col] === 'X') {
                    return [$row, $col];
                }
            }
        }
        return null;
    }

    /**
     * Mengecek apakah sebuah koordinat aman (bukan rintangan dan tidak keluar batas)
     */
    private function isValidStep(int $row, int $col): bool
    {
        if ($row < 0 || $row >= $this->height || $col < 0 || $col >= $this->width) {
            return false;
        }
        
        $char = $this->grid[$row][$col];
        return $char === '.' || $char === 'X';
    }

    /**
     * Kalkulasi semua kemungkinan titik akhir berdasarkan aturan North -> East -> South
     */
    private function calculateProbableLocations(int $startX, int $startY): array
    {
        $endpoints = [];

        for ($northSteps = 1; $northSteps < $this->height; $northSteps++) {
            $rowAfterNorth = $startX - $northSteps;
            $colAfterNorth = $startY;
            
            if (!$this->isValidStep($rowAfterNorth, $colAfterNorth)) {
                break;
            }

            for ($eastSteps = 1; $eastSteps < $this->width; $eastSteps++) {
                $rowAfterEast = $rowAfterNorth;
                $colAfterEast = $colAfterNorth + $eastSteps;
                
                if (!$this->isValidStep($rowAfterEast, $colAfterEast)) {
                    break;
                }

                for ($southSteps = 1; $southSteps < $this->height; $southSteps++) {
                    $finalRow = $rowAfterEast + $southSteps;
                    $finalCol = $colAfterEast;
                    
                    if (!$this->isValidStep($finalRow, $finalCol)) {
                        break;
                    }
                    
                    $key = "{$finalRow},{$finalCol}";
                    $endpoints[$key] = [$finalRow, $finalCol];
                }
            }
        }

        return $endpoints;
    }

    /**
     * Menampilkan hasil akhir ke terminal (Daftar koordinat & Visual map)
     */
    private function displayResults(array $endpoints): void
    {
        $this->info("Probable Item Coordinate(s) found:");
        $this->newLine();

        if (empty($endpoints)) {
            $this->warn("No valid paths found!");
            return;
        }

        foreach ($endpoints as $ep) {
            $this->line("- Row: {$ep[0]}, Col: {$ep[1]}");
        }

        $this->newLine();
        $this->info("Visual Grid (Probable locations marked with '$'):");
        $this->newLine();

        foreach ($this->grid as $r => $rowChars) {
            $line = '';
            foreach ($rowChars as $c => $char) {
                $key = "{$r},{$c}";
                if (isset($endpoints[$key]) && $char === '.') {
                    $line .= '$';
                } else {
                    $line .= $char;
                }
            }
            $this->line($line);
        }
    }
}