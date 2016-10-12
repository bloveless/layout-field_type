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
}