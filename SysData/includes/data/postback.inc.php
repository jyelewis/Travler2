<?php
class data_postback
{
	protected $hiddendata;
	function __construct($formname)
	{
		$this->formname = $formname;
		
		if (isset($_REQUEST['formname']) && $_REQUEST['formname'] == $formname)
		{
			$this->ispostback = TRUE;
			$this->hiddendata['formname'] = $_REQUEST['formname'];
			$this->hiddendata['formid'] = $_REQUEST['formid'];
			$this->getalldata();
		} else {
			$this->ispostback = FALSE;
		}
	}
	
	protected function getalldata()
	{
		$datarecovername = 'htmlformdata:'.$this->hiddendata['formname'].';formid:'.$this->hiddendata['formid'];
                $postbacktied = new data_tied(false, $datarecovername);
		$this->olddata = $postbacktied->data;
		$this->data = $this->addnewvalues();
	}
	
	protected function addnewvalues()
	{
	if (isset($this->olddata['inputdata'])){
		foreach($this->olddata['inputdata'] as $currname => $currdata)
		{
			$htmlname = $currdata['name'];	
			if ($currdata['type'] == 'checkbox')
			{
				if (isset($_REQUEST[$htmlname]))
				{
					$currdata['attributes']['checked'] = 'checked';
				} else {
					unset($currdata['attributes']['checked']);
				}
				$rebuilddata[$currname] = $currdata;
				$isrebuilt = TRUE;
			}
			
			if ($currdata['type'] == 'radio')
			{
				$groupname = $currdata['group'];
				if (isset($_REQUEST[$groupname]))
				{
					if ($_REQUEST[$groupname] == $currdata['attributes']['value'])
					{
						$currdata['attributes']['checked'] = 'checked';
					} else {
						unset($currdata['attributes']['checked']);
					}
				} else {
					unset($currdata['attributes']['checked']);
				}
				$rebuilddata[$currname] = $currdata;
				$isrebuilt = TRUE;
			}
			
			if (isset($_REQUEST[$htmlname]) && $currdata['type'] == 'textarea')
			{
				$newvalue = $_REQUEST[$htmlname];
				$currdata['textarea']->setvalue($newvalue);
				$isrebuilt = TRUE;
			}
			
			if (isset($_REQUEST[$htmlname]) && $currdata['type'] == 'select')
			{
				$newvalue = $_REQUEST[$htmlname];
				foreach($currdata['selectoptions'] as $selectoptionnum => $selectoption)
				{
					if (isset($currdata['selectoptions'][$selectoptionnum]['selected']))
					{
						unset($currdata['selectoptions'][$selectoptionnum]['selected']);
					}
					
					if ($currdata['selectoptions'][$selectoptionnum]['htmlvalue'] == $newvalue)
					{
						$currdata['selectoptions'][$selectoptionnum]['selected'] = TRUE;
						$currdata['attributes']['value'] = $currdata['selectoptions'][$selectoptionnum]['value'];
					}
				}
				$rebuilddata[$currname] = $currdata;
				$isrebuilt = TRUE;
			}
				
			if(isset($_REQUEST[$htmlname]) && !isset($isrebuilt))
			{	
				$newvalue = $_REQUEST[$htmlname];
				$oldvalue = $currdata['attributes']['value'];
				$currdata['attributes']['value'] = $newvalue;
				$currdata['oldvalue'] = $oldvalue;
				$rebuilddata[$currname] = $currdata;
			} else {
				$oldvalue = $currdata['attributes']['value'];
				$currdata['attributes']['value'] = $oldvalue;
				$currdata['oldvalue'] = $oldvalue;
				$rebuilddata[$currname] = $currdata;
			}
			unset($isrebuilt);
		}
		$rebuildarray['inputdata'] = $rebuilddata;
		$rebuildarray['formdata'] = $this->olddata['formdata'];
		$rebuildarray['formobject'] = $this->olddata['formobject'];
		return $rebuildarray;
	} else {
		header('location: '.$_SERVER['PHP_SELF']);
	}
	}
	
	public function inputisset($input)
	{
		if (isset($this->data['inputdata'][$input]['name']))
		{
			$htmlname = $this->data['inputdata'][$input]['name'];
			if (isset($_REQUEST[$htmlname]))
			{
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}


	public function getvalue($name)
	{
		if (isset($this->data['inputdata'][$name]['type']) && $this->data['inputdata'][$name]['type'] == 'checkbox')
		{
			if (isset($this->data['inputdata'][$name]['attributes']['checked']))
			{
				return TRUE;
			} else {
				return FALSE;
			}
		} elseif (isset($this->data['inputdata'][$name]['type']) && $this->data['inputdata'][$name]['type'] == 'textarea')
		{
			return $this->data['inputdata'][$name]['textarea']->textarea->getcontent();
		} else {
			if ($this->inputisset($name))
			{
				return $this->data['inputdata'][$name]['attributes']['value'];
			} else {
				return FALSE;
			}
		}
	}
	
	public function getgroupvalue($name)
	{
		foreach($this->data['inputdata'] as $input)
		{
			if ($input['groupname'] == $name)
			{
				$groupname = $input['group'];
				if (isset($_REQUEST[$groupname]))
				{
					return $_REQUEST[$groupname];
				}
			}
		}
	}
	
	public function setvalue($name, $value)
	{
		if ($this->data['inputdata'][$name]['type'] == 'checkbox' || $this->data['inputdata'][$name]['type'] == 'radio')
		{
			if ($value) {
				//for unseting all other set checked buttons when doing a radion button
				if ($this->data['inputdata'][$name]['type'] == 'radio')
				{
					$groupnum = $this->data['inputdata'][$name]['group'];
					foreach($this->data['inputdata'] as $currdataname => $currdata)
					{
						if ($currdata['group'] == $groupnum)
						{
							if (isset($currdata['attributes']))
							{
								unset($this->data['inputdata'][$currdataname]['attributes']['checked']);
							}
						}
					}
				}
				//end unset code
				$this->data['inputdata'][$name]['attributes']['checked'] = 'checked';
			} else {
				unset($this->data['inputdata'][$name]['attributes']['checked']);
			}
		} elseif ($this->data['inputdata'][$name]['type'] == 'select') {
			foreach($this->data['inputdata'][$name]['selectoptions'] as $selectoptionnum => $selectoption)
			{
				if (isset($this->data['inputdata'][$name]['selectoptions'][$selectoptionnum]['selected']))
				{
					unset($this->data['inputdata'][$name]['selectoptions'][$selectoptionnum]['selected']);
				}
				
				if ($this->data['inputdata'][$name]['selectoptions'][$selectoptionnum]['value'] == $value)
				{
					$this->data['inputdata'][$name]['selectoptions'][$selectoptionnum]['selected'] = TRUE;
					$this->data['inputdata'][$name]['attributes']['value'] = $value;
				}
			}
		} elseif($this->data['inputdata'][$name]['type'] == 'textarea') {
			$this->data['inputdata'][$name]['textarea']->setvalue($value);
		} else {
			$this->data['inputdata'][$name]['attributes']['value'] = $value;
		}
	}
	//start input editing new
	public function addinputclass($input, $class)
	{
		if(isset($this->data['inputdata'][$input]))
		{
			foreach($this->data['inputdata'][$input]['classes'] as $currclassnum => $currclass)
			{
				if ($currclass == $class){
					unset ($this->data['inputdata'][$input]['classes'][$currclassnum]);
				}
			}
			$this->data['inputdata'][$input]['classes'][] = $class;
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function removeinputclass($input, $class)
	{
		if(isset($this->data['inputdata'][$input]))
		{
			foreach($this->data['inputdata'][$input]['classes'] as $testclassnum => $testclass)
			{
				if ($testclass == $class)
				{
					$tounset = $testclassnum;
				}
			}
			if (isset($tounset))
			{
				unset($this->data['inputdata'][$input]['classes'][$tounset]);
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	
	public function addinputattribute($input, $attribute, $value)
	{
		if(isset($this->data['inputdata'][$input]))
		{
			$this->data['inputdata'][$input]['attributes'][$attribute] = $value;
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function removeinputattribute($input, $attribute)
	{
		if(isset($this->data['inputdata'][$input]))
		{
			if(isset($this->data['inputdata'][$input]['attributes'][$attribute]))
			{
				unset($this->data['inputdata'][$input]['attributes'][$attribute]);
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	//end input editing new
	public function checked($name, $tocheck)
	{
		if($tocheck)
		{
			$this->data['inputdata'][$name]['attributes']['checked'] = 'checked';
		} else {
			unset($this->data['inputdata'][$name]['attributes']['checked']);
		}
	}
}
?>
