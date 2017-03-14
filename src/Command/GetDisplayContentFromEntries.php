<?php namespace Fritzandandre\LayoutFieldType\Command;

class GetDisplayContentFromEntries
{
    protected $entries;

    protected $pivotTableModel;

    /**
     * GetDisplayContentFromEntries constructor.
     *
     * @param $pivotTableModel
     * @param $entries
     */
    public function __construct($pivotTableModel, $entries)
    {
        $this->pivotTableModel = $pivotTableModel;
        $this->entries = $entries;
    }

    /**
     * Iterate over each entry and get the content to be displayed on the front-end.
     *
     * @return string
     */
    public function handle()
    {
        $displayContent = "";

        foreach ($this->entries as $entry) {
            /**
             * If the widget exists. I.E. wasn't uninstalled or deleted.
             */
            if(class_exists($entry->widget_type)) {
                /**
                 * Create each widget and get the HTML to display it.
                 */
                $extension = app($entry->widget_type);

                /**
                 * We can only display the widget if it is not null
                 * and is currently installed.
                 */
                if ($extension && $extension->isInstalled()) {
                    $extension->setEntryId($entry->widget_id);
                    $displayContent .= $extension->render();
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
                $this->pivotTableModel->where('id', $entry->getId())->delete();
            }
        }

        return $displayContent;
    }
}