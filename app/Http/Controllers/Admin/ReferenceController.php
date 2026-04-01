<?php namespace App\Http\Controllers\Admin;

class ReferenceController extends \Praust\App\Http\Controllers\Admin\PraustActionController
{
	public string $module_name = 'Referencje';


	protected function afterConstruct()
	{
		parent::afterConstruct();
		\Praust\App\Http\Controllers\Extends\PraustSEO::addSeoTab($this->availableTabsLang);
	}
}
