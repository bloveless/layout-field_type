<?php namespace Fritzandandre\LayoutFieldType\Command;

/**
 * Class SetFormOptions
 *
 * @package Fritzandandre\LayoutFieldType\Command
 */
class SetFormOptions
{
    protected $form;
    protected $extension;
    protected $fieldSlug;
    protected $instanceId;
    protected $sortOrder;

    /**
     * SetFormOptions constructor.
     *
     * @param      $form
     * @param      $extension
     * @param      $fieldSlug
     * @param      $instanceId
     * @param null $sortOrder
     */
    public function __construct($form, $extension, $fieldSlug, $instanceId, $sortOrder = null)
    {
        $this->form       = $form;
        $this->extension  = $extension;
        $this->fieldSlug  = $fieldSlug;
        $this->instanceId = $instanceId;
        $this->sortOrder  = $sortOrder;
    }

    /**
     * Get the form ready for the layout field type by setting all the necessary
     * options for saving and rendering.
     */
    public function handle()
    {
        $this->form->setOption('form_view', 'fritzandandre.field_type.layout::form')
                   ->setOption('field_slug', $this->fieldSlug)
                   ->setOption('layout_instance', $this->instanceId)
                   ->setOption('extension_class', get_class($this->extension))
                   ->setOption('prefix', $this->fieldSlug . '_' . $this->instanceId . '_')
                   ->setOption('sort_order', $this->sortOrder);
    }
}