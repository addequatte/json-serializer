<?php

namespace Addequatte\JsonSerializer\Processor;

use Addequatte\JsonSerializer\Model\JsonSerializable;

interface ProcessorInterface
{
    /**
     * @param JsonSerializable $class
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    public function process(JsonSerializable $class, string $field, mixed $value): mixed;
}