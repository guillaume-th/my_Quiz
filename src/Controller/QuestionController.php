<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\HistoriqueQuizz;
use App\Controller\HistoriqueQuizzController;
use App\Entity\QuizzCount;
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
            $date = new \DateTime();
            $quizz = new QuizzCount();
            $quizz->setTime($date);
            $quizz->setCategorie($cat);
            $quizzfait = $this->getDoctrine()->getManager();
            $quizzfait->persist($quizz);
            $quizzfait->flush();
            if ($idQuestion == NULL) {
                $correctionquest = $this->getDoctrine()
                    ->getRepository(Question::class)
                    ->findBy([
                        $qryCategorieId => $id,
                    ]);
            } else {
                $correctionquest = $this->getDoctrine()
                    ->getRepository(Question::class)
                    ->findBy([
                        $qryCategorieId => $id,
                    ]);
            }
            $reponsecorrect=[];
            for ($i=$tmpQuestion->id; $i < $tmpQuestion->id + 10 ; $i++) { 
            $reponsetransit = $this->getDoctrine()
            ->getRepository(Reponse::class)
            ->findBy([
                $qryQuestionId => $i,
                'reponseExpected' => '1',
            ]);
            array_push($reponsecorrect,$reponsetransit);
        }
        // dd($reponsecorrect);
            return $this->render('question/score.html.twig', [
                'count' => $count,
                'categorie' => $id,
                'categorieall' => $cat,
                'reponse_correction' => $reponsecorrect,
                'questions_correction' => $correctionquest,
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
            $request = Request::createFromGlobals();
            $response = new Response(
                "Content",
                Response::HTTP_OK,
                ["content-type" => "text/html"]
            );
            $oldCookie = $request->cookies->get("history");
            var_dump($oldCookie);
            if ($oldCookie) {
                $oldValue = json_decode($oldCookie);
                foreach ($oldValue as $i => $c) {
                    if ($categorie->id == $c->categorie) {
                        array_splice($oldValue, $i);
                    }
                }
                array_push($oldValue, $value);
                $cookie = new Cookie("history", json_encode($oldValue), time() + 2 * 24 * 60 * 60);
            } else {
                $cookie = new Cookie("history", json_encode([$value]), time() + 2 * 24 * 60 * 60);
            }
            $response->headers->setCookie($cookie);
            $response->send();
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
        if ($this->isCsrfTokenValid('delete' . $question->id, $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('question_index', [], Response::HTTP_SEE_OTHER);
    }
}
