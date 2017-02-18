<?php namespace Fritzandandre\LayoutFieldType\Widget\Console\Command;

use Anomaly\Streams\Platform\Support\Parser;
use Illuminate\Filesystem\Filesystem;

/**
 * Class UpdateAddonServiceProvider
 *
 * @link    http://fritzandandre.com
 * @author  Brennon Loveless <brennon@fritzandandre.com>
 * @package Fritzandandre\LayoutFieldType\Widget\Console\Command
 */
class UpdateAddonServiceProvider
{
    protected $addon;
    protected $addonType;
    protected $namespace;
    protected $vendor;
    protected $widgetName;

    /**
     * UpdateAddonComposer constructor.
     *
     * @param $addon
     * @param $widgetName
     */
    public function __construct($addon, $addonType, $namespace, $vendor, $widgetName)
    {
        $this->addon      = $addon;
        $this->addonType  = $addonType;
        $this->namespace  = $namespace;
        $this->vendor     = $vendor;
        $this->widgetName = $widgetName;
    }

    /**
     *
     * @param Filesystem $filesystem
     */
    public function handle(Parser $parser, Filesystem $filesystem)
    {
        $ucAddonSlug = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->addon->getSlug())));

        $serviceProviderPath    = $this->addon->getPath('src/' . $ucAddonSlug . ucwords($this->addonType) . 'ServiceProvider.php');
        $serviceProviderContent = file_get_contents($serviceProviderPath);

        $registerFunctionPos = strpos($serviceProviderContent, 'public function register');

        /**
         * Then there are two cases we need to handle
         * First, if the register function already exists
         * add the register widget command as the first
         * line in the service provider.
         */
        if ($registerFunctionPos) {
            /**
             * Find the position that we will be inserting the
             * addon registration code.
             */
            $registerOpenBracePos = strpos($serviceProviderContent, '{', $registerFunctionPos);
            $registerNewLinePos   = strpos($serviceProviderContent, PHP_EOL, $registerOpenBracePos);

            $template = $filesystem->get(
                __DIR__ . '/../../../../resources/stubs/WidgetServiceProviderUpdate.stub'
            );

            /**
             * Generate the parsed template with the correct
             * path and namespace.
             */
            $parsedTemplate = $parser->parse($template, [
                'path'      => "__DIR__ . '/../" . 'addons/' . $this->vendor . '/' . $this->widgetName . '_widget-extension' . "/'",
                'namespace' => $this->vendor . '.extension.' . $this->widgetName . '_widget',
            ]);

            /**
             * Insert our code after the newline that follows the opening brace.
             */
            $newServiceProvider = substr_replace($serviceProviderContent, $parsedTemplate, $registerNewLinePos + 1, 0);

            /**
             * Last, write out the new service provider.
             */
            $filesystem->put($serviceProviderPath, $newServiceProvider);
        }

        /**
         * Second, the register method doesn't exit
         * We will just add our own register method.
         */
        if (!$registerFunctionPos) {
            /**
             * Find the position that we will be inserting the
             * addon registration code. In this case it is the
             * very last closing curly brace.
             */
            $classClosingBracePos = strrpos($serviceProviderContent, '}', $registerFunctionPos);

            $template = $filesystem->get(
                __DIR__ . '/../../../../resources/stubs/WidgetServiceProviderRegisterFunction.stub'
            );

            /**
             * Generate the parsed template with the correct
             * path and namespace.
             */
            $parsedTemplate = $parser->parse($template, [
                'path'      => "__DIR__ . '/../" . 'addons/' . $this->vendor . '/' . $this->widgetName . '_widget-extension' . "/'",
                'namespace' => $this->vendor . '.extension.' . $this->widgetName . '_widget',
            ]);

            /**
             * Insert our code before the closing brace on the class.
             */
            $newServiceProvider = substr_replace($serviceProviderContent, $parsedTemplate, $classClosingBracePos, 0);

            /**
             * Last, write out the new service provider.
             */
            $filesystem->put($serviceProviderPath, $newServiceProvider);
        }
    }
}