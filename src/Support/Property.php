<?php

namespace MohammedManssour\DTO\Support;

use ReflectionType;
use ReflectionProperty;
use Illuminate\Support\Str;
use MohammedManssour\DTO\Concerns\AsDTO;
use MohammedManssour\DTO\Support\MapInto;
use ReflectionException;

class Property
{
    public function __construct(public object $object, public string $name) {}

    public function reflection(): ReflectionProperty
    {
        return once(fn() => new ReflectionProperty($this->object, $this->name));
    }

    private function type()
    {
        return once(fn() => $this->reflection()->getType());
    }

    public function typeName(): string
    {
        return once(fn() => $this->type()->getName());
    }

    private function isEnum(): bool
    {
        return enum_exists($this->typeName());
    }

    private function isDTO(): bool
    {
        return in_array(
            AsDTO::class,
            class_uses_recursive($this->typeName())
        );
    }

    private function hasMapIntoAttribute(): bool
    {
        $attributes = $this->reflection()->getAttributes(MapInto::class);
        return count($attributes) > 0;
    }

    public function assign($value)
    {
        try {
            $setterMethod = 'set' . Str::studly($this->name);
            if (method_exists($this->object, $setterMethod)) {
                $this->object->{$setterMethod}($value);

                return;
            }

            if ($this->hasMapIntoAttribute()) {
                $this->assignMapInto($value);
                return;
            }

            if ($this->type()?->isBuiltin()) {
                $this->assignPlain($value);
                return;
            }

            if ($this->isEnum()) {
                $this->assignEnum($value);
                return;
            }

            if ($this->isDTO()) {
                $this->assignDTO($value);
                return;
            }
        } catch(ReflectionException $e) {}
    }

    private function assignEnum($value)
    {
        if (!($value instanceof \UnitEnum)) {
            $typeName = $this->typeName();
            $value = $typeName::from($value);
        }

        $this->assignPlain($value);
    }

    public function assignDTO($value)
    {
        $typeName = $this->typeName();
        if (is_object($value) && get_class($value) == $typeName) {
            $this->assignPlain($value);
            return;
        }


        $this->assignPlain($typeName::from($value));
    }

    private function assignMapInto($value)
    {
        if (!is_iterable($value)) {
            throw new \InvalidArgumentException("MapInto attribute can only be used with array values");
        }

        $attributes = $this->reflection()->getAttributes(MapInto::class);
        $dtoClass =  $attributes[0]->newInstance()->class;

        $mappedArray = [];
        foreach ($value as $item) {
            if (is_object($item) && get_class($item) === $dtoClass) {
                $mappedArray[] = $item;
            } else {
                $mappedArray[] = $dtoClass::from($item);
            }
        }

        $this->assignPlain($mappedArray);
    }

    private function assignPlain($value)
    {
        $this->object->{$this->name} = $value;
    }
}
