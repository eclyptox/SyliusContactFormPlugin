<?php

declare(strict_types=1);

namespace Tests\MangoSylius\SyliusContactFormPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use MangoSylius\SyliusContactFormPlugin\Entity\ContactFormMessage;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;

final class MessageContext implements Context
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;


    public function __construct(
        EntityManagerInterface $entityManager,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->entityManager = $entityManager;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    /**
     * @Given there is a customer :customer that send a contact form
     */
    public function thereIsACustomerThatSendAContactForm(Customer $customer)
    {
        $message = new ContactFormMessage();
        $message->setSenderName($customer->getFullName());
        $message->setPhone($customer->getPhoneNumber());
        $message->setEmail($customer->getEmail());
        $message->setSendTime(new \DateTime());
        $message->setMessage("message");
        $message->setCustomer($customer);
        $this->entityManager->persist($message);
        $this->entityManager->flush();
        $contactMessage = $this->entityManager->getRepository(ContactFormMessage::class)->find(1);
    }
}