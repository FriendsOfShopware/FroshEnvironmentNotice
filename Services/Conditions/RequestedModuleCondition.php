<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Services\Conditions;

use Enlight_Controller_Front;
use FroshEnvironmentNotice\Interfaces\ConditionInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RequestedModuleCondition
 */
class RequestedModuleCondition implements ConditionInterface
{
    const MODULE_NAME = 'module';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * RequestedModuleCondition constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return bool
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function isMet(array $configuration): bool
    {
        if (!array_key_exists(self::MODULE_NAME, $configuration)) {
            return false;
        }

        /** @var Enlight_Controller_Front|null $front */
        /** @noinspection PhpUnhandledExceptionInspection */
        $front = $this->container->get('front', Container::NULL_ON_INVALID_REFERENCE);

        if (is_null($front)) {
            return false;
        }

        return strcasecmp($front->Request()->getModuleName(), $configuration[self::MODULE_NAME]) === 0;
    }
}
