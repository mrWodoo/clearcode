<?php

namespace AppBundle\Controller;

use AppBundle\Document\Address;
use AppBundle\Document\Agreement;
use AppBundle\Document\Person;
use AppBundle\Enum\Document\AddressTypeEnum;
use AppBundle\Enum\RESTResponseEnum;
use AppBundle\Repository\PersonRepository;
use AppBundle\Service\PersonService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
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
     * @var PersonService
     */
    protected $personService;

    /**
     * PersonController constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ValidatorInterface $validator,
        PersonService $personService
    )
    {
        $this->validator        = $validator;
        $this->personService    = $personService;
    }

    /**
     * @Route("/person/{id}", name="readPerson", requirements={"id": "([a-z0-9]{1,32})"})
     * @Route("/person", name="readPeople")
     * @Route("/", name="readPeopleAlias")
     * @Method({"GET"})
     */
    public function readAction(string $id = null) : JsonResponse
    {
        $people = $this->personService->fetchAsArray($id);

        // Specified person not found
        if (!$people && $id) {
            return $this->getJsonErrorResponse(RESTResponseEnum::NOT_FOUND, [
                'Person not found'
            ]);
        }

        return $this->getJsonResponse($people);
    }

    /**
     * @Route("/person/delete/{id}", name="deletePerson", requirements={"id": "([a-z0-9]{1,32})"})
     * @Method({"DELETE"})
     */
    public function deleteAction($id)
    {
        try {
            $this->personService->deletePerson($id);
        } catch (DocumentNotFoundException $exception) {
            return $this
                ->getJsonErrorResponse(RESTResponseEnum::NOT_FOUND, [
                    'Given person not found'
                ]);
        }

        return $this->getJsonResponse('Person removed');
    }
}
