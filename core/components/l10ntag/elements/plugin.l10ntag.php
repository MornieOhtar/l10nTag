<?php
 $babelparser = $modx->getService('babelparser', 'BabelParser', $modx->getOption('core_path') . 'components/l10ntag/model/babelparser/');

if (!($babelparser instanceof BabelParser)) return;

$output = &$modx->resource->_output;
$output = $babelparser->parseString($output);