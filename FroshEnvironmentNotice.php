<?php declare(strict_types=1);

namespace FroshEnvironmentNotice;

use Enlight_Controller_Plugins_ViewRenderer_Bootstrap;
use Enlight_Event_EventArgs;
use Shopware\Components\Plugin;

class FroshEnvironmentNotice extends Plugin
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Plugins_ViewRenderer_FilterRender' => 'injectEnvironmentNotice',
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function injectEnvironmentNotice(Enlight_Event_EventArgs $args)
    {
        /** @var Enlight_Controller_Plugins_ViewRenderer_Bootstrap $bootstrap */
        $bootstrap = $args->get('subject');
        $moduleName = $bootstrap->Front()->Request()->getModuleName();
        if ($moduleName === 'widget') {
            return;
        }

        $args->setReturn(str_replace('</body>', '<div style="position: fixed;top: 10px;right: 10px;border-radius: 5px;padding: 15px;color: white;background: red;font-weight: bold;z-index: 9999999999;font-size: 1.2em;pointer-events: none;">Das ist eine Stagingumgebung</div></body>', $args->getReturn()));
    }
}
