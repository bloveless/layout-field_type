<?php namespace Fritzandandre\LayoutFieldType\Command;

use Illuminate\Foundation\Bus\DispatchesJobs;

class GetFormHtmlFromLayoutEntries
{
    use DispatchesJobs;

    protected $layoutEntries;
    protected $fieldSlug;

    public function __construct($layoutEntries, $fieldSlug)
    {
        $this->layoutEntries = $layoutEntries;
        $this->fieldSlug     = $fieldSlug;
    }

    public function handle()
    {
        $formContents = [];
        $instance     = 0;

        foreach ($this->layoutEntries as $layoutEntry) {
            $extension = $layoutEntry->getWidget();
            $form      = $extension->getForm();

            /**
             * Setup some options for the form.
             */
            $this->dispatch(new SetFormOptions(
                $form,
                $extension,
                $this->fieldSlug,
                $instance++,
                $layoutEntry->sort_order
            ));

            /**
             * This will be used for deleting and updating.
             */
            $form->setOption('id', $layoutEntry->id);

            $form->make($layoutEntry->widget_id);

            /**
             * Append the form html to the form contents array.
             */
            $formContents[] = $form->getFormContent()->render();
        }

        return $formContents;
    }
}