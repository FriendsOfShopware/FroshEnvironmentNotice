<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Models;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Shopware\Components\Model\ModelEntity;

/**
 * Class Notice
 *
 * @ORM\Entity()
 * @ORM\Table(name="frosh_environment_notices")
 */
class Notice extends ModelEntity implements JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", nullable=false)
     */
    private $message;

    /**
     * @var Slot
     *
     * @ORM\ManyToOne(targetEntity="FroshEnvironmentNotice\Models\Slot", inversedBy="notices", fetch="EAGER")
     */
    private $slot;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Notice
     */
    public function setId(int $id): Notice
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Notice
     */
    public function setName(string $name): Notice
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return Notice
     */
    public function setMessage(string $message): Notice
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Slot
     */
    public function getSlot(): Slot
    {
        return $this->slot;
    }

    /**
     * @param Slot $slot
     *
     * @return Notice
     */
    public function setSlot(Slot $slot): Notice
    {
        $this->slot = $slot;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'message' => $this->getMessage(),
            'name' => $this->getName(),
            'slot' => $this->getSlot(),
        ];
    }
}
