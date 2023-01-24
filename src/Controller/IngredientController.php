<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /** 
     * /**
      * controller function display all ingredients
      *
      * @param IngredientRepository $repository
      * @param PaginatorInterface $paginator
      * @param Request $request
      * @return Response
      */
    #[Route('/ingredient', name: 'ingredient.index', methods:['GET'])]
    public function index(
        IngredientRepository $repository,
        PaginatorInterface $paginator,
        Request $request): Response
            {
            $ingredients = $paginator->paginate(
                $repository->findAll(),
                $request->query->getInt('page', 1),
                10 /* limit per page */
            );

            return $this->render('pages/ingredient/index.html.twig', [   
                'ingredients' => $ingredients
            ]);
    }
    /**
     *  controller function show a form which create ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
        ): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient= $form->getData();
        
            $manager->persist($ingredient);
            $manager->flush();
            
            $this->addFlash(
                'success',
                'Votre ingredient a été ajouté avec succès !'
            );
            return $this->redirectToRoute('ingredient.index');     
        } 
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * Funcion edit ingredient
     * 
     * @param Ingredient $ingredient
     * @param Request $request
     * **/
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
        public function edit(Ingredient $ingredient,
        Request $request, 
        EntityManagerInterface $manager 
        ) : Response    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient= $form->getData();
        
            $manager->persist($ingredient);
            $manager->flush();
            
            $this->addFlash(
                'success',
                'Votre ingredient a été modifié avec succès !'
            );
            return $this->redirectToRoute('ingredient.index');     
        } 
        return  $this->render('pages/ingredient/edit.html.twig' ,[
            'form' => $form->createView()
        ]);
    }

    /**
     *  controller function delete a form
     *
     *     
     * /** */
    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    public function delete(
        EntityManagerInterface $manager,
        Ingredient $ingredient
        ) : Response {   
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success', 
            'L\'ingredient a été supprimé avec succès!'
            );

        return $this->redirectToRoute('ingredient.index');   
    }
}
