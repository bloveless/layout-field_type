<?php namespace Fritzandandre\LayoutFieldType\Command;

use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Fritzandandre\LayoutFieldType\PivotTable\PivotTableModel;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class CreateUpdateLayoutRows
 *
 * @package Fritzandandre\LayoutFieldType\Command
 */
class CreateUpdateLayoutRows
{
    use DispatchesJobs;

    protected $inputValue;
    /**
     * @var PivotTableModel
     */
    protected $pivotTableModel;
    protected $relatedEntryId;
    protected $fieldSlug;

    /**
     * CreateUpdateLayoutRows constructor.
     *
     * @param $inputValue
     * @param $pivotTableModel
     * @param $relatedEntryId
     * @param $fieldSlug
     */
    public function __construct($inputValue, $pivotTableModel, $relatedEntryId, $fieldSlug)
    {
        $this->inputValue      = $inputValue;
        $this->pivotTableModel = $pivotTableModel;
        $this->relatedEntryId  = $relatedEntryId;
        $this->fieldSlug       = $fieldSlug;
    }

    /**
     * Create or update the layout row and related widget.
     */
    public function handle()
    {
        foreach ($this->inputValue as $layoutInstance => $instanceParams) {

            $layoutRow = $this->pivotTableModel->find($instanceParams['id']);

            $widgetId = ($layoutRow) ? $layoutRow->widget_id : null;

            $extension = app($instanceParams['extension']);
            /** @var FormBuilder $form */
            $form = $extension->getForm();

            /**
             * Setup some options for the form.
             */
            $this->dispatch(new SetFormOptions(
                $form,
                $extension,
                $this->fieldSlug,
                $layoutInstance
            ));

            /**
             * Make will save the related form entry
             */
            $form->make($widgetId);

            /**
             * Some standard attributes to create or search by.
             */
            $attributes = [
                'entry_id'    => $this->relatedEntryId,
                'widget_type' => get_class($extension),
                'widget_id'   => $form->getFormEntryId(),
            ];

            /**
             * If the widget doesn't already exist then create it (and append the sort order)
             */
            if ($form->getFormEntry()->wasRecentlyCreated) {
                $this->pivotTableModel->insert(array_merge(
                    $attributes,
                    ['sort_order' => $instanceParams['sort_order']]
                ));
            }

            /**
             * If the widget already exists then it has already been saved and we just have to update the sort order
             */
            if (!$form->getFormEntry()->wasRecentlyCreated) {
                $this->pivotTableModel->where($attributes)->update(['sort_order' => $instanceParams['sort_order']]);
            }
        }
    }
}