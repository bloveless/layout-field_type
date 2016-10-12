<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class FritzandandreFieldTypeLayoutCreateLayoutFields extends Migration
{
    /**
     * The field type fields.
     *
     * @var array
     */
    protected $fields = [
        'entry'  => 'anomaly.field_type.polymorphic',
        'widget' => 'anomaly.field_type.polymorphic'
    ];
}
