<?php declare(strict_types=1);

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use FroshEnvironmentNotice\Models\Notice;
use FroshEnvironmentNotice\Models\Slot;
use Shopware\Components\CSRFWhitelistAware;
use Shopware\Components\Model\ModelEntity;
use Shopware\Components\Model\ModelRepository;

class Shopware_Controllers_Backend_FroshEnvironmentNoticeEditorApi extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /**
     * @var ModelRepository
     */
    private $noticeRepository;

    /**
     * @var ModelRepository
     */
    private $slotRepository;

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
            'ajaxSlotsGet',
            'ajaxSlotsList',
            'ajaxSlotsInsert',
            'ajaxSlotsUpdate',
            'ajaxSlotsDelete',
        ];
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $this->Request()->replacePost(json_decode(file_get_contents('php://input'), true));
        $this->noticeRepository = $this->getModelManager()->getRepository(Notice::class);
        $this->slotRepository = $this->getModelManager()->getRepository(Slot::class);
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
        $this->listActionGeneric($this->noticeRepository);
    }

    public function ajaxMessagesGetAction()
    {
        $this->getActionGeneric($this->noticeRepository);
    }

    public function ajaxMessagesInsertAction()
    {
        $this->insertActionGeneric(Notice::class, [
            'slot' => Slot::class,
        ]);
    }

    public function ajaxMessagesUpdateAction()
    {
        $this->updateActionGeneric($this->noticeRepository, [
            'slot' => Slot::class,
        ]);
    }

    public function ajaxMessagesDeleteAction()
    {
        $this->deleteActionGeneric($this->noticeRepository);
    }

    public function ajaxSlotsListAction()
    {
        $this->listActionGeneric($this->slotRepository);
    }

    public function ajaxSlotsGetAction()
    {
        $this->getActionGeneric($this->slotRepository);
    }

    public function ajaxSlotsInsertAction()
    {
        $this->insertActionGeneric(Slot::class);
    }

    public function ajaxSlotsUpdateAction()
    {
        $this->updateActionGeneric($this->slotRepository);
    }

    public function ajaxSlotsDeleteAction()
    {
        $this->deleteActionGeneric($this->slotRepository);
    }

    /**
     * @param ModelRepository $repository
     * @param int             $relationDepth
     */
    protected function listActionGeneric(ModelRepository $repository, int $relationDepth = 3)
    {
        $this->setResponseData(200, $this->dehydrateDoctrineModel($repository->findAll(), $relationDepth), 'items');
    }

    /**
     * @param ModelRepository $repository
     */
    protected function getActionGeneric(ModelRepository $repository, int $relationDepth = 2)
    {
        if (($model = $this->findModelOrFailResponse($repository, (int) $this->Request()->get('id'))) === false) {
            return;
        }

        $this->setResponseData(200, $this->dehydrateDoctrineModel($model, $relationDepth));
    }

    /**
     * @param string $modelClass
     * @param array  $relations
     * @param int    $relationDepth
     */
    protected function insertActionGeneric(string $modelClass, array $relations = [], int $relationDepth = 2)
    {
        $data = $this->Request()->getPost();
        unset($data['id']);
        /** @var ModelEntity $model */
        $model = new $modelClass();
        $model->fromArray($this->hydrateRelatedEntitiesInArray($data, $relations));

        $this->getModelManager()->persist($model);
        try {
            $this->getModelManager()->flush($model);
            $this->setResponseData(201, $this->dehydrateDoctrineModel($model, $relationDepth));
        } catch (\Doctrine\ORM\OptimisticLockException $e) {
            $this->setResponseException($e);
        }
    }

    /**
     * @param ModelRepository $repository
     * @param array           $relations
     */
    protected function updateActionGeneric(ModelRepository $repository, array $relations = [], int $relationDepth = 2)
    {
        if (($model = $this->findModelOrFailResponse($repository, $this->Request()->getPost('id'))) === false) {
            return;
        }

        $data = $this->Request()->getPost();
        unset($data['id']);
        $model->fromArray($this->hydrateRelatedEntitiesInArray($data, $relations));

        $this->getModelManager()->persist($model);
        try {
            $this->getModelManager()->flush($model);
            $this->setResponseData(200, $this->dehydrateDoctrineModel($model, $relationDepth));
        } catch (\Doctrine\ORM\OptimisticLockException $e) {
            $this->setResponseException($e);
        }
    }

    /**
     * @param ModelRepository $repository
     */
    protected function deleteActionGeneric(ModelRepository $repository)
    {
        if (($model = $this->findModelOrFailResponse($repository, $this->Request()->getPost('id'))) === false) {
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
     * @return ModelEntity|false
     */
    protected function findModelOrFailResponse(ModelRepository $repository, int $id)
    {
        try {
            /** @var ModelEntity $model */
            $model = $repository->find($id);
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

    /**
     * @param array $data
     * @param array $relations
     *
     * @return array
     */
    protected function hydrateRelatedEntitiesInArray(array $data, array $relations)
    {
        foreach ($relations as $relationName => $relationModelClass) {
            try {
                $relationObject = $this->getModelManager()->find($relationModelClass, $data[$relationName]['id']);
            } catch (Exception $exception) {
                // TODO log it like it is hot
            }
            $data[$relationName] = $relationObject;
        }

        return $data;
    }

    /**
     * Modified variant of Doctrine\Common\Util\Debug::export
     *
     * @param $var
     * @param int $maxDepth
     *
     * @return array|mixed
     */
    protected function dehydrateDoctrineModel($var, int $maxDepth)
    {
        $return = null;
        $isObj = is_object($var);

        if ($var instanceof Collection) {
            $var = $var->toArray();
        }

        if ($maxDepth) {
            if (is_array($var)) {
                $return = [];
                foreach ($var as $k => $v) {
                    $return[$k] = $this->dehydrateDoctrineModel($v, $maxDepth - 1);
                }

                return $return;
            } elseif ($isObj) {
                $return = new \stdclass();

                if ($var instanceof \DateTime) {
                    $return->date = $var->format('c');
                    $return->timezone = $var->getTimeZone()->getName();
                } else {
                    /** @noinspection PhpParamsInspection */
                    $reflClass = ClassUtils::newReflectionObject($var);

                    if ($var instanceof \ArrayObject || $var instanceof \ArrayIterator) {
                        $return = $this->dehydrateDoctrineModel($var->getArrayCopy(), $maxDepth - 1);
                    }

                    foreach ($reflClass->getProperties() as $reflProperty) {
                        $name = $reflProperty->getName();
                        $reflProperty->setAccessible(true);
                        $return->$name = $this->dehydrateDoctrineModel($reflProperty->getValue($var), $maxDepth - 1);
                    }
                }

                return $return;
            }

            return $var;
        }
        if (is_object($var) || is_array($var)) {
            if (method_exists($var, 'getId')) {
                return $var->getId();
            }
        } else {
            return $var;
        }

        return null;
    }
}
