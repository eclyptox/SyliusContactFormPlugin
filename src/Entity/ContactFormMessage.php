<?php

declare(strict_types=1);

namespace MangoSylius\SyliusContactFormPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_contact_form")
 */
class ContactFormMessage implements ResourceInterface
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
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $senderName;
    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string|null
     * @Assert\NotBlank
     */
    protected $email;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     *
     * @var string|null
     */
    protected $phone;
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
     * @var Customer|null
     * @ORM\ManyToOne(targetEntity="Sylius\Component\Core\Model\Customer")
     */
    protected $customer;

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
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer|null $customer
     */
    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return string|null
     */
    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    /**
     * @param string|null $senderName
     */
    public function setSenderName(?string $senderName): void
    {
        $this->senderName = $senderName;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

}
