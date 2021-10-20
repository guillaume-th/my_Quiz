<?php

namespace App\Controller;

use App\Entity\HistoriqueQuizz;
use App\Form\HistoriqueQuizzType;
use App\Repository\HistoriqueQuizzRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/historique/quizz")
 */
class HistoriqueQuizzController extends AbstractController
{
    /**
     * @Route("/", name="historique_quizz_index", methods={"GET"})
     */
    public function index(HistoriqueQuizzRepository $historiqueQuizzRepository): Response
    {
        return $this->render('historique_quizz/index.html.twig', [
            'historique_quizzs' => $historiqueQuizzRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="historique_quizz_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $historiqueQuizz = new HistoriqueQuizz();
        $form = $this->createForm(HistoriqueQuizzType::class, $historiqueQuizz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($historiqueQuizz);
            $entityManager->flush();

            return $this->redirectToRoute('historique_quizz_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('historique_quizz/new.html.twig', [
            'historique_quizz' => $historiqueQuizz,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="historique_quizz_show", methods={"GET"})
     */
    public function show(HistoriqueQuizz $historiqueQuizz): Response
    {
        return $this->render('historique_quizz/show.html.twig', [
            'historique_quizz' => $historiqueQuizz,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="historique_quizz_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, HistoriqueQuizz $historiqueQuizz): Response
    {
        $form = $this->createForm(HistoriqueQuizzType::class, $historiqueQuizz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('historique_quizz_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('historique_quizz/edit.html.twig', [
            'historique_quizz' => $historiqueQuizz,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="historique_quizz_delete", methods={"POST"})
     */
    public function delete(Request $request, HistoriqueQuizz $historiqueQuizz): Response
    {
        if ($this->isCsrfTokenValid('delete'.$historiqueQuizz->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($historiqueQuizz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('historique_quizz_index', [], Response::HTTP_SEE_OTHER);
    }
}
