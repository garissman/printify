<?php

namespace Garissman\Printify\Structures;

use Illuminate\Database\Eloquent\MassAssignmentException;
use Spatie\LaravelData\Data;

/**
 * @property mixed $id
 */
abstract class BaseStructure extends Data
{
    private array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public static function from(mixed ...$payloads): static
    {
        $payload = $payloads[0] ?? [];
        if (is_array($payload)) {
            return new static($payload);
        }
        return parent::from(...$payloads);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     * @return $this
     *
     * @throws MassAssignmentException
     */
    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    public function setAttribute($key, $value): static
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function __get(string $key)
    {
        return $this->attributes[$key];
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
