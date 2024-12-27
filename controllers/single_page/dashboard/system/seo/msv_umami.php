<?php

namespace Concrete\Package\MsvUmami\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class MsvUmami extends DashboardPageController  {

    public function view()
    {
        $packageConfig = $this->app->make(PackageService::class)->getByHandle('msv_umami')->getFileConfig();
        $this->set('script_url', h($packageConfig->get('tracking.scriptURL')));
        $this->set('website_id', h($packageConfig->get('tracking.websiteID')));

    }

    public function update_configuration() {
        if ($this->post() && $this->token->validate('msv_umami')) {

            $packageConfig = $this->app->make(PackageService::class)->getByHandle('msv_umami')->getFileConfig();
            $packageConfig->save('tracking.scriptURL', rtrim($this->post('script_url'),'/'));
            $packageConfig->save('tracking.websiteID', $this->post('website_id'));

            $this->flash('success', t('Umami settings saved, cache cleared'));

            $pageCache = PageCache::getLibrary();
            if (is_object($pageCache)) {
                $pageCache->flush();
            }

            return Redirect::to('/dashboard/system/seo/msv_umami')->send();
        }
    }

}
