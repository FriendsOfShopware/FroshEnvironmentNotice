<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Components\Bootstrap;

use Shopware\Components\DependencyInjection\Compiler\TagReplaceTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class NoticeInjectActionCollectionPass
 */
class NoticeInjectActionCollectionPass implements CompilerPassInterface
{
    use TagReplaceTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->replaceArgumentWithTaggedServices(
            $container,
            'frosh_environment_notice.services.notice_inject_actions',
            'frosh_environment_notice.notice_inject_action',
            0
        );
    }
}
