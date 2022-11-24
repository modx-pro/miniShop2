<?php

/** @var modX $modx */
$exists = $chunks = false;
$output = null;
$showDanger = false;
/** @var array $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $exists = $modx->getObject('transport.modTransportPackage', ['package_name' => 'pdoTools']);
        break;

    case xPDOTransport::ACTION_UPGRADE:
        $exists = $modx->getObject('transport.modTransportPackage', ['package_name' => 'pdoTools']);
        $miniShop2 = $this->modx->getService('miniShop2');
        if ($miniShop2->version < '4.0.0') {
            $showDanger = true;
        }

        if (!empty($options['attributes']['chunks'])) {
            $chunks = '<ul id="formCheckboxes" style="height:200px;overflow:auto;">';
            foreach ($options['attributes']['chunks'] as $v) {
                $chunks .= '
                <li>
                    <label>
                        <input type="checkbox" name="update_chunks[]" value="' . $v . '"> ' . $v . '
                    </label>
                </li>';
            }
            $chunks .= '</ul>';
        }
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

$output = '';
if ($showDanger) {
    $output = "
    <div style='background-color: #f8d7da; border-color:#f5c2c7; color: #842029;  padding:10px; border-radius:5px; margin-bottom: 10px;'>
    <h3>Внимание!</h3>
    <p> Установка текущей версии компонента <strong>{$options['signature']}</strong> может сломать Ваш сайт.</p>
    <p><strong>Пожалуйста, сделайте резервную копию перед установкой!</strong></p>
    </div>
";
}

if (!$exists) {
    switch ($modx->getOption('manager_language')) {
        case 'ru':
            $output .= 'Этот компонент требует <b>pdoTools</b> для быстрой работы сниппетов.<br/>Он будет автоматически скачан и установлен.';
            break;
        default:
            $output .= 'This component requires <b>pdoTools</b> for fast work of snippets.<br/>It will be automatically downloaded and installed?';
    }
}

if ($chunks) {
    if (!$exists) {
        $output .= '<br/><br/>';
    }

    switch ($modx->getOption('manager_language')) {
        case 'ru':
            $output .= 'Выберите чанки, которые нужно <b>перезаписать</b>:<br/>
                <small>
                    <a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = true;});">отметить все</a> |
                    <a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = false;});">cнять отметки</a>
                </small>
            ';
            break;
        default:
            $output .= 'Select chunks, which need to <b>overwrite</b>:<br/>
                <small>
                    <a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = true;});">select all</a> |
                    <a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = false;});">deselect all</a>
                </small>
            ';
    }

    $output .= $chunks;
}

return $output;
