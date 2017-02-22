<?php namespace Fritzandandre\LayoutFieldType\Widget\Console;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Fritzandandre\LayoutFieldType\Widget\Console\Command\MakeWidgetPath;
use Fritzandandre\LayoutFieldType\Widget\Console\Command\UpdateAddonComposer;
use Fritzandandre\LayoutFieldType\Widget\Console\Command\UpdateAddonServiceProvider;
use Fritzandandre\LayoutFieldType\Widget\Console\Command\UpdateWidgetMigration;
use Fritzandandre\LayoutFieldType\Widget\Console\Command\WriteWidgetExtension;
use Fritzandandre\LayoutFieldType\Widget\Console\Command\WriteWidgetForm;
use Fritzandandre\LayoutFieldType\Widget\Console\Command\WriteWidgetLang;
use Fritzandandre\LayoutFieldType\Widget\Console\Command\WriteWidgetModel;
use Fritzandandre\LayoutFieldType\Widget\Console\Command\WriteWidgetRenderView;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class MakeWidget
 *
 * @link    http://fritzandandre.com
 * @author  Brennon Loveless <brennon@fritzandandre.com>
 * @package Fritzandandre\LayoutFieldType\Widget\Command
 */
class MakeWidget extends Command
{

    use DispatchesJobs;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:widget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new widget inside an existing addon.';

    /**
     * Execute the console command.
     *
     * @param AddonCollection $addons
     * @param Repository $config
     * @throws \Exception
     */
    public function fire(AddonCollection $addons, Repository $config)
    {
        $namespace = $this->argument('namespace');
        $widgetName = $this->argument('widget_name');

        if (!str_is('*.*.*', $namespace)) {
            throw new \Exception("The namespace should be snake case and formatted like: {vendor}.{type}.{slug}");
        }

        /**
         * Split the namespace into useable parts.
         */
        list($vendor, $type, $slug) = array_map(
            function ($value) {
                return str_slug(strtolower($value), '_');
            },
            explode('.', $namespace)
        );

        $type = str_singular($type);

        $addon = $addons->get($namespace);

        if(!$addon) {
            throw new \Exception("The addon namespace provided was not found.");
        }

        $path = $this->dispatch(new MakeWidgetPath($namespace, $vendor, $widgetName));

        /**
         * First write out all the new files necessary for a widget.
         */
        $this->dispatch(new WriteWidgetExtension($path, $vendor, $widgetName));
        $this->dispatch(new WriteWidgetModel($path, $vendor, $addon->getSlug(), $widgetName));
        $this->dispatch(new WriteWidgetForm($path, $vendor, $widgetName));

        /**
         * Then update the composer.json file for the new extension.
         */
        $this->dispatch(new UpdateAddonComposer($addon, $vendor, $widgetName));

        /**
         * Last update the service provider to register the new extension.
         */
        $this->dispatch(new UpdateAddonServiceProvider($addon, $type, $namespace, $vendor, $widgetName));

        /**
         * Then create a new migration for the widget stream.
         */
        $this->call(
            'make:migration',
            [
                'name'     => 'create_' . $widgetName . '_stream',
                '--addon'  => $namespace,
                '--fields' => true,
            ]
        );

        /**
         * There is a specific and very simple form for the
         * widget migration, so overwrite the generated
         * migration with the newer simpler migration.
         */
        $this->dispatch(new UpdateWidgetMigration($addon, $widgetName));

        /**
         * Then write out the lang file.
         */
        $this->dispatch(new WriteWidgetLang($path, $widgetName));

        /**
         * Last write out a basic render file.
         */
        $this->dispatch(new WriteWidgetRenderView($path));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['namespace', InputArgument::REQUIRED, 'The addon to add this widget to.'],
            ['widget_name', InputArgument::REQUIRED, 'The name of the widget.']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['shared', null, InputOption::VALUE_NONE, 'Indicates if the addon should be created in shared addons.'],
            ['migration', null, InputOption::VALUE_NONE, 'Indicates if a fields migration should be created.'],
        ];
    }
}
