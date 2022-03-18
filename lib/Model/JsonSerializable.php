<?php

namespace Addequatte\JsonSerializer\Model;

use Addequatte\JsonSerializer\Processor\FieldProcessor;
use Addequatte\JsonSerializer\Traits\FieldGetterTrait;

abstract class JsonSerializable implements \JsonSerializable
{

    use FieldGetterTrait;

    /**
     * @var string[]
     */
    protected array $hiddenFields = [];

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->getFields($this) as $key => $var) {
            if (!in_array($key, $this->hiddenFields ?? [])
                && $key != 'hiddenFields'
                && method_exists($this, 'get' . $key)) {
                if (is_iterable($var)) {
                    $result[$key] = [];
                    foreach ($var as $iterableKey => $iterableItem) {
                        $result[$key][$iterableKey] = $iterableItem;
                    }
                } else {
                    $result[$key] = $var;
                }
            }
        }

        return $result;
    }

    /**
     * @return string[]
     */
    public function getHiddenFields(): array
    {
        return $this->hiddenFields;
    }

    
    /**
     * @param array $hiddenFields
     * @return void
     */
    public function setHiddenFields(array $hiddenFields): void
    {
        $this->hiddenFields = $hiddenFields;
    }
}