<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Categorie;
use App\Entity\Question;
use App\Form\QuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/question")
 */
class QuestionController extends AbstractController
{
    private static $countscore = 0;
    /**
     * @Route("/{id}/{idQuestion}/{count}", name="question_index", methods={"GET"})
     */
    public function index($id, $idQuestion = NULL , $count=null): Response
    {
        if($count!=null){
            $score = $this->getDoctrine()
                ->getRepository(Reponse::class)
                ->findOneBy([
                    "id" => $id,
                ]);
                var_dump($score->reponseExpected);
                if($score->reponseExpected==true){
                    self::$countscore++;
                }
                var_dump(self::$countscore);
        }
        if ($idQuestion == NULL) {
            $question = $this->getDoctrine()
                ->getRepository(Question::class)
                ->findOneBy([
                    "idCategorie" => $id,
                ]);
        }
        else{
            $question = $this->getDoctrine()
                ->getRepository(Question::class)
                ->findOneBy([
                    "idCategorie" => $id,
                    "id" => $idQuestion
                ]);
        }
        if($question == NULL){
            return $this->render('question/score.html.twig', [
                'count' => $count,
                'categorie' => $id,
            ]);
            
        }
        else{
            $reponses = $this->getDoctrine()
            ->getRepository(Reponse::class)
            ->findBy([
                "idQuestion" => $question->id,
            ]);
        return $this->render('question/index.html.twig', [
            'questions' => $question,
            'reponses' => $reponses,
            '$count' => $count,

        ]);
        }
        
    }

    /**
     * @Route("/new", name="question_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('question/new.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="question_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Question $question): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('question/edit.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="question_delete", methods={"POST"})
     */
    public function delete(Request $request, Question $question): Response
    {
        if ($this->isCsrfTokenValid('delete' . $question->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('question_index', [], Response::HTTP_SEE_OTHER);
    }
}
