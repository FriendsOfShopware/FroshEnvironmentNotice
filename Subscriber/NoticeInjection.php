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
     * NoticeInjection constructor.
     *
     * @param ModifyHtmlText      $htmlTextModifier
     * @param NoticeMarkupBuilder $markupBuilder
     * @param ModelRepository     $noticeRepository
     */
    public function __construct(ModifyHtmlText $htmlTextModifier, NoticeMarkupBuilder $markupBuilder, ModelRepository $noticeRepository)
    {
        $this->htmlTextModifier = $htmlTextModifier;
        $this->markupBuilder = $markupBuilder;
        $this->noticeRepository = $noticeRepository;
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

        if ($module === 'widget' ||
            $bootstrap->Front()->Request()->getControllerName() === 'FroshEnvironmentNoticeEditor') {
            return;
        }

        /** @var Notice[] $notices */
        $notices = $this->noticeRepository->findBy([
            'name' => $module,
        ]);

        $slots = [];

        foreach ($notices as $notice) {
            if (array_key_exists($notice->getSlot()->getId(), $slots)) {
                $slots[$notice->getSlot()->getId()]['items'][] = $notice;
            } else {
                $slots[$notice->getSlot()->getId()] = [
                    'slot' => $notice->getSlot(),
                    'items' => [$notice],
                ];
            }
        }

        foreach ($slots as $slot) {
            /** @var Slot $slotItem */
            $slotItem = $slot['slot'];

            $messages = array_map(function (Notice $notice) {
                return $notice->getMessage();
            }, $slot['items']);

            $args->setReturn(
                $this->htmlTextModifier->insertAfterTag(
                    'body',
                    $this->markupBuilder->buildNoticeInSlot(join(PHP_EOL, $messages), $slotItem),
                    $args->getReturn()
                )
            );
        }
    }
}
