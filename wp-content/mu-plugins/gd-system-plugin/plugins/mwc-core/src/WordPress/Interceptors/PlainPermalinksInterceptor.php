<?php

namespace GoDaddy\WordPress\MWC\Core\WordPress\Interceptors;

use Exception;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Core\Admin\Notices\Notice;
use GoDaddy\WordPress\MWC\Core\Admin\Notices\Notices;

/**
 * Handles WordPress permalinks structure behavior.
 */
class PlainPermalinksInterceptor extends AbstractInterceptor
{
    /**
     * Adds hooks.
     *
     * @throws Exception
     * @return void
     */
    public function addHooks()
    {
        Register::action()
            ->setGroup('admin_init')
            ->setHandler([$this, 'displayAdminNotice'])
            ->execute();
    }

    /**
     * Should load when plain permalinks are detected.
     *
     * @return bool
     */
    public static function shouldLoad() : bool
    {
        // Plain permalinks are set when the permalink_structure is an empty string.
        return get_option('permalink_structure') === '';
    }

    /**
     * Displays the plain permalinks admin notice.
     *
     * @return void
     */
    public function displayAdminNotice() : void
    {
        Notices::enqueueAdminNotice(Notice::getNewInstance()
            ->setId('mwc-plain-permalinks-enable')
            ->setType(Notice::TYPE_ERROR)
            ->setDismissible(false)
            ->setTitle($this->getPlainPermalinksAdminNoticeTitle())
            ->setContent($this->getPlainPermalinksAdminNoticeContent())
        );
    }

    /**
     * Gets the plain permalinks admin notice title.
     *
     * @return string
     */
    protected function getPlainPermalinksAdminNoticeTitle() : string
    {
        return __('Warning! Plain Permalinks Detected', 'mwc-core');
    }

    /**
     * Gets the plain permalinks admin notice content.
     *
     * @return string
     */
    protected function getPlainPermalinksAdminNoticeContent() : string
    {
        $body = esc_htmL__('Plain permalinks will cause issues on your Managed WooCommerce Store. You won\'t be able to properly use some features. Please update your permalinks as soon as possible to any other option than "plain".', 'mwc-core');
        $cta = '<a href="'.admin_url('options-permalink.php').'" class="button button-primary">'.esc_htmL__('Update permalinks', 'mwc-core').'</a>';

        return "<p>{$body}</p><p>{$cta}</p>";
    }
}
