<?php

namespace OpenSRS\mail;

use OpenSRS\Mail;
use OpenSRS\Exception;

/*
 *  Required object values:
 *  data - 
 */

class CreateDomain extends Mail
{
    private $_dataObject;
    private $_formatHolder = '';
    private $_osrsm;

    public $resultRaw;
    public $resultFormatted;
    public $resultSuccess;

    public function __construct($formatString, $dataObject)
    {
        parent::__construct();

        $this->_dataObject = $dataObject;
        $this->_formatHolder = $formatString;
        $this->_validateObject();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    // Validate the object
    private function _validateObject()
    {
        $allPassed = true;
        $compile = '';

        if (!isset($this->_dataObject->data->admin_username) || $this->_dataObject->data->admin_username == '') {
            if (APP_MAIL_USERNAME == '') {
                throw new Exception('oSRS-eMail Error - username is not defined.');
                $allPassed = false;
            } else {
                $this->_dataObject->data->admin_username = APP_MAIL_USERNAME;
            }
        }
        if (!isset($this->_dataObject->data->admin_password) || $this->_dataObject->data->admin_password == '') {
            if (APP_MAIL_PASSWORD == '') {
                throw new Exception('oSRS-eMail Error - password is not defined.');
                $allPassed = false;
            } else {
                $this->_dataObject->data->admin_password = APP_MAIL_PASSWORD;
            }
        }
        if (!isset($this->_dataObject->data->admin_domain) || $this->_dataObject->data->admin_domain == '') {
            if (APP_MAIL_DOMAIN == '') {
                throw new Exception('oSRS-eMail Error - authentication domain is not defined.');
                $allPassed = false;
            } else {
                $this->_dataObject->data->admin_domain = APP_MAIL_DOMAIN;
            }
        }

        // Command required values
        if (!isset($this->_dataObject->data->domain) || $this->_dataObject->data->domain == '') {
            throw new Exception('oSRS-eMail Error - newdomain is not defined.');
            $allPassed = false;
        } else {
            $compile .= ' domain="'.$this->_dataObject->data->domain.'"';
        }

        // Command optional values
        if (isset($this->_dataObject->data->timezone) && $this->_dataObject->data->timezone != '') {
            $compile .= ' timezone="'.$this->_dataObject->data->timezone.'"';
        }

        if (isset($this->_dataObject->data->language) && $this->_dataObject->data->language != '') {
            $compile .= ' language="'.$this->_dataObject->data->language.'"';
        }

        if (isset($this->_dataObject->data->filtermx) && $this->_dataObject->data->filtermx != '') {
            $compile .= ' filtermx="'.$this->_dataObject->data->filtermx.'"';
        }

        if (isset($this->_dataObject->data->spam_tag) && $this->_dataObject->data->spam_tag != '') {
            $compile .= ' spam_tag="'.$this->_dataObject->data->spam_tag.'"';
        }

        if (isset($this->_dataObject->data->spam_folder) && $this->_dataObject->data->spam_folder != '') {
            $compile .= ' spam_folder="'.$this->_dataObject->data->spam_folder.'"';
        }

        if (isset($this->_dataObject->data->spam_level) & $this->_dataObject->data->spam_level != '') {
            $compile .= ' spam_level="'.$this->_dataObject->data->spam_level.'"';
        }

        // Run the command
        if ($allPassed) {
            // Execute the command
            $this->_processRequest($compile);
        } else {
            throw new Exception('oSRS-eMail Error - Missing data.');
        }
    }

    // Post validation functions
    private function _processRequest($command = '')
    {
        $sequence = array(
            0 => 'ver ver="3.4"',
            1 => 'login user="'.$this->_dataObject->data->admin_username.'" domain="'.$this->_dataObject->data->admin_domain.'" password="'.$this->_dataObject->data->admin_password.'"',
            2 => 'create_domain'.$command,
            3 => 'quit',
        );
        $tucRes = $this->makeCall($sequence);
        $arrayResult = $this->parseResults($tucRes);

        // Results
        $this->resultFullRaw = $arrayResult;
        $this->resultRaw = $arrayResult;
        $this->resultFullFormatted = $this->convertArray2Formatted($this->_formatHolder, $this->resultFullRaw);
        $this->resultFormatted = $this->convertArray2Formatted($this->_formatHolder, $this->resultRaw);
    }
}