<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Models;

use ArrayAccess;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Shopware\Components\Model\ModelEntity;

/**
 * Class Slot
 *
 * @ORM\Entity()
 * @ORM\Table(name="frosh_environment_notice_slots")
 */
class Slot extends ModelEntity implements JsonSerializable
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
     * @ORM\Column(name="style", type="string", nullable=false)
     */
    private $style;

    /**
     * @ORM\OneToMany(targetEntity="FroshEnvironmentNotice\Models\Notice", mappedBy="slot", orphanRemoval=true, cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection
     */
    private $notices;

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
     * @return Slot
     */
    public function setId(int $id): Slot
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
     * @return Slot
     */
    public function setName(string $name): Slot
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }

    /**
     * @param string $style
     *
     * @return Slot
     */
    public function setStyle(string $style): Slot
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getNotices(): Collection
    {
        return $this->notices;
    }

    /**
     * @param ArrayAccess|Collection|array $notices
     *
     * @return Slot
     */
    public function setNotices($notices): Slot
    {
        $this->notices = new ArrayCollection($notices);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'style' => $this->getStyle(),
            'name' => $this->getName(),
            'notices' => $this->getNotices()->getValues(),
        ];
    }
}
