<?php

namespace Addequatte\JsonSerializer\Processor;

use Addequatte\JsonSerializer\Model\JsonSerializable;

class FieldProcessor implements ProcessorInterface
{

    /**
     * @var array
     */
    private array $closures = [];

    /**
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    public function process(JsonSerializable $class, string $field, mixed $value): mixed
    {
        $className = $this->getClassName($class);

        return match (true) {
            $this->isIterable($value) => $this->iterableProcess($className, $field, $value),
            default => array_key_exists($field, $this->closures[$className] ?? []) ? $this->closures[$className][$field]($value) : $value,
        };
    }

    /**
     * @param JsonSerializable $class
     * @return string
     */
    private function getClassName(JsonSerializable $class): string
    {
        $result = '';
        foreach ($this->closures as $key => $value) {
            if (is_a($class, $key) || is_subclass_of($class, $key)) {
                $result = $key;
            }
        }

        return $result;
    }

    /**
     * @param string $className
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    private function iterableProcess(string $className, string $field, mixed $value): mixed
    {
        $result = [];

        if (array_key_exists($field, $this->closures[$className] ?? [])) {
            $result = $this->closures[$className][$field]($value);
        } else {
            foreach ($value as $ikey => $item) {
                $result[$ikey] = $item instanceof JsonSerializable ? $item->jsonSerialize() : $item;
            }
        }

        return $result;
    }

    /**
     * @param $field
     * @return bool
     */
    private function isIterable($field): bool
    {
        return is_iterable($field);
    }

    /**
     * @param $field
     * @return bool
     */
    private function isDateTime($field): bool
    {
        return $field instanceof \DateTimeInterface;
    }

    /**
     * @param string $field
     * @param \Closure $closure
     * @return void
     */
    public function addClosure(string $className, string $field, \Closure $closure): void
    {
        if (!array_key_exists($field, $this->closures[$className] ?? [])) {
            $this->closures[$className][$field] = $closure;
        }
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->closures = [];
    }
}