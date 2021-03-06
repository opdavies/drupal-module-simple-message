<?php

declare(strict_types=1);

namespace Drupal\simple_message;

use Drupal\Core\Config\Config;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DisplaySimpleMessage implements EventSubscriberInterface {

  use StringTranslationTrait;

  private MessengerInterface $messenger;
  private AdminContext $adminContext;
  private Config $config;

  public function __construct(
    MessengerInterface $messenger,
    AdminContext $adminContext,
    ConfigFactoryInterface $configFactory
  ) {
    $this->messenger = $messenger;
    $this->adminContext = $adminContext;
    $this->config = $configFactory->get('simple_message.config');
  }

  public function displayMessage(GetResponseEvent $event): void {
    if ($this->adminContext->isAdminRoute()) {
      return;
    }

    if ($message = $this->config->get('message')) {
      $this->messenger->addMessage($this->t($message));
    }
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['displayMessage'];

    return $events;
  }

}
