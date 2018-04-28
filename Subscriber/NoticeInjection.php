<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Plugins_ViewRenderer_Bootstrap;
use Enlight_Event_EventArgs;
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
     * @param ModifyHtmlText $htmlTextModifier
     * @param NoticeMarkupBuilder $markupBuilder
     * @param ModelRepository $noticeRepository
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

        /** @var Notice|null $notice */
        $notice = $this->noticeRepository->findOneBy([
            'name' => $module,
        ]);

        if (is_null($notice)) {
            return;
        }

        $message = $notice->getMessage();

        $args->setReturn(
            $this->htmlTextModifier->insertAfterTag(
                'body',
                $this->markupBuilder->buildNotice([
                    'position' => 'fixed',
                    'top' => '10px',
                    'right' => '10px',
                    'border-radius' => '5px',
                    'padding' => '15px',
                    'color' => 'white',
                    'background' => 'red',
                    'font-weight' => 'bold',
                    'z-index' => '9999999999',
                    'font-size' => '1.2em',
                    'pointer-events' => 'none',
                ], $message),
                $args->getReturn()
            )
        );
    }
}
