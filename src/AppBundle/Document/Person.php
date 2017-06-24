<?php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 * @MongoDB\Document(repositoryClass="AppBundle\Repository\PersonRepository")
 */
class Person
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 3,
     *     max = 32
     * )
     */
    protected $firstName;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 3,
     *     max = 32
     * )
     */
    protected $lastName;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\Length(
     *     max = 15
     * )
     */
    protected $phone;

    /**
     * @var Address[]
     * @MongoDB\ReferenceMany(targetDocument="Address", cascade={"remove"})
     */
    protected $addresses;

    /**
     * @var Agreement
     * @MongoDB\ReferenceOne(targetDocument="Agreement", cascade={"remove"})
     */
    protected $agreement;

    /**
     * Person constructor.
     */
    public function __construct()
    {
        $this->addresses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string $firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string $lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone = null)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Add address
     *
     * @param \AppBundle\Document\Address $address
     */
    public function addAddress(\AppBundle\Document\Address $address)
    {
        $this->addresses[] = $address;
    }

    /**
     * Remove address
     *
     * @param \AppBundle\Document\Address $address
     */
    public function removeAddress(\AppBundle\Document\Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection|Address[]
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set agreement
     *
     * @param \AppBundle\Document\Agreement $agreement
     * @return $this
     */
    public function setAgreement(\AppBundle\Document\Agreement $agreement)
    {
        $this->agreement = $agreement;
        return $this;
    }

    /**
     * Get agreement
     *
     * @return \AppBundle\Document\Agreement $agreement
     */
    public function getAgreement()
    {
        return $this->agreement;
    }
}
