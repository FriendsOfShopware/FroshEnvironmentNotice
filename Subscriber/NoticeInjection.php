<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Plugins_ViewRenderer_Bootstrap;
use Enlight_Event_EventArgs;
use FroshEnvironmentNotice\Interfaces\ConditionInterface;
use FroshEnvironmentNotice\Interfaces\NoticeInjectActionInterface;
use FroshEnvironmentNotice\Models\Trigger;
use Shopware\Components\Model\ModelRepository;

class NoticeInjection implements SubscriberInterface
{
    /**
     * @var ConditionInterface[]
     */
    private $conditions;

    /**
     * @var NoticeInjectActionInterface[]
     */
    private $noticeInjectActions;

    /**
     * @var ModelRepository
     */
    private $triggerRepository;

    /**
     * NoticeInjection constructor.
     *
     * @param ArrayCollection $conditions
     * @param ArrayCollection $noticeInjectActions
     * @param ModelRepository $triggerRepository
     */
    public function __construct(
        ArrayCollection $conditions,
        ArrayCollection $noticeInjectActions,
        ModelRepository $triggerRepository
    ) {
        $this->conditions = $conditions->toArray();
        $this->noticeInjectActions = $noticeInjectActions->toArray();
        $this->triggerRepository = $triggerRepository;
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

        /** @var Trigger $trigger */
        foreach ($this->triggerRepository->findAll() as $trigger) {
            if (!is_null($condition = $this->getCondition($trigger->getConditionType()))) {
                /** @var array $conditionConfiguration */
                $conditionConfiguration = json_decode($trigger->getConditionConfiguration(), true);
                if ($condition->isMet($conditionConfiguration)
                    && !is_null($action = $this->getNoticeInjectAction($trigger->getActionType()))) {
                    $actionConfiguration = json_decode($trigger->getActionConfiguration(), true);
                    $args->setReturn($action->injectNotice($actionConfiguration, $args->getReturn()));
                }
            }
        }
    }

    /**
     * @param string $conditionType
     *
     * @return ConditionInterface|null
     */
    protected function getCondition(string $conditionType)
    {
        foreach ($this->conditions as $condition) {
            if (is_a($condition, $conditionType)) {
                return $condition;
            }
        }

        return null;
    }

    /**
     * @param string $actionType
     *
     * @return NoticeInjectActionInterface|null
     */
    protected function getNoticeInjectAction(string $actionType)
    {
        foreach ($this->noticeInjectActions as $noticeInjectAction) {
            if (is_a($noticeInjectAction, $actionType)) {
                return $noticeInjectAction;
            }
        }

        return null;
    }
}
