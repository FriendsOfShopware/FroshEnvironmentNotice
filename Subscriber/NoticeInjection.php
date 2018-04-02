<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Plugins_ViewRenderer_Bootstrap;
use Enlight_Event_EventArgs;
use FroshEnvironmentNotice\Services\ModifyHtmlText;
use FroshEnvironmentNotice\Services\NoticeMarkupBuilder;

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
     * NoticeInjection constructor.
     *
     * @param ModifyHtmlText      $htmlTextModifier
     * @param NoticeMarkupBuilder $markupBuilder
     */
    public function __construct(ModifyHtmlText $htmlTextModifier, NoticeMarkupBuilder $markupBuilder)
    {
        $this->htmlTextModifier = $htmlTextModifier;
        $this->markupBuilder = $markupBuilder;
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

        if ($bootstrap->Front()->Request()->getModuleName() === 'widget' ||
            $bootstrap->Front()->Request()->getControllerName() === 'FroshEnvironmentNoticeEditor') {
            return;
        }

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
                ], 'Das ist eine Stagingumgebung'),
                $args->getReturn()
            )
        );
    }
}
