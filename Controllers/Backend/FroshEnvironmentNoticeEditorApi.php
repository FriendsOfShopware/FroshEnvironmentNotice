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
            'ajaxMessagesGet',
            'ajaxMessagesList',
            'ajaxMessagesInsert',
            'ajaxMessagesUpdate',
            'ajaxMessagesDelete',
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
        $this->Response()->setHttpResponseCode((int) $data['code']);
        $this->Response()->setBody($resultData);
    }

    public function ajaxMessagesListAction()
    {
        $this->setResponseData(200, $this->noticeRepository->findAll(), 'items');
    }

    public function ajaxMessagesGetAction()
    {
        if (($model = $this->findNoticeOrFailResponse((int) $this->Request()->get('id'))) === false) {
            return;
        }

        $this->setResponseData(200, $model);
    }

    public function ajaxMessagesInsertAction()
    {
        $data = $this->Request()->getPost();
        unset($data['id']);
        $model = new Notice();
        $model->fromArray($data);

        $this->getModelManager()->persist($model);
        try {
            $this->getModelManager()->flush($model);
            $this->setResponseData(201, $model);
        } catch (\Doctrine\ORM\OptimisticLockException $e) {
            $this->setResponseException($e);
        }
    }

    public function ajaxMessagesUpdateAction()
    {
        if (($model = $this->findNoticeOrFailResponse($this->Request()->getPost('id'))) === false) {
            return;
        }

        $data = $this->Request()->getPost();
        unset($data['id']);
        $model->fromArray($data);

        $this->getModelManager()->persist($model);
        try {
            $this->getModelManager()->flush($model);
            $this->setResponseData(200, $model);
        } catch (\Doctrine\ORM\OptimisticLockException $e) {
            $this->setResponseException($e);
        }
    }

    public function ajaxMessagesDeleteAction()
    {
        if (($model = $this->findNoticeOrFailResponse($this->Request()->getPost('id'))) === false) {
            return;
        }

        try {
            $this->getModelManager()->remove($model);
            $this->getModelManager()->flush($model);
            $this->setResponseData(200, []);
        } catch (Exception $e) {
            $this->setResponseException($e);
        }
    }

    /**
     * @param int $id
     *
     * @return Notice|false
     */
    protected function findNoticeOrFailResponse(int $id)
    {
        try {
            /** @var Notice $model */
            $model = $this->noticeRepository->find($id);
            if (is_null($model)) {
                throw new InvalidArgumentException('$model is null');
            }

            return $model;
        } catch (Exception $exception) {
            $this->setResponseException($exception, 404);

            return false;
        }
    }

    /**
     * @param int          $statusCode
     * @param array|object $data
     * @param string       $dataKey
     */
    protected function setResponseData(int $statusCode, $data, string $dataKey = 'data')
    {
        $this->View()->assign([
            'success' => $statusCode >= 200 && $statusCode < 400,
            'code' => $statusCode,
            $dataKey => $data,
        ]);
    }

    /**
     * @param Throwable $exception
     * @param int       $statusCode
     */
    protected function setResponseException(Throwable $exception, int $statusCode = 503)
    {
        $this->setResponseData($statusCode, [
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
