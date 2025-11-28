<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Auth\Google\BaksDevAuthGoogleBundle;
use BaksDev\Auth\Google\Type\Event\AccountGoogleEventType;
use BaksDev\Auth\Google\Type\Event\AccountGoogleEventUid;
use BaksDev\Auth\Google\Type\Identifier\AccountGoogleIdentifier;
use BaksDev\Auth\Google\Type\Identifier\AccountGoogleIdentifierType;
use Symfony\Config\DoctrineConfig;

return static function(ContainerConfigurator $container, DoctrineConfig $doctrine) {

    $doctrine->dbal()->type(AccountGoogleEventUid::TYPE)->class(AccountGoogleEventType::class);
    $doctrine->dbal()->type(AccountGoogleIdentifier::TYPE)->class(AccountGoogleIdentifierType::class);

    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);

    $emDefault->mapping('auth-google')
        ->type('attribute')
        ->dir(BaksDevAuthGoogleBundle::PATH.'Entity')
        ->isBundle(false)
        ->prefix(BaksDevAuthGoogleBundle::NAMESPACE.'\\Entity')
        ->alias('auth-google');
};
