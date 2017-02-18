<?php namespace Fritzandandre\LayoutFieldType\Widget\Console\Command;

use Illuminate\Filesystem\Filesystem;

/**
 * Class WriteWidgetRenderView
 *
 * @link    http://fritzandandre.com
 * @author  Brennon Loveless <brennon@fritzandandre.com>
 * @package Fritzandandre\LayoutFieldType\Widget\Console\Command
 */
class WriteWidgetRenderView
{
    private $path;

    /**
     * WriteWidgetLang constructor.
     *
     * @param $path
     */
    public function __construct($path)
    {
        $this->path       = $path;
    }

    /**
     * Handle the command.
     *
     * @param Filesystem $filesystem
     */
    public function handle(Filesystem $filesystem)
    {
        $path = "{$this->path}/resources/views/render.twig";

        $template = $filesystem->get(
            __DIR__ . '/../../../../resources/stubs/resources/views/render.stub'
        );

        $filesystem->makeDirectory(dirname($path), 0755, true, true);

        $filesystem->put($path, $template);
    }
}
