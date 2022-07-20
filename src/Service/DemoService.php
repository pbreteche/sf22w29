<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class DemoService
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sayHello(bool $log=false): void
    {
        if ($log) {
            $this->logger->debug('hello');
        } else {
            dump('hello');
        }
    }
}
