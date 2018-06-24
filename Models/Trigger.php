<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Models;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Shopware\Components\Model\ModelEntity;

/**
 * Class Trigger
 *
 * @ORM\Entity()
 * @ORM\Table(name="frosh_environment_notice_triggers")
 */
class Trigger extends ModelEntity implements JsonSerializable
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
     * @ORM\Column(name="condition_type", type="string", nullable=false)
     */
    private $conditionType;

    /**
     * @var string
     *
     * @ORM\Column(name="condition_configuration", type="string", nullable=false)
     */
    private $conditionConfiguration;

    /**
     * @var string
     *
     * @ORM\Column(name="action_type", type="string", nullable=false)
     */
    private $actionType;

    /**
     * @var string
     *
     * @ORM\Column(name="action_configuration", type="string", nullable=false)
     */
    private $actionConfiguration;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getConditionType(): string
    {
        return $this->conditionType;
    }

    /**
     * @param string $conditionType
     *
     * @return Trigger
     */
    public function setConditionType(string $conditionType): Trigger
    {
        $this->conditionType = $conditionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getConditionConfiguration(): string
    {
        return $this->conditionConfiguration;
    }

    /**
     * @param string $conditionConfiguration
     *
     * @return Trigger
     */
    public function setConditionConfiguration(string $conditionConfiguration): Trigger
    {
        $this->conditionConfiguration = $conditionConfiguration;

        return $this;
    }

    /**
     * @return string
     */
    public function getActionType(): string
    {
        return $this->actionType;
    }

    /**
     * @param string $actionType
     *
     * @return Trigger
     */
    public function setActionType(string $actionType): Trigger
    {
        $this->actionType = $actionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getActionConfiguration(): string
    {
        return $this->actionConfiguration;
    }

    /**
     * @param string $actionConfiguration
     *
     * @return Trigger
     */
    public function setActionConfiguration(string $actionConfiguration): Trigger
    {
        $this->actionConfiguration = $actionConfiguration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
