<?php


namespace App\Service;


use App\Entity\Client;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\PhoneRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Service extends AbstractController
{
    private $phoneRepository;

    private $clientRepository;

    private $paginatorInterface;

    private $exceptionService;

    private $client;

    private $entityManager;

    public function __construct(PhoneRepository $phoneRepository,
                                ClientRepository $clientRepository,
                                PaginatorInterface $paginatorInterface,
                                ExceptionService $exceptionService,
                                Client $client,
                                EntityManagerInterface $entityManager
                                )
    {
        $this->phoneRepository = $phoneRepository;
        $this->clientRepository = $clientRepository;
        $this->paginatorInterface = $paginatorInterface;
        $this->exceptionService = $exceptionService;
        $this->client = $client;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $repositoryName
     * @param $page
     * @return PaginationInterface
     */
    public function getAll($repositoryName, $page)
    {
        return $this->paginatorInterface->paginate(
            $this->$repositoryName->findAll(),
            $page,
            10
        );
    }

    /**
     * @param $repositoryName
     * @param $page
     * @return PaginationInterface
     */
    public function getAllByClients($repositoryName, $page)
    {
        return $this->paginatorInterface->paginate(
            $this->$repositoryName->findBy(
                ['id' => $this->getUser()->getClients()]
            ),
            $page,
            10
        );
    }

    /**
     * @param $data
     * @param $violations
     * @throws ResourceValidationException
     */
    public function add($data, $violations)
    {
        $this->exceptionService->invalidJson($violations);

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param $violations
     * @throws ResourceValidationException
     */
    public function update($violations)
    {
        $this->exceptionService->invalidJson($violations);
        $this->getDoctrine()->getManager()->flush();

    }
}