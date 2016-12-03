<?php namespace Fritzandandre\LayoutFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Model\EloquentQueryBuilder;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Fritzandandre\LayoutFieldType\FormBuilder\Command\PrepareFormForLayout;
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

    public function getPivotTableName()
    {
        return $this->entry->getTableName() . '_' . $this->getField();
    }

    /**
     * Get the contents of the layout field for displaying on the front end.
     * @return string
     */
    public function display()
    {
        $rawEntries = DB::table($this->getPivotTableName())->where('entry_id', $this->getEntry()->getId())->get();
        $output = "";

        foreach ($rawEntries as $rawEntry) {
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
        $rawEntries   = DB::table($this->getPivotTableName())->where('entry_id', $this->getEntry()->getId())->get();
        $formContents = [];
        $instance = 0;

        foreach ($rawEntries as $rawEntry) {
            $addon = app($rawEntry->widget_type);
            $form  = $addon->getForm();
            $this->dispatch(new PrepareFormForLayout(
                $addon,
                $form,
                $this->getFieldName(),
                $instance++,
                $rawEntry->widget_id
            ));

            $form->make($rawEntry->widget_id);

            $formContents[] = $form->getFormContent()->render();
        }

        return view($this->getInputView(), ['forms_html' => $formContents, 'field_type' => $this])->render();
    }

    /**
     * Handle saving the field.
     */
    public function handle()
    {
        $fieldTypeEntryId = $this->getEntry()->getId();

        foreach ($this->getInputValue() as $layoutInstance => $instanceParams) {
            /** @var FormBuilder $form */
            $addon = app($instanceParams['addon']);
            $form  = $addon->getForm();
            $this->dispatch(new PrepareFormForLayout(
                $addon,
                $form,
                $instanceParams['field_slug'],
                $layoutInstance,
                $instanceParams['entry_id']
            ));
            $form->make($instanceParams['entry_id']);

            if ($form->getFormEntry()->wasRecentlyCreated) {
                $attributes = [
                    'entry_id'    => $fieldTypeEntryId,
                    'widget_type' => get_class($addon),
                    'widget_id'   => $form->getFormEntryId(),
                ];

                DB::table($this->getPivotTableName())->insert($attributes);
            }
        }
    }
}
