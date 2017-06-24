<?php

namespace AppBundle\Controller;

use AppBundle\Document\Person;
use AppBundle\Enum\RESTResponseEnum;
use AppBundle\Helper\ValidatorHelper;
use AppBundle\Service\PersonService;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\ConstraintViolationListInterface;
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
     * @var ValidatorHelper
     */
    protected $validatorHelper;

    /**
     * PersonController constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ValidatorInterface $validator,
        PersonService $personService,
        ValidatorHelper $validatorHelper
    )
    {
        $this->validator        = $validator;
        $this->personService    = $personService;
        $this->validatorHelper  = $validatorHelper;
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
     * @Route("/person/update/{id}", name="updatePerson", requirements={"id": "([a-z0-9]{1,32})"})
     * @Method({"PATCH"})
     */
    public function updateAction($id, Request $request)
    {
        /** @var Person $person */
        $person     = $this->personService->getRepository()->find($id);

        if (!$person) {
            return $this->getJsonErrorResponse(RESTResponseEnum::NOT_FOUND, [
                'Person not found'
            ]);
        }

        // Get and validate input
        $inputJson  = json_decode($request->getContent(), JSON_BIGINT_AS_STRING);

        if (json_last_error()) {
            return $this->getJsonErrorResponse(RESTResponseEnum::BAD_REQUEST, [
                'Invalid input'
            ]);
        }

        // Update person
        $update = $this->personService->processCreateUpdate($inputJson, $person);

        // Handle validation errors
        if ($update instanceof ConstraintViolationListInterface) {
            return $this
                ->getJsonErrorResponse(
                    RESTResponseEnum::BAD_REQUEST,
                        $this->validatorHelper->constraintViolationListToArray($update)
                );
        }

        $this->personService->savePerson($person);

        return $this->getJsonResponse([
            'Success'
        ]);
    }

    /**
     * @Route("/person/create", name="createPerson")
     * @Method({"PUT"})
     */
    public function createAction(Request $request)
    {
        // Get and validate input
        $inputJson  = json_decode($request->getContent(), JSON_BIGINT_AS_STRING);

        if (json_last_error()) {
            return $this->getJsonErrorResponse(RESTResponseEnum::BAD_REQUEST, [
                'Invalid input'
            ]);
        }

        /** @var Person $person */
        $person     = new Person();

        // Update person
        $update = $this->personService->processCreateUpdate($inputJson, $person);

        // Handle validation errors
        if ($update instanceof ConstraintViolationListInterface) {
            return $this
                ->getJsonErrorResponse(
                    RESTResponseEnum::BAD_REQUEST,
                    $this->validatorHelper->constraintViolationListToArray($update)
                );
        }

        $this->personService->savePerson($person);

        return $this->getJsonResponse([
            'id'        => $person->getId()
        ]);
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
