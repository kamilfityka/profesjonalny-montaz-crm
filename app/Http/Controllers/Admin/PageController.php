<?php namespace App\Http\Controllers\Admin;

class PageController extends \Praust\App\Http\Controllers\Admin\PraustPageController
{
	public string $module_name = 'Strony';

	protected function afterConstruct()
	{
		parent::afterConstruct();
		\Praust\App\Http\Controllers\Extends\PraustSEO::addSeoTab($this->availableTabsLang);
	}
}
