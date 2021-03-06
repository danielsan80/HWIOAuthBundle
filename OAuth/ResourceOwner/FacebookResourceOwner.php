<?php

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware.Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HWI\Bundle\OAuthBundle\OAuth\ResourceOwner;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * FacebookResourceOwner
 *
 * @author Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 */
class FacebookResourceOwner extends GenericOAuth2ResourceOwner
{
    /**
     * {@inheritDoc}
     */
    protected $paths = array(
        'identifier' => 'id',
        'nickname'   => 'username',
        'realname'   => 'name',
        'email'      => 'email',
        'profilepicture' => 'picture.data.url',
    );

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationUrl($redirectUri, array $extraParameters = array())
    {
        return parent::getAuthorizationUrl($redirectUri, array_merge(array('display' => $this->options['display']), $extraParameters));
    }

    /**
     * {@inheritDoc}
     */
    public function revokeToken($token)
    {
        $parameters = array(
            'client_id'     => $this->options['client_id'],
            'client_secret' => $this->options['client_secret'],
        );

        $response = $this->httpRequest($this->normalizeUrl($this->options['revoke_token_url'], array('token' => $token)), $parameters, array(), 'POST');
        $response = $this->getResponseContent($response);

        return 'true' == $response;
    }

    /**
     * {@inheritDoc}
     */
    protected function configureOptions(OptionsResolverInterface $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'authorization_url'   => 'https://www.facebook.com/dialog/oauth',
            'access_token_url'    => 'https://graph.facebook.com/oauth/access_token',
            'revoke_token_url'    => 'https://graph.facebook.com/me/permissions',
            'infos_url'           => 'https://graph.facebook.com/me?fields=id,name,first_name,last_name,username,email,picture',

            'use_commas_in_scope' => true,

            'display'             => null,
        ));

        $resolver->setAllowedValues(array(
            // @link https://developers.facebook.com/docs/reference/dialogs/#display
            'display' => array('page', 'popup', 'touch'),
        ));
    }
}
