<?php

namespace GoDaddy\WordPress\MWC\Core\Features\EmailNotifications\DataStores;

use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\EmailNotifications\Contracts\EmailNotificationContract;
use GoDaddy\WordPress\MWC\Core\Features\EmailNotifications\Contracts\EmailNotificationDataStoreContract;
use GoDaddy\WordPress\MWC\Core\Features\EmailNotifications\DataStores\WooCommerce\EmailTemplateDataStore;
use GoDaddy\WordPress\MWC\Core\Features\EmailNotifications\Exceptions\EmailNotificationNotAvailableException;
use GoDaddy\WordPress\MWC\Core\Features\EmailNotifications\Exceptions\EmailNotificationNotFoundException;
use GoDaddy\WordPress\MWC\Core\Features\EmailNotifications\Exceptions\EmailTemplateNotFoundException;
use GoDaddy\WordPress\MWC\Core\Features\EmailNotifications\Exceptions\InvalidClassNameException;
use InvalidArgumentException;

/**
 * Data store for email notifications.
 */
class EmailNotificationDataStore implements EmailNotificationDataStoreContract
{
    use CanGetNewInstanceTrait;

    /**
     * Gets an email notification with the given ID.
     *
     * @param string $id
     * @return EmailNotificationContract
     * @throws EmailNotificationNotFoundException|EmailTemplateNotFoundException|InvalidClassNameException|EmailNotificationNotAvailableException
     */
    public function read(string $id) : EmailNotificationContract
    {
        /** @var EmailNotificationContract $notification */
        $notification = OptionsSettingsDataStore::getNewInstance($this->getOptionNameTemplate($id))->read($this->getNotificationInstance($id));

        return $this->readContentAndTemplate($notification);
    }

    /**
     * Reads the notification's content & template properties.
     *
     * @param EmailNotificationContract $notification
     * @return EmailNotificationContract
     * @throws EmailTemplateNotFoundException|InvalidClassNameException
     */
    protected function readContentAndTemplate(EmailNotificationContract $notification) : EmailNotificationContract
    {
        // we have a single email template available and all email notifications use it
        $notification->setTemplate(EmailTemplateDataStore::getNewInstance()->read('default'));

        // we need to set the template first, so that calling setContent() can also set the inner content of the template
        $notification->setContent(EmailContentDataStore::getNewInstance()->read($notification->getId()));

        return $notification;
    }

    /**
     * Gets the notification instance from the given ID.
     *
     * @param string $id
     * @return EmailNotificationContract
     * @throws EmailNotificationNotFoundException|InvalidClassNameException|EmailNotificationNotAvailableException
     */
    protected function getNotificationInstance(string $id) : EmailNotificationContract
    {
        if (! $class = Configuration::get("email_notifications.notifications.{$id}.class")) {
            throw new EmailNotificationNotFoundException(sprintf(
                __('No email notification found with the ID %s.', 'mwc-core'),
                $id
            ));
        }

        if (! is_a($class, EmailNotificationContract::class, true)) {
            throw new InvalidClassNameException(sprintf(
                __('The class for %s must implement the EmailNotificationContract interface', 'mwc-core'),
                $id
            ));
        }

        /* @var EmailNotificationContract $notificationInstance */
        $notificationInstance = new $class();

        if (! $notificationInstance->isAvailable()) {
            throw new EmailNotificationNotAvailableException(sprintf(
                __('The email notification with ID %s is not available on this site', 'mwc-core'),
                $id
            ));
        }

        return $notificationInstance->setId($id);
    }

    /**
     * Saves the given email notification.
     *
     * @param EmailNotificationContract $notification
     * @return EmailNotificationContract
     * @throws InvalidArgumentException
     */
    public function save(EmailNotificationContract $notification) : EmailNotificationContract
    {
        OptionsSettingsDataStore::getNewInstance($this->getOptionNameTemplate($notification->getId()))->save($notification);

        $this->saveContentAndTemplate($notification);

        return $notification;
    }

    /**
     * Saves the notification content and template.
     *
     * @param EmailNotificationContract $notification
     */
    protected function saveContentAndTemplate(EmailNotificationContract $notification)
    {
        if ($content = $notification->getContent()) {
            EmailContentDataStore::getNewInstance()->save($content);
        }

        if ($template = $notification->getTemplate()) {
            EmailTemplateDataStore::getNewInstance()->save($template);
        }
    }

    /**
     * Gets the option name template.
     *
     * @param string $notificationId
     * @return string
     */
    protected function getOptionNameTemplate(string $notificationId) : string
    {
        return 'mwc_'.$notificationId.'_email_notification_'.OptionsSettingsDataStore::SETTING_ID_MERGE_TAG;
    }

    /**
     * Returns an array of all available EmailNotificationContract objects.
     *
     * @return EmailNotificationContract[]
     * @throws EmailNotificationNotFoundException
     * @throws EmailTemplateNotFoundException
     * @throws InvalidClassNameException
     */
    public function all() : array
    {
        $newNotifications = [];

        foreach (array_keys(Configuration::get('email_notifications.notifications')) as $notification) {
            try {
                $newNotifications[] = $this->read($notification);
            } catch (EmailNotificationNotAvailableException $exception) {
                // the notification is not available, skip it
            }
        }

        return $newNotifications;
    }
}
