<?php

declare(strict_types=1);

namespace MangoSylius\SyliusContactFormPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MangoSylius\SyliusContactFormPlugin\Entity\ContactFormMessage;
use MangoSylius\SyliusContactFormPlugin\Form\Type\ContactFormType;
use MangoSylius\SyliusContactFormPlugin\Repository\ContactMessageRepository;
use ReCaptcha\ReCaptcha;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormError;
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
    /** @var ChannelContextInterface */
    private $channelContext;
    /** @var ContactMessageRepository */
    private $contactFormRepository;

    private $recaptchaPublic;
    private $recaptchaSecret;

    public function __construct(
        TranslatorInterface $translator,
        EngineInterface $templatingEngine,
        EntityManagerInterface $entityManager,
        SenderInterface $mailer,
        RouterInterface $router,
        FlashBagInterface $flashBag,
        FormFactoryInterface $builder,
        UserRepositoryInterface $adminUserRepository,
        ChannelContextInterface $channelContext,
        ContactMessageRepository $contactFormRepository,
        string $recaptchaPublic,
        string $recaptchaSecret
    ) {
        $this->translator = $translator;
        $this->templatingEngine = $templatingEngine;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->builder = $builder;
        $this->adminUserRepository = $adminUserRepository;
        $this->channelContext = $channelContext;
        $this->contactFormRepository = $contactFormRepository;
        $this->recaptchaPublic = $recaptchaPublic;
        $this->recaptchaSecret = $recaptchaSecret;
    }

    public function showMessageAction(int $id)
    {
        $contactMessages = $this->contactFormRepository->find($id);

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
            if ($this->recaptchaPublic != null || $this->recaptchaSecret != null) {
                $recaptcha = new ReCaptcha($this->recaptchaSecret);
                $resp = $recaptcha->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());
                if (!$resp->isSuccess()) {
                    $form->addError(new FormError($this->translator->trans('mango_sylius.contactForm.error.recaptcha')));
                }
            }
            if ($form->isValid()) {
                $contact->setSendTime(new \DateTime());
                $this->entityManager->persist($contact);
                $this->entityManager->flush();

                $channel = $this->channelContext->getChannel();
                assert($channel instanceof ChannelInterface);
                $contactEmail = $channel->getContactEmail();

                if ($contactEmail !== null) {
                    $this->mailer->send('contact_shop_manager_mail', [$contactEmail], ['contact' => $contact]);
                }
                $this->mailer->send('contact_customer', [$contact->getEmail()], ['contact' => $contact]);
                $this->flashBag->add('success', $this->translator->trans('mango_sylius.contactForm.success'));
            } else {
                $this->flashBag->add('error', $this->translator->trans('mango_sylius.contactForm.error.form'));
            }

            return new RedirectResponse($this->router->generate('sylius_shop_contact_request'));
        }

        return new Response($this->templatingEngine->render('@MangoSyliusContactFormPlugin/ContactForm/_form.html.twig', [
            'form' => $form->createView(),
            'key' => $this->recaptchaPublic,
        ]));
    }
}
