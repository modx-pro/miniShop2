<?php

class msProductFileMultipleProcessor extends modProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        if (!$method = $this->getProperty('method', false)) {
            return $this->failure();
        }
        $ids = json_decode($this->getProperty('ids'), true);
        $type = $this->getProperty('type', false);
        if (empty($ids)) {
            return $this->success();
        }

        /** @var miniShop2 $miniShop2 */
        $miniShop2 = $this->modx->getService('miniShop2');

        foreach ($ids as $id) {
            /** @var modProcessorResponse $response */
            $arg = $type
                ? ['product_id' => $id]
                : ['id' => $id];
            $response = $miniShop2->runProcessor('mgr/gallery/' . $method, $arg);
            if ($response->isError()) {
                return $response->getResponse();
            }
        }

        return !empty($response)
            ? $response->getResponse()
            : $this->success();
    }
}

return 'msProductFileMultipleProcessor';
