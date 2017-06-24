<?php

namespace spec\AppBundle\Controller;

use AppBundle\Controller\PersonController;
use AppBundle\Document\Person;
use AppBundle\Helper\ValidatorHelper;
use AppBundle\Repository\PersonRepository;
use AppBundle\Service\PersonServiceInterface;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PersonControllerSpec extends ObjectBehavior
{
    function it_is_initializable(
        ValidatorInterface $validator,
        PersonServiceInterface $personService,
        ValidatorHelper $validatorHelper
    )
    {
        $this
            ->beConstructedWith($validator, $personService, $validatorHelper);

        $this
            ->shouldHaveType(PersonController::class);
    }

    public function it_will_send_response_with_404_on_delete_when_person_not_found(
        ValidatorInterface $validator,
        PersonServiceInterface $personService,
        ValidatorHelper $validatorHelper
    )
    {
        $id = 'id that should never exist';

        $this
            ->beConstructedWith($validator, $personService, $validatorHelper);

        $personService
            ->deletePerson($id)
            ->willThrow(DocumentNotFoundException::class);

        /** @var JsonResponse $response */
        $response = $this->deleteAction($id);

        $response->shouldHaveType(JsonResponse::class);

        $response->getStatusCode()->shouldBe(404);
    }

    public function it_will_send_response_with_404_on_read_when_person_not_found(
        ValidatorInterface $validator,
        PersonServiceInterface $personService,
        ValidatorHelper $validatorHelper
    )
    {
        $id = 'id that should never exist';

        $this
            ->beConstructedWith($validator, $personService, $validatorHelper);

        $personService->fetchAsArray($id)->willReturn([]);

        /** @var JsonResponse $response */
        $response = $this->readAction($id);

        $response->shouldHaveType(JsonResponse::class);

        $response->getStatusCode()->shouldBe(404);
    }

    public function it_will_send_response_with_code_200_when_reading_everything(
        ValidatorInterface $validator,
        PersonServiceInterface $personService,
        ValidatorHelper $validatorHelper)
    {
        $this
            ->beConstructedWith($validator, $personService, $validatorHelper);

        $personService->fetchAsArray(null)->willReturn([]);

        /** @var JsonResponse $response */
        $response = $this->readAction();

        $response->shouldHaveType(JsonResponse::class);

        $response->getStatusCode()->shouldBe(200);
    }

    public function it_will_send_response_with_code_404_on_update_when_document_not_found(
        ValidatorInterface $validator,
        PersonServiceInterface $personService,
        ValidatorHelper $validatorHelper,

        Request $request,
        PersonRepository $personRepository
    )
    {
        $id = 'id that should never exist';

        $this
            ->beConstructedWith($validator, $personService, $validatorHelper);

        $personService->getRepository()->willReturn($personRepository);

        /** @var JsonResponse $response */
        $response = $this->updateAction($id, $request);

        $response->shouldHaveType(JsonResponse::class);

        $response->getStatusCode()->shouldBe(404);
    }

    public function it_will_send_response_with_code_400_on_update_when_document_found_and_invalid_input
    (
        ValidatorInterface $validator,
        PersonServiceInterface $personService,
        ValidatorHelper $validatorHelper,
        Request $request,
        PersonRepository $personRepository
    )
    {
        $id = 'just id';
        $person     = new Person();

        $this
            ->beConstructedWith($validator, $personService, $validatorHelper);

        $personService->getRepository()->willReturn($personRepository);
        $personRepository->find($id)->willReturn($person);

        /** @var JsonResponse $response */
        $response = $this->updateAction($id, $request);

        $response->shouldHaveType(JsonResponse::class);

        $response->getStatusCode()->shouldBe(400);
    }

    public function it_will_send_response_with_code_200_on_update_when_document_found_and_valid_input
    (
        ValidatorInterface $validator,
        PersonServiceInterface $personService,
        ValidatorHelper $validatorHelper,
        Request $request,
        PersonRepository $personRepository
    )
    {
        $id         = 'just id';
        $input      = [
            'firstName' => 'name'
        ];
        $person     = new Person();

        $this
            ->beConstructedWith($validator, $personService, $validatorHelper);

        $personService->getRepository()->willReturn($personRepository);
        $personRepository->find($id)->willReturn($person);
        $request->getContent()->willReturn(json_encode($input));

        $personService->processCreateUpdate($input, $person)->willReturn($person);
        $personService->savePerson($person)->willReturn(null);


        /** @var JsonResponse $response */
        $response = $this->updateAction($id, $request);

        $response->shouldHaveType(JsonResponse::class);

        $response->getStatusCode()->shouldBe(200);
    }

    public function it_will_send_response_with_code_400_on_create_when_document_found_and_invalid_input
    (
        ValidatorInterface $validator,
        PersonServiceInterface $personService,
        ValidatorHelper $validatorHelper,

        Request $request,
        PersonRepository $personRepository
    )
    {
        $id = 'just id';
        $person     = new Person();

        $this
            ->beConstructedWith($validator, $personService, $validatorHelper);

        $personService->getRepository()->willReturn($personRepository);
        $personRepository->find($id)->willReturn($person);

        /** @var JsonResponse $response */
        $response = $this->createAction($request);

        $response->shouldHaveType(JsonResponse::class);

        $response->getStatusCode()->shouldBe(400);
    }

    public function it_will_send_response_with_code_200_on_create_when_document_found_and_valid_input
    (
        ValidatorInterface $validator,
        PersonServiceInterface $personService,
        ValidatorHelper $validatorHelper,
        Request $request,
        PersonRepository $personRepository
    )
    {
        $id         = 'just id';
        $input      = [
            'firstName' => 'name'
        ];
        $person     = new Person();

        $this
            ->beConstructedWith($validator, $personService, $validatorHelper);

        $personService->getRepository()->willReturn($personRepository);
        $personRepository->find($id)->willReturn($person);
        $request->getContent()->willReturn(json_encode($input));

        $personService->processCreateUpdate($input, $person)->willReturn($person);
        $personService->savePerson($person)->willReturn(null);


        /** @var JsonResponse $response */
        $response = $this->createAction($request);

        $response->shouldHaveType(JsonResponse::class);

        $response->getStatusCode()->shouldBe(200);
    }
}
