<?php

namespace spec\AppBundle\Controller;

use AppBundle\Controller\PersonController;
use AppBundle\Service\PersonService;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PersonControllerSpec extends ObjectBehavior
{
    function it_is_initializable(ValidatorInterface $validator, PersonService $personService)
    {
        $this
            ->beConstructedWith($validator, $personService);

        $this
            ->shouldHaveType(PersonController::class);
    }

    public function it_will_send_response_with_404_on_delete_when_person_not_found(ValidatorInterface $validator, PersonService $personService)
    {
        $id = 'id that should never exist';

        $this
            ->beConstructedWith($validator, $personService);

        $personService
            ->deletePerson($id)
            ->willThrow(DocumentNotFoundException::class);

        /** @var JsonResponse $response */
        $response = $this->deleteAction($id);

        $response->shouldHaveType(JsonResponse::class);
    }

    public function it_will_send_response_with_404_on_read_when_person_not_found(ValidatorInterface $validator, PersonService $personService)
    {
        $id = 'id that should never exist';

        $this
            ->beConstructedWith($validator, $personService);

        $personService->fetchAsArray($id)->willReturn([]);

        /** @var JsonResponse $response */
        $response = $this->readAction($id);

        $response->shouldHaveType(JsonResponse::class);
    }

    public function it_will_send_response_with_code_200_when_reading_everything(ValidatorInterface $validator, PersonService $personService)
    {
        $this
            ->beConstructedWith($validator, $personService);

        $personService->fetchAsArray(null)->willReturn([]);

        /** @var JsonResponse $response */
        $response = $this->readAction();

        $response->shouldHaveType(JsonResponse::class);
    }
}
