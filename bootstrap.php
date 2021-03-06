<?php

namespace core;

use core\classes\AdminPassport;
use dashboard\classes\DashboardUI;
use wula\cms\CmfModule;
use wulaphp\app\App;
use wulaphp\auth\Passport;
use wulaphp\io\Response;
use wulaphp\router\Router;

class CoreModule extends CmfModule {
	public function getName() {
		return '内核';
	}

	public function getDescription() {
		return 'wulacms内容管理框架内核模块';
	}

	public function getHomePageURL() {
		return 'https://www.wulacms.com/modules/core';
	}

	/**
	 * @param Passport $passport
	 *
	 * @filter passport\newAdminPassport
	 *
	 * @return Passport
	 */
	public static function createAdminPassport($passport) {
		if ($passport instanceof Passport) {
			$passport = new AdminPassport();
		}

		return $passport;
	}

	/**
	 * @param DashboardUI $ui
	 *
	 * @bind dashboard\initUI
	 */
	public static function initDashboard(DashboardUI $ui) {
		$passport = whoami('admin');
		if ($passport->cando('m:system')) {
			$system = $ui->getMenu('system');

			$account       = $system->getMenu('account');
			$account->name = '账户';
			$account->pos  = 1;
			$account->icon = 'fa fa-id-card-o';

			if ($passport->cando('m:system/module')) {
				$module       = $system->getMenu('module');
				$module->name = '模块';
				$module->url  = App::hash('~core/module/installed');
				$module->icon = 'fa fa-cubes';
				$module->pos  = 10;
				$module->badge = '5';

				$m            = $module->getMenu('installed');
				$m->name      = '已安装模块';
				$m->url       = $module->url;
				$m->iconStyle = 'color:green';
				$m->icon      = 'fa fa-cubes';
				$m->pos       = 1;

				$m       = $module->getMenu('new');
				$m->name = '未安装模块';
				$m->icon = 'fa fa-cubes';
				$m->pos  = 2;

				$m            = $module->getMenu('up');
				$m->name      = '可升级模块';
				$m->badge     = '5';
				$m->iconStyle = 'color:orange';
				$m->icon      = 'fa fa-cubes';
				$m->pos       = 3;

			}
		}
	}

	public function getVersionList() {
		$v['1.0.0'] = '';

		return $v;
	}

	/**
	 * @bind router\beforeDispatch
	 */
	public static function beforeDispatch($router) {
		if (!WULACMF_INSTALLED) {
			$installURL = App::url('core/install');
			if (WWWROOT_DIR != '/') {
				$regURL = substr($installURL, strlen(WWWROOT_DIR) - 1);
			} else {
				$regURL = $installURL;
			}
			$regURL = ltrim($regURL, '/');
			if (!Router::is($regURL . '(/.*)?', true)) {
				Response::redirect($installURL);
			}
		}
	}
}

App::register(new CoreModule());