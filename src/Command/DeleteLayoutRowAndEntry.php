<?php namespace Fritzandandre\LayoutFieldType\Command;

use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\DB;

class DeleteLayoutRowAndEntry
{
    protected $tableName;

    protected $layoutRowId;

    /**
     * DeleteLayoutRowAndEntry constructor.
     *
     * @param $layoutRowId
     */
    public function __construct($tableName, $layoutRowId)
    {
        $this->tableName   = $tableName;
        $this->layoutRowId = $layoutRowId;
    }

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