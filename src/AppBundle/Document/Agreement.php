<?php
namespace AppBundle\Document;

use AppBundle\Enum\Document\AddressTypeEnum;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Agreement
{
    /**
     * @var string|null
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 32,
     *     max = 64
     * )
     */
    protected $number;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    protected $signingDate;

    /**
     * Get id
     *
     * @return string|null $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return $this
     */
    public function setNumber(string $number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return string $number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param \DateTime $signingDate
     * @return $this
     */
    public function setSigningDate(\DateTime $signingDate)
    {
        $this->signingDate = $signingDate;
        return $this;
    }

    /**
     * Get signingDate
     *
     * @return \DateTime $signingDate
     */
    public function getSigningDate()
    {
        return $this->signingDate;
    }
}
