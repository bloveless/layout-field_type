<?php namespace Fritzandandre\LayoutFieldType\Http\Controller;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Extension\ExtensionCollection;
use Anomaly\Streams\Platform\Http\Controller\AdminController;

/**
 * Class AjaxController
 *
 * @link          http://fritzandandre.com
 * @author        Brennon Loveless <brennon@fritzandandre.com>
 * @package       Fritzandandre\LayoutFieldType\Http\Controller\Admin
 */
class AjaxController extends AdminController
{
    /**
     * @param ExtensionCollection $extensions
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function widgets(ExtensionCollection $extensions)
    {
        return view('fritzandandre.field_type.layout::choose_widget', [
            'slug'    => $this->request->get('slug'),
            'widgets' => $extensions->search('fritzandandre.field_type.layout::widget.*')->installed()
        ]);
    }

    public function form(AddonCollection $addons)
    {
        $type  = $this->request->get('type');
        $addon = $addons->get($type);
        $form  = $addon->getForm();
        $form->setOption('wrapper_view', 'fritzandandre.field_type.layout::wrapper')
             ->setOption('form_view', 'fritzandandre.field_type.layout::form')
             ->setOption('prefix', 'test_prefix_');

        return $form->render();
    }
}