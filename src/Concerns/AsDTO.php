<?php

namespace MohammedManssour\DTO\Concerns;

use ReflectionProperty;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use MohammedManssour\DTO\Support\Property;

trait AsDTO
{
    public static function from(mixed ...$args): static
    {
        // Check if we have a single positional argument that is a known type
        if (count($args) === 1 && is_numeric(array_key_first($args))) {
            $data = reset($args);

            if ($data instanceof Request) {
                return static::fromRequest($data);
            }

            if ($data instanceof Model) {
                return static::fromModel($data);
            }

            if ($data instanceof Collection) {
                return static::fromCollection($data);
            }

            if (is_array($data)) {
                return static::fromArray($data);
            }

            throw new \InvalidArgumentException('Unsupported data type for DTO conversion');
        }

        return static::fromCollection(collect($args));
    }

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
            (new Property($object, $key))->assign($value);
        });

        return $object;
    }

    public function toArray(): array
    {
        $attributes = (array) $this;

        foreach ($attributes as $key => $value) {
            $attributes[$key] = $this->getArraybleValue($value);
        }

        return $attributes;
    }

    protected function getArraybleValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $subKey => $subValue) {
                $value[$subKey] = $this->getArraybleValue($subValue);
            }
            return $value;
        }

        if (!is_object($value)) {
            return $value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->value;
        }

        if ($value instanceof CarbonInterface) {
            return $value;
        }

        if (method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        return (array) $value;
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
        return (new ReflectionProperty(static::class, $key))->isInitialized($this);
    }
}
