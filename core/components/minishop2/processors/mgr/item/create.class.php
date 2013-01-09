<?php
/**
 * Create an Item
 * 
 * @package minishop2
 * @subpackage processors
 */
class miniShop2ItemCreateProcessor extends modObjectCreateProcessor {
	public $classKey = 'miniShop2Item';
	public $languageTopics = array('minishop2');
	public $permission = 'new_document';
	
	public function beforeSet() {
		$alreadyExists = $this->modx->getObject('miniShop2Item',array(
			'name' => $this->getProperty('name'),
		));
		if ($alreadyExists) {
			$this->modx->error->addField('name',$this->modx->lexicon('minishop2.item_err_ae'));
		}
		return !$this->hasErrors();
	}
	
}

return 'miniShop2ItemCreateProcessor';