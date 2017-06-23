<?php

namespace AppBundle\Controller;

use AppBundle\Document\Person;
use AppBundle\Enum\RestResponseEnum;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PersonController extends AbstractController
{
    /**
     * @Route("/person/{id}", name="readPerson", requirements={"id": "([a-z0-9]{1,32})"})
     * @Route("/person", name="readPeople")
     * @Route("/", name="readPeopleAlias")
     * @Method({"GET"})
     */
    public function readAction(string $id = null) : JsonResponse
    {
        /** @var EntityManagerInterface $dm */
        $dm         = $this->get('doctrine_mongodb');
        /** @var ObjectRepository $repository */
        $repository = $dm->getRepository('AppBundle:Person');

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
        /** @var EntityManagerInterface $dm */
        $dm         = $this->get('doctrine_mongodb');
        /** @var ObjectRepository $repository */
        $repository = $dm->getRepository('AppBundle:Person');

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
