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
     * @param array  $styleRules
     * @param string $message
     *
     * @return string
     */
    public function buildNoticeInSlot(string $message, Slot $slot): string
    {
        $id = preg_replace('/([^a-zA-Z0-9]|^[0-9]*)/i', '', $message . $slot->getName());
        $message = nl2br($message);

        return "<style>{$this->buildSlots($slot, $id)}</style><div id=\"{$id}\">{$message}</div>";
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

    /**
     * @param string $property
     * @param string $value
     *
     * @return string
     */
    public function buildStyleSetter(string $property, string $value): string
    {
        return "{$property}: {$value}";
    }
}
