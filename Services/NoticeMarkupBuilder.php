<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Services;

use Enlight_Event_EventArgs;
use Enlight_Event_EventManager;
use Enlight_Event_Exception;
use Enlight_View_Default;
use FroshEnvironmentNotice\Exceptions\ViewPreparationException;
use FroshEnvironmentNotice\Models\Notice;
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
     * @var string
     */
    private $viewDirectory;

    /**
     * @var Enlight_Event_EventManager
     */
    private $eventManager;

    /**
     * NoticeMarkupBuilder constructor.
     *
     * @param LessCompiler               $lessCompiler
     * @param string                     $viewDirectory
     * @param Enlight_Event_EventManager $eventManager
     */
    public function __construct(LessCompiler $lessCompiler, string $viewDirectory, Enlight_Event_EventManager $eventManager)
    {
        $this->lessCompiler = $lessCompiler;
        $this->viewDirectory = $viewDirectory;
        $this->eventManager = $eventManager;
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
     * @param Enlight_View_Default $view
     *
     * @throws ViewPreparationException
     */
    public function prepareView(Enlight_View_Default $view)
    {
        $view->addTemplateDir($this->viewDirectory);

        try {
            $args = new Enlight_Event_EventArgs([
                'subject' => $this,
                'view' => $view,
            ]);
            $this->eventManager->notify('Frosh_EnvironmentNotice_NoticeMarkupBuilder_prepareView', $args);
        } catch (Enlight_Event_Exception $e) {
            throw new ViewPreparationException($e);
        }
    }

    /**
     * @param Enlight_View_Default $view
     * @param Notice[]             $notices
     *
     * @throws ViewPreparationException
     *
     * @return string
     */
    public function buildInjectableHtml(Enlight_View_Default $view, array $notices): string
    {
        $slots = [];

        foreach ($notices as $notice) {
            if (!array_key_exists($notice->getSlot()->getId(), $slots)) {
                $slots[$notice->getSlot()->getId()] = $this->serializeSlot($notice->getSlot(), $notices);
            }
        }

        $viewData = [
            'environment_notice' => [
                'slots' => array_values($slots),
                'notices' => array_map([$this, 'serializeNotice'], $notices),
            ],
        ];

        try {
            $args = new Enlight_Event_EventArgs($viewData);
            $viewData = $this->eventManager->filter('Frosh_EnvironmentNotice_NoticeMarkupBuilder_prepareViewData', $args);
        } catch (Enlight_Event_Exception $exception) {
            throw new ViewPreparationException($exception);
        }

        $view->assign($viewData);

        return $view->fetch('environment_notice/notice/index.tpl');
    }

    /**
     * @param Slot     $slot
     * @param Notice[] $notices
     *
     * @return array
     */
    protected function serializeSlot(Slot $slot, array $notices): array
    {
        /** @var array $result */
        $result = $slot->jsonSerialize();

        $result['css'] = $this->buildSlots($slot, 'slot-' . $slot->getId());
        $result['notices'] = array_map([$this, 'serializeNotice'], array_values(array_filter($notices, function (Notice $notice) use ($slot) {
            return $notice->getSlot()->getId() === $slot->getId();
        })));

        return $result;
    }

    /**
     * @param Notice $notice
     *
     * @return array
     */
    protected function serializeNotice(Notice $notice): array
    {
        return $notice->jsonSerialize();
    }
}
