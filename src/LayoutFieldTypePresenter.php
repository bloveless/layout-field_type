<?php namespace Fritzandandre\LayoutFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldTypePresenter;

/**
 * Class LayoutFieldTypePresenter
 *
 * @package Fritzandandre\LayoutFieldType
 */
class LayoutFieldTypePresenter extends FieldTypePresenter
{
    /**
     * Render the layout field type.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getObject()->display();
    }
}