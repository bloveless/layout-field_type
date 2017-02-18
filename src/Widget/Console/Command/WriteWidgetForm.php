<?php namespace Fritzandandre\LayoutFieldType\Widget\Console\Command;

use Anomaly\Streams\Platform\Support\Parser;
use Illuminate\Filesystem\Filesystem;

/**
 * Class WriteWidgetForm
 *
 * @link    http://fritzandandre.com
 * @author  Brennon Loveless <brennon@fritzandandre.com>
 * @package Fritzandandre\LayoutFieldType\Widget\Console\Command
 */
class WriteWidgetForm
{
    protected $path;
    protected $vendor;
    protected $widgetName;

    /**
     * WriteWidgetForm constructor.
     *
     * @param $path
     * @param $vendor
     * @param $widgetName
     */
    public function __construct($path, $vendor, $widgetName)
    {
        $this->path       = $path;
        $this->vendor     = $vendor;
        $this->widgetName = $widgetName;
    }

    /**
     * Create and a widget form for the requested widget.
     *
     * @param Parser     $parser
     * @param Filesystem $filesystem
     */
    public function handle(Parser $parser, Filesystem $filesystem)
    {
        $ucVendor     = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->vendor)));
        $ucWidgetName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->widgetName)));


        $path = "{$this->path}/src/{$ucWidgetName}Widget/Form/{$ucWidgetName}WidgetFormBuilder.php";

        $template = $filesystem->get(
            __DIR__ . '/../../../../resources/stubs/Widget/Form/WidgetForm.stub'
        );

        $filesystem->makeDirectory(dirname($path), 0755, true, true);

        $filesystem->put($path, $parser->parse($template, [
            'vendor'         => $this->vendor,
            'uc_vendor'      => $ucVendor,
            'widget_name'    => $this->widgetName,
            'uc_widget_name' => $ucWidgetName,
        ]));
    }
}