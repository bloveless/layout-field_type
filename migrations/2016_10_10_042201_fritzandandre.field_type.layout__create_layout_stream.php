<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class FritzandandreFieldTypeLayoutCreateLayoutStream extends Migration
{
    /**
     * The stream properties.
     *
     * @var array
     */
    protected $stream = [
        'slug' => 'layout',
        'sortable' => true
    ];

    /**
     * The stream assignments.
     *
     * @var array
     */
    protected $assignments = [
        'entry' => [
            'required' => true
        ],
        'widget' => [
            'required' => true
        ]
    ];
}
