<?php namespace Fritzandandre\LayoutFieldType\Widget\Console\Command;

use Illuminate\Filesystem\Filesystem;

/**
 * Class UpdateAddonComposer
 *
 * @link    http://fritzandandre.com
 * @author  Brennon Loveless <brennon@fritzandandre.com>
 * @package Fritzandandre\LayoutFieldType\Widget\Console\Command
 */
class UpdateAddonComposer
{
    protected $addon;
    protected $vendor;
    protected $widgetName;

    /**
     * UpdateAddonComposer constructor.
     *
     * @param $addon
     * @param $widgetName
     */
    public function __construct($addon, $vendor, $widgetName)
    {
        $this->addon      = $addon;
        $this->vendor     = $vendor;
        $this->widgetName = $widgetName;
    }

    /**
     * Update the composer.json file for the addon we are adding
     * the widget to, so that it will register the new src
     * directory.
     *
     * @param Filesystem $filesystem
     */
    public function handle(Filesystem $filesystem)
    {
        /**
         * Generate the namespace and src directories.
         */
        $ucVendor     = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->vendor)));
        $ucWidgetName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->widgetName)));

        $widgetNamespace = $ucVendor . '\\' . $ucWidgetName . 'WidgetExtension\\';
        $srcDir          = "addons/{$this->vendor}/{$this->widgetName}_widget-extension/src/";

        /**
         * Read in the composer.json file.
         */
        $composerFilePath = $this->addon->getPath('composer.json');
        $composerFile = json_decode(file_get_contents($composerFilePath));

        /**
         * Add our new entry to the json file.
         */
        $composerFile->autoload->{'psr-4'}->{$widgetNamespace} = $srcDir;

        /**
         * Encode it all pretty like.
         */
        $jsonSrc = json_encode($composerFile, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        /**
         * Replace four spaces with two (so it retains its original format)
         * and write it back to the filesystem.
         */
        $filesystem->put($composerFilePath, str_replace("    ", "  ", $jsonSrc));
    }
}