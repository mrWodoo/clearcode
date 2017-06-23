<?php

namespace AppBundle\Controller;

use AppBundle\Document\Address;
use AppBundle\Document\Agreement;
use AppBundle\Document\Person;
use AppBundle\Enum\Document\AddressTypeEnum;
use AppBundle\Enum\RestResponseEnum;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PersonController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * PersonController constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @Route("/person/{id}", name="readPerson", requirements={"id": "([a-z0-9]{1,32})"})
     * @Route("/person", name="readPeople")
     * @Route("/", name="readPeopleAlias")
     * @Method({"GET"})
     */
    public function readAction(string $id = null) : JsonResponse
    {
        /** @var ManagerRegistry $doctrine */
        $doctrine = $this->get('doctrine_mongodb');

        /** @var EntityManagerInterface $dm */
        $dm         = $doctrine->getManager();

        /** @var ObjectRepository $repository */
        $repository = $doctrine->getRepository('AppBundle:Person');

        /** @var Person[] $people */
        $people     = [];


        // Find specific person
        if ($id) {
            $person = $repository
                ->find($id);

            if ($person) {
                $people[] = $person;
            }
        } else {
            // Find everyone
            $people = $repository
                ->findAll();
        }

        return $this->getJsonResponse($people);
    }

    /**
     * @Route("/person/delete/{id}", name="deletePerson", requirements={"id": "([a-z0-9]{1,32})"})
     * @Method({"DELETE"})
     */
    public function deleteAction($id)
    {
        /** @var ManagerRegistry $doctrine */
        $doctrine = $this->get('doctrine_mongodb');

        /** @var EntityManagerInterface $dm */
        $dm         = $doctrine->getManager();

        /** @var ObjectRepository $repository */
        $repository = $doctrine->getRepository('AppBundle:Person');

        $person = $repository
            ->find($id);

        if (!$person) {
            return $this
                ->getJsonErrorResponse(RESTResponseEnum::NOT_FOUND, [
                    'Given person not found'
                ]);
        }

        $dm->remove($person);
        $dm->flush();

        return $this->getJsonResponse('Person removed');
    }
}
