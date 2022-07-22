<?php

namespace App\Service;

interface DemoServiceInterface
{
    public function sayHello(bool $log=false): void;
}
