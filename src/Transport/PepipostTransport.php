<?php
namespace Pepipost\LaravelPepipostDriver\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Mail\Transport\Transport;
use Swift_Attachment;
use Swift_Image;
use Swift_Mime_SimpleMessage;
use Swift_MimePart;

class PepipostTransport extends Transport
{
    

    const MAXIMUM_FILE_SIZE = 7340032;
    const BASE_URL = 'https://api.pepipost.com/v2/sendEmail';

    /**
     * @var Client
     */
    private $client;
    private $attachments;
    private $numberOfRecipients;
    private $apiKey;
    private $endpoint;

    public function __construct(ClientInterface $client, $api_key, $endpoint = null)
    {
        $this->client = $client;
        $this->apiKey = $api_key;
        $this->endpoint = isset($endpoint) ? $endpoint : self::BASE_URL;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        echo $message;
        $this->beforeSendPerformed($message);

        $data = [
            'personalizations' => $this->getPersonalizations($message),
            'from'             => $this->getFrom($message),
            'subject'          => $message->getSubject(),
        ];

        if ($contents = $this->getContents($message)) {
            $data['content'] = $contents;
        }

        if ($reply_to = $this->getReplyTo($message)) {
            $data['replyToId'] = $reply_to;
        }

        $attachments = $this->getAttachments($message);
        if (count($attachments) > 0) {
            $data['attachments'] = $attachments;
        }

       $data = $this->setParameters($message, $data);

        $payload = [
            'headers' => [
		'api_key'      => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ];

        $response = $this->post($payload);

        if (method_exists($response, 'getHeaderLine')) {
            $message->getHeaders()->addTextHeader('X-Message-Id', $response->getHeaderLine('X-Message-Id'));
        }

        if (is_callable([$this, "sendPerformed"])) {
            $this->sendPerformed($message);
        }

        if (is_callable([$this, "numberOfRecipients"])) {
            return $this->numberOfRecipients ?: $this->numberOfRecipients($message);
        }
        return $response;
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
    private function getPersonalizations(Swift_Mime_SimpleMessage $message)
    {
        $setter = function (array $addresses) {
            $recipients = [];
            foreach ($addresses as $email => $name) {
                $address = [];
                $address['email'] = $email;
                if ($name) {
                    $address['name'] = $name;
                }
                $recipients[] = $address;
            }
            return $recipients;
        };
	$personalization= $this->getTo($message);
		
        return $personalization;
    }


     /**
     * Get From Addresses.
     *
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
    private function getTo(Swift_Mime_SimpleMessage $message)
    {

        if ($message->getTo()) {
	    $toarray = [];
            foreach ($message->getTo() as $email => $name) {
		$recipient = [];
		$recipient['recipient'] = $email;
		 if ($cc = $message->getCc()) {
          		 $recipient['recipient_cc'] = $this->getCC($message);
       		 }
		 if ($bcc = $message->getBcc()) {
          		 $recipient['recipient_bcc'] = $this->getBCC($message);
		}
                $toarray[] = $recipient;
        	}
	
   }
        return $toarray;
}
      /**
     * Get From Addresses.
     *
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
    private function getCC(Swift_Mime_SimpleMessage $message)
    {
        $ccarray = array();
        if ($message->getCc()) {
            foreach ($message->getCc() as $email => $name) {
                $ccarray[] = $email;
            }
        }
        return $ccarray;
    }
    
    /**
     * Get From Addresses.
     *
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
    private function getBCC(Swift_Mime_SimpleMessage $message)
    {
        $bccarray = array();
        if ($message->getBcc()) {
            foreach ($message->getBcc() as $email => $name) {
                $bccarray[] = $email;
            }
        }
        return $bccarray;
    }


    /**
     * Get From Addresses.
     *
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
    private function getFrom(Swift_Mime_SimpleMessage $message)
    {
        if ($message->getFrom()) {
            foreach ($message->getFrom() as $email => $name) {
                return ['fromEmail' => $email, 'fromName' => $name];
            }
        }
        return [];
    }

    /**
     * Get ReplyTo Addresses.
     *
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
    private function getReplyTo(Swift_Mime_SimpleMessage $message)
    {
        if ($message->getReplyTo()) {
            foreach ($message->getReplyTo() as $email => $name) {
                //return ['email' => $email, 'name' => $name];
                return $email;
            }
        }
        return null;
    }

    /**
     * Get contents.
     *
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
   private function getContents(Swift_Mime_SimpleMessage $message)
    {
        return $message->getBody();
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
    private function getAttachments(Swift_Mime_SimpleMessage $message)
    {
        $attachments = [];
        foreach ($message->getChildren() as $attachment) {
            if ((!$attachment instanceof Swift_Attachment && !$attachment instanceof Swift_Image)
                || !strlen($attachment->getBody()) > self::MAXIMUM_FILE_SIZE
            ) {
                continue;
            }
            $attachments[] = [
                'fileContent'     => base64_encode($attachment->getBody()),
                'fileName'    => $attachment->getFilename(),
            ];
        }
        return $this->attachments = $attachments;
    }

    /**
     * Set Request Body Parameters
     *
     * @param Swift_Mime_SimpleMessage $message
     * @param array $data
     * @return array
     * @throws \Exception
     */
    protected function setParameters(Swift_Mime_SimpleMessage $message, $data)
    {
        $this->numberOfRecipients = 0;
       $smtp_api = [];
       foreach ($message->getChildren() as $attachment) {
            if (!$attachment instanceof Swift_Image) {
                continue;
            }
            $smtp_api = $attachment->getBody();
        }
        foreach ($smtp_api as $key => $val) {

            switch ($key) {

                case 'api_key':
                    $this->apiKey = $val;
                    continue 2;

                case 'personalizations':
                    $this->setPersonalizations($data, $val);
                    continue 2;

                case 'attachments':
                    $val = array_merge($this->attachments, $val);
                    break;
                    }
                   

           array_set($data, $key, $val);
        }
        return $data;
    }

    private function setPersonalizations(&$data, $personalizations)
    {
	echo "in personalisations";
        foreach ($personalizations as $index => $params) {
		echo $index;
            foreach ($params as $key => $val) {
		echo $key;
		echo $val;
                if (in_array($key, ['recipient', 'recipient_cc', 'recipient_bcc'])) {
		      array_set($data, 'personalizations.' . $key, [$val]);
		      echo "data value";
		      echo $key;
		      print_r($data);
		      echo "data end";
                    ++$this->numberOfRecipients;
                } else {
		      array_set($data, 'personalizations.' . $key, [$val]);
                }
            }
        }
    }

    /**
     * @param $payload
     * @return Response
     */
    private function post($payload)
    {
        return $this->client->post($this->endpoint, $payload);
    }
}

