<?php namespace Fritzandandre\LayoutFieldType\Http\Admin\Controller;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Extension\ExtensionCollection;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Fritzandandre\LayoutFieldType\Command\SetFormOptions;

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
        $type      = $this->request->get('type');
        $extension = $addons->get($type);

        /** @var FormBuilder $form */
        $form       = $extension->getForm();
        $instanceId = $this->request->get('instance_id');
        $fieldSlug  = $this->request->get('field_slug');
        $sortOrder  = $this->request->get('sort_order');

        $this->dispatch(new SetFormOptions($form, $extension, $fieldSlug, $instanceId));

        /**
         * Add the extension name to the form data so
         * it can be displayed in the ajax form.
         */
        $form->addFormData('extension_name', trans($extension->getName()));
        $form->setOption('wrapper_view', 'fritzandandre.field_type.layout::ajax_form');
        $form->setOption('sort_order', $sortOrder);

        return $form->render();
    }
}