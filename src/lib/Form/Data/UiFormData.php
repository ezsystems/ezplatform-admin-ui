<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class UiFormData
{
    /**
     * @Assert\Valid
     * @var mixed
     */
    protected $data;

    /** @var string */
    protected $onSuccessRedirectionUrl;

    /** @var string */
    protected $onFailureRedirectionUrl;

    /**
     * @param mixed $data
     * @param string|null $onSuccessRedirectionUrl
     * @param string|null $onFailureRedirectionUrl
     */
    public function __construct($data, ?string $onSuccessRedirectionUrl, ?string $onFailureRedirectionUrl)
    {
        $this->data = $data;
        $this->onSuccessRedirectionUrl = $onSuccessRedirectionUrl;
        $this->onFailureRedirectionUrl = $onFailureRedirectionUrl;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getOnSuccessRedirectionUrl(): ?string
    {
        return $this->onSuccessRedirectionUrl;
    }

    /**
     * @param string $onSuccessRedirectionUrl
     */
    public function setOnSuccessRedirectionUrl(string $onSuccessRedirectionUrl)
    {
        $this->onSuccessRedirectionUrl = $onSuccessRedirectionUrl;
    }

    /**
     * @return string
     */
    public function getOnFailureRedirectionUrl(): ?string
    {
        return $this->onFailureRedirectionUrl;
    }

    /**
     * @param string $onFailureRedirectionUrl
     */
    public function setOnFailureRedirectionUrl(string $onFailureRedirectionUrl)
    {
        $this->onFailureRedirectionUrl = $onFailureRedirectionUrl;
    }
}
