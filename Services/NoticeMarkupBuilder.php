<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Services;

use FroshEnvironmentNotice\Models\Slot;
use Shopware\Components\Theme\LessCompiler;

/**
 * Class NoticeMarkupBuilder
 */
class NoticeMarkupBuilder
{
    /**
     * @var LessCompiler
     */
    private $lessCompiler;

    /**
     * NoticeMarkupBuilder constructor.
     *
     * @param LessCompiler $lessCompiler
     */
    public function __construct(LessCompiler $lessCompiler)
    {
        $this->lessCompiler = $lessCompiler;
    }

    /**
     * @param Slot   $slot
     * @param string $id
     *
     * @return string
     */
    public function buildSlots(Slot $slot, string $id)
    {
        $filename = tempnam('/tmp', 'NMB');

        file_put_contents($filename, <<<EOL
#{$id} {
    {$slot->getStyle()}
}
EOL
);

        $this->lessCompiler->reset();
        $this->lessCompiler->compile($filename, '/');

        return $this->lessCompiler->get();
    }
}
