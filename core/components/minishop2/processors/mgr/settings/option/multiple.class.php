<?php

class msTypeMultipleProcessor extends modProcessor
{


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$method = $this->getProperty('method', false)) {
            return $this->failure();
        }

        /** @var miniShop2 $miniShop2 */
        $miniShop2 = $this->modx->getService('miniShop2');

        if ($method == 'assign') {
            $categories = json_decode($this->getProperty('categories'), true);
            $options = json_decode($this->getProperty('options'), true);
            if ($categories && $options) {
                foreach ($options as $option) {
                    foreach ($categories as $category) {
                        $miniShop2->runProcessor('mgr/settings/option/assign', array(
                            'option_id' => $option,
                            'category_id' => $category,
                        ));
                    }
                }
            }
        } elseif ($ids = json_decode($this->getProperty('ids'), true)) {
            foreach ($ids as $id) {
                /** @var modProcessorResponse $response */
                $response = $miniShop2->runProcessor('mgr/settings/option/' . $method, array('id' => $id));
                if ($response->isError()) {
                    return $response->getResponse();
                }
            }
        }

        return $this->success();
    }

}

return 'msTypeMultipleProcessor';