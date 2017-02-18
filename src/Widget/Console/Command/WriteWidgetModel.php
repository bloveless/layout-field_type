<?php namespace Fritzandandre\LayoutFieldType\Widget\Console\Command;

use Anomaly\Streams\Platform\Support\Parser;
use Illuminate\Filesystem\Filesystem;

/**
 * Class WriteWidgetModel
 *
 * @link    http://fritzandandre.com
 * @author  Brennon Loveless <brennon@fritzandandre.com>
 * @package Fritzandandre\LayoutFieldType\Widget\Console\Command
 */
class WriteWidgetModel
{
    protected $path;
    protected $vendor;
    protected $addonSlug;
    protected $widgetName;

    /**
     * WriteWidgetModel constructor.
     *
     * @param $path
     * @param $vendor
     * @param $addonSlug
     * @param $widgetName
     */
    public function __construct($path, $vendor, $addonSlug, $widgetName)
    {
        $this->path       = $path;
        $this->vendor     = $vendor;
        $this->addonSlug  = $addonSlug;
        $this->widgetName = $widgetName;
    }

    /**
     * Create and a widget model for the requested widget.
     *
     * @param Parser     $parser
     * @param Filesystem $filesystem
     */
    public function handle(Parser $parser, Filesystem $filesystem)
    {
        $ucVendor     = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->vendor)));
        $ucWidgetName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->widgetName)));
        $ucAddonSlug  = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->addonSlug)));

        $path = "{$this->path}/src/{$ucWidgetName}Widget/{$ucWidgetName}WidgetModel.php";

        $template = $filesystem->get(
            __DIR__ . '/../../../../resources/stubs/Widget/WidgetModel.stub'
        );

        $filesystem->makeDirectory(dirname($path), 0755, true, true);

        $filesystem->put($path, $parser->parse($template, [
            'vendor'         => $this->vendor,
            'uc_vendor'      => $ucVendor,
            'uc_addon_slug'  => $ucAddonSlug,
            'widget_name'    => $this->widgetName,
            'uc_widget_name' => $ucWidgetName,
        ]));
    }
}