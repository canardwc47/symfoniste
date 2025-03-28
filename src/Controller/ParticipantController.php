<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


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

    #[Route('/add', name: 'add', methods: ['GET', 'POST'])]
    public function add(Request                $request,
                        EntityManagerInterface $entityManager,
                        Uploader  $uploader,
                        UserPasswordHasherInterface $userPasswordHasher

    ): Response
    {
        $participant = new Participant();
        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() && $participantForm->isValid()) {

            $plainPassword = $participant->getMdp();
            $hashedPassword = $userPasswordHasher->hashPassword($participant, $plainPassword);
            $participant->setMdp($hashedPassword);

            /**
             * @var UploadedFile $image
             */
            $image = $participantForm->get('image')->getData();
            if ($image) {
                // Utilisation du service Uploader pour enregistrer l'image
                $imageName = $uploader->save($image, $participant->getName(), $this->getParameter('participant_image_dir'));
                $participant->setImage($imageName);  // Enregistrer le nom de l'image dans l'entité
            }

            // Sauvegarde dans la base de données
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participant/add.html.twig', [
            'form' => $participantForm->createView(),
        ]);
    }


    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function detail(Participant $participant): Response
    {
        return $this->render('participant/detail.html.twig', [
            'participant' => $participant,
        ]);
    }



    #[Route('/update/{id}', name: 'update', methods: ['GET', 'POST'])]
    public function edit(Request $request,
                         Participant $participant,
                         EntityManagerInterface $entityManager,
                         Uploader $uploader
    ): Response

    {
        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() && $participantForm->isValid()) {

            $imageFile = $participantForm->get('image')->getData();

                if ($imageFile) {
                    $participant->setFilename($uploader->upload($imageFile));
                }
                else{
                    $participant->setFilename(null);
                }

            $entityManager->flush();

            return $this->redirectToRoute('participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participant/update.html.twig', [
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
