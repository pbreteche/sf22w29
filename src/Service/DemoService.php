<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class DemoService
{
    /** @var LoggerInterface */
    private $logger;
    /** @var string */
    private $prefix;

    public  $suffix;

    public function __construct(string $prefix, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->prefix = $prefix;
    }

    public function init(int $value)
    {
        dump($value);
    }

    public function sayHello(bool $log=false): void
    {
        if ($log) {
            $this->logger->debug($this->prefix.' hello '.$this->suffix);
        } else {
            dump($this->prefix.' hello '.$this->suffix);
        }
    }
}
