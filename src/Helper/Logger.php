<?php

namespace App\Helper;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Logger
{
    private $logDir;
    
    public function __construct(ParameterBagInterface $params)
    {
        $this->logDir = $params->get('kernel.logs_dir');
    }

    private function getLogfile(string $logfile): string
    {
        $path = $this->logDir . '/' . $logfile;
        if (false === realpath($path)) {
            touch($path);
        }
        return $path;
    }

    public function logJson(string $logfile, string $content): void
    {
        $path = $this->getLogfile($logfile);
        $content = \json_encode(\json_decode($content));
        file_put_contents($path, $content . PHP_EOL, FILE_APPEND);
    }

    public function logArray(string $logfile, array $content): void
    {
        $path = $this->getLogfile($logfile);
        $content = print_r($content, true);
        file_put_contents($path, $content . PHP_EOL, FILE_APPEND);
    }
}