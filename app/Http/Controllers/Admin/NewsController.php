<?php namespace App\Http\Controllers\Admin;

class NewsController extends \Praust\App\Http\Controllers\Admin\PraustActionCategoryController
{
	public string $module_name = 'Blog';


	protected function afterConstruct()
	{
		parent::afterConstruct();
		\Praust\App\Http\Controllers\Extends\PraustSEO::addSeoTab($this->availableTabsLang);
	}
}
