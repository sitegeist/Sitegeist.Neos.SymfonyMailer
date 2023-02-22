<?php
declare(strict_types=1);

namespace Sitegeist\Neos\SymfonyMailer\Domain;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionResponse;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Utility\MediaTypes;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailFactory
{
    /**
     * @param string $subject
     * @param Address[]|Address|string $from
     * @param Address[]|Address|string $to
     * @param string|null $text
     * @param string|null $html
     * @param Address[]|Address|string|null $replyTo
     * @param Address[]|Address|string|null $cc
     * @param Address[]|Address|string|null $bcc
     * @param array|null $attachments
     * @return Email
     */
    public function createMail(
        string $subject,
        array|Address|string $to,
        array|Address|string $from,
        string $text = null,
        string $html = null,
        array|Address|string $replyTo = null,
        array|Address|string $cc = null,
        array|Address|string $bcc = null,
        array $attachments = null
    ): Email
    {
        $mail = new Email();

        $mail
            ->from($from )
            ->subject($subject);

        if (is_array($to)) {
            $mail->to(...$to);
        } else {
            $mail->to($to);
        }

        if ($replyTo) {
            if (is_array($replyTo)) {
                $mail->replyTo(...$replyTo);
            } else {
                $mail->replyTo($replyTo);
            }
        }

        if ($cc) {
            if (is_array($cc)) {
                $mail->cc(...$cc);
            } else {
                $mail->cc($cc);
            }
        }

        if ($bcc) {
            if (is_array($bcc)) {
                $mail->bcc(...$bcc);
            } else {
                $mail->bcc($bcc);
            }
        }

        if ($text) {
            $mail->text($text);
        }

        if ($html) {
            $mail->html($html);
        }

        if (is_array($attachments)) {
            $this->addAttachmentsToMail($mail, $attachments);
        }

        return $mail;
    }

    protected function addAttachmentsToMail(Email $mail, array $attachments): void
    {
        if (is_array($attachments)) {
            foreach ($attachments as $attachment) {
                if (is_string($attachment)) {
                    $mail->attachFromPath($attachment);
                } elseif (is_object($attachment) && ($attachment instanceof UploadedFileInterface)) {
                    $mail->attach($attachment->getStream()->getContents(), $attachment->getClientFilename(), $attachment->getClientMediaType());
                } elseif (is_object($attachment) && ($attachment instanceof PersistentResource)) {
                    $stream = $attachment->getStream();
                    if (!is_bool($stream)) {
                        $content = stream_get_contents($stream);
                        if (!is_bool($content)) {
                            $mail->attach($content, $attachment->getFilename(), $attachment->getMediaType());
                        }
                    }
                } elseif (is_array($attachment) && isset($attachment['content']) && isset($attachment['name'])) {
                    $content = $attachment['content'];
                    $name = $attachment['name'];
                    $type =  $attachment['type'] ?? MediaTypes::getMediaTypeFromFilename($name);
                    $mail->attach($content, $name, $type);
                } elseif (is_array($attachment)) {
                    $this->addAttachmentsToMail($mail, $attachment);
                }
            }
        }
    }
}
