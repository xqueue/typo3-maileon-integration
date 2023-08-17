<?php
namespace XQueue\Typo3MaileonIntegration\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Subscription
 */
class Subscription extends AbstractEntity
{
    /**
     * salutation
     *
     * @var string
     */
    protected $salutation = '';

    /**
     * firstname
     *
     * @var string
     */
    protected $firstname = '';

    /**
     * lastname
     *
     * @var string
     */
    protected $lastname = '';

    /**
     * organization
     *
     * @var string
     */
    protected $organization = '';

    /**
     * position
     *
     * @var string
     */
    protected $position = '';

    /**
     * subscriptionnumber
     *
     * @var string
     */
    protected $subscriptionnumber = '';

    /**
     * email
     *
     * @var string
     */
    protected $email = '';

    /**
     * approval
     *
     * @var bool
     */
    protected $approval = false;

    /**
     * privacy
     *
     * @var bool
     */
    protected $privacy = false;

    /**
     * Returns the salutation
     *
     * @return string $salutation
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * Sets the salutation
     *
     * @param string $salutation
     * @return void
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;
    }

    /**
     * Returns the firstname
     *
     * @return string $firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Sets the firstname
     *
     * @param string $firstname
     * @return void
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Returns the lastname
     *
     * @return string $lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Sets the lastname
     *
     * @param string $lastname
     * @return void
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Returns the organization
     *
     * @return string $organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Sets the organization
     *
     * @param string $organization
     * @return void
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * Returns the position
     *
     * @return string $position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the position
     *
     * @param string $position
     * @return void
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Returns the subscriptionnumber
     *
     * @return string $subscriptionnumber
     */
    public function getSubscriptionnumber()
    {
        return $this->subscriptionnumber;
    }

    /**
     * Sets the subscriptionnumber
     *
     * @param string $subscriptionnumber
     * @return void
     */
    public function setSubscriptionnumber($subscriptionnumber)
    {
        $this->subscriptionnumber = $subscriptionnumber;
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the approval
     *
     * @return bool $approval
     */
    public function getApproval()
    {
        return $this->approval;
    }

    /**
     * Sets the approval
     *
     * @param bool $approval
     * @return void
     */
    public function setApproval($approval)
    {
        $this->approval = $approval;
    }

    /**
     * Returns the boolean state of approval
     *
     * @return bool
     */
    public function isApproval()
    {
        return $this->approval;
    }

    /**
     * Returns the privacy
     *
     * @return bool $privacy
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * Sets the privacy
     *
     * @param bool $privacy
     * @return void
     */
    public function setPrivacy($privacy)
    {
        $this->privacy = $privacy;
    }

    /**
     * Returns the boolean state of privacy
     *
     * @return bool
     */
    public function isPrivacy()
    {
        return $this->privacy;
    }
}
