<?php namespace Fritzandandre\LayoutFieldType\Widget\Console\Command;

use Anomaly\Streams\Platform\Support\Parser;
use Illuminate\Filesystem\Filesystem;

/**
 * Class WriteAddonLang
 *
 * @link    http://fritzandandre.com
 * @author  Brennon Loveless <brennon@fritzandandre.com>
 * @package Fritzandandre\LayoutFieldType\Widget\Console\Command
 */
class WriteWidgetLang
{
    private $path;
    private $widgetName;

    /**
     * WriteWidgetLang constructor.
     *
     * @param $path
     * @param $widgetName
     */
    public function __construct($path, $widgetName)
    {
        $this->path       = $path;
        $this->widgetName = $widgetName;
    }

    /**
     * Handle the command.
     *
     * @param Parser     $parser
     * @param Filesystem $filesystem
     */
    public function handle(Parser $parser, Filesystem $filesystem)
    {
        $path = "{$this->path}/resources/lang/en/addon.php";

        $title = ucwords(str_replace('_', ' ', $this->widgetName));
        $type  = 'Extension';

        $template = $filesystem->get(
            __DIR__ . '/../../../../resources/stubs/resources/lang/en/addon.stub'
        );

        $filesystem->makeDirectory(dirname($path), 0755, true, true);

        $filesystem->put($path, $parser->parse($template, compact('title', 'type')));
    }
}
