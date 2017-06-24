<?php

namespace AppBundle\Service;

use AppBundle\Document\Person;
use AppBundle\Repository\PersonRepository;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;

class PersonService
{
    /**
     * @var PersonRepository
     */
    protected $personRepository;

    /**
     * PersonService constructor.
     * @param PersonRepository $personRepository
     */
    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
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
            $agreementArray = [];
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
            $addressesArray = [];
            $addresses      = $person->getAddresses();

            if ($addresses) {
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

        return $return;
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
}