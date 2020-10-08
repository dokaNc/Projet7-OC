<?php


namespace App\Service;

use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\PhoneRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class Service extends AbstractController
{
    /**
     * @var PhoneRepository
     */
    private $phoneRepository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var PaginatorInterface
     */
    private $paginatorInterface;

    /**
     * @var ExceptionService
     */
    private $exceptionService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(PhoneRepository $phoneRepository,
                                ClientRepository $clientRepository,
                                UserRepository $userRepository,
                                PaginatorInterface $paginatorInterface,
                                ExceptionService $exceptionService,
                                EntityManagerInterface $entityManager
                                )
    {
        $this->phoneRepository = $phoneRepository;
        $this->clientRepository = $clientRepository;
        $this->paginatorInterface = $paginatorInterface;
        $this->exceptionService = $exceptionService;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
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
}