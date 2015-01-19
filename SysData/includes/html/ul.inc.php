<?php
class html_ul extends html_attributes
{
	private $lidata;
	private $lis;
	private $finalul;
	function __construct($lidata = array())
	{
		$catchclassul = new html_element('', '');
		$catchclassli = new html_element('', '');
		$catchclassstripeli = new html_element('', '');
		$this->li = $catchclassli;
		$this->stripeli = $catchclassstripeli;
		$this->ul = $catchclassul;
		$this->lidata = $lidata;
		$this->stripe = FALSE;
	}
	
	private function generatelis()
	{
		$i = 0;
		foreach($this->lidata as $currcontent)
		{
			$libuilding = new html_element('li', $currcontent['content']);
			foreach($currcontent['classes'] as $class)
			{
				$libuilding->addclass($class);
			}
			foreach($currcontent['attributes'] as $attname => $attvalue)
			{
				$libuilding->addattribute($attname, $attvalue);
			}
			
			if ($this->stripe)
			{
				if ($i%$this->numstripe==0)
				{
					foreach($this->stripeli->getclassarray() as $classtoadd)
					{
						$libuilding->addclass($classtoadd);
					}
				} else {
					foreach($this->li->getclassarray() as $classtoadd)
					{
						$libuilding->addclass($classtoadd);
					}
				}
			} else {
				foreach($this->li->getclassarray() as $classtoadd)
				{
					$libuilding->addclass($classtoadd);
				}
			}
			

//start new code
			if ($this->stripe)
			{
				if ($i%$this->numstripe==0)
				{
					foreach($this->stripeli->getattributearray() as $attributetoaddname => $attributetoaddvalue)
					{
						$libuilding->addattribute($attributetoaddname, $attributetoaddvalue);
					}
				} else {
					foreach($this->li->getattributearray() as $attributetoaddname => $attributetoaddvalue)
					{
						$libuilding->addattribute($attributetoaddname, $attributetoaddvalue);
					}
				}
			} else {
				foreach($this->li->getattributearray() as $attributetoaddname => $attributetoaddvalue)
				{
					$libuilding->addattribute($attributetoaddname, $attributetoaddvalue);
				}
			}
//end new code
			$this->lis[] = $libuilding;
			$i++;
		}
	}
	
	private function generateul()
	{
		if (isset($this->lis))
		{
			$ulbuilding = new html_element('ul', PHP_EOL.implode('', $this->lis));
		} else {
			$ulbuilding = new html_element('ul');
		}
		foreach($this->ul->getclassarray() as $classtoadd)
		{
			$ulbuilding->addclass($classtoadd);
		}
		foreach($this->ul->getattributearray() as $attributetoaddname => $attributetoaddvalue)
		{
			$ulbuilding->addattribute($attributetoaddname, $attributetoaddvalue);
		}
		$finalul = $ulbuilding;
		$this->finalul = $finalul;
	}
	
	public function addli($newli, $classes = array(), $attributes = array())
	{
		$this->lidata[] = array(
		 'content' => $newli
		,'classes' => $classes
		,'attributes' => $attributes
		);
	}
	
	public function removeli($licontent)
	{
		foreach($this->lidata as $linum => $arrayli)
		{
			if ($arrayli == $licontent)
			{
				unset($this->lidata[$linum]);
			}
		}
	}
	
	public function liarray()
	{
		return $lidata;
	}
	
	public function stripe($numstripe = 2, $tostripe = TRUE)
	{
		$this->stripe = $tostripe;
		$this->numstripe = $numstripe;
	}
	
	public function __tostring()
	{
		$this->generatelis();
		$this->generateul();
		return $this->finalul->__tostring();
	}
}
?>