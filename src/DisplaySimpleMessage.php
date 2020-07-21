<?php

namespace Drupal\simple_message;

use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DisplaySimpleMessage implements EventSubscriberInterface {

  use StringTranslationTrait;

  private $messenger;
  private $adminContext;
  private $config;

  public function __construct(
    MessengerInterface $messenger,
    AdminContext $adminContext,
    ConfigFactoryInterface $configFactory
   ) {
    $this->messenger = $messenger;
    $this->adminContext = $adminContext;
    $this->config = $configFactory->get('simple_message.config');
  }

  public function displayMessage(GetResponseEvent $event) {
    if ($this->adminContext->isAdminRoute()) {
      return;
    }

    if ($message = $this->config->get('message')) {
      $this->messenger->addMessage($message);
    }
    return;

    $this->messenger->addMessage($this->t('This site is running on a <a href="@vagrant">Vagrant</a> server, deployed with <a href="@ansible">Ansible</a> and <a href="@ansistrano">Ansistrano</a>.', [
      '@ansible' => 'https://ansible.com',
      '@ansistrano' => 'https://ansistrano.com',
      '@vagrant' => 'https://vagrantup.com',
    ]));
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['displayMessage'];

    return $events;
  }

}
