<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\HistoriqueQuizz;
use App\Controller\HistoriqueQuizzController;
use App\Form\QuestionType;
use Symfony\Component\HttpFoundation\Cookie; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/question")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/{id}/{idQuestion}/{count}", name="question_index", methods={"GET"})
     */
    public function index($id, $idQuestion = NULL, $count = null): Response
    {
        $qryCategorieId = "idCategorie";
        $tmpQuestion = $this->getDoctrine()
            ->getRepository(Question::class)
            ->findOneBy([
                "idCategorie" => $id,
            ]);
        if ($tmpQuestion == null) {
            $qryCategorieId = "categorie";
            $tmpQuestion = $this->getDoctrine()
            ->getRepository(Question::class)
            ->findOneBy([
                $qryCategorieId => $id,
            ]);
        }
        // var_dump($tmpQuestion); 
        $qryQuestionId = "idQuestion";
        $reponse = $this->getDoctrine()
            ->getRepository(Reponse::class)
            ->findBy([
                $qryQuestionId => $tmpQuestion->id,
            ]);
        if ($reponse == null) {
            $qryQuestionId = "question";
        }

        $session = new Session();
        if ($count != null) {
            $score = $this->getDoctrine()
                ->getRepository(Reponse::class)
                ->findOneBy([
                    "id" => $count,
                ]);
            if ($score->reponseExpected == true) {
                $session->set('countscore', $session->get('countscore') + 1);
            }
        } else {
            $session->start();
            $session->set('countscore', 0);
        }

        if ($idQuestion == NULL) {
            $question = $this->getDoctrine()
                ->getRepository(Question::class)
                ->findOneBy([
                    $qryCategorieId => $id,
                ]);
        } else {
            $question = $this->getDoctrine()
                ->getRepository(Question::class)
                ->findOneBy([
                    $qryCategorieId => $id,
                    "id" => $idQuestion
                ]);
        }
        if ($question == NULL) {
            $count = $session->get('countscore');
            $cat = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findOneBy([
                "id" => $id,
            ]);
            $this->setScore($count, $cat);
            return $this->render('question/score.html.twig', [
                'count' => $count,
                'categorie' => $id,
            ]);
        } else {
            $reponses = $this->getDoctrine()
                ->getRepository(Reponse::class)
                ->findBy([
                    $qryQuestionId => $question->id,
                ]);
            shuffle($reponses);
            return $this->render('question/index.html.twig', [
                'questions' => $question,
                'reponses' => $reponses,
                '$count' => $count,

            ]);
        }
    }

    /**
     * @Route("/score", name="setScore", methods={"GET","POST"})
     */
    public function setScore($score, Categorie $categorie)
    {
        $user = $this->get('security.token_storage')->getToken();
        if ($user) {
            $user = $user->getUser(); 
            $history = new HistoriqueQuizz();

            $history->setScore($score);
            $history->setUser($user);
            $history->setCategorie($categorie);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($history);
            $entityManager->flush();
        } else {
            // $value = "score:$score;categorie:$categorie->id;"
            $value = [
                "categorie" => $categorie->id,
                "score" => $score,
                "user" => "guest"
            ];

            // $cookie = Cookie::create("history")
            // ->withValue(json_encode($value))
            // ->withExpires(time() + 2 * 24 * 60 * 60) 
            // ->withDomain("quiz.com")
            // ->withSecure(true); 
            $cookie = new Cookie("history", json_encode($value), time() + 2 * 24 * 60 * 60);
            // setcookie("history", json_encode($value), time() + 2 * 24 * 60 * 60);
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
