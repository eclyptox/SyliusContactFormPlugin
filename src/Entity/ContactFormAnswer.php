<?php

declare(strict_types=1);

namespace MangoSylius\SyliusContactFormPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_contact_form_answer")
 */
class ContactFormAnswer implements ResourceInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string|null
     * @Assert\NotBlank
     */
    protected $message;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime|null
     */
    protected $sendTime;

    /**
     * @var ContactFormMessage|null
     * @ORM\ManyToOne(targetEntity="MangoSylius\SyliusContactFormPlugin\Entity\ContactFormMessage")
     */
    protected $contactFormMessage;

    /**
     * @var AdminUser|null
     * @ORM\ManyToOne(targetEntity="Sylius\Component\Core\Model\AdminUser")
     */
    protected $sender;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return \DateTime|null
     */
    public function getSendTime(): ?\DateTime
    {
        return $this->sendTime;
    }

    /**
     * @param \DateTime|null $sendTime
     */
    public function setSendTime(?\DateTime $sendTime): void
    {
        $this->sendTime = $sendTime;
    }

    /**
     * @return ContactFormMessage|null
     */
    public function getContactFormMessage(): ?ContactFormMessage
    {
        return $this->contactFormMessage;
    }

    /**
     * @param ContactFormMessage|null $contactFormMessage
     */
    public function setContactFormMessage(?ContactFormMessage $contactFormMessage): void
    {
        $this->contactFormMessage = $contactFormMessage;
    }

    /**
     * @return AdminUser|null
     */
    public function getSender(): ?AdminUser
    {
        return $this->sender;
    }

    /**
     * @param AdminUser|null $sender
     */
    public function setSender(?AdminUser $sender): void
    {
        $this->sender = $sender;
    }
}
