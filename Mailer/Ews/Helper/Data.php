<?php

namespace Mailer\Ews\Helper;

use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Request\CreateItemType;
use \jamesiarmes\PhpEws\ArrayType\ArrayOfRecipientsType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use \jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use \jamesiarmes\PhpEws\Enumeration\MessageDispositionType;
use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use \jamesiarmes\PhpEws\Type\BodyType;
use \jamesiarmes\PhpEws\Type\EmailAddressType;
use \jamesiarmes\PhpEws\Type\MessageType;
use \jamesiarmes\PhpEws\Type\SingleRecipientType;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *  \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * get base url with store code
     */
    public function getBaseUrlWithStoreCode() {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key)
    {
        $result = $this->scopeConfig
                ->getValue(
                        $key,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        );
        return $result;
    }

    public function sendEWSMail($MailMessageObject) {

      $customRecipients = $MailMessageObject->getRecipients();
      $customeHeaders = $MailMessageObject->getHeaders();

            $host = $this->getConfig('ewssection/ewsgroup/ews_hostname');
            $username = $this->getConfig('ewssection/ewsgroup/ews_username');
            $password = $this->getConfig('ewssection/ewsgroup/ews_password');
            $version = $this->getConfig('ewssection/ewsgroup/ews_version');;
            $client = new Client($host, $username, $password, $version);

              // Build the request,
              $request = new CreateItemType();
              $request->Items = new NonEmptyArrayOfAllItemsType();
              // Save the message, but do not send it.
              $request->MessageDisposition = MessageDispositionType::SEND_ONLY;
              // Create the message.
              $message = new MessageType();
              $message->Subject = $MailMessageObject->getSubject();
              $message->ToRecipients = new ArrayOfRecipientsType();
              // Set the sender.
              $message->From = new SingleRecipientType();
              $message->From->Mailbox = new EmailAddressType();
              $message->From->Mailbox->EmailAddress = $username;
              // Set the recipient.
              $recipient = new EmailAddressType();
                                        $recipient->Name = $customeHeaders['To'][0];
                                        $recipient->EmailAddress = $customRecipients[0];
              $message->ToRecipients->Mailbox[] = $recipient;
              // Set the message body.
              $message->Body = new BodyType();
              $message->Body->BodyType = BodyTypeType::HTML;
              $message->Body->_ = <<<Body {$MailMessageObject->getBodyHtml()->getRawContent()} BODY;
              // Add the message to the request.
              $request->Items->Message[] = $message;
              $response = $client->CreateItem($request);
              // Iterate over the results, printing any error messages or message ids.
              $response_messages = $response->ResponseMessages->CreateItemResponseMessage;
              foreach ($response_messages as $response_message) {
              // Make sure the request succeeded.
              if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                  $code = $response_message->ResponseCode;
                  $message = $response_message->MessageText;
                  fwrite(STDERR, "Message failed to create with \"$code: $message\"\n");
                  continue;
              }
              // Iterate over the created messages, printing the id for each.
              foreach ($response_message->Items->Message as $item) {
                  $output = '- Id: ' . $item->ItemId->Id . "\n";
                  $output .= '- Change key: ' . $item->ItemId->ChangeKey . "\n";
                  fwrite(STDOUT, "Message created successfully.\n$output");
              }
              }

    }



}
