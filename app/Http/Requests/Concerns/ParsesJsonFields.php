<?php

namespace App\Http\Requests\Concerns;

trait ParsesJsonFields
{
    protected function decodeJsonFields(array $fields): void
    {
        foreach ($fields as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $value = $this->input($field);

            if (is_array($value)) {
                continue;
            }

            if ($value === null || $value === '') {
                $this->merge([$field => null]);

                continue;
            }

            $decoded = json_decode((string) $value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge([$field => $decoded]);
            }
        }
    }
}
