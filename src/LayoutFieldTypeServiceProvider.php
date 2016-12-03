<?php namespace Fritzandandre\LayoutFieldType;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;

/**
 * Class LayoutFieldTypeServiceProvider
 *
 * @link          http://fritzandandre.com
 * @author        Brennon Loveless <brennon@fritzandandre.com>
 * @package       Fritzandandre\LayoutFieldType
 */
class LayoutFieldTypeServiceProvider extends AddonServiceProvider
{
    protected $routes = [
        'admin/layout-field_type/widgets' => 'Fritzandandre\LayoutFieldType\Http\Admin\Controller\AjaxController@widgets',
        'admin/layout-field_type/form'    => 'Fritzandandre\LayoutFieldType\Http\Admin\Controller\AjaxController@form'
    ];
}
