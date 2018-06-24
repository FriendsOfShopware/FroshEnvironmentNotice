<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Interfaces;

/**
 * Interface NoticeInjectActionInterface
 */
interface NoticeInjectActionInterface
{
    /**
     * @param array $configuration
     * @param mixed $response
     * @return mixed
     */
    public function injectNotice(array $configuration, $response);
}
