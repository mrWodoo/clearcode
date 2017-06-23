<?php
namespace AppBundle\Document;

use AppBundle\Enum\Document\AddressTypeEnum;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Address
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
     *     max = 255
     * )
     */
    protected $address;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 3,
     *     max = 100
     * )
     */
    protected $city;

    /**
     * @see AddressTypeEnum
     * @MongoDB\Field(type="string")
     * @Assert\Choice(
     *     callback = "getTypes"
     * )
     */
    protected $type;

    /**
     * @return array
     */
    public function getTypes() : array
    {
        return AddressTypeEnum::getTypes();
    }

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
     * Set address
     *
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address) : Address
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return string $address
     */
    public function getAddress() : string
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return $this
     */
    public function setCity(string $city) : Address
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return string $city
     */
    public function getCity() : string
    {
        return $this->city;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType(string $type) : Address
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string $type
     */
    public function getType() : string
    {
        return $this->type;
    }
}
