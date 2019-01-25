<?php

namespace Properties\Traits;

trait HasProperties
{
    /**
     * Return properties on the inheriting model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function properties()
    {
        return $this->morphToMany(config('properties.model', \Properties\Models\Property::class), 'propertyable')
                    ->withPivot('value');
    }

    /**
     * A simplified alias for attaching a property with a custom value.
     *
     * @return \Properties\Models\Property
     */
    public function attachProperty($propertyKey, $value = null)
    {
        $model = config('properties.model', \Properties\Models\Property::class);
        $property = $model::find($propertyKey);

        if (!$property) {
            throw new \Exception("Property '{$propertyKey}' not found");
        }

        if ($property->type == 'JSON' || $property->type == 'SCHEMA') {
            if (!is_null($value) && !is_array($value)) {
                throw new \Exception("Property '{$propertyKey}' requires its value to be an array");
            }

            if ($property->type == 'SCHEMA') {
                $originalProps = collect($property->default)->keyBy('key');
                $requiredParams = $originalProps->keys();
                $defaultValues = $originalProps->all();

                foreach ($requiredParams as $key) {
                    if (!isset($value[$key])) {
                        $value[$key] = $defaultValues[$key]->default;
                    }
                }
            }

            $value = json_encode($value);
        }

        $this->properties()->attach($propertyKey, ['value' => $value]);

        return $this;
    }
}
