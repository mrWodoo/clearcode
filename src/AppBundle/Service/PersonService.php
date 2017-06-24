<?php

namespace AppBundle\Service;

use AppBundle\Document\Address;
use AppBundle\Document\Agreement;
use AppBundle\Document\Person;
use AppBundle\Enum\Document\AddressTypeEnum;
use AppBundle\Repository\PersonRepository;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PersonService
{
    /**
     * @var PersonRepository
     */
    protected $personRepository;

    /**
     * PersonService constructor.
     * @param PersonRepository $personRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(PersonRepository $personRepository, ValidatorInterface $validator)
    {
        $this->personRepository = $personRepository;
        $this->validator        = $validator;
    }

    /**
     * Get people or specified person and output it as array
     *
     * @param string|null $id
     * @return Person[]
     */
    public function fetchAsArray(string $id = null) : array
    {
        $people = $this->personRepository->fetch($id);

        $return = [];

        foreach ($people AS $person) {
            // Build agreement output
            $agreementArray = null;
            $agreement      = $person->getAgreement();

            if ($agreement) {
                $signingDate = $agreement->getSigningDate();

                $agreementArray = [
                    'id'            => $agreement->getId(),
                    'number'        => $agreement->getNumber(),
                    'signingDate'   => ($signingDate) ? $signingDate->format('Y-m-d H:i:s') : null
                ];
            }

            // Build addresses output
            $addressesArray = null;
            $addresses      = $person->getAddresses();

            if (count($addresses)) {
                $addressesArray = [];
                foreach ($addresses AS $address) {
                    $addressesArray[] = [
                        'id'        => $address->getId(),
                        'address'   => $address->getAddress(),
                        'city'      => $address->getCity(),
                        'type'      => $address->getType()
                    ];
                }

            }

            $return[] = [
                'id'            => $person->getId(),
                'firstName'     => $person->getFirstName(),
                'lastName'      => $person->getLastName(),
                'phone'         => $person->getPhone(),
                'agreement'     => $agreementArray,
                'addresses'     => $addressesArray
            ];
        }

        if ($id) {
            return $return[array_keys($return)[0]];
        }

        return $return;
    }

    /**
     * @param Person $person
     * @return Address[]
     */
    public function getAdressesGrouppedByType(Person $person) : array
    {
        $output = [];

        foreach ($person->getAddresses() AS $address) {
            $output[$address->getType()] = $address;
        }

        return $output;
    }

    /**
     * @param string $personId
     * @return bool
     * @throws DocumentNotFoundException
     */
    public function deletePerson(string $personId) : bool
    {
        /** @var Person $person */
        $person = $this->personRepository->find($personId);

        if (!$person) {
            throw new DocumentNotFoundException('Person not found');
        }

        $this->personRepository->remove($person);

        return true;
    }

    /**
     * @param Person $person
     */
    public function savePerson(Person $person)
    {
        $dm = $this->getRepository()->getDocumentManager();

        $dm->persist($person);
        $dm->flush();
    }

    /**
     * @return PersonRepository
     */
    public function getRepository()
    {
        return $this->personRepository;
    }

    /**
     * Update person and it's children (agreement, addresses).

     * @param array $input
     * @param Person $person
     * @return Person|ConstraintViolationListInterface
     */
    public function processCreateUpdate(array $input, Person $person)
    {
        if (array_key_exists('firstName', $input)) {
            $person->setFirstName($input['firstName']);
        }

        if (array_key_exists('lastName', $input)) {
            $person->setLastName($input['lastName']);
        }

        if (array_key_exists('phone', $input)) {
            $person->setPhone($input['phone']);
        }

        // Update/create agreement
        if (array_key_exists('agreement', $input)) {
            $agremeentData  = $input['agreement'];
            $agreement      = $person->getAgreement() ?? new Agreement();

            // Assign agreement
            $person->setAgreement($agreement);

            if (array_key_exists('number', $agremeentData)) {
                $agreement->setNumber($agremeentData['number']);
            }

            if (array_key_exists('signingDate', $agremeentData)) {
                $signingDate = new \DateTime($agremeentData['signingDate']);
                $agreement->setSigningDate($signingDate);
            }

            // Validate agreement
            $agreementErrors = $this->validator->validate($agreement);

            if (count($agreementErrors)) {
                return $agreementErrors;
            }
        }

        // Update/create address
        if (array_key_exists('addresses', $input)) {
            $addressesInput = $input['addresses'];
            $adressesByType = $this->getAdressesGrouppedByType($person);

            // Loop through input
            foreach ($addressesInput AS $type => $addressInput) {
                // Only accept valid types
                if (in_array($type, AddressTypeEnum::getTypes())) {
                    $address = $adressesByType[$type] ?? new Address();

                    // Assign address if new
                    if (!$address->getId()) {
                        $person->addAddress($address);
                    }

                    $address->setType($type);

                    if (array_key_exists('address', $addressInput)) {
                        $address->setAddress($addressInput['address']);
                    }

                    if (array_key_exists('city', $addressInput)) {
                        $address->setCity($addressInput['city']);
                    }

                    // Validate address
                    $addressErrors = $this->validator->validate($address);

                    if (count($addressErrors)) {
                        return $addressErrors;
                    }
                }
            }
        }

        // Validate person
        $personErrors = $this->validator->validate($person);

        if (count($personErrors)) {
            return $personErrors;
        }

        return $person;
    }
}
