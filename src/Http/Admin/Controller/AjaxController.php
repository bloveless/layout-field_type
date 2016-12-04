<?php namespace Fritzandandre\LayoutFieldType\Http\Admin\Controller;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Extension\ExtensionCollection;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Fritzandandre\LayoutFieldType\Command\PrepareFormForLayout;

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
            'field_slug' => $this->request->get('field_slug'),
            'widgets'    => $extensions->search('fritzandandre.field_type.layout::widget.*')->installed(),
        ]);
    }

    /**
     * Get the HTML for the form.
     *
     * @param AddonCollection $addons
     * @return mixed
     */
    public function form(AddonCollection $addons)
    {
        $type       = $this->request->get('type');
        $addon      = $addons->get($type);
        $form       = $addon->getForm();
        $instanceId = $this->request->get('instance_id');
        $fieldSlug  = $this->request->get('field_slug');

        $this->dispatch(new PrepareFormForLayout($addon, $form, $fieldSlug, $instanceId));

        return $form->render();
    }
}