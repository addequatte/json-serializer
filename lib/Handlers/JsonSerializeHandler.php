<?php

namespace Addequatte\JsonSerializer\Handlers;

use Addequatte\JsonSerializer\Model\JsonSerializable;
use Addequatte\JsonSerializer\Processor\ProcessorInterface;
use Addequatte\JsonSerializer\Traits\FieldGetterTrait;

class JsonSerializeHandler
{
    use FieldGetterTrait;

    /**
     * @var array
     */
    private array $hiddenFields = [];

    /**
     * @var ProcessorInterface|null
     */
    private $processor;

    /**
     * @param ProcessorInterface|null $processor
     */
    public function __construct(ProcessorInterface $processor = null)
    {
        $this->processor = $processor;
    }

    /**
     * @param mixed $data
     * @return array
     */
    public function jsonSerialize(mixed $data): array
    {
        $result = [];
        $isIterable = is_iterable($data);
        $data = $isIterable ? $data : [$data];

        foreach ($data as $dataKey => $item) {
            if ($item instanceof JsonSerializable) {

                $this->hideFields($item);

                foreach ($this->getFields($item) as $key => $var) {
                    if (!in_array($key, $item->getHiddenFields())
                        && $key != 'hiddenFields'
                        && method_exists($item, 'get' . $key)) {
                        if ($var instanceof JsonSerializable) {
                            $result[$dataKey][$key] = $this->jsonSerialize($var);
                        } elseif (is_iterable($var)) {
                            $result[$dataKey][$key] = [];
                            foreach ($var as $iterableKey => $iterableItem) {
                                $result[$dataKey][$key][$iterableKey] = $iterableItem instanceof JsonSerializable
                                    ? $this->jsonSerialize($iterableItem)
                                    : $iterableItem;
                            }
                        } else {
                            $result[$dataKey][$key] = !is_null($this->processor)
                                ? $this->processor->process($item, $key, $var)
                                : $var;
                        }
                    }
                }
            }
        }

        return $isIterable ? $result : $result[0];
    }

    /**
     * @param JsonSerializable $model
     * @return array
     */
    private function getHiddenFields(JsonSerializable $model): array
    {
        $result = [];
        foreach ($this->hiddenFields as $key => $value) {
            if (is_a($model, $key) || is_subclass_of($model, $key)) {
                $result = $value;
            }
        }

        return $result;
    }

    /**
     * @param JsonSerializable $model
     * @return void
     */
    private function hideFields(JsonSerializable $model): void
    {
        $hiddenFields = $this->getHiddenFields($model);

        $model->setHiddenFields($hiddenFields);

        foreach ($this->getFields($model) as $key => $var) {
            if (!in_array($key, $hiddenFields)) {
                if($var instanceof JsonSerializable) {
                    $this->hideFields($var);
                }elseif (is_iterable($var)) {
                    foreach ($var as $item) {
                        if($var instanceof JsonSerializable) {
                            $this->hideFields($item);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $className
     * @param array $hiddenFields
     * @return void
     */
    public function addHiddenFields(string $className, array $hiddenFields): void
    {
        if (!array_key_exists($className, $this->hiddenFields ?? [])) {
            $this->hiddenFields[$className] = $hiddenFields;
        }
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->hiddenFields = [];
    }
}