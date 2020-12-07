<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Category;

/**
* @Route("/categories", name="category_")
*/

class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $category = $this->getDoctrine()
             ->getRepository(Category::class)
             ->findAll();

        if (!$category) {
            throw $this->createNotFoundException(
            'No category found in category\'s table.'
            );
        }

        return $this->render('category/index.html.twig',
            ['categories' => $category]
        );
    }

    /**
     * @Route("/{categoryName}", requirements={"id"="\d+"}, methods={"GET"}, name="show")
     */
    public function show(string $categoryName):Response
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No '.$categoryName.' has been sent to find a category in category\'s table.');
        }

        $category = $this->getDoctrine()
        ->getRepository(Category::class)
        ->findBy(['name' => $categoryName]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category],
                ['id' => 'DESC'],
                3
            );

        return $this->render('category/show.html.twig', [
            'categoryName' => $categoryName,
            'programs' => $programs]);
    }
}
