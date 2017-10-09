<?php

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    const MSG_SUCCESS = 'success';
    const MSG_WARNING = 'warning';
    const MSG_DANGER = 'danger';

    public function performAccessCheck()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    protected function flashSuccess($message, array $parameters = [], $domain = null, $locale = null)
    {
        $this->flashMessage(self::MSG_SUCCESS, $message, $parameters, $domain, $locale);
    }

    protected function flashWarning($message, array $parameters = [], $domain = null, $locale = null)
    {
        $this->flashMessage(self::MSG_WARNING, $message, $parameters, $domain, $locale);
    }

    protected function flashDanger($message, array $parameters = [], $domain = null, $locale = null)
    {
        $this->flashMessage(self::MSG_DANGER, $message, $parameters, $domain, $locale);
    }

    protected function flashMessage( $type, string $message, array $parameters = [], $domain = null, $locale = null)
    {
        $this->addFlash($type, /** @Ignore */
            $this->get('translator')->trans($message, $parameters, $domain, $locale));
    }


}
