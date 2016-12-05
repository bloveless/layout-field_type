<?php namespace Fritzandandre\LayoutFieldType\PivotTable;

use Anomaly\Streams\Platform\Model\EloquentModel;

class PivotTableModel extends EloquentModel
{
    protected $widget;

    public function getWidget()
    {
        $this->widget = app($this->widget_type);
        $this->widget->setEntryId($this->widget_id);

        return $this->widget;
    }
}