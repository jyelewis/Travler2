<?php
class html_element extends html_attributes
{
	protected $content;
	protected $tagname;
	protected $attributes;
	
	public function __construct($tagname, $content = '', $attributes = array(), $classes = array())
	{
		$this->tagname = $tagname;
		$this->content = $content;
		$this->classes = $classes;
		$this->attributes = $attributes;
	}
	
	public function htmlfirsttag()
	{
		return '<'.$this->tagname.$this->getattributesource().'>';
	}
	
	public function htmlendtag()
	{
		return '</'.$this->tagname.'>';
	}
	
	public function getsource()
	{
		$content = $this->htmlfirsttag().PHP_EOL.
			$this->content
		.$this->htmlendtag();
		return $content."\n";
	}
	
	public function setcontent($content)
	{
		$this->content = $content;
	}
	
	public function getcontent()
	{
		return $this->content;
	}
	
	public function __tostring()
	{
		return $this->getsource();
	}
}
?>
