<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Service\FileUploader;
use App\Services\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/participant', name: 'participant_')]
final class ParticipantController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }


    #[Route('/ajouter', name: 'ajouter', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request                $request,
                        EntityManagerInterface $entityManager,
                        FileUploader $fileUploader,
                        UserPasswordHasherInterface $userPasswordHasher

    ): Response
    {
        $participant = new Participant();
        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);

/*        if (!$this->isGranted("ROLE_ADMIN")){
            throw $this->createAccessDeniedException("Vous n'avez pas les droits pour créer un participant");
        }*/

        if ($participantForm->isSubmitted() && $participantForm->isValid()) {

            //traitement de l'image
            /** @var UploadedFile $imageFile */

            $imageFile = $participantForm->get('image')->getData();
            if($imageFile){
                $participant->setFilename($fileUploader ->upload($imageFile));
            }

            $plainPassword = $participant->getMdp();
            $hashedPassword = $userPasswordHasher->hashPassword($participant, $plainPassword);

            $participant->setMdp($hashedPassword);

            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participant/ajouter.html.twig', [
            'participant' => $participant,
            'form' =>  $participantForm,
            'app_image_participant_directory' => $this->getParameter('app.images_participant_directory'),

        ]);
    }


    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function detail(
        Participant $participant,
        Security $security,

    ): Response
    {
        if ($security->getUser()) {
        return $this->render('participant/detail.html.twig', [
            'participant' => $participant,
        ]);
    } else {
            return $this->redirectToRoute('app_login');
        }
    }



    #[Route('/update/{id}', name: 'update', methods: ['GET', 'POST'])]
    public function edit(Request $request,
                         Participant $participant,
                         EntityManagerInterface $entityManager,
                         FileUploader $fileUploader
    ): Response

    {
        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() && $participantForm->isValid()) {

            $imageFile = $participantForm->get('image')->getData();
            if(($participantForm->has('deleteImage') && $participantForm['deleteImage']->getData()) || $imageFile) {
                $fileUploader->delete($participant->getFilename(), $this->getParameter('app.images_participant_directory'));
                if ($imageFile) {
                    $participant->setFilename($fileUploader->upload($imageFile));
                }
                else{
                    $participant->setFilename(null);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participant/modifier.html.twig', [
            'participant' => $participant,
            'form' => $participantForm,
        ]);
    }




    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Participant $participant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $participant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('participant_index', [], Response::HTTP_SEE_OTHER);
    }
}
