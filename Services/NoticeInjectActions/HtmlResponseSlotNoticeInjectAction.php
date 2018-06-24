<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Services\NoticeInjectActions;

use FroshEnvironmentNotice\Interfaces\NoticeInjectActionInterface;
use FroshEnvironmentNotice\Models\Notice;
use FroshEnvironmentNotice\Models\Slot;
use FroshEnvironmentNotice\Services\ModifyHtmlText;
use FroshEnvironmentNotice\Services\NoticeMarkupBuilder;
use Shopware\Components\Model\ModelRepository;

/**
 * Class HtmlResponseSlotNoticeInjectAction
 */
class HtmlResponseSlotNoticeInjectAction implements NoticeInjectActionInterface
{
    const NOTICE_NAME = 'name';

    /**
     * @var NoticeMarkupBuilder
     */
    private $noticeMarkupBuilder;

    /**
     * @var ModifyHtmlText
     */
    private $modifyHtmlText;

    /**
     * @var ModelRepository
     */
    private $noticeRepository;

    /**
     * HtmlResponseSlotNoticeInjectAction constructor.
     * @param ModifyHtmlText $modifyHtmlText
     * @param NoticeMarkupBuilder $noticeMarkupBuilder
     * @param ModelRepository $noticeRepository
     */
    public function __construct(
        ModifyHtmlText $modifyHtmlText,
        NoticeMarkupBuilder $noticeMarkupBuilder,
        ModelRepository $noticeRepository
    ) {
        $this->noticeRepository = $noticeRepository;
        $this->modifyHtmlText = $modifyHtmlText;
        $this->noticeMarkupBuilder = $noticeMarkupBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function injectNotice(array $configuration, $response)
    {
        // TODO allow multiple execution of this method without creating overlapping notices

        if (!is_string($response) || stripos($response, '</body') === false) {
            return $response;
        }

        $notices = $this->getMatchingNotices($configuration);
        $slots = $this->groupNoticesBySlots($notices);

        foreach ($slots as $slot) {
            /** @var Slot $slotItem */
            $slotItem = $slot['slot'];

            $messages = array_map(function (Notice $notice) {
                return $notice->getMessage();
            }, $slot['items']);

            $response = $this->modifyHtmlText->insertAfterTag(
                'body',
                $this->noticeMarkupBuilder->buildNoticeInSlot(join(PHP_EOL, $messages), $slotItem),
                $response
            );
        }

        return $response;
    }

    /**
     * @param array $configuration
     * @return Notice[]
     */
    protected function getMatchingNotices(array $configuration)
    {
        if (!array_key_exists(self::NOTICE_NAME, $configuration)) {
            return [];
        }

        return $this->noticeRepository->findBy([
            'name' => $configuration[self::NOTICE_NAME],
        ]);
    }

    /**
     * @param Notice[] $notices
     * @return array
     */
    protected function groupNoticesBySlots(array $notices): array
    {
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
        return $slots;
    }
}
