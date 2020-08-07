<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Exception\ResourceValidationException;
use App\Repository\PhoneRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class PhoneController
 * @package App\Controller
 * @Route("/api")
 */

class PhoneController extends AbstractController
{
    private $entityManager;

    private $repository;

    public function __construct(EntityManagerInterface $entityManager, PhoneRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Rest\Get(
     *     path = "/phones/{page<\d+>?1}",
     *     name = "app_phone_list",
     *     requirements = {"id"="\d+"}
     * )
     * @param PaginatorInterface $paginator
     * @param $page
     * @return View
     */
    public function listAction(PaginatorInterface $paginator, $page)
    {

        $phone = $paginator->paginate(
            $this->repository->findAll(),
            $page,
            10
        );

        return View::create($phone, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Get(
     *     path = "/phone/{id}",
     *     name = "app_phone_show",
     *     requirements = {"id"="\d+"}
     * )
     * @param Phone $phone
     * @return View
     */
    public function showAction(Phone $phone)
    {
        return View::create($phone, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Post(
     *    path = "/phone",
     *    name = "app_phone_create"
     * )
     *
     * @ParamConverter(
     *     "phone",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="Create" }
     *     }
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param Phone $phone
     * @param ConstraintViolationList $violations
     * @return View
     * @throws ResourceValidationException
     */
    public function createAction(Phone $phone, ConstraintViolationList $violations)
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

        $this->entityManager->persist($phone);
        $this->entityManager->flush();

        return View::create($phone, Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_phone_show',
                ['id' => $phone->getId(), UrlGeneratorInterface::ABSOLUTE_URL])
            ]);
    }

    /**
     * @Rest\Put(
     *     path = "/phone/{id}",
     *     name = "app_phone_update",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_ADMIN")
     * @ParamConverter("newPhone", converter="fos_rest.request_body")
     * @param Phone $phone
     * @param Phone $newPhone
     * @param ConstraintViolationList $violations
     * @return mixed
     * @throws ResourceValidationException
     */
    public function updateAction(Phone $phone, Phone $newPhone, ConstraintViolationList $violations)
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

        $phone->setBrand($newPhone->getBrand());
        $phone->setModel($newPhone->getModel());
        $phone->setColor($newPhone->getColor());
        $phone->setDescription($newPhone->getDescription());
        $phone->setPrice($newPhone->getPrice());

        $this->getDoctrine()->getManager()->flush();

        return View::create($phone, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path = "/phone/{id}",
     *     name = "app_phone_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param Phone $phone
     * @return View
     */
    public function deleteAction(Phone $phone)
    {
        $this->entityManager->remove($phone);
        $this->entityManager->flush();

        return View::create($phone, Response::HTTP_NO_CONTENT);
    }

}