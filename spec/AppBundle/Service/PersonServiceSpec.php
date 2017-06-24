<?php

namespace spec\AppBundle\Service;

use AppBundle\Document\Address;
use AppBundle\Document\Agreement;
use AppBundle\Document\Person;
use AppBundle\Enum\Document\AddressTypeEnum;
use AppBundle\Repository\PersonRepository;
use AppBundle\Service\PersonServiceInterface;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PersonServiceSpec extends ObjectBehavior
{
    function it_is_initializable(PersonRepository $personRepository, ValidatorInterface $validator)
    {
        $this
            ->beConstructedWith($personRepository, $validator);

        $this->shouldHaveType(PersonServiceInterface::class);
    }

    public function it_throws_exception_on_delete_person_when_document_not_found
    (
        PersonRepository $personRepository,
        ValidatorInterface $validator
    )
    {
        $this
            ->beConstructedWith($personRepository, $validator);

        $this
            ->shouldThrow(DocumentNotFoundException::class)
            ->during('deletePerson', [
                'id that cant exist'
            ]);
    }

    public function it_returns_array_with_proper_format_on_fetch_as_array_with_full_data
    (
        PersonRepository $personRepository,
        ValidatorInterface $validator
    )
    {
        $this
            ->beConstructedWith($personRepository, $validator);

        $person     = new Person();

        $agreement  = new Agreement();
        $adresses   = [];
        $person->setAgreement($agreement);

        for ($i = 0; $i < 3; $i++) {
            $address = new Address();
            $person->addAddress($address);

            $adresses[] = $address;
        }

        $personRepository
            ->fetch(null)
            ->willReturn([
                $person
            ]);

        $people = $this->fetchAsArray();

        $people->shouldBeArray();
        $people->shouldHaveKey('0');

        $people[0]->shouldHaveKey('id');
        $people[0]->shouldHaveKey('agreement');
        $people[0]->shouldHaveKey('addresses');

        $people[0]['agreement']->shouldBeArray();
        $people[0]['addresses']->shouldBeArray();

    }

    public function it_returns_array_with_proper_format_on_fetch_as_array_without_agreement_and_addresses
    (
        PersonRepository $personRepository,
        ValidatorInterface $validator
    )
    {
        $this
            ->beConstructedWith($personRepository, $validator);

        $person     = new Person();

        $personRepository
            ->fetch(null)
            ->willReturn([
                $person
            ]);

        $people = $this->fetchAsArray();

        $people->shouldBeArray();
        $people->shouldHaveKey('0');

        $people[0]->shouldHaveKey('id');
        $people[0]->shouldHaveKeyWithValue('agreement', null);
        $people[0]->shouldHaveKeyWithValue('addresses', null);
    }

    public function it_updates_person_and_agreement_and_addresses
    (
        PersonRepository $personRepository,
        ValidatorInterface $validator
    )
    {
        $this
            ->beConstructedWith($personRepository, $validator);

        $person         = new Person();
        $input          = [
            'firstName'     => 'John',
            'lastName'      => 'Doe',
            'phone'         => '123456789',
            'agreement'     => [
                'number'        => md5(microtime(true)),
                'signingDate'   => date('Y-m-d H:i:s')
            ],
            'addresses'     => [
                'home'          => [
                    'address'       => 'Some Street 13/37',
                    'city'          => 'localhost'
                ],
                'billing'       => [
                    'address'       => 'Another Street 23/37',
                    'city'          => '127.0.0.1'
                ],
                'shipping'      => [
                    'address'       => 'And another one 33/37',
                    'city'          => '420'
                ],
                'falseType'     => [
                    'address'       => 'xyz',
                    'city'          => 'qqq'
                ]
            ]
        ];

        $updatedPerson  = $this->processCreateUpdate($input, $person);

        $updatedPerson->getFirstName()->shouldBeLike($input['firstName']);
        $updatedPerson->getLastName()->shouldBeLike($input['lastName']);
        $updatedPerson->getPhone()->shouldBeLike($input['phone']);

        $updatedPerson->getAddresses()->shouldHaveCount(3);

        for ($i = 0; $i < 3; $i++) {
            $address = $updatedPerson->getAddresses()[$i];
            $address->getType()->shouldHaveValue(AddressTypeEnum::getTypes());
        }

        $updatedPerson->getAgreement()->shouldBeAnInstanceOf(Agreement::class);
    }

    public function getMatchers()
    {
        return [
            'haveValue' => function ($value, $array) {
                return in_array($value, $array);
            },
        ];
    }
}
