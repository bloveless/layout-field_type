<?php namespace Fritzandandre\LayoutFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Http\Request;

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
     * Handle saving the field.
     *
     * @param FormBuilder $builder
     */
    public function handle(FormBuilder $builder, Request $request)
    {
        dump($this->getInputValue());
        dump($request->input());
        dump($builder->getFormInput());

        foreach($this->getInputValue() as $layoutInstance => $instanceParams) {
            $form = app($instanceParams['class'])->build();
            dd($form->handle());
        }

        exit;
    }
}
