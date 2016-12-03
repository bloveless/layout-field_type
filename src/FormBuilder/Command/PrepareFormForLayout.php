<?php namespace Fritzandandre\LayoutFieldType\FormBuilder\Command;

/**
 * Class PrepareFormForLayout
 *
 * @package Fritzandandre\LayoutFieldType\FormBuilder\Command
 */
class PrepareFormForLayout
{
    protected $form;
    protected $fieldSlug;
    protected $instanceId;
    protected $entryId;

    public function __construct($form, $fieldSlug, $instanceId, $entryId = null)
    {
        $this->form       = $form;
        $this->fieldSlug  = $fieldSlug;
        $this->instanceId = $instanceId;
        $this->entryId    = $entryId;
    }

    /**
     * Get the form ready for the layout field type by setting all the necessary
     * options for saving and rendering.
     */
    public function handle()
    {
        $this->form->setOption('wrapper_view', 'fritzandandre.field_type.layout::form_wrapper')
                   ->setOption('form_view', 'fritzandandre.field_type.layout::form')
                   ->setOption('layout_prefix', $this->fieldSlug)
                   ->setOption('widget_form', get_class($this->form))
                   ->setOption('layout_instance', $this->instanceId)
                   ->setOption('prefix', $this->fieldSlug . '_' . $this->instanceId . '_')
                   ->setOption('entry_id', $this->entryId);
    }
}