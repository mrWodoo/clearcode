<?php

namespace spec\AppBundle\Service;

use AppBundle\Repository\PersonRepository;
use AppBundle\Service\PersonService;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PersonServiceSpec extends ObjectBehavior
{
    function it_is_initializable(PersonRepository $personRepository)
    {
        $this
            ->beConstructedWith($personRepository);

        $this->shouldHaveType(PersonService::class);
    }

    public function it_throws_exception_on_delete_person_when_document_not_found(PersonRepository $personRepository)
    {
        $this
            ->beConstructedWith($personRepository);

        $this
            ->shouldThrow(DocumentNotFoundException::class)
            ->during('deletePerson', [
                'id that cant exist'
            ]);
    }
}
