<?php declare(strict_types=1);

use FroshEnvironmentNotice\Models\Notice;
use Shopware\Components\CSRFWhitelistAware;
use Shopware\Components\Model\ModelRepository;

class Shopware_Controllers_Backend_FroshEnvironmentNoticeEditorApi extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /**
     * @var ModelRepository
     */
    private $noticeRepository;

    /**
     * {@inheritdoc}
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'ajaxList',
        ];
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $this->noticeRepository = $this->getModelManager()->getRepository(Notice::class);
    }

    public function postDispatch()
    {
        $data = $this->View()->getAssign();
        $pretty = $this->Request()->getParam('pretty', false);

        array_walk_recursive($data, function (&$value) {
            // Convert DateTime instances to ISO-8601 Strings
            if ($value instanceof DateTime) {
                $value = $value->format(DateTime::ISO8601);
            }
        });

        $data = Zend_Json::encode($data);
        if ($pretty) {
            $data = Zend_Json::prettyPrint($data);
        }

        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody($data);
    }

    public function ajaxListAction()
    {
        $this->View()->assign([
            'success' => true,
            'items' => $this->noticeRepository->findAll(),
        ]);
    }
}
