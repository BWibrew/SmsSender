<?php

namespace App\Controller;

use App\Entity\SmsMessage;
use App\Form\SmsMessageType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SmsMessagesController extends AbstractController
{
    /**
     * List all SMS messages, ordered with newest first.
     *
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'messages' => $this->getDoctrine()->getRepository(SmsMessage::class)->findAllSortedByCreatedAt(),
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     */
    public function createNew(Request $request): Response
    {
        $form = $this->createForm(SmsMessageType::class, new SmsMessage());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->persistSmsMessageEntity($form->getData());

            return $this->redirectToRoute('index');
        }

        return $this->render('create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Persist the SmsMessage entity to the database.
     *
     * @param $smsMessageData
     */
    protected function persistSmsMessageEntity($smsMessageData): void
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($smsMessageData);
        $entityManager->flush();
    }
}
