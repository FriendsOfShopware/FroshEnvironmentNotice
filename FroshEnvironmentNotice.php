<?php declare(strict_types=1);

namespace FroshEnvironmentNotice;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use FroshEnvironmentNotice\Models\Notice;
use FroshEnvironmentNotice\Models\Slot;
use Shopware\Components\Model\ModelEntity;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;

class FroshEnvironmentNotice extends Plugin
{
    /**
     * @var string[]
     */
    private $modelClasses = [
        Notice::class,
        Slot::class,
    ];

    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        parent::install($context);
        $this->installSchema();
        $this->seed();
    }

    /**
     * @param UpdateContext $context
     */
    public function update(UpdateContext $context)
    {
        parent::update($context);
        $this->installSchema();
        $this->seed();
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        parent::uninstall($context);
        $this->uninstallSchema();
    }

    private function installSchema()
    {
        (new SchemaTool($this->getModelManager()))->updateSchema($this->getModelMetadata(), true);
    }

    private function uninstallSchema()
    {
        (new SchemaTool($this->getModelManager()))->dropSchema($this->getModelMetadata());
    }

    private function seed()
    {
        $amountOfSlots = $this->getModelManager()->getRepository(Slot::class)->createQueryBuilder('slot')->getMaxResults();

        if (!$amountOfSlots) {
            $datas = json_decode(file_get_contents($this->container->getParameter('frosh_environment_notice.seeds.slots')), true);
            /** @var ModelEntity[] $models */
            $models = array_map(function ($data) {
                return (new Slot())->fromArray($data);
            }, $datas);
            array_walk($models, [$this->getModelManager(), 'persist']);
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->getModelManager()->flush($models);
        }
    }

    /**
     * @return ClassMetadata[]
     */
    private function getModelMetadata(): array
    {
        return array_map([$this->getModelManager(), 'getClassMetadata'], $this->modelClasses);
    }

    /**
     * @return ModelManager
     */
    private function getModelManager(): ModelManager
    {
        /* @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get('models');
    }
}
