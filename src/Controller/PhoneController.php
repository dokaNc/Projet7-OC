<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class PhoneController extends AbstractController
{
    /**
     * @Route("/phones/", name="list_phone", methods={"GET"})
     * @param PhoneRepository $phoneRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function index(PhoneRepository $phoneRepository, SerializerInterface $serializer)
    {
        $phones = $phoneRepository->findAll();
        $data = $serializer->serialize($phones, 'json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
