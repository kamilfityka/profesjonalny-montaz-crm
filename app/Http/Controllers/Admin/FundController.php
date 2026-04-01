<?php namespace App\Http\Controllers\Admin;

class FundController extends \Praust\App\Http\Controllers\Admin\PraustActionController
{
	public string $module_name = 'Fundusze';


	protected function afterConstruct()
	{
		parent::afterConstruct();
		\Praust\App\Http\Controllers\Extends\PraustSEO::addSeoTab($this->availableTabsLang);
	}
}
