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
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Theme\Compiler;
use Shopware\Components\Theme\LessCompiler;
use Shopware\Models\Shop\Repository;
use Shopware\Models\Shop\Shop;

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
     * @var Container
     */
    private $container;

    /**
     * @var Compiler
     */
    private $themeCompiler;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * NoticeMarkupBuilder constructor.
     *
     * @param LessCompiler               $lessCompiler
     * @param string                     $viewDirectory
     * @param Enlight_Event_EventManager $eventManager
     * @param Enlight_Template_Manager   $templateManager
     * @param Compiler                   $themeCompiler
     * @param Container                  $container
     * @param ModelManager               $modelManager
     */
    public function __construct(
        LessCompiler $lessCompiler,
        string $viewDirectory,
        Enlight_Event_EventManager $eventManager,
        Enlight_Template_Manager $templateManager,
        Compiler $themeCompiler,
        Container $container,
        ModelManager $modelManager
    ) {
        $this->lessCompiler = $lessCompiler;
        $this->viewDirectory = $viewDirectory;
        $this->eventManager = $eventManager;
        $this->templateManager = $templateManager;
        $this->themeCompiler = $themeCompiler;
        $this->container = $container;
        $this->modelManager = $modelManager;
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
     * @param Slot   $slot
     * @param string $id
     *
     * @throws ViewPreparationException
     *
     * @return string
     */
    protected function buildSlotLess(Slot $slot, string $id)
    {
        /** @var Shop $shop */
        $shop = null;
        if ($this->container->has('shop')) {
            $shop = $this->container->get('shop');
        } else {
            /** @var Repository $shopRepository */
            $shopRepository = $this->modelManager->getRepository(Shop::class);
            $shop = $shopRepository->getActiveDefault();
        }

        try {
            $variables = $this->themeCompiler->getThemeConfiguration($shop)->getConfig();
        } catch (\Exception $exception) {
            throw new ViewPreparationException($exception);
        }

        $filename = tempnam(sys_get_temp_dir(), 'NMB');

        file_put_contents($filename, <<<EOL
/**
 * TODO add mixin support
 * @import "variables";
 * @import "mixins";
 **/

#{$id} {
    {$slot->getStyle()}
}
EOL
);

        $this->lessCompiler->reset();
        $this->lessCompiler->setVariables($variables);
        $this->lessCompiler->compile($filename, '/');

        return $this->lessCompiler->get();
    }

    /**
     * @param Slot     $slot
     * @param Notice[] $notices
     *
     * @throws ViewPreparationException
     *
     * @return array
     */
    protected function serializeSlot(Slot $slot, array $notices): array
    {
        /** @var array $result */
        $result = $slot->jsonSerialize();

        $result['css'] = $this->buildSlotLess($slot, 'slot-' . $slot->getId());
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
