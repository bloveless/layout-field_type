<?php namespace Fritzandandre\LayoutFieldType\Command;

/**
 * Class PrepareFormForLayout
 *
 * @package Fritzandandre\LayoutFieldType\Command
 */
class PrepareFormForLayout
{
    protected $addon;
    protected $form;
    protected $fieldSlug;
    protected $instanceId;
    protected $entryId;
    protected $sortOrder;

    public function __construct($addon, $form, $fieldSlug, $instanceId, $entryId = null, $sortOrder = null)
    {
        $this->addon      = $addon;
        $this->form       = $form;
        $this->fieldSlug  = $fieldSlug;
        $this->instanceId = $instanceId;
        $this->entryId    = $entryId;
        $this->sortOrder  = $sortOrder;
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
                   ->setOption('addon_class', get_class($this->addon))
                   ->setOption('widget_form', get_class($this->form))
                   ->setOption('layout_instance', $this->instanceId)
                   ->setOption('prefix', $this->fieldSlug . '_' . $this->instanceId . '_')
                   ->setOption('entry_id', $this->entryId)
                   ->setOption('sort_order', $this->sortOrder);
    }
}