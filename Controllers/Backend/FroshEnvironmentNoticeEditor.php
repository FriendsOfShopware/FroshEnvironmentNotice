<?php declare(strict_types=1);

use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Backend_FroshEnvironmentNoticeEditor extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /**
     * {@inheritdoc}
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
        ];
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->View()->addTemplateDir($this->container->getParameter('frosh_environment_notice.view_dir'));
    }

    public function indexAction()
    {
    }
}
