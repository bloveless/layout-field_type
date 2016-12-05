<?php namespace Fritzandandre\LayoutFieldType\Command;

use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\DB;

class DeleteLayoutRowAndEntry
{
    /**
     * The pivot table name for this field type.
     * @var
     */
    protected $tableName;

    /**
     * The layout row to be deleted.
     * @var
     */
    protected $layoutRowId;

    /**
     * DeleteLayoutRowAndEntry constructor.
     *
     * @param $tableName
     * @param $layoutRowId
     */
    public function __construct($tableName, $layoutRowId)
    {
        $this->tableName   = $tableName;
        $this->layoutRowId = $layoutRowId;
    }

    /**
     * Handle deleting the layout row and it's related entry.
     */
    public function handle()
    {
        $deleteRow = DB::table($this->tableName)->where('id', $this->layoutRowId)->first();
        $addon     = app($deleteRow->widget_type);
        $form      = $addon->getForm();

        /** @var FormBuilder $form */
        $form->make($deleteRow->widget_id);
        $form->getFormEntry()->forceDelete();

        DB::table($this->tableName)->where('id', $this->layoutRowId)->delete();
    }
}