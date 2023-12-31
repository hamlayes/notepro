<?php

namespace App\Controller;

use App\Entity\ClassLevel;
use App\Form\ClassLevelType;
use App\Repository\ClassLevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/classe')]
class ClassLevelController extends AbstractController
{
    #[Route('/', name: 'app_class_level_index', methods: ['GET'])]
    public function index(ClassLevelRepository $classLevelRepository): Response
    {
        return $this->render('class_level/index.html.twig', [
            'class_levels' => $classLevelRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_class_level_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $classLevel = new ClassLevel();
        $form = $this->createForm(ClassLevelType::class, $classLevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($classLevel);
            $entityManager->flush();

            return $this->redirectToRoute('app_class_level_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('class_level/new.html.twig', [
            'class_level' => $classLevel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_class_level_show', methods: ['GET'])]
    public function show(ClassLevel $classLevel): Response
    {
        return $this->render('class_level/show.html.twig', [
            'class_level' => $classLevel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_class_level_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ClassLevel $classLevel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClassLevelType::class, $classLevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_class_level_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('class_level/edit.html.twig', [
            'class_level' => $classLevel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_class_level_delete', methods: ['POST'])]
    public function delete(Request $request, ClassLevel $classLevel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$classLevel->getId(), $request->request->get('_token'))) {
            $entityManager->remove($classLevel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_class_level_index', [], Response::HTTP_SEE_OTHER);
    }
}
