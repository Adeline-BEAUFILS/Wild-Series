<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Form\ProgramType;
use Symfony\Component\HttpFoundation\Request;
/**
* @Route("/programs", name="program_")
*/

class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }
    /**
     * The controller for the program add form
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request) : Response
    {
        // Create a new Category Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
         if ($form->isSubmitted()) {
             // Deal with the submitted data
            // Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Category Object
            $entityManager->persist($program);
            // Flush the persisted object
            $entityManager->flush();
            // Finally redirect to categories list
            return $this->redirectToRoute('program_index');
        }
        return $this->render('category/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * Getting a program by id
     *
     * @Route("/{id}", name="show")
     * @return Response
     */
    public function show(Program $program):Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$program.' found in program\'s table.'
            );
        }

        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program_id' => $program]);


        return $this->render('program/show.html.twig', [
            'program' => $program, 'seasons' => $seasons
        ]);
    }
     /**
     * Getting a season by id
     *
     * @Route("/{program}/seasons/{season}", name="season_show")
     * @return Response
     */
    public function showSeason(Program $program, Season $season ):Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$season.' found in season\'s table.'
            );
        }

        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season_id' => $season]);

        return $this->render('program/season_show.html.twig', [
            'program' => $program, 'season' => $season, 'episodes' => $episodes
        ]);
    }
     /**
     * Getting a episode by id
     *
     * @Route("/{program}/seasons/{season}/episodes/{episode}", name="episode_show")
     * @return Response
     */
    public function showEpisode(Program $program, Season $season, Episode $episode):Response
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $program, 'season' => $season, 'episode' => $episode
        ]);
    }
    
}