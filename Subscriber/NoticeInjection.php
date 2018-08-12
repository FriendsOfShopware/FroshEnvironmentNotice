<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Plugins_ViewRenderer_Bootstrap;
use Enlight_Event_EventArgs;
use FroshEnvironmentNotice\Exceptions\ViewPreparationException;
use FroshEnvironmentNotice\Models\Notice;
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
     * @param string              $viewDirectory
     */
    public function __construct(
        ModifyHtmlText $htmlTextModifier,
        NoticeMarkupBuilder $markupBuilder,
        ModelRepository $noticeRepository
    ) {
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

        try {
            $this->markupBuilder->prepareView($bootstrap->Action()->View());
            $args->setReturn(
                $this->htmlTextModifier->insertAfterTag(
                    'body',
                    $this->markupBuilder->buildInjectableHtml($bootstrap->Action()->View(), $notices),
                    $args->getReturn()
                )
            );
        } catch (ViewPreparationException $exception) {
            // TODO log it
        }
    }
}
