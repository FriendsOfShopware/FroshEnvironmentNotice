<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Plugins_ViewRenderer_Bootstrap;
use Enlight_Event_EventArgs;
use FroshEnvironmentNotice\Models\Notice;
use FroshEnvironmentNotice\Models\Slot;
use FroshEnvironmentNotice\Services\ModifyHtmlText;
use FroshEnvironmentNotice\Services\NoticeMarkupBuilder;
use Shopware\Components\Model\ModelRepository;

class NoticeInjection implements SubscriberInterface
{
    /**
     * @var ModifyHtmlText
     */
    private $htmlTextModifier;

    /**
     * @var NoticeMarkupBuilder
     */
    private $markupBuilder;

    /**
     * @var ModelRepository
     */
    private $noticeRepository;

    /**
     * @var string
     */
    private $viewDirectory;

    /**
     * NoticeInjection constructor.
     *
     * @param ModifyHtmlText      $htmlTextModifier
     * @param NoticeMarkupBuilder $markupBuilder
     * @param ModelRepository     $noticeRepository
     * @param string              $viewDirectory
     */
    public function __construct(
        ModifyHtmlText $htmlTextModifier,
        NoticeMarkupBuilder $markupBuilder,
        ModelRepository $noticeRepository,
        string $viewDirectory
    ) {
        $this->htmlTextModifier = $htmlTextModifier;
        $this->markupBuilder = $markupBuilder;
        $this->noticeRepository = $noticeRepository;
        $this->viewDirectory = $viewDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Plugins_ViewRenderer_FilterRender' => 'injectNotice',
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function injectNotice(Enlight_Event_EventArgs $args)
    {
        /** @var Enlight_Controller_Plugins_ViewRenderer_Bootstrap $bootstrap */
        $bootstrap = $args->get('subject');

        $module = strtolower($bootstrap->Front()->Request()->getModuleName());

        if ($module === 'widget') {
            return;
        }

        /** @var Notice[] $notices */
        $notices = $this->noticeRepository->findBy([
            'name' => $module,
        ]);

        if (empty($notices)) {
            return;
        }

        $slots = [];

        foreach ($notices as $notice) {
            if (!array_key_exists($notice->getSlot()->getId(), $slots)) {
                $slots[$notice->getSlot()->getId()] = $this->serializeSlot($notice->getSlot(), $notices);
            }
        }

        $bootstrap->Action()->View()->addTemplateDir($this->viewDirectory);
        $bootstrap->Action()->View()->assign('environment_notice', [
            'slots' => array_values($slots),
            'notices' => array_map([$this, 'serializeNotice'], $notices),
        ]);
        $noticeData = $bootstrap->Action()->View()->fetch('environment_notice/notice/index.tpl');

        $args->setReturn(
            $this->htmlTextModifier->insertAfterTag(
                'body',
                $noticeData,
                $args->getReturn()
            )
        );
    }

    protected function serializeSlot(Slot $slot, array $notices): array
    {
        /** @var array $result */
        $result = $slot->jsonSerialize();

        $result['css'] = $this->markupBuilder->buildSlots($slot, 'slot-' . $slot->getId());
        $result['notices'] = array_map([$this, 'serializeNotice'], array_values(array_filter($notices, function (Notice $notice) use ($slot) {
            return $notice->getSlot()->getId() === $slot->getId();
        })));

        return $result;
    }

    protected function serializeNotice(Notice $notice): array
    {
        return $notice->jsonSerialize();
    }
}
