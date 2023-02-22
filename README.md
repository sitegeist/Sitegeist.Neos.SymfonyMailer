# Sitegeist.Neos.SymfonyMailer

Use the symfony mailer from Neos CMS, especially together with Neos.Fusion.Form but it can also be used directly via PHP.

### Authors & Sponsors

* Martin Ficzel - ficzel@sitegeist.de

*The development and the public releases of this package is generously sponsored by our employer http://www.sitegeist.de.*

## Installation

Sitegeist.Neos.SymfonyMailer is available via packagist `composer require sitegeist/neos-symfonymailer`.
We use semantic-versioning, so every breaking change will increase the major version number.

## Fusion `Sitegeist.Neos.SymfonyMailer:MailAction`

The prototype `Sitegeist.Neos.SymfonyMailer:MailAction` allows to specify an email that will be sent after the runtime form
was successfully submitted.

Options:
 - `senderAddress`: (`string`|`array`)
 - `senderName`: (`string`)
 - `recipientAddress`: (`string`|`array`)
 - `recipientName`: (`string`)
 - `replyToAddress`: (`string`|`array`)
 - `carbonCopyAddress`: (`string`|`array`)
 - `blindCarbonCopyAddress`: (`string`|`array`)
 - `subject`: (`string`) The email subject
 - `text`: (`string`) The plaintext content
 - `html`: (`string`) The html content (if `text` and `html` are defined a multipart email is created)
 - `attachments.[key]`: (string) The string is treated as a path where the attachment is read from.
 - `attachments.[key]`: (`UploadedFileInterface`|`FlowResource`) The uploaded file or resource is added to the mail
 - `attachments.[key]`: (`array`) Create a file on the fly from `name` and `content`
 - `testMode`: (`boolean`) Show debug information instead of actually sending the email.
 - `dsn`: (`string`) Use the specified mailer dsn instead of the global setting

Example:
```neosfusion
form = Neos.Fusion.Form:Runtime.RuntimeForm {
    # ... 
    actions {
        email = Sitegeist.Neos.SymfonyMailer:MailAction {
            senderAddress = ${q(node).property('mailFrom')}
            recipientAddress = ${q(node).property('mailTo')}

            subject = ${q(node).property('mailSubject')}
            text = afx`Thank you {data.firstName} {data.lastName} from {data.city}, {data.street}`
            html = afx`<h1>Thank you {data.firstName} {data.lastName}</h1><p>from {data.city}, {data.street}</p>`

            attachments {
                upload = ${data.file}
                resource = "resource://Form.Test/Private/Fusion/Test.translation.csv"
                jsonFile {
                    content = ${Json.stringify(data)}
                    name = 'data.json'
                }
            }
        }
    }
}
```
## Usage via PHP

The package provides two factory classes to create Mailers and Emails easily.

- `Sitegeist\Neos\SymfonyMailer\Domain\MailerFactory` with the method `createMailer` that will create a mailer for the specified dsn or the configured default dsn.
- `Sitegeist\Neos\SymfonyMailer\Domain\MailFactory` with the method `createMail` that will create a mail based on the provided arguments.

Example:
```php
use Sitegeist\Neos\SymfonyMailer\Domain\MailerFactory;
use Sitegeist\Neos\SymfonyMailer\Domain\MailFactor;

class MailController
{
    #[Flow\Inject]
    protected MailerFactory $mailerFactory;

    #[Flow\Inject]
    protected MailFactory $mailFactory;

    public function exampleAction()
    {
        $mailer = $this->mailerFactory->createMailer();
        $mail = $this->mailFactory->createMail(
            $subject,
            $recipient,
            $sender,
            $text,
            $html
        );
        $mailer->send($mail);
    }
```

## Configuration

The package allows to configure the dsn used by the mailer globally via settings. You can use the dsn specification as
it is documented by symfony here: https://symfony.com/doc/current/mailer.html#transport-setup

```yaml
Sitegeist:
  Neos:
    SymfonyMailer:
      dsn: 'sendmail://default'
```

## Contribution

We will gladly accept contributions. Please send us pull requests.

## License

See [LICENSE](./LICENSE)
