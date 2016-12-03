<?php namespace Fritzandandre\LayoutFieldType\Contract;

/**
 * Interface LayoutExtensionInterface
 *
 * @link          http://fritzandandre.com
 * @author        Brennon Loveless <brennon@fritzandandre.com>
 * @package       Fritzandandre\LayoutFieldType\Contract
 */
interface LayoutExtensionInterface
{
    /**
     * Get the form used to create and edit widgets.
     *
     * @return \Illuminate\Foundation\Application|mixed
     */
    public function getForm();

    /**
     * Set the entry id for rendering.
     *
     * @param $entryId
     */
    public function setEntryId($entryId);

    /**
     * Render the content.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render();
}