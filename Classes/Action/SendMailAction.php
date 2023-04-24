<?php

declare(strict_types=1);

namespace Sitegeist\Neos\SymfonyMailer\Action;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionResponse;
use Neos\Fusion\Form\Runtime\Action\AbstractAction;
use Sitegeist\Neos\SymfonyMailer\Domain\MailerFactory;
use Sitegeist\Neos\SymfonyMailer\Domain\MailFactory;
use Symfony\Component\Mime\Address;

class SendMailAction extends AbstractAction
{
    #[Flow\Inject]
    protected MailerFactory $mailerFactory;

    #[Flow\Inject]
    protected MailFactory $mailFactory;


    /**
     * @return ActionResponse|null
     */
    public function perform(): ?ActionResponse
    {
        $recipientAddress = $this->options['recipientAddress'] ?? null;
        $recipientName = $this->options['recipientName'] ?? '';

        $senderAddress = $this->options['senderAddress'] ?? null;
        $senderName = $this->options['senderName'] ?? '';

        $this->mail = $this->mailFactory->createMail(
            $this->options['subject'],
            is_array($recipientAddress) ? $recipientAddress : new Address($recipientAddress, $recipientName),
            is_array($senderAddress) ? $senderAddress : new Address($senderAddress, $senderName),
            $this->options['text'] ?? null,
            $this->options['html'] ?? null,
            $this->options['replyToAddress'] ?? null,
            $this->options['carbonCopyAddress'] ?? null,
            $this->options['blindCarbonCopyAddress'] ?? null,
            $this->options['attachments'] ?? null
        );

        if ($this->options['testMode'] === true) {
            $response = new ActionResponse();
            $response->setContent(
                \Neos\Flow\var_dump(
                    [
                        'sender' => $this->mail->getFrom(),
                        'recipients' => $this->mail->getTo(),
                        'replyToAddress' => $this->mail->getReplyTo(),
                        'carbonCopyAddress' => $this->mail->getCc(),
                        'blindCarbonCopyAddress' => $this->mail->getBcc(),
                        'text' => $this->mail->getTextBody(),
                        'html' => $this->mail->getHtmlBody(),
                        'attachments' => $this->mail->getAttachments()
                    ],
                    'E-Mail "' . $this->mail->getSubject() . '"',
                    true
                )
            );
            return $response;
        }

        $mailer = $this->mailerFactory->createMailer($this->options['dsn'] ?? null);
        $mailer->send($this->mail);
        return null;
    }
}
