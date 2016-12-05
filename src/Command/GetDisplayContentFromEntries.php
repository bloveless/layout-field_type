<?php namespace Fritzandandre\LayoutFieldType\Command;

class GetDisplayContentFromEntries
{
    protected $entries;

    /**
     * GetDisplayContentFromEntries constructor.
     *
     * @param $entries
     */
    public function __construct($entries)
    {
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
             * Create each widget and get the HTML to display it.
             */
            $extension = app($entry->widget_type);
            $extension->setEntryId($entry->widget_id);
            $displayContent .= $extension->render();
        }

        return $displayContent;
    }
}