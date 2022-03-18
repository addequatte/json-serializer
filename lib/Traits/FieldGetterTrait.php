<?php

namespace Addequatte\JsonSerializer\Traits;

use Addequatte\JsonSerializer\Model\JsonSerializable;

trait FieldGetterTrait
{
    /**
     * @param object $model
     * @return array
     */
    protected function getFields(object $model):array
    {
        $result = [];

        $classes = array_merge([get_class($model)],array_diff(class_parents($model), [JsonSerializable::class]));

        foreach ($classes as $class) {
            $foo = \Closure::bind(function ($model) {
                return get_object_vars($model);
            }, null, $class);

            $result = array_merge($result, $foo($model));
        }

        return $result;
    }
}