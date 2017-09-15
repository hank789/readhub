<?php namespace App\Providers;
/**
 * @author: wanghui
 * @date: 2017/9/14 下午8:14
 * @email: wanghui@yonglibao.com
 */

use Publiux\laravelcdn\CdnServiceProvider as PubliuxCdnServiceProvider;

class CdnServiceProvider extends PubliuxCdnServiceProvider {

    public function register()
    {
        parent::register();
        $this->app->bind(
            'Publiux\laravelcdn\Contracts\ProviderFactoryInterface',
            'App\Third\Cdn\ProviderFactory'
        );
    }

}