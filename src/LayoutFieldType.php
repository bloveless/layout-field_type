<?php namespace Fritzandandre\LayoutFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Fritzandandre\LayoutFieldType\Command\DeleteLayoutRowAndEntry;
use Fritzandandre\LayoutFieldType\Command\PrepareFormForLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Get the pivot table name for this field type.
     *
     * @return string
     */
    public function getPivotTableName()
    {
        return $this->entry->getTableName() . '_' . $this->getField();
    }

    /**
     * Get the contents of the layout field for displaying on the front end.
     *
     * @return string
     */
    public function display()
    {
        /**
         * Get the widgets from the db for this layout field.
         */
        $rawEntries = DB::table($this->getPivotTableName())->where('entry_id', $this->getEntry()->getId())->get();

        $output = "";

        foreach ($rawEntries as $rawEntry) {
            /**
             * Create each widget and get the HTML to display it.
             */
            $addon = app($rawEntry->widget_type);
            $addon->setEntryId($rawEntry->widget_id);
            $output .= $addon->render();
        }

        return $output;
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
        $rawEntries = DB::table($this->getPivotTableName())->where('entry_id',
            $this->getEntry()->getId())->orderBy('sort_order')->get();

        $formContents = [];
        $instance     = 0;

        foreach ($rawEntries as $rawEntry) {
            $addon = app($rawEntry->widget_type);
            $form  = $addon->getForm();

            /**
             * Setup some options for the form.
             */
            $this->dispatch(new PrepareFormForLayout(
                $addon,
                $form,
                $this->getFieldName(),
                $instance++,
                $rawEntry->widget_id,
                $rawEntry->sort_order
            ));

            /**
             * This will be used for deleting.
             */
            $form->setOption('layout_row_id', $rawEntry->id);

            $form->make($rawEntry->widget_id);

            /**
             * Append the form html to the form contents array.
             */
            $formContents[] = $form->getFormContent()->render();
        }

        return view($this->getInputView(), ['forms_html' => $formContents, 'field_type' => $this])->render();
    }

    /**
     * Handle saving the field.
     */
    public function handle(Request $request)
    {
        $fieldTypeEntryId = $this->getEntry()->getId();

        /**
         * Take care of any deletes
         */
        if ($deleteIds = $request->input($this->getFieldName() . '_delete_ids')) {
            $deleteIds = explode(',', $deleteIds);
            foreach ($deleteIds as $deleteId) {
                $this->dispatch(new DeleteLayoutRowAndEntry($this->getPivotTableName(), $deleteId));
            }
        }

        foreach ($this->getInputValue() as $layoutInstance => $instanceParams) {
            $addon = app($instanceParams['addon']);
            /** @var FormBuilder $form */
            $form  = $addon->getForm();

            /**
             * Setup some options for the form.
             */
            $this->dispatch(new PrepareFormForLayout(
                $addon,
                $form,
                $instanceParams['field_slug'],
                $layoutInstance,
                $instanceParams['entry_id']
            ));
            $form->make($instanceParams['entry_id']);

            /**
             * Some standard attributes to create or search by.
             */
            $attributes = [
                'entry_id'    => $fieldTypeEntryId,
                'widget_type' => get_class($addon),
                'widget_id'   => $form->getFormEntryId(),
            ];

            /**
             * If the widget doesn't already exist then create it (and append the sort order)
             */
            if ($form->getFormEntry()->wasRecentlyCreated) {
                DB::table($this->getPivotTableName())->insert(array_merge($attributes,
                    ['sort_order' => $instanceParams['sort_order']]));
            }

            /**
             * If the widget already exists then it has already been saved and we just have to update the sort order
             */
            if (!$form->getFormEntry()->wasRecentlyCreated) {
                DB::table($this->getPivotTableName())->where($attributes)->update(['sort_order' => $instanceParams['sort_order']]);
            }
        }
    }
}
