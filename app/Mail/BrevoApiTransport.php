<?php

namespace App\Mail;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

class BrevoApiTransport implements TransportInterface
{
    private ?string $apiKey;

    private string $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    public function __construct(?string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        $email = $this->toEmail($message);
        $envelope = $envelope ?? Envelope::create($email);

        $payload = $this->buildPayload($email, $envelope);

        $response = $this->postJson($payload);

        $sentMessage = new SentMessage($message, $envelope);
        $sentMessage->setMessageId($response['messageId'] ?? uniqid());

        return $sentMessage;
    }

    public function __toString(): string
    {
        return 'brevo+api://';
    }

    private function toEmail(RawMessage $message): Email
    {
        if ($message instanceof Email) {
            return $message;
        }

        $email = new Email;
        $email->html($message->toString());

        return $email;
    }

    private function buildPayload(Email $email, Envelope $envelope): array
    {
        $from = $email->getFrom()[0] ?? $envelope->getSender();
        $payload = [
            'sender' => [
                'email' => $from->getAddress(),
                'name' => $from->getName() ?: null,
            ],
            'to' => $this->formatAddresses($email->getTo() ?: $envelope->getRecipients()),
            'subject' => $email->getSubject() ?? '(No subject)',
        ];

        if ($email->getCc()) {
            $payload['cc'] = $this->formatAddresses($email->getCc());
        }

        if ($email->getBcc()) {
            $payload['bcc'] = $this->formatAddresses($email->getBcc());
        }

        if ($email->getTextBody()) {
            $payload['textContent'] = $email->getTextBody();
        }

        if ($email->getHtmlBody()) {
            $payload['htmlContent'] = $email->getHtmlBody();
        }

        $replyTo = $email->getReplyTo();
        if ($replyTo) {
            $payload['replyTo'] = [
                'email' => $replyTo[0]->getAddress(),
                'name' => $replyTo[0]->getName() ?: null,
            ];
        }

        return $payload;
    }

    /**
     * @param  Address[]  $addresses
     * @return array<int, array<string, string|null>>
     */
    private function formatAddresses(array $addresses): array
    {
        return array_map(fn (Address $a) => [
            'email' => $a->getAddress(),
            'name' => $a->getName() ?: null,
        ], $addresses);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function postJson(array $payload): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Brevo API key is not configured.');
        }

        $ch = curl_init($this->apiUrl);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'content-type: application/json',
                'api-key: '.$this->apiKey,
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException('Brevo API cURL error: '.$error);
        }

        $data = json_decode($response ?: '{}', true);

        if (! is_array($data)) {
            throw new \RuntimeException('Brevo API returned an invalid JSON response.');
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            $message = $data['message'] ?? $data['error'] ?? 'Unknown Brevo API error';
            throw new \RuntimeException('Brevo API error (HTTP '.$httpCode.'): '.$message);
        }

        return $data;
    }
}
