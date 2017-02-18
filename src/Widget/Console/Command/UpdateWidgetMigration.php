<?php namespace Fritzandandre\LayoutFieldType\Widget\Console\Command;

use Anomaly\Streams\Platform\Support\Parser;
use Illuminate\Filesystem\Filesystem;

/**
 * Class UpdateWidgetMigration
 *
 * @link    http://fritzandandre.com
 * @author  Brennon Loveless <brennon@fritzandandre.com>
 * @package Fritzandandre\LayoutFieldType\Widget\Console\Command
 */
class UpdateWidgetMigration
{
    protected $addon;
    protected $widgetName;

    /**
     * UpdateWidgetMigration constructor.
     *
     * @param $addon
     * @param $widgetName
     */
    public function __construct($addon, $widgetName)
    {
        $this->addon      = $addon;
        $this->widgetName = $widgetName;
    }

    /**
     * Update the migration that was most recently created to
     * have a custom namespace and initial fields/assignments
     * for a simple widget.
     *
     * @param Parser     $parser
     * @param Filesystem $filesystem
     */
    public function handle(Parser $parser, Filesystem $filesystem)
    {
        /**
         * First we find the migration file that was most recently created
         * since we just created the migration file.
         */
        $migrationDir = $this->addon->getPath('migrations');

        $mostRecentFile = null;
        foreach (scandir($migrationDir, SCANDIR_SORT_DESCENDING) as $file) {
            if ($file != '.' && $file != '..') {
                $mostRecentFile = $migrationDir . "/{$file}";
                break;
            }
        }

        $migrationFile = file_get_contents($mostRecentFile);

        /**
         * Strip off the header as that is the only part we need.
         */
        $migrationHeader = substr($migrationFile, 0, strpos($migrationFile, '{'));

        $template = $filesystem->get(
            __DIR__ . '/../../../../resources/stubs/WidgetMigration.stub'
        );

        $parsedFile = $parser->parse($template, [
            'addon_slug'  => $this->addon->getSlug(),
            'widget_name' => $this->widgetName,
        ]);

        /**
         * Then merge the header and the parsed stub together to form
         * the final migration file.
         */
        $filesystem->put($mostRecentFile, $migrationHeader . $parsedFile);
    }
}