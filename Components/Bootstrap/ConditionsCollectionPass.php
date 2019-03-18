<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Components\Bootstrap;

use Shopware\Components\DependencyInjection\Compiler\TagReplaceTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ConditionsCollectionPass
 */
class ConditionsCollectionPass implements CompilerPassInterface
{
    use TagReplaceTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->replaceArgumentWithTaggedServices(
            $container,
            'frosh_environment_notice.services.conditions',
            'frosh_environment_notice.condition',
            0
        );
    }
}
