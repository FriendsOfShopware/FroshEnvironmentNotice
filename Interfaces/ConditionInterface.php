<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Interfaces;

/**
 * Interface ConditionInterface
 */
interface ConditionInterface
{
    /**
     * @return bool
     */
    public function isMet(array $configuration): bool;
}
