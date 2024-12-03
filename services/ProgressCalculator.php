<?php

class ProgressCalculator {
    private $progress = 0;
    private $lastAdvice = null;

    public function calculatePercentage($totalFiles, $currentFile) {
        $this->progress = round(($currentFile / $totalFiles) * 100);

         // Muestra el progreso solo si cambió desde el último aviso
        if ($this->progress !== $this->lastAdvice) {
            echo "{$this->progress}% de los archivos encriptados\n";
            $this->lastAdvice = $this->progress;
        }
    }
}