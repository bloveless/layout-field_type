<?php namespace Fritzandandre\LayoutFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeAccessor;

class LayoutFieldTypeAccessor extends FieldTypeAccessor
{
    public function set($value)
    {
        $entry = $this->fieldType->getEntry();

        $attributes = $entry->getAttributes();

        $attributes[$this->fieldType->getColumnName()] = $value;

        $entry->setRawAttributes($attributes);
    }

    public function get()
    {
        $entry = $this->fieldType->getEntry();

        $attributes = $entry->getAttributes();

        // dd($attributes);

        return array_get($attributes, $this->fieldType->getColumnName());
    }
}