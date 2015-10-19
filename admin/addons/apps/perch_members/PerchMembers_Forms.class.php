<?php

class PerchMembers_Forms extends PerchAPI_Factory
{
    protected $table     = 'members_forms';
	protected $pk        = 'formID';
	protected $singular_classname = 'PerchMembers_Form';
	
	protected $default_sort_column = 'formTitle';
	
	public $static_fields   = array();



	public function find_or_create($key)
	{
		$f = $this->get_one_by('formKey', $key);

		if ($f) {
			return $f;
		}

		$f = $this->create(array(
			'formKey'=>$key,
			'formTitle'=>'Registration form',
			'formSettings'=>'',
		));

		return $f;
	}


}