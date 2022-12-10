<?php

namespace Atournayre\Component\ExceptionHandler\Contracts;

interface StatusCodeProvider
{
        public function getStatusCode(): int;
}
