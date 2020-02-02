<?php

namespace App\Controller;

use App\Entity\SmsMessage;
use App\Form\SmsMessageType;
use App\Producer\SendSmsProducer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
            'messages' => $this->getSmsMessageRepository()->findAllSortedByCreatedAt(),
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @param SendSmsProducer $producer
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function createNew(Request $request, SendSmsProducer $producer, SerializerInterface $serializer): Response
    {
        $form = $this->createForm(SmsMessageType::class, new SmsMessage());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($form->getData());
            $entityManager->flush();

            $producer->publish($serializer->serialize($form->getData(), 'json'));

            return $this->redirectToRoute('index');
        }

        return $this->render('create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/twilio-callback", name="twilio_callback")
     * @param Request $request
     * @return Response
     */
    public function handleCallback(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $smsMessage = $this->getSmsMessageRepository()->findOneBy(['twilio_sid' => $request->get('MessageSid')]);

        if ($smsMessage) {
            $smsMessage->setStatus($request->get('MessageStatus'));
            $entityManager->flush();
        }

        return new Response();
    }

    /**
     * @return \App\Repository\SmsMessageRepository
     */
    protected function getSmsMessageRepository(): \App\Repository\SmsMessageRepository
    {
        return $this->getDoctrine()->getRepository(SmsMessage::class);
    }
}
