<?php


namespace App\Controller;


use App\Entity\Client;
use App\Entity\User;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
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
    /**
     * @var ClientRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, ClientRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Rest\Get(
     *     path = "/clients/{page<\d+>?1}",
     *     name = "app_client_user_list",
     *     requirements = {"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')")
     * @param PaginatorInterface $paginator
     * @param $page
     * @return View
     */
    public function listUserClientAction(PaginatorInterface $paginator, $page)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $client = $paginator->paginate(
                $this->repository->findBy(
                    ['id' => $this->getUser()->getClients()]
                ),
                $page,
                10
            );
        } elseif ($this->isGranted('ROLE_SUPERADMIN')) {
            $client = $paginator->paginate(
                $this->repository->findAll(),
                $page,
                10
            );
        }

        return View::create($client, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Get(
     *     path = "/client/{id}",
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
     *    path = "/client",
     *    name = "app_client_create"
     * )
     * @ParamConverter(
     *     "client",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="Create" }
     *     }
     * )
     * @IsGranted("ROLE_ADMIN")
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
     *     path = "/client/{id}",
     *     name = "app_client_update",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_ADMIN")
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
     *     path = "/client/{id}",
     *     name = "app_client_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_ADMIN")
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