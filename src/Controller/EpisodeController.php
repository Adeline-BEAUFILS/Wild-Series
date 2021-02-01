<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Form\CommentType;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @Route("/episode")
 */
class EpisodeController extends AbstractController
{
    /**
     * @Route("/", name="episode_index", methods={"GET"})
     */
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="episode_new", methods={"GET","POST"})
     */
    public function new(Request $request, Slugify $slugify ,MailerInterface $mailer): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $entityManager->persist($episode);
            $entityManager->flush();
            $program = $episode->getProgram();

            // Once the form is submitted, valid and the data inserted in database, you can define the success flash message
            $this->addFlash('success', "l'épisode à été crée");

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('56f23e496d-5edd50@inbox.mailtrap.io')
                ->subject('Un nouvel épisode vient d\'être publiée !')
                ->html($this->renderView('episode/newEpisodeEmail.html.twig', ['episode' => $episode , 'program' => $program]));

            $mailer->send($email);
            // [...]

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="episode_show", methods={"GET","POST"})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"slug": "slug"}})
     */
    public function show(Episode $episode, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $user= $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setAuthor($user);
            $comment->setEpisode($episode);
            $entityManager->persist($comment);
            $entityManager->flush();


        

        }

        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
            'comment' => $comment,

        ]);
    }

    /**
     * @Route("/{slug}/edit", name="episode_edit", methods={"GET","POST"})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"slug": "slug"}})
     */
    public function edit(Request $request, Episode $episode, Slugify $slugify): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            
            $this->getDoctrine()->getManager()->flush();

                    // Once the form is submitted, valid and the data inserted in database, you can define the success flash message
        $this->addFlash('success', "L'épisode à été modifié");

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="episode_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Episode $episode): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($episode);
            $entityManager->flush();
        }

        // Once the form is submitted, valid and the data inserted in database, you can define the success flash message
        $this->addFlash('danger', "l'épisode à été supprimé");

        return $this->redirectToRoute('episode_index');
    }
}
