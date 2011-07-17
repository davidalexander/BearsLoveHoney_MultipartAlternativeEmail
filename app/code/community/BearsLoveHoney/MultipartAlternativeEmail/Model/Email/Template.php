<?php
class BearsLoveHoney_MultipartAlternativeEmail_Model_Email_Template extends Mage_Core_Model_Email_Template
{

    /**
     * Send mail to recipient
     *
     * @param   array|string       $email        E-mail(s)
     * @param   array|string|null  $name         receiver name(s)
     * @param   array              $variables    template variables
     * @return  boolean
     **/
    public function send($email, $name = null, array $variables = array())
    {
        if (!$this->isValidForSend()) {
            Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
            return false;
        }

        $emails = array_values((array)$email);
        $names = is_array($name) ? $name : (array)$name;
        $names = array_values($names);
        foreach ($emails as $key => $email) {
            if (!isset($names[$key])) {
                $names[$key] = substr($email, 0, strpos($email, '@'));
            }
        }

        $variables['email'] = reset($emails);
        $variables['name'] = reset($names);

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $mail = $this->getMail();

        $setReturnPath = Mage::getStoreConfig(self::XML_PATH_SENDING_SET_RETURN_PATH);
        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $this->getSenderEmail();
                break;
            case 2:
                $returnPathEmail = Mage::getStoreConfig(self::XML_PATH_SENDING_RETURN_PATH_EMAIL);
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if ($returnPathEmail !== null) {
            $mailTransport = new Zend_Mail_Transport_Sendmail("-f".$returnPathEmail);
            Zend_Mail::setDefaultTransport($mailTransport);
        }

        foreach ($emails as $key => $email) {
            $mail->addTo($email, '=?utf-8?B?' . base64_encode($names[$key]) . '?=');
        }

        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($variables, true);

        /**
         * BearsLoveHoney Multipart Alternative Email - START
         * HTML should come first in the templates
         * then the boundary text '--EMAIL_BOUNDARY--'
         * then the plain text part
         */
        // if($this->isPlain()) {
        //     $mail->setBodyText($text);
        // } else {
        //     $mail->setBodyHTML($text);
        // }
        $boundary = Mage::getStoreConfig('multipartalternativeemail_options/configfields/multipartalternativeemail_boundary');
        $boundary_location = strpos($text, $boundary);
        if ($boundary_location) {
            $sHtml = substr($text, 0, strpos($text, $boundary));
            $sText = str_replace($boundary, '', substr($text, $boundary_location));
            $mail->setBodyHTML($sHtml);
            $mail->setBodyText($sText);
        } else {
            if($this->isPlain()) {
                $mail->setBodyText($text);
            } else {
                $mail->setBodyHTML($text);
            }
        }
        // BearsLoveHoney Multipart Alternative Email - END

        $mail->setSubject('=?utf-8?B?' . base64_encode($this->getProcessedTemplateSubject($variables)) . '?=');
        $mail->setFrom($this->getSenderEmail(), $this->getSenderName());

        try {
            $mail->send();
            $this->_mail = null;
        }
        catch (Exception $e) {
            $this->_mail = null;
            Mage::logException($e);
            return false;
        }

        return true;
    }

}