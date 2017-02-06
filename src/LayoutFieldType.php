<?php namespace Fritzandandre\LayoutFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Fritzandandre\LayoutFieldType\Command\CreateUpdateLayoutRows;
use Fritzandandre\LayoutFieldType\Command\DeleteLayoutRows;
use Fritzandandre\LayoutFieldType\Command\GetFormHtmlFromLayoutEntries;
use Fritzandandre\LayoutFieldType\Command\GetDisplayContentFromEntries;
use Fritzandandre\LayoutFieldType\PivotTable\PivotTableModel;

/**
 * Class LayoutFieldType
 *
 * @link          http://fritzandandre.com
 * @author        Brennon Loveless <brennon@fritzandandre.com>
 * @package       Fritzandandre\LayoutFieldType
 */
class LayoutFieldType extends FieldType
{
    /**
     * The view to use when rendering the field type.
     *
     * @var string
     */
    protected $inputView = 'fritzandandre.field_type.layout::input';

    /**
     * The field class
     * @var string
     */
    protected $class = "layout-container";

    /**
     * Store the pivot table model so it only gets created once.
     *
     * @var PivotTableModel
     */
    protected $pivotTableModel;

    /**
     * Get the pivot table name for this field type.
     *
     * @return string
     */
    public function getPivotTableName()
    {
        return $this->entry->getTableName() . '_' . $this->getField();
    }

    /**
     * Get the pivot table model for this instance of the field type.
     *
     * @return PivotTableModel
     */
    public function getPivotTableModel()
    {
        /**
         * If the model doesn't exist then create one.
         */
        if (!$this->pivotTableModel) {
            $this->pivotTableModel = (new PivotTableModel())->setTable($this->getPivotTableName());
        }

        return $this->pivotTableModel;
    }

    /**
     * Get the contents of the layout field for displaying on the front end.
     *
     * @return string
     */
    public function display()
    {
        if($this->entry) {
            /**
             * Get the widgets from the db for this layout field.
             */
            $entries = $this->getPivotTableModel()->where('entry_id', $this->getEntry()->getId())->orderBy('sort_order')->get();

            return $this->dispatch(new GetDisplayContentFromEntries($entries));
        }

        return "";
    }

    /**
     * Render the input and wrapper.
     *
     * @param  array $payload
     * @return string
     */
    public function render($payload = [])
    {
        /**
         * Get all the rows from the db for this layout field.
         */
        $layoutEntries = $this->getPivotTableModel()
                              ->where('entry_id', $this->getEntry()->getId())
                              ->orderBy('sort_order')->get();

        /**
         * Get the individual form html for each layout entry
         */
        $formContents = $this->dispatch(new GetFormHtmlFromLayoutEntries($layoutEntries, $this->getFieldName()));

        return view($this->inputView, ['forms_html' => $formContents, 'field_type' => $this])->render();
    }

    /**
     * Handle saving the field.
     */
    public function handle()
    {
        /**
         * Handle any deletes.
         */
        $inputValue = $this->dispatch(new DeleteLayoutRows($this->getPivotTableName(), $this->getInputValue()));

        /**
         * Handle any updates and creates.
         */
        $this->dispatch(new CreateUpdateLayoutRows(
            $inputValue,
            $this->getPivotTableModel(),
            $this->getEntry()->getId(),
            $this->getPrefix() . $this->getField()
        ));
    }
}
