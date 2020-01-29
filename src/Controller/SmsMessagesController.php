<?php

namespace App\Controller;

use App\Entity\SmsMessage;
use App\Form\SmsMessageType;
use App\Producer\SendSmsProducer;
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
     * @param SendSmsProducer $producer
     * @return Response
     */
    public function createNew(Request $request, SendSmsProducer $producer): Response
    {
        $form = $this->createForm(SmsMessageType::class, new SmsMessage());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $smsMessage = $this->persistSmsMessageEntity($form->getData());

            $producer->publish(serialize($smsMessage));

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
     * @return SmsMessage
     */
    protected function persistSmsMessageEntity($smsMessageData): SmsMessage
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($smsMessageData);
        $entityManager->flush();

        return $smsMessageData;
    }
}
