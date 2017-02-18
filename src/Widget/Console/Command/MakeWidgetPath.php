<?php namespace Fritzandandre\LayoutFieldType\Widget\Console\Command;

use Anomaly\Streams\Platform\Addon\AddonCollection;

/**
 * Class MakeWidgetPath
 *
 * @link    http://fritzandandre.com
 * @author  Brennon Loveless <brennon@fritzandandre.com>
 * @package Fritzandandre\LayoutFieldType\Widget\Console\Command
 */
class MakeWidgetPath
{
    protected $namespace;
    protected $vendor;
    protected $widgetName;

    /**
     * MakeWidgetPath constructor.
     *
     * @param $namespace
     * @param $vendor
     * @param $widgetName
     */
    public function __construct($namespace, $vendor, $widgetName)
    {
        $this->namespace  = $namespace;
        $this->vendor     = $vendor;
        $this->widgetName = $widgetName;
    }

    /**
     * Get the path to the new widget.
     *
     * @param AddonCollection $addons
     * @return string
     */
    public function handle(AddonCollection $addons)
    {
        return $addons->get($this->namespace)->getPath('addons/'. $this->vendor . '/' . $this->widgetName . '_widget-extension');
    }
}