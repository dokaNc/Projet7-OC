<?php


namespace App\Controller;


use App\Entity\Client;
use App\Exception\ResourceValidationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class ClientController
 * @package App\Controller
 * @Route("/api")
 */


class ClientController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Rest\Get(
     *     path = "/clients",
     *     name = "app_client_list",
     *     requirements = {"id"="\d+"}
     * )
     * @return View
     */
    public function listAction()
    {
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->findAll();

        return View::create($client, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Get(
     *     path = "/clients/{id}",
     *     name = "app_client_show",
     *     requirements = {"id"="\d+"}
     * )
     * @param Client $client
     * @return View
     */
    public function showAction(Client $client)
    {
        return View::create($client, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Post(
     *    path = "/clients",
     *    name = "app_client_create"
     * )
     * @ParamConverter(
     *     "client",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="Create" }
     *     }
     * )
     * @param Client $client
     * @param ConstraintViolationList $violations
     * @return View
     * @throws ResourceValidationException
     */
    public function createAction(Client $client, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data: ';
            foreach ($violations as $violation) {
                $message .= sprintf(
                    "Field '%s': %s ",
                    $violation->getPropertyPath(),
                    $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return View::create($client, Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_client_show',
                ['id' => $client->getId(), UrlGeneratorInterface::ABSOLUTE_URL])
            ]);
    }

    /**
     * @Rest\Put(
     *     path = "/clients/{id}",
     *     name = "app_client_update",
     *     requirements = {"id"="\d+"}
     * )
     * @ParamConverter("newClient", converter="fos_rest.request_body")
     * @param Client $client
     * @param Client $newClient
     * @param ConstraintViolationList $violations
     * @return mixed
     * @throws ResourceValidationException
     */
    public function updateAction(Client $client, Client $newClient, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field '%s': %s ",
                    $violation->getPropertyPath(),
                    $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $client->setName($newClient->getName());

        $this->getDoctrine()->getManager()->flush();

        return View::create($client, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path = "/clients/{id}",
     *     name = "app_client_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @param Client $client
     * @return View
     */
    public function deleteAction(Client $client)
    {
        $this->entityManager->remove($client);
        $this->entityManager->flush();

        return View::create($client, Response::HTTP_NO_CONTENT);
    }
}