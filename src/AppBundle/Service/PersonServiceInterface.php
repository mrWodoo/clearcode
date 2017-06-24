<?php

namespace AppBundle\Service;

use AppBundle\Document\Person;

interface PersonServiceInterface
{
    /**
     * @param string|null $id
     * @return array
     */
    public function fetchAsArray(string $id = null) : array;

    /**
     * @param Person $person
     * @return array
     */
    public function getAdressesGrouppedByType(Person $person) : array;

    /**
     * @param string $personId
     * @return bool
     */
    public function deletePerson(string $personId) : bool;

    /**
     * @param Person $person
     * @return mixed
     */
    public function savePerson(Person $person);

    /**
     * @param array $input
     * @param Person $person
     * @return mixed
     */
    public function processCreateUpdate(array $input, Person $person);

    /**
     * @return mixed
     */
    public function getRepository();
}
