<?php

class Hobis_Api_Mail extends Zend_Mail
{
    const CATEGORY_LOW	= 'low';
	const CATEGORY_MED	= 'med';
	const CATEGORY_HIGH	= 'high';

    // Useful when parsing email contents (THESE ARE CASE SENSITIVE)
    //  Colons were used b/c there were instances where the same word may be used multiple times, i.e. "status"
    //  Would have been cool if we had something more secure to anchor to, like "Dsn Code"
    const RESPONSE_RECIPIENT    = 'Final-Recipient:';
    const RESPONSE_STATUS       = 'Status:';

    const SOFT_BOUNCE_THRESHOLD = 10;

    /**
     * Flag to determine whether we want to check dsn history for an email address to determine if we
     * want to attempt sending email to it
     *
     * @var bool
     */
    protected $checkDsnHistory;

    /**
     * Invalid Receipients from the last send()
     *
     * @var array
     */
    protected $invalidRecipients = array();

    /**
     * Setter for checkDsnHistory flag
     *
     * @param bool
     */
    public function setCheckDsnHistory($checkDsnHistory)
    {
        $this->checkDsnHistory = $checkDsnHistory;
    }

    /**
     * Getter for checkDsnHistory flag
     *
     * @return bool
     */
    public function getCheckDsnHistory()
    {
        return ((bool) $this->checkDsnHistory === true) ? true : false;
    }

    /**
     * Get Invalid Recipients
     *  returns a list of invalid receipts from the last send()
     *
     * @return array
     */
    public function getInvalidRecipients()
    {
        return $this->invalidRecipients;
    }

    /**
     * Overriding Zend's send() so we can implement dsn history tracking
     *
     * @param object - Only keeping this to maintain parent sig, Hobis_Api_Mail_Package::factory will set the transport singleton
     * @return object
     */
    public function send($transport = null)
    {
        if (false === (parent::$_defaultTransport instanceof Zend_Mail_Transport_Abstract)) {
            throw new Hobis_Api_Exception(sprintf('Invalid defaultTransport: %s', serialize(parent::$_defaultTransport)));
        }

        // NOTE: This is here for future reference, disabling for now
        // If flag is true, check dsn track to make sure we can send this email
        /**
        if ($this->getCheckDsnHistory() === true) {

            // Future
            $validRecipients = Some_App_Class::validateRecipients($this->getRecipients());
            $this->invalidRecipients = array_diff($this->getRecipients(), $validRecipients);

            if (!Hobis_Api_Array_Package::populated($validRecipients)) {
                throw new Hobis_Api_Exception('No valid recipients available');
            }

            // Need to be able to remap email address back to their intended delivery option
            //  i.e. "to" vs "cc" vs "bcc"
            // Headers stores this info
            $headers = $this->getHeaders();

            // Reset recipients
            // This method will flush email addresses despite their mapping
            // i.e. all email addresses in "to", "cc", "bcc"
            $this->clearRecipients();

            // Valid recipients may not match the original recipients b/c some may be invalid
            //  So we need to reset the list to only the valid recipients
            foreach ($validRecipients as $validRecipient) {

                if ((Hobis_Api_Array_Package::populatedKey('To', $headers)) &&
                    (in_array($validRecipient, $headers['To'], true))) {
                    $this->addTo($validRecipient);
                }

                if ((Hobis_Api_Array_Package::populatedKey('Cc', $headers)) &&
                    (in_array($validRecipient, $headers['Cc'], true))) {
                    $this->addCc($validRecipient);
                }

                if ((Hobis_Api_Array_Package::populatedKey('Bcc', $headers)) &&
                    (in_array($validRecipient, $headers['Bcc'], true))) {
                    $this->addBcc($validRecipient);
                }
            }
        }
         *
         */

        parent::$_defaultTransport->send($this);

        return $this;
    }
}