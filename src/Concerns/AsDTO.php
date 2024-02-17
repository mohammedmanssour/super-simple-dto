<?php

namespace MohammedManssour\DTO\Concerns;

use ReflectionProperty;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait AsDTO
{
    public static function fromRequest(Request $request, bool $useAll = false): static
    {
        if ($useAll || !method_exists($request, 'validated')) {
            return static::fromCollection(
                collect($request->all())
            );
        }

        return static::fromCollection(
            collect($request->validated())
        );
    }

    public static function fromModel(Model $model): static
    {
        return static::fromCollection(
            collect($model->getAttributes())
        );
    }

    public static function fromArray(array $array): static
    {
        return static::fromCollection(
            collect($array)
        );
    }

    public static function fromCollection(Collection $collection): static
    {
        $object = new static();
        $collection->each(function ($value, $key) use (&$object) {
            if (method_exists($object, $key)) {
                $object->{$key}($value);

                return;
            }

            if (property_exists($object, $key)) {
                $object->{$key} = $value;

                return;
            }
        });

        return $object;
    }

    public function toArray(): array
    {
        $attributes = (array) $this;

        foreach ($attributes as $key => $value) {
            if (!is_object($value)) {
                continue;
            }

            if ($value instanceof \UnitEnum) {
                $attributes[$key] = $value->value;

                continue;
            }

            if ($value instanceof CarbonInterface) {
                $attributes[$key] = $value;

                continue;
            }

            if (method_exists($value, 'toArray')) {
                $attributes[$key] = $value->toArray();

                continue;
            }
            $attributes[$key] = (array) $value;
        }

        return $attributes;
    }

    /**
     * checks if a property was initialized
     *
     * This helper method is different from the php isset function
     * The native issue function will return false if the property is initialized with `false` or `null`
     *  This helper will return false only and only if the property was not initialized
     */
    public function isset($key)
    {
        return (new ReflectionProperty(self::class, $key))->isInitialized($this);
    }
}
