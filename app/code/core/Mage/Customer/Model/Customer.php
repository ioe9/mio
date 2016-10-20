<?php
class Mage_Customer_Model_Customer extends Mage_Core_Model_Abstract
{
    /**#@+
     * Configuration pathes for email templates and identities
     */
    const XML_PATH_REGISTER_EMAIL_TEMPLATE = 'customer/create_account/email_template';
    const XML_PATH_REGISTER_EMAIL_IDENTITY = 'customer/create_account/email_identity';
    const XML_PATH_REMIND_EMAIL_TEMPLATE = 'customer/password/remind_email_template';
    const XML_PATH_FORGOT_EMAIL_TEMPLATE = 'customer/password/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY = 'customer/password/forgot_email_identity';
    const XML_PATH_DEFAULT_EMAIL_DOMAIN         = 'customer/create_account/email_domain';
    const XML_PATH_IS_CONFIRM                   = 'customer/create_account/confirm';
    const XML_PATH_CONFIRM_EMAIL_TEMPLATE       = 'customer/create_account/email_confirmation_template';
    const XML_PATH_CONFIRMED_EMAIL_TEMPLATE     = 'customer/create_account/email_confirmed_template';
    const XML_PATH_GENERATE_HUMAN_FRIENDLY_ID   = 'customer/create_account/generate_human_friendly_id';
    /**#@-*/

    /**#@+
     * Codes of exceptions related to customer model
     */
    const EXCEPTION_EMAIL_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_EMAIL_OR_PASSWORD = 2;
    const EXCEPTION_EMAIL_EXISTS              = 3;
    const EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN = 4;
    /**#@-*/

    /**#@+
     * Subscriptions
     */
    const SUBSCRIBED_YES = 'yes';
    const SUBSCRIBED_NO  = 'no';
    /**#@-*/

    const CACHE_TAG = 'customer';

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'customer';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'customer';

    /**
     * List of errors
     *
     * @var array
     */
    protected $_errors = array();


    /**
     * Customer addresses array
     *
     * @var array
     * @deprecated after 1.4.0.0-rc1
     */
    protected $_addresses = null;

    /**
     * Customer addresses collection
     *
     * @var Mage_Customer_Model_Entity_Address_Collection
     */
    protected $_addressesCollection;

    /**
     * Is model deleteable
     *
     * @var boolean
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var boolean
     */
    protected $_isReadonly = false;

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Confirmation requirement flag
     *
     * @var boolean
     */
    private static $_isConfirmationRequired;

    /**
     * Initialize customer model
     */
    function _construct()
    {
        $this->_init('customer/customer');
    }

    /**
     * Retrieve customer sharing configuration model
     *
     * @return Mage_Customer_Model_Config_Share
     */
    public function getSharingConfig()
    {
        return Mage::getSingleton('customer/config_share');
    }

    /**
     * Authenticate customer
     *
     * @param  string $login
     * @param  string $password
     * @throws Mage_Core_Exception
     * @return true
     *
     */
    public function authenticate($login, $password)
    {
        $this->loadByEmail($login);
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }
        Mage::dispatchEvent('customer_customer_authenticated', array(
           'model'    => $this,
           'password' => $password,
        ));

        return true;
    }

    /**
     * Load customer by email
     *
     * @param   string $customerEmail
     * @return  Mage_Customer_Model_Customer
     */
    public function loadByEmail($customerEmail)
    {
        $this->_getResource()->loadByEmail($this, $customerEmail);
        return $this;
    }


    /**
     * Processing object before save data
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->getGroupId();
        return $this;
    }

    /**
     * Change customer password
     *
     * @param   string $newPassword
     * @return  Mage_Customer_Model_Customer
     */
    public function changePassword($newPassword)
    {
        $this->_getResource()->changePassword($this, $newPassword);
        return $this;
    }

    /**
     * Set plain and hashed password
     *
     * @param string $password
     * @return Mage_Customer_Model_Customer
     */
    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        $this->setPasswordConfirmation(null);
        return $this;
    }

    /**
     * Hash customer password
     *
     * @param   string $password
     * @param   int    $salt
     * @return  string
     */
    public function hashPassword($password, $salt = null)
    {
        return $this->_getHelper('core')
            ->getHash($password, !is_null($salt) ? $salt : Mage_Admin_Model_User::HASH_SALT_LENGTH);
    }

    /**
     * Get helper instance
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length = 8)
    {
        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
        return Mage::helper('core')->getRandomString($length, $chars);
    }

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        $hash = $this->getPasswordHash();
        if (!$hash) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }


    /**
     * Encrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function encryptPassword($password)
    {
        return Mage::helper('core')->encrypt($password);
    }

    /**
     * Decrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function decryptPassword($password)
    {
        return Mage::helper('core')->decrypt($password);
    }

   
    /**
     * Send email with new account related information
     *
     * @param string $type
     * @param string $backUrl
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '')
    {
        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        $this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            array('customer' => $this, 'back_url' => $backUrl));

        return $this;
    }

    /**
     * Check if accounts confirmation is required in config
     *
     * @return bool
     */
    public function isConfirmationRequired()
    {
        if ($this->canSkipConfirmation()) {
            return false;
        }
        if (self::$_isConfirmationRequired === null) {
           
            self::$_isConfirmationRequired = (bool)Mage::getStoreConfig(self::XML_PATH_IS_CONFIRM);
        }

        return self::$_isConfirmationRequired;
    }

    /**
     * Generate random confirmation key
     *
     * @return string
     */
    public function getRandomConfirmationKey()
    {
        return md5(uniqid());
    }

    /**
     * Send email with new customer password
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendPasswordReminderEmail()
    {
        $this->_sendEmailTemplate(self::XML_PATH_REMIND_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('customer' => $this));

        return $this;
    }

    /**
     * Send corresponding email template
     *
     * @param string $emailTemplate configuration path of email template
     * @param string $emailSender configuration path of email identity
     * @param array $templateParams
     * @return Mage_Customer_Model_Customer
     */
    protected function _sendEmailTemplate($template, $sender, $templateParams = array())
    {
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getEmail(), $this->getName());
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig($sender));
        $mailer->setTemplateId(Mage::getStoreConfig($template));
        $mailer->setTemplateParams($templateParams);
        $mailer->send();
        return $this;
    }

    /**
     * Send email with reset password confirmation link
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendPasswordResetConfirmationEmail()
    {
        $this->_sendEmailTemplate(self::XML_PATH_FORGOT_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('customer' => $this));

        return $this;
    }

    /**
     * Retrieve customer group identifier
     *
     * @return int
     */
    public function getGroupId()
    {
        if (!$this->hasData('group_id')) {
            $groupId = Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
            $this->setData('group_id', $groupId);
        }
        return $this->getData('group_id');
    }

    /**
     * Validate customer attribute values.
     * For existing customer password + confirmation will be validated only when password is set (i.e. its change is requested)
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();
       
        if (!Varien_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Varien_Validate::is($password , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The password cannot be empty.');
        }
        if (strlen($password) && !Varien_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = Mage::helper('customer')->__('The minimum password length is %s', 6);
        }
        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }

   
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Unset subscription
     *
     * @return Mage_Customer_Model_Customer
     */
    function unsetSubscription()
    {
        if (isset($this->_isSubscribed)) {
            unset($this->_isSubscribed);
        }
        return $this;
    }



    /**
     * Add error
     *
     * @return Mage_Customer_Model_Customer
     */
    function addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Retreive errors
     *
     * @return array
     */
    function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Reset errors array
     *
     * @return Mage_Customer_Model_Customer
     */
    function resetErrors()
    {
        $this->_errors = array();
        return $this;
    }

    /**
     * Print error
     *
     * @param $error
     * @param $line
     * @return boolean
     */
    function printError($error, $line = null)
    {
        if ($error == null) {
            return false;
        }

        $liStyle = 'background-color: #FDD; ';
        echo '<li style="' . $liStyle . '">';
        echo '<img src="' . Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif') . '" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>' . $line . '</b></small>';
        }
        echo '</li>';
    }

    /**
     * Prepare customer for delete
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Get customer created at date timestamp
     *
     * @return int|null
     */
    public function getCreatedAtTimestamp()
    {
        $date = $this->getCreatedAt();
        if ($date) {
            return Varien_Date::toTimestamp($date);
        }
        return null;
    }

    /**
     * Reset all model data
     *
     * @return Mage_Customer_Model_Customer
     */
    public function reset()
    {
        $this->setData(array());
        $this->setOrigData();
        $this->_attributes = null;

        return $this;
    }

    /**
     * Checks model is deleteable
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deleteable flag
     *
     * @param boolean $value
     * @return Mage_Customer_Model_Customer
     */
    public function setIsDeleteable($value)
    {
        $this->_isDeleteable = (bool)$value;
        return $this;
    }

    /**
     * Checks model is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is readonly flag
     *
     * @param boolean $value
     * @return Mage_Customer_Model_Customer
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (bool)$value;
        return $this;
    }

    /**
     * Check whether confirmation may be skipped when registering using certain email address
     *
     * @return bool
     */
    public function canSkipConfirmation()
    {
        return $this->getId() && $this->hasSkipConfirmationIfEmail()
            && strtolower($this->getSkipConfirmationIfEmail()) === strtolower($this->getEmail());
    }

    /**
     * Clone current object
     */
    public function __clone()
    {
        $newAddressCollection = $this->getPrimaryAddresses();
        $newAddressCollection = array_merge($newAddressCollection, $this->getAdditionalAddresses());
        $this->setId(null);
        $this->cleanAllAddresses();
        foreach ($newAddressCollection as $address) {
            $this->addAddress(clone $address);
        }
    }
    /**
     * Change reset password link token
     *
     * Stores new reset password link token
     *
     * @param string $newResetPasswordLinkToken
     * @return Mage_Customer_Model_Customer
     */
    public function changeResetPasswordLinkToken($newResetPasswordLinkToken) {
        if (!is_string($newResetPasswordLinkToken) || empty($newResetPasswordLinkToken)) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid password reset token.'),
                self::EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN);
        }
        $this->_getResource()->changeResetPasswordLinkToken($this, $newResetPasswordLinkToken);
        return $this;
    }

    /**
     * Check if current reset password link token is expired
     *
     * @return boolean
     */
    public function isResetPasswordLinkTokenExpired()
    {
        $resetPasswordLinkToken = $this->getRpToken();
        $resetPasswordLinkTokenCreatedAt = $this->getRpTokenCreatedAt();

        if (empty($resetPasswordLinkToken) || empty($resetPasswordLinkTokenCreatedAt)) {
            return true;
        }

        $tokenExpirationPeriod = Mage::helper('customer')->getResetPasswordLinkExpirationPeriod();

        $currentDate = Varien_Date::now();
        $currentTimestamp = Varien_Date::toTimestamp($currentDate);
        $tokenTimestamp = Varien_Date::toTimestamp($resetPasswordLinkTokenCreatedAt);
        if ($tokenTimestamp > $currentTimestamp) {
            return true;
        }

        $dayDifference = floor(($currentTimestamp - $tokenTimestamp) / (24 * 60 * 60));
        if ($dayDifference >= $tokenExpirationPeriod) {
            return true;
        }

        return false;
    }

    /**
     * Clean password's validation data (password, password_confirmation)
     *
     * @return Mage_Customer_Model_Customer
     */
    public function cleanPasswordsValidationData()
    {
        $this->setData('password', null);
        $this->setData('password_confirmation', null);
        return $this;
    }
}
