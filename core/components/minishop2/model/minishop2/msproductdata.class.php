<?php
class msProductData extends xPDOSimpleObject {

	/**
	 * {@inheritdoc}
	 */
	public function save($cacheFlag= null) {
		$save = parent::save();

		$id = $this->get('id');
		$tags = $this->get('tags');

		if (!empty($tags)) {
			$table = $this->xpdo->getTableName('msProductTag');
			$tags_arr = explode(',', $tags);

			$sql = "DELETE FROM {$table} WHERE `product_id` = '{$id}' AND `tag` NOT IN ('$tags');";
			foreach ($tags_arr as $tag) {
				$sql .= "INSERT INTO {$table} (`product_id`,`tag`) VALUES ('{$id}','{$tag}') ON DUPLICATE KEY UPDATE `tag` = '{$tag}';";
			}

			$this->xpdo->exec($sql);
		}

		return $save;
	}

}