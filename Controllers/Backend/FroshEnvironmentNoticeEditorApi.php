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
            'ajaxInsert',
        ];
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $this->Request()->replacePost(json_decode(file_get_contents('php://input'), true));
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

        $resultData = Zend_Json::encode($data);
        if ($pretty) {
            $resultData = Zend_Json::prettyPrint($resultData);
        }

        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setHttpResponseCode((int)$data['code']);
        $this->Response()->setBody($resultData);
    }

    public function ajaxListAction()
    {
        $this->View()->assign([
            'success' => true,
            'code' => 200,
            'items' => $this->noticeRepository->findAll(),
        ]);
    }

    public function ajaxInsertAction()
    {
        $data = $this->Request()->getPost();
        unset($data['id']);
        $model = new Notice();
        $model->fromArray($data);

        $this->getModelManager()->persist($model);
        try {
            $this->getModelManager()->flush($model);
            $this->View()->assign([
                'success' => true,
                'data' => $model,
                'code' => 201,
            ]);
        } catch (\Doctrine\ORM\OptimisticLockException $e) {
            $this->View()->assign([
                'success' => false,
                'data' => [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ],
                'code' => 503,
            ]);
        }
    }
}
