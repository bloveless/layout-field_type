<?php namespace Fritzandandre\LayoutFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeSchema;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Doctrine\DBAL\Exception\TableExistsException;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class LayoutFieldTypeSchema
 *
 * @link          http://fritzandandre.com
 * @author        Brennon Loveless <brennon@fritzandandre.com>
 * @package       Fritzandandre\LayoutFieldType
 */
class LayoutFieldTypeSchema extends FieldTypeSchema
{
    /**
     * Create the pivot table.
     *
     * @param Blueprint           $table
     * @param AssignmentInterface $assignment
     * @throws TableExistsException
     */
    public function addColumn(Blueprint $table, AssignmentInterface $assignment)
    {
        $table = $table->getTable() . '_' . $this->fieldType->getField();

        if ($this->schema->hasTable($table)) {
            throw new TableExistsException($table . ' already exists.', null);
        }

        $this->schema->create(
            $table,
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('sort_order');
                $table->timestamps();
                $table->integer('entry_id');
                $table->string('widget_type');
                $table->integer('widget_id');
            }
        );
    }

    /**
     * Rename the pivot table.
     *
     * @param Blueprint $table
     * @param FieldType $from
     */
    public function renameColumn(Blueprint $table, FieldType $from)
    {
        $this->schema->rename(
            $table->getTable() . '_' . $from->getField(),
            $table->getTable() . '_' . $this->fieldType->getField()
        );
    }

    /**
     * Drop the pivot table.
     *
     * @param Blueprint $table
     */
    public function dropColumn(Blueprint $table)
    {
        $this->schema->dropIfExists(
            $table->getTable() . '_' . $this->fieldType->getField()
        );
    }
}