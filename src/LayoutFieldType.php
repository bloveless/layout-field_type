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
     * Render the input and wrapper.
     *
     * @param  array $payload
     * @return string
     */
    public function render($payload = [])
    {
        $rawForms     = DB::table($this->getPivotTableName())->where('entry_id', $this->getEntry()->getId())->get();
        $formContents = [];

        foreach ($rawForms as $rawForm) {
            $form = app($rawForm->widget_type);
            $this->dispatch(new PrepareFormForLayout(
                $form,
                $this->getFieldName(),
                $rawForm->widget_id,
                $rawForm->widget_id
            ));

            $form->make($rawForm->widget_id);

            $formContents[] = $form->getFormContent()->render();
        }

        return view($this->getInputView(), ['forms_html' => $formContents, 'field_type' => $this])->render();
    }

    /**
     * Handle saving the field.
     *
     * @param FormBuilder $builder
     */
    public function handle(FormBuilder $builder, EloquentQueryBuilder $query)
    {
        $fieldTypeEntryId = $this->getEntry()->getId();

        foreach ($this->getInputValue() as $layoutInstance => $instanceParams) {

            /** @var FormBuilder $form */
            $form = app($instanceParams['form']);
            $this->dispatch(new PrepareFormForLayout(
                $form,
                $instanceParams['field_slug'],
                $layoutInstance,
                $instanceParams['entry_id']
            ));
            $form->make($instanceParams['entry_id']);

            if ($form->getFormEntry()->wasRecentlyCreated) {
                $attributes = [
                    'entry_id'    => $fieldTypeEntryId,
                    'widget_type' => get_class($form),
                    'widget_id'   => $form->getFormEntryId(),
                ];

                DB::table($this->getPivotTableName())->insert($attributes);
            }
        }
    }
}
