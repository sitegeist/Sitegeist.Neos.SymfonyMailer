<?php
declare(strict_types=1);

namespace Sitegeist\Neos\SymfonyMailer\Action;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionResponse;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Fusion\Form\Runtime\Domain\ActionInterface;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Psr\Http\Message\UploadedFileInterface;
use Sitegeist\Neos\SymfonyMailer\Domain\MailerFactory;
use Sitegeist\Neos\SymfonyMailer\Domain\MailFactory;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailActionImplementation extends AbstractFusionObject implements ActionInterface
{
    #[Flow\Inject]
    protected MailerFactory $mailerFactory;

    #[Flow\Inject]
    protected MailFactory $mailFactory;

    protected MailerInterface $mailer;
    protected Email $mail;
    protected bool $testMode = false;

    public function evaluate()
    {
        $dsn = $this->fusionValue('dsn') ?: null;
        $this->mailer = $this->mailerFactory->createMailer($dsn);

        $subject = $this->fusionValue('subject') ?: null;
        $text = $this->fusionValue('text') ?: null;
        $html = $this->fusionValue('html') ?: null;

        $recipientAddress = $this->fusionValue('recipientAddress') ?: null;
        $recipientName = $this->fusionValue('recipientName') ?: null;

        $senderAddress = $this->fusionValue('senderAddress') ?: null;
        $senderName = $this->fusionValue('senderName') ?: null;

        $replyToAddress = $this->fusionValue('replyToAddress') ?: null;
        $carbonCopyAddress = $this->fusionValue('carbonCopyAddress') ?: null;
        $blindCarbonCopyAddress = $this->fusionValue('blindCarbonCopyAddress') ?: null;

        $attachments = $this->fusionValue('attachments') ?: null;

        $this->mail = $this->mailFactory->createMail(
            $subject,
            is_array($recipientAddress) ? $recipientAddress : new Address($recipientAddress, $recipientName),
            is_array($senderAddress) ? $senderAddress : new Address($senderAddress, $senderName),
            $text,
            $html,
            $replyToAddress,
            $carbonCopyAddress,
            $blindCarbonCopyAddress,
            $attachments
        );

        $this->testMode = $this->fusionValue('testMode') ?: false;

        return $this;
    }

    public function perform(): ?ActionResponse
    {

        if ($this->testMode === true) {
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

        $this->mailer->send($this->mail);
        return null;
    }
}
