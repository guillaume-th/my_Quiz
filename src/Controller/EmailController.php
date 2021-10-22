<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\EmailType;

/**
 * @Route("/admin")
 */
class EmailController extends AbstractController
{
    /**
     * @Route("/email", name="admin_email")
     */
    public function index(): Response
    {
        return $this->render('email/index.html.twig', [
            'controller_name' => 'EmailController',
        ]);
    }

    /**
     * @Route("/email/lastMonth/{connected}", name="admin_email_last_month")
     */
    public function email_last_month($connected, MailerInterface  $mailer)
    {

        $request = Request::createFromGlobals();
        $form = $this->createForm(EmailType::class);
        $form->handleRequest($request);
        $connected = $connected === "true" ? true : false;

        if ($form->isSubmitted() && $form->isValid()) {
            $addresses = [];
            $date = new \DateTime();
            $date->modify("-1 month");

            $entityManager = $this->getDoctrine()->getManager();
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findAllConnectedLastPeriod($date, $connected);
            foreach ($users as $user) {
                array_push($addresses, $user->getEmail());
            }


            $email = (new Email())
                ->from("quizbot@mail.com")
                ->to(...$addresses)
                ->text($form->get("email")->getData());

            // $mailer = new MailerInterface(); 
            if (count($addresses) > 0) {
                $mailer->send($email);
                return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->render('admin/index.html.twig', ["errors" => "No adresses found"]);
            }
        }
        return $this->renderForm('email/new.html.twig', [
            'form' => $form,
        ]);
    }
}
