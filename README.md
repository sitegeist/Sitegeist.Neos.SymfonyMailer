# Sitegeist.Neos.SymfonyMailer

Use the [Symfony Mailer Component](https://symfony.com/doc/current/mailer.html) from Neos CMS, especially together with Neos.Fusion.Form but it can also be used directly via PHP.

### Authors & Sponsors

* Martin Ficzel - ficzel@sitegeist.de

*The development and the public releases of this package is generously sponsored by our employer http://www.sitegeist.de.*

## Installation

Sitegeist.Neos.SymfonyMailer is available via packagist `composer require sitegeist/neos-symfonymailer`.
We use semantic-versioning, so every breaking change will increase the major version number.

## Neos.Fusion.Form Action `Sitegeist.Neos.SymfonyMailer:SendMailAction`

The `Sitegeist.Neos.SymfonyMailer:SendMailAction` allows to specify an email that will be sent after the runtime form
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
 - `attachments.[key]`: (`array{name:string, content:string}`) Create a file on the fly from `name` and `content`
 - `attachments.[key]`: (`iterable`) If iterables (Collections) are passed the files are attached recursively 
 - `testMode`: (`boolean`) Show debug information instead of actually sending the email.
 - `dsn`: (`string`) Use the specified mailer dsn instead of the global setting

Example:
```neosfusion
form = Neos.Fusion.Form:Runtime.RuntimeForm {
    # ... 
    actions {
        type = 'Sitegeist.Neos.SymfonyMailer:SendMail'
        options {
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

## Configuration

The package allows to configure the dsn used by the mailer globally via settings. You can use the dsn specification as
it is documented by symfony here: https://symfony.com/doc/current/mailer.html#transport-setup

```yaml
Sitegeist:
  Neos:
    SymfonyMailer:
      dsn: 'sendmail://default'
```

## Usage via PHP

The package is built upon the package Sitegeist.Neos.SymfonyMailer.Factories which can be used directly from php.
See https://github.com/sitegeist/Sitegeist.Neos.SymfonyMailer.Factories how this is done.

## Contribution

We will gladly accept contributions. Please send us pull requests.

## License

See [LICENSE](./LICENSE)
