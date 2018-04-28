<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Models;

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
     * @return array
     */
    public function getStyle(): array
    {
        return json_decode($this->style, true);
    }

    /**
     * @param string $style
     * @return Slot
     */
    public function setStyle(array $style): Slot
    {
        $this->style = json_encode($style);
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
        ];
    }
}
