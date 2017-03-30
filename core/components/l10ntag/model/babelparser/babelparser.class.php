<?php

class BabelParser {
	public $TAGSTART = "[@";
	public $TAGEND = "]";
	public function getTagStart ($input, $offset) {
		return strpos($input, $this->TAGSTART, $offset);
	}

	public function getTagEnd ($input, $offset) {
		$tagEnd = strpos($input, $this->TAGEND, $offset) + $this->tagEndLen;
		while ($tagEnd && $input[$tagEnd+1] == $this->TAGEND) {
			$tagEnd++;
		}
		return $tagEnd;
	}

	public function __construct (&$modx, $options) {
		$this->xpdo = $modx;
		$this->tagStartLen = strlen($this->TAGSTART);
		$this->tagEndLen = strlen($this->TAGEND);
		$this->cultureKey = $this->xpdo->getOption('cultureKey');
	}
	public function parseString($input){
		$var = &$input;
		$tagStart = strpos($var,$this->TAGSTART);
		$tagEnd=0;
		$tag='';
		while($tagStart!=false){
			$tagEnd=$this->getTagEnd($var,$tagStart);
			if(!$tagEnd){
				$var=substr($var,$tagStart);
				break;
			}
			$tag = substr($var,$tagStart,$tagEnd - $tagStart);
			$newTag = $this->parseTag($tag);
			$var = str_replace($tag, $newTag , $var);
			$tagStart = $this->getTagStart($var,$tagEnd-(strlen($tag)-strlen($newTag)));
		}
		return $var;
	}
	public function parseTag($tag){
		$tagCulture = substr($tag,$this->tagStartLen,strpos($tag, " ")-$this->tagStartLen);
		$cultureLen = strlen($tagCulture);
		if($tagCulture == $this->cultureKey){
			return trim(substr($tag,$this->tagStartLen + $cultureLen,strlen($tag)-$this->tagStartLen - $this->tagEndLen-$cultureLen));
		}
		else{
			return "";
		};
	}
}
