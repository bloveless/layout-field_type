<?php namespace Fritzandandre\LayoutFieldType\Widget\Command;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\AddonIntegrator;

/**
 * Class RegisterWidgetAddon
 *
 * @link    http://fritzandandre.com
 * @author  Brennon Loveless <brennon@fritzandandre.com>
 * @package Fritzandandre\LayoutFieldType\Widget\Command
 */
class RegisterWidgetAddon
{
    protected $path;
    protected $namespace;

    /**
     * RegisterWidgetAddon constructor.
     *
     * @param $path
     * @param $namespace
     */
    public function __construct($path, $namespace)
    {
        $this->path      = $path;
        $this->namespace = $namespace;
    }

    /**
     * Register the widget with the addon system.
     *
     * @param AddonCollection $addons
     * @param AddonIntegrator $integrator
     */
    public function handle(AddonCollection $addons, AddonIntegrator $integrator)
    {
        if (interface_exists("Fritzandandre\\LayoutFieldType\\Contract\\LayoutExtensionInterface")) {

            $addon = $integrator->register(
                $this->path,
                $this->namespace,
                true,
                true
            );

            $addons->push($addon);
        }
    }
}