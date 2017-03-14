<?php namespace Fritzandandre\LayoutFieldType\Command;

use Illuminate\Foundation\Bus\DispatchesJobs;

class GetFormHtmlFromLayoutEntries
{
    use DispatchesJobs;

    protected $pivotTableModel;

    protected $layoutEntries;

    protected $fieldSlug;

    /**
     * GetFormHtmlFromLayoutEntries constructor.
     *
     * @param $pivotTableModel
     * @param $layoutEntries
     * @param $fieldSlug
     */
    public function __construct($pivotTableModel, $layoutEntries, $fieldSlug)
    {
        $this->pivotTableModel = $pivotTableModel;
        $this->layoutEntries   = $layoutEntries;
        $this->fieldSlug       = $fieldSlug;
    }

    public function handle()
    {
        $formContents = [];
        $instance     = 0;

        foreach ($this->layoutEntries as $layoutEntry) {
            /**
             * If the widget exists. I.E. wasn't uninstalled or deleted.
             */
            if(class_exists($layoutEntry->widget_type)) {
                $extension = $layoutEntry->getWidget();

                /**
                 * We can only display the widget if it is not null
                 * and is currently installed.
                 */
                if($extension && $extension->isInstalled()) {
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
                    $formContents[] = [
                        'name' => trans($extension->getName()),
                        'html' => $form->getFormContent()->render(),
                    ];
                }
            }
            /**
             * Since this entry no longer points to a valid widget. (I.E. uninstalled or deleted)
             * then the DB table containing the data has been deleted anyway. So lets clean up
             * this entry. We only delete the row if the class doesn't exist in the system
             * anymore, just in case there is some recovery that could happen when the extension
             * is re-installed or re-activated.
             */
            else {
                $this->pivotTableModel->where('id', $layoutEntry->getId())->delete();
            }
        }

        return $formContents;
    }
}