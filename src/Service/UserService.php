<?php


namespace App\Service;


use App\Entity\User;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use App\Repository\PhoneRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService extends Service
{
    /**
     * @var string
     */
    private $repositoryName = 'clientRepository';

    /**
     * @var ExceptionService
     */
    private $exceptionService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(PhoneRepository $phoneRepository,
                                ClientRepository $clientRepository,
                                UserPasswordEncoderInterface $passwordEncoder,
                                UserRepository $userRepository,
                                PaginatorInterface $paginatorInterface,
                                ExceptionService $exceptionService,
                                EntityManagerInterface $entityManager)
    {
        parent::__construct($phoneRepository, $clientRepository, $userRepository, $paginatorInterface, $exceptionService, $entityManager);

        $this->exceptionService = $exceptionService;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @param $page
     * @return PaginationInterface
     */
    public function getAllData($page)
    {
        return $this->getAll($this->repositoryName, $page);
    }

    /**
     * @param $page
     * @return PaginationInterface
     */
    public function getAllDataClient($page)
    {
        return $this->getAllByClients($this->repositoryName, $page);
    }

    /**
     * @param User $user
     * @param int $id
     * @return View
     */
    public function getData(User $user, int $id)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            if ($userClient = $this->getUser()->getClients() === $user->getClients()) {
                $userClient = $this->userRepository->findBy(['id' => $id]);
                return View::create($userClient, Response::HTTP_ACCEPTED);
            } else {
                throw $this->createAccessDeniedException();
            }
        }
    }

    /**
     * @param User $user
     * @param $violations
     * @throws ResourceValidationException
     */
    public function addUser(User $user, $violations)
    {
        $this->exceptionService->invalidJson($violations);

        $user->getEmail();
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, (
                    $user->getPassword()
                ))
        );
        $user->setClients($this->getUser()->getClients());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @return User
     */
    public function deleteData(User $user)
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            if ($this->getUser()->getClients() === $user->getClients()) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();
            } else {
                throw $this->createAccessDeniedException();
            }
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $user;



    }
}