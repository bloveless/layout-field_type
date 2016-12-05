<?php namespace Fritzandandre\LayoutFieldType\Command;

use Illuminate\Foundation\Bus\DispatchesJobs;

class DeleteLayoutRows
{
    use DispatchesJobs;

    protected $pivotTable;
    protected $inputValue;

    public function __construct($pivotTable, $inputValue)
    {
        $this->pivotTable = $pivotTable;
        $this->inputValue = $inputValue;
    }

    public function handle()
    {
        /**
         * Take care of any deletes
         */
        if ($deleteIds = array_get($this->inputValue, 'delete_ids')) {
            $deleteIds = explode(',', $deleteIds);
            foreach ($deleteIds as $deleteId) {
                $this->dispatch(new DeleteLayoutRowAndEntry($this->pivotTable, $deleteId));
            }
        }

        /**
         * Remove the delete id's so they aren't processed any further.
         */
        unset($this->inputValue['delete_ids']);

        return $this->inputValue;
    }
}