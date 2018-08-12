<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Services;

use Enlight_Event_EventArgs;
use Enlight_Event_EventManager;
use Enlight_Event_Exception;
use Enlight_Template_Manager;
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
     * @var Enlight_Template_Manager
     */
    private $templateManager;

    /**
     * @var Enlight_View_Default|null
     */
    private $view;

    /**
     * NoticeMarkupBuilder constructor.
     *
     * @param LessCompiler               $lessCompiler
     * @param string                     $viewDirectory
     * @param Enlight_Event_EventManager $eventManager
     * @param Enlight_Template_Manager   $templateManager
     */
    public function __construct(
        LessCompiler $lessCompiler,
        string $viewDirectory,
        Enlight_Event_EventManager $eventManager,
        Enlight_Template_Manager $templateManager
    ) {
        $this->lessCompiler = $lessCompiler;
        $this->viewDirectory = $viewDirectory;
        $this->eventManager = $eventManager;
        $this->templateManager = $templateManager;
    }

    /**
     * @return Enlight_View_Default|null
     */
    public function getView(): Enlight_View_Default
    {
        if (is_null($this->view)) {
            return $this
                ->setView(new Enlight_View_Default($this->templateManager))
                ->getView()
            ;
        }

        return $this->view;
    }

    /**
     * @param Enlight_View_Default|null $view
     *
     * @return NoticeMarkupBuilder
     */
    public function setView(Enlight_View_Default $view): NoticeMarkupBuilder
    {
        $this->view = $view;

        return $this;
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
     * @throws ViewPreparationException
     */
    public function prepareView()
    {
        static $isAlreadyPrepared = false;

        if ($isAlreadyPrepared) {
            return;
        }

        $this->getView()->addTemplateDir($this->viewDirectory);

        try {
            $args = new Enlight_Event_EventArgs([
                'subject' => $this,
                'view' => $this->getView(),
            ]);
            $this->eventManager->notify('Frosh_EnvironmentNotice_NoticeMarkupBuilder_prepareView', $args);
        } catch (Enlight_Event_Exception $e) {
            throw new ViewPreparationException($e);
        }

        $isAlreadyPrepared = true;
    }

    /**
     * @param Notice[] $notices
     *
     * @throws ViewPreparationException
     *
     * @return string
     */
    public function buildInjectableHtml(array $notices): string
    {
        $this->prepareView();

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
            $args = new Enlight_Event_EventArgs();
            $args->setReturn($viewData);
            $args = $this->eventManager->filter('Frosh_EnvironmentNotice_NoticeMarkupBuilder_prepareViewData', $args);
            $viewData = $args->getReturn();
        } catch (Enlight_Event_Exception $exception) {
            throw new ViewPreparationException($exception);
        }

        $this->getView()->assign($viewData);

        return $this->getView()->fetch('environment_notice/notice/index.tpl');
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
