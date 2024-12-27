<?php
namespace Concrete\Package\MsvUmami;

use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Concrete\Core\Page\Page;
use Concrete\Core\Package\Package;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Single as SinglePage;

class Controller extends Package
{
    protected $pkgHandle = 'msv_umami';
    protected $appVersionRequired = '8.4';
    protected $pkgVersion = '0.75';

    public function getPackageName()
    {
        return t('Analytics with Umami ');
    }

    public function getPackageDescription()
    {
        return t('Umami Analytics Integration');
    }

    public function install() {
        $pkg = parent::install();

        $single_page = SinglePage::add('/dashboard/system/seo/msv_umami', $pkg);

        if ($single_page) {
            $single_page->update(['cName' => t('Umami Analytics'), 'cDescription' => t('Umami Integration Settings')]);
        }
    }

    public function on_start()
    {
        Events::addListener('on_header_required_ready', function ($event) {

            $c = Page::getCurrentPage();

            if (is_object($c) && !$c->isEditMode() && !$c->isAdminArea()) {
                $packageConfig = $this->app->make(PackageService::class)->getByHandle('msv_umami')->getFileConfig();
                $scriptURL = $packageConfig->get('tracking.scriptURL');
                $websiteID = $packageConfig->get('tracking.websiteID');

                if (!$scriptURL) {
                    $scriptURL = 'https://cloud.umami.is/script.js';
                }

                $output = true;
                $loggedIn = false;

                if (!empty($websiteID)) {
                    $user = new User();

                    if ($user->isRegistered()) {
                        $loggedIn = true;
                        $repository = app()->make(GroupRepository::class);
                        $g = $repository->getGroupByName('Administrators');

                        if ($user->isSuperUser() || $user->inGroup($g)) {
                            $output = false;
                        }
                    }

                    $v = View::getInstance();
                    if ($output) {
                        $scriptOutputHeader = '<script defer src="'. $scriptURL .'" data-website-id="' . $websiteID . '" ' . ($loggedIn ? 'data-tag="signed-in"' : '') . '></script>';

                        $scriptOutputFooter = '<script>
                        (() => {
 
                        document.querySelectorAll(\'a[href*="/download_file/"]\').forEach(a => {
                        if (!a.getAttribute(\'data-umami-event\')) {
                            a.setAttribute(\'data-umami-event\', \'download-link-click\');    
                            a.setAttribute(\'data-umami-event-file\', a.title || a.innerHTML.trim()); 
                        }    
                        });
                        
                        document.querySelectorAll(\'a\').forEach(a => {
                        if (a.host && a.host !== window.location.host && !a.getAttribute(\'data-umami-event\')) {
                            a.setAttribute(\'data-umami-event\', \'outbound-link-click\');
                            a.setAttribute(\'data-umami-event-url\', a.href);
                        }
                        });
                            
                         })();
                                             
                        </script>';

                      $v->addHeaderItem($scriptOutputHeader);
                      $v->addFooterItem($scriptOutputFooter);

                    }
                }
            }
        });
    }
}
