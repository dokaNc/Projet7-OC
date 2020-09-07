<?php


namespace App\Service;


use App\Entity\User;
use App\Exception\ResourceValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService extends Service
{
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

    public function __construct(ExceptionService $exceptionService,
                                EntityManagerInterface $entityManager,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->exceptionService = $exceptionService;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param $violations
     * @param User $user
     * @throws ResourceValidationException
     */
    public function addUser($violations, User $user)
    {
        $this->exceptionService->invalidJson($violations);

        $user->getEmail();
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, (
                    $user->getPassword()
                )
            )
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
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $user;
    }
}