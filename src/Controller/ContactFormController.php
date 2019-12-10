<?php

declare(strict_types=1);

namespace MangoSylius\ContactFormPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MangoSylius\ContactFormPlugin\Entity\ContactFormMessage;
use MangoSylius\ContactFormPlugin\Form\Type\ContactFormType;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContactFormController
{
    /** @var TranslatorInterface */
    private $translator;
    /** @var EngineInterface */
    private $templatingEngine;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var SenderInterface */
    private $mailer;
    /** @var RouterInterface */
    private $router;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var FormFactoryInterface */
    private $builder;
    /** @var UserRepositoryInterface */
    private $adminUserRepository;

    public function __construct(
        TranslatorInterface $translator,
        EngineInterface $templatingEngine,
        EntityManagerInterface $entityManager,
        SenderInterface $mailer,
        RouterInterface $router,
        FlashBagInterface $flashBag,
        FormFactoryInterface $builder,
        UserRepositoryInterface $adminUserRepository
    ) {
        $this->translator = $translator;
        $this->templatingEngine = $templatingEngine;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->builder = $builder;
        $this->adminUserRepository = $adminUserRepository;
    }

    public function showMessageAction(int $id)
    {
        $contactMessages = $this->entityManager->getRepository(ContactFormMessage::class)->find($id);

        return new Response($this->templatingEngine->render('@MangoSyliusContactFormPlugin/ContactForm/show.html.twig', [
            'message' => $contactMessages,
        ]));
    }

    public function createContactMessage(Request $request): Response
    {
        $contact = new ContactFormMessage();
        $form = $this->builder->create(ContactFormType::class, $contact, [
            'action' => $this->router->generate('mango_sylius_contact_form_message_send'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $contact->setSendTime(new \DateTime());
                $this->entityManager->persist($contact);
                $this->entityManager->flush();
                $SM = $this->adminUserRepository->find(1);
                assert($SM instanceof AdminUser);
                $this->mailer->send('contact_shop_manager_mail', [$SM->getEmail()], ['contact' => $contact]);
                $this->mailer->send('contact_customer', [$contact->getEmail()], ['contact' => $contact]);
                $this->flashBag->add('success', $this->translator->trans('mango_sylius.contactForm.success'));
            } else {
                $this->flashBag->add('error', $this->translator->trans('mango_sylius.contactForm.error'));
            }

            return new RedirectResponse($this->router->generate('sylius_shop_contact_request'));
        }

        return new Response($this->templatingEngine->render('@MangoSyliusContactFormPlugin/ContactForm/_form.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
