<?php

class msOrderMultipleProcessor extends modProcessor
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
        if (empty($ids)) {
            return $this->success();
        }

        foreach ($ids as $id) {
            $this->modx->error->reset();
            /** @var modProcessorResponse $response */
            $response = $this->modx->runProcessor(
                'mgr/orders/' . $method,
                array('id' => $id),
                array('processors_path' => MODX_CORE_PATH . 'components/minishop2/processors/')
            );
            if ($response->isError()) {
                return $response->getResponse();
            }
        }

        return $this->success();
    }

}

return 'msOrderMultipleProcessor';