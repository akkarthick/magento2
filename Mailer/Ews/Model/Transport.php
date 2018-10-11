<?php
/**
 * Mail Transport
 */
namespace Mailer\Ews\Model;

class Transport extends \Zend_Mail_Transport_Smtp implements \Magento\Framework\Mail\TransportInterface
{

  /**
   * @var \Magento\Framework\Mail\MessageInterface
   */
  protected $_message;
  protected $_helper;

  /**
   * @param MessageInterface $message
   * @param null $parameters
   * @throws \InvalidArgumentException
   */
  public function __construct(\Magento\Framework\Mail\MessageInterface $message,\Mailer\Ews\Helper\Data $_helper,$parameters = null)
  {
      if (!$message instanceof \Zend_Mail) {
          throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
      }
      parent::__construct($parameters);
      $this->_message = $message;
      $this->_helper=$_helper;
  }


    /**
     * Send a mail using this transport
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        try {
              $this->_helper->sendEWSMail($this->_message);
            //parent::send($this->_message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}
