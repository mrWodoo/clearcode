<?php

namespace AppBundle\Repository;

use AppBundle\Document\Person;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\DocumentRepository;

class PersonRepository extends DocumentRepository
{
    /**
     * @param string|null $id
     * @return Person[]|null
     */
    public function fetch(string $id = null)
    {
        $queryBuilder = $this
            ->getDocumentManager()
            ->createQueryBuilder(Person::class);

        $queryBuilder
            ->select('id', 'firstName', 'lastName', 'phone', 'agreement', 'addresses');

        // Specify id
        if ($id) {
            $queryBuilder
                ->field('id')
                ->equals($id);
        }

        return $queryBuilder
            ->getQuery()
            ->execute();
    }

    /**
     * Remove person
     * @param Person $person
     */
    public function remove(Person $person)
    {
        $this->getDocumentManager()->remove($person);
        $this->getDocumentManager()->flush();
    }
}
