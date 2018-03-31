<?php declare(strict_types=1);

namespace FroshEnvironmentNotice;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use FroshEnvironmentNotice\Models\Notice;
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
    private $models = [
        Notice::class,
    ];

    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        parent::install($context);
        $this->installSchema();
    }

    /**
     * @param UpdateContext $context
     */
    public function update(UpdateContext $context)
    {
        parent::update($context);
        $this->installSchema();
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

    /**
     * @return ClassMetadata[]
     */
    private function getModelMetadata(): array
    {
        return array_map([$this->getModelManager(), 'getClassMetadata'], $this->models);
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
