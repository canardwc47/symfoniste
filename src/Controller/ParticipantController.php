<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
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
    #[Route('/ajouter', name: 'ajouter', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request                $request,
                        EntityManagerInterface $entityManager,
                        FileUploader $fileUploader, // Injection du service FileUploader
                        UserPasswordHasherInterface $userPasswordHasher,
                        SiteRepository $siteRepository
    ): Response
    {
        $participant = new Participant();
        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);

/*        if (!$this->isGranted("ROLE_ADMIN")){
            throw $this->createAccessDeniedExce
ption("Vous n'avez pas les droits pour créer un participant");
        }*/

        if ($participantForm->isSubmitted() && $participantForm->isValid()) {
            /** @var UploadedFile $imageFile */

            if ($participantForm->get('images')->getData()) {
                $imageFile = $participantForm->get('images')->getData();

                $fileName = $fileUploader->upload($imageFile, $this->getUser()->getPseudo(),$this->getParameter('images_participant_directory'));
                $this->getUser()->setFilename($fileName);
            }

            $plainPassword = $participant->getMdp();
            $hashedPassword = $userPasswordHasher->hashPassword($participant, $plainPassword);
            $participant->setMdp($hashedPassword);

            $site = $siteRepository->findOneBy([]);
            $participant->setSite($site);

            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('participant_detail', ['id' => $participant->getId()]);
        }

        return $this->render('participant/ajouter.html.twig', [
            'participants' => $participant,
            'form' => $participantForm,
            'app_image_participant_directory' => $this->getParameter('images_participant_directory'),
        ]);
    }



    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function detail(
        Participant $participant,
        Security $security,
        EntityManagerInterface $em
    ): Response
    {
        if ($security->getUser()) {

            $detailSorties = $em->getRepository(Participant::class)->detailSortiesParticipant($participant->getId());

        return $this->render('participant/detail.html.twig', [
            'detailSorties' => $detailSorties,
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

        // Vérifier si le formulaire a été soumis et est valide
        if ($participantForm->isSubmitted() && $participantForm->isValid()) {
            // Gérer l'image si nécessaire
            $imageFile = $participantForm->get('images')->getData();

            // Check if the image should be deleted or a new one is uploaded
            if (($participantForm->has('deleteImage') && $participantForm['deleteImage']->getData()) || $imageFile) {
                // Check if a filename exists before trying to delete
                $filename = $participant->getFilename();
                if ($filename) {
                    // Supprimer l'ancienne image
                    $fileUploader->delete($filename, $this->getParameter('images_participant_directory'));
                } else {

                }
                // Si un fichier est téléchargé, l'ajouter
                if ($imageFile) {
                    $fileName = $fileUploader->upload(
                            $imageFile,
                            $this->getUser()->getPseudo(),
                            $this->getParameter('images_participant_directory')
                        );
                    $this->getUser()->setFilename($fileName);
                }
            }


            // Persister les changements dans la base de données
            $entityManager->flush();

            // Rediriger vers la page des détails ou index après modification
            return $this->redirectToRoute('participant_detail', ['id' => $participant->getId()]);
        }

        // Affichage du formulaire pour modifier le participant
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

        return $this->redirectToRoute('/', [], Response::HTTP_SEE_OTHER);
    }
}
