<?php

namespace App\Controller;

use App\Entity\Groupes;
use App\Repository\GroupesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class GroupeController extends AbstractController
{
    /**
     * @Route("/api/groupes", name="groupes", methods={"GET"})
     */
    public function getAllGroupes(GroupesRepository $groupesRepository, SerializerInterface $serializer): JsonResponse
    {
        $groupesList =  $groupesRepository->findAll();

        $jsonGroupesList = $serializer->serialize($groupesList, 'json');
        return new JsonResponse($jsonGroupesList, 200, [], true);
    }

    /**
     * @Route("/api/groupes/{id}", name="detailGroupe", methods={"GET"})
     */
    public function getDetailGroupe(Groupes $groupe, SerializerInterface $serializer): JsonResponse
    {
        $jsonGroupe = $serializer->serialize($groupe, 'json', ['groups' => 'groupes']);
        return new JsonResponse($jsonGroupe, 200, [], true);
    }

    /**
     * @Route("/api/groupes/{id}", name="deleteGroupe", methods={"DELETE"})
     */
    public function deleteGroupe(Groupes $groupe, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($groupe);
        $em->flush();
        return new JsonResponse(null, 204);
    }

    /**
     * @Route("/api/groupes", name="createGroupe", methods={"POST"})
     */
    public function createGroupe(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse 
    {
        $groupe = $serializer->deserialize($request->getContent(), Groupes::class, 'json');
        $em->persist($groupe);
        $em->flush();

        $jsonGroupe = $serializer->serialize($groupe, 'json', ['groups' => 'groupes']);
        $location = $urlGenerator->generate('detailGroupe', ['id' => $groupe->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonGroupe, 201, ["Location" => $location], true);
    }

        /**
     * @Route("/api/groupes/{id}", name="updateGroupe", methods={"PUT"})
     */
    public function updateGroupe(Request $request, SerializerInterface $serializer, GroupesRepository $groupeRepository, Groupe $currentGroupe, EntityManagerInterface $em): JsonResponse
    {
        $updatedGroupe = $serializer->deserialize($request->getContent(), 
                Groupes::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentGroupe]);
        $content = $request->toArray();
        $idGroupe = $content['idGroupe'] ?? -1;
        $updatedGroupe->setGroupe($groupeRepository->find($idGroupe));
        
        $em->persist($updatedGroupe);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
?>