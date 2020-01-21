<?php

declare(strict_types=1);

namespace MangoSylius\SyliusContactFormPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MangoSylius\SyliusContactFormPlugin\Entity\ContactFormMessage;
use MangoSylius\SyliusContactFormPlugin\Form\Type\ContactFormType;
use MangoSylius\SyliusContactFormPlugin\Repository\ContactMessageRepository;
use MangoSylius\SyliusContactFormPlugin\Service\ContactFormSettingsProviderInterface;
use ReCaptcha\ReCaptcha;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContactFormController
{
    /** @var ContactFormSettingsProviderInterface */
    private $contactFormSettings;
    /** @var EngineInterface */
    private $templatingEngine;
    /** @var TranslatorInterface */
    private $translator;
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
    /** @var TokenStorageInterface */
    private $token;
    /** @var string */
    private $recaptchaPublic;
    /** @var string */
    private $recaptchaSecret;

    public function __construct(
        ContactFormSettingsProviderInterface $contactFormSettings,
        EngineInterface $templatingEngine,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        SenderInterface $mailer,
        RouterInterface $router,
        FlashBagInterface $flashBag,
        FormFactoryInterface $builder,
        UserRepositoryInterface $adminUserRepository,
        ChannelContextInterface $channelContext,
        ContactMessageRepository $contactFormRepository,
        TokenStorageInterface $tokenStorage,
        string $recaptchaPublic,
        string $recaptchaSecret
    ) {
        $this->contactFormSettings = $contactFormSettings;
        $this->templatingEngine = $templatingEngine;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->builder = $builder;
        $this->adminUserRepository = $adminUserRepository;
        $this->channelContext = $channelContext;
        $this->contactFormRepository = $contactFormRepository;
        $this->token = $tokenStorage;
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
        $token = $this->token->getToken();
        if ($token !== null) {
            $user = $token->getUser();
            if ($user !== null && $user instanceof ShopUser) {
                $customer = $user->getCustomer();
                assert($customer instanceof Customer);
                $contact->setCustomer($customer);
                $contact->setEmail($user->getEmail());
                $contact->setSenderName($customer->getFirstName() . ' ' . $customer->getLastName());
            }
        }

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

                if ($contactEmail !== null && $this->contactFormSettings->isSendManager() !== false) {
                    $this->mailer->send('contact_shop_manager_mail', [$contactEmail], ['contact' => $contact]);
                }
                if ($this->contactFormSettings->isSendCustomer() !== false) {
                    $this->mailer->send('contact_customer', [$contact->getEmail()], ['contact' => $contact]);
                }
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
