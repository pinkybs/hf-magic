<?php


class Scaffolding_Core
{
	// Model object that needs to be scaffolded.
	protected $model;
	protected $session;
	protected $db;
	protected $inspector;
	//protected $contr;

	protected $columns;
	// from class User_Model:
	protected $tablename=''; // users
	protected $prettyname=''; // User
	protected $prettyplural=''; // Users
	protected $tableplurar=''; // users
	protected $base; //base url

	function __construct(&$model)
	{
		$this->model=&$model;
		//	$this->contr=&$controller;
		$t=Model_Inspect::byModel(get_class($model));
		//$t=new Model_Inspector($model);
		$this->db=new Database();
		$this->session=new Session();
		$this->base=url::base(TRUE).url::current().'/';
		$this->tablename = inflector::plural(strtolower(substr(get_class($this->model),0,strpos(get_class($this->model),'_Model'))));
		$this->prettyname = ucfirst(inflector::singular($this->tablename));
		$this->prettyplural = inflector::plural($this->prettyname);
		$this->tableplural=strtolower($this->prettyplural);
		
		$this->inspector=new Model_Inspector($model);
		$this->columns=$this->inspector->inspect();
	}

	public function scaffold($args)
	{
	if(count($args)==0)
		return $this->_index();
	switch($args[0])
	{
	case 'manage':
		return $this->_manage();
	break;
	case 'addnew':
		return $this->_addnew($args);
	break;
	case 'edit':
		return $this->_edit($args);
	break;
	case 'delete':
		return $this->_delete($args);
	break;
	case 'relationship':
		return $this->_relationship($args,false,false);
	break;
	case 'relationshiphabtm':
		return $this->_relationship($args);
	break;
	case 'relationshiphabtmdelete':
		return $this->_relationshipdelete($args,TRUE);
	break;
	case 'relationshipdelete':
		return $this->_relationshipdelete($args);
	break;
	default:
		return $this->_index();
	break;
	}

	}

	protected function _index()
	{
		$ret='<h1>'.$this->prettyplural.' management</h1><p> Welcome to managment page for '.$this->tablename.' table.Choose your action:</p>
			<p>
			
<ul>
<li><a href="'.$this->base.'manage">Manage '.$this->tableplural.'</a></li>
<li><a href="'.$this->base.'addnew">Add new '.inflector::singular($this->tableplural).'</a></li>
			
';

		return $ret;

	}

	protected function getOption($col,$key)
	{
		if(!array_key_exists('options',$col))
			return false;
		if(!array_key_exists($key,$col['options']))
			return false;
		return $col['options'][$key];
	}

	protected function _manage()
	{
		// Initial
		$this->base=explode('manage',$this->base);
		$this->base=$this->base[0];
		$db = new Database;
		$count = $db->count_records($this->tablename);
		$pag = new Pagination(array(
		    // 'base_url'    => 'welcome/pagination_example/page/', // base_url will default to current uri
		    'uri_segment'    => 'manage', // pass a string as uri_segment to trigger former 'label' functionality
		    'total_items'    => $count, // use db count query here of course
		    'items_per_page' => 50, // it may be handy to set defaults for stuff like this in config/pagination.php
		    'style'          => 'digg' // pick one from: classic (default), digg, extended, punbb, or add your own!
	    ));
		
		$ret='<h1>'.$this->prettyplural.' management</h1><a href="'.$this->base.'">Back to the management page.</a><br/><p> Here you can view and edit existing '.$this->tableplural.'.</p>';

		$ret.=(string) $pag;
		
		$ret.='<table border="1"><tr>';

		//Columns headers
		$columns=array();
		foreach($this->columns as $column)
		{
			if($this->getOption($column,'hide'))
			{
				continue;
			}
			$ret.='<th>'.$column['name'].'</th>';
			$columns[]=$column[0];
		}
		$ret.='<th>Edit</th><th>Delete</th></tr>';

		//Get rows
		$db = new Database();
		$result=$this->inspector->getList(Query::query($db)->limit(50)->offset($pag->sql_offset));

		//Rows processing
		foreach($result as $row)
		{
			$ret.='<tr>';
			foreach($columns as $col)
			{
			if($this->getOption($this->columns[$col],'hide'))
			 	continue;
			$ret.='<td>';
				if(strpos($this->columns[$col][1],'relationship')===0)
				{
					if($this->columns[$col][1]=='relationship_has_and_belongs_to_many')
					{
						$ret.='<a href="'.$this->base.'relationshiphabtm/'.$row['id'].'/'.$this->columns[$col]['has_and_belongs_to_many'].'">A total of '.$row[$col].'</a></td>';
					}
					else
						$ret.='<a href="'.$this->base.'relationship/'.$row['id'].'/'.$this->columns[$col]['has_many'].'">A total of '.$row[$col].'</a></td>';
					continue;
				}
				if(strlen($row[$col])>50)
					$ret.=substr($row[$col],0,47).'<b>...</b>';
				else
					$ret.=$row[$col];
				$ret.='</td>';
			}
			$ret.='<td><a href="'.$this->base.'edit/'.$row['id'].'">E</a></td>';
			$ret.='<td><a href="'.$this->base.'delete/'.$row['id'].'">X</a></td></tr>';
		}



		$ret.="</table>";


		return $ret;
	}

	protected function Col2Field($col,&$form)
	{
		if($col[0]=='id')
		{
			return $form->hidden('_id');
				
		}
		if($l=$this->getOption($col,'load'))
		{
			var_dump($l);
		}

		switch($col[1])
		{
			case 'varchar':
				return $form->input('_'.$col[0]);
			break;
			case 'text':
				return $form->textarea('_'.$col[0])->rows(6)->cols(35);
			break;
			case 'foreign':
			return $form->dropdown('_'.$col[0]);
			break;
			default:
			return $form->input('_'.$col[0]);
			break;
		}
		
	}

	protected function Fieldval2Dbval(&$field,$col)
	{
			switch($col[1])
			{
			case 'text':
				return implode("\r\n",explode("\n",$field->value));
			break;
			default:
				return $field->value;
			break;
			}
		
	}

	protected function Dbval2Fieldval(&$field,$col,$value=false)
	{
			switch($col[1])
			{
			case 'foreign':
				$in=Model_Inspect::byTable($col['foreign']);
				$res=$in->getKeyList(new Query(new Database));
				$opts=array();
				$selected=false;
				foreach($res as $val)
				{
					$opts[$val['id']]=$val['_key'];
					if($val['_key']==$value) $selected=$val['id'];
				}
				$field  ->options($opts);
				if($selected)
					$field->selected($selected);
			break;
			default:
				if($value)
					$field->value($value);
				else
					$field->value('');
			break;
			}
		
	}

	protected function _delete($args)
	{
		//Initial
		$this->base=explode('delete',$this->base);
		$this->base=$this->base[0];

		$db = new Database();
		$db->delete($this->tablename,array('id'=>$args[1]));
		url::redirect($this->base.'manage');
	}


	protected function _edit($args)
	{
		//Initial
		$this->base=explode('edit',$this->base);
		$this->base=$this->base[0];

		//Get result
		$qr=new Query(new Database());
		$qr->where($this->tablename.'.id',$args[1]);
		$result=$this->inspector->getOne($qr);
		if(count($result)==0)
			return 'This record does not exist.';

		$result=$result[0];

		//More initial stuff
		$ret='<h1>Editing '.strtolower($this->prettyname).' '.$result['_keyfield'].'</h1><a href="'.$this->base.'manage">Back to the '.$this->tableplural.' list .</a><p>';
		$form=new Forge(NULL,'');

		//Get fields and their values
		foreach($this->columns as $col)
		{
			if($this->getOption($col,'hide_edit'))
				continue;
			if(strpos($col[1],'relationship_')===0) continue;
			$field=$this->Col2Field($col,$form);
			$this->Dbval2Fieldval($field,$col,$result[$col[0]]);

			$field->label($col['name']);
		}

		$form->submit('Submit');

		//Process input
		if($form->validate())
		{
			$updates=array();

			foreach($this->columns as $col)
			{
				if($this->getOption($col,'hide_edit'))
					continue;

				//Get actual db value
				if(strpos($col[1],'relationship_')===0) continue;
				$formfield='_'.$col[0];
				$formfield=$form->$formfield;
				if($col[1]=='foreign')
				{
					$dbvalue=$result['_inspector_'.$col[0]];
				}
				else
					$dbvalue=$result[$col[0]];

				//Get actual field value
				$formfield=$this->Fieldval2Dbval($formfield,$col);

				//Check whether needs to be updated
				if($formfield!=$dbvalue)
				{
					if($sav=$this->getOption($col,'save'))
					{
						$formfield=call_user_func($sav,$formfield);
					}
					$updates[$col[0]]=$formfield;
				}
			}

			//Run sql query
			if(count($updates)>0)
			{
				$db=new Database();
				if(				$db->update($this->tablename,$updates,array($this->tablename.'.id' => $args[1])))
					Message::add('Data changed successfully.');
				else
					Message::add('There was an error in the processing.');
			}
		}
		return $ret.$form->render().'</p>';

	}

	protected function _addnew($args)
	{
		$this->base=explode('addnew',$this->base);
		$this->base=$this->base[0];
		$ret='<h1>Adding new '.strtolower($this->prettyname).'</h1><a href="'.$this->base.'">Back to the management page.</a><p>';
		$form=new Forge(NULL,'');

		foreach($this->columns as $col)
		{
			if($this->getOption($col,'hide_edit'))
				continue;
			$field=$this->Col2Field($col,$form);
			$this->Dbval2Fieldval($field,$col);


			$field->label($col['name']);
		}

		$form->submit('Submit');
		
		if($form->validate())
		{
			$inserts=array();
			foreach($this->columns as $col)
			{
			if($this->getOption($col,'hide_edit'))
				continue;
				$formfield='_'.$col[0];
				$formfield=$form->$formfield;
				$formfield=$this->Fieldval2Dbval($formfield,$col);
				if($formfield!='')
				{
					if($sav=$this->getOption($col,'save'))
					{
						$formfield=call_user_func($sav,$formfield);
					}
					$inserts[$col[0]]=$formfield;
				}
			}
			if(count($inserts)>0)
			{
				$db=new Database();
				if($db->insert($this->tablename,$inserts))
					Message::add('Data changed successfully.');
				else
					Message::add('There was an error in the processing.');
			}
		}
		return $ret.$form->render().'</p>';

	}

	protected function _relationship($args,$del=true,$add=true)
	{
		//Initial
		$this->base=explode('relationship',$this->base);
		$this->base=$this->base[0];
		$roles=array();

		//Get result
		$result=$this->inspector->getRelated(new Query(new Database()),$args[1],$args[2]);

		$ret='<h1>Editing relationships of '.strtolower($this->prettyname).'</h1><a href="'.$this->base.'manage">Back to the '.$this->tableplural.' list .</a><p>';


		if(count($result)==0)
		{
			$ret.='This relationship is empty.<br/>';
		}else
		{
		//More initial stuff
		$relname=Model_Inspect::byTable($args[2])->getKeyField();
		$relname=$relname['name'];
		
		$ret.='<table border="1"><tr><th>Id</th><th>'.$relname.'</th>'.($del ? '<th>Delete</th>' : '').'</tr>';
		$id=1;

		foreach($result as $row)
		{
			$ret.='<tr><td>'.($id++).'</td><td>'.$row['name'].'</td>'.($del ? '<td><a href="'.$this->base.'relationship'.($del ? 'habtm':'').'delete/'.$args[1].'/'.$args[2].'/'.$row['id'].'">X</a>':'').'</tr>';
			$roles[]=$row['id'];
		}

		$ret.='</table>';
		}
		$ret.='</p>';

		if($add)
		{
		$ret.='<br/><h2>Add new relationship</h2><p>';
		$form=new Forge();

		$field=$form->dropdown('_'.$args[2]);

		//Fooling Dbval2Fieldval

		$col=array();
		$col[1]='foreign';
		$col['foreign']=$args[2];
		$this->Dbval2Fieldval($field,$col);

		$form->submit('Submit');

		if($form->validate())
		{
			var_dump($field->value);
			if(in_array($field->value,$roles))
			{
				Message::add('This relationship is present.');
			} else
			{
				$tabrel=$this->tablename.'_'.$args[2];
				$db=new Database();
				$db->insert($tabrel,array(
					substr($this->tablename,0,-1).'_id'=>$args[1],
					substr($args[2],0,-1).'_id'=>$field->value
					));

				Message::add_flash('Relationship has been added.');
				if($del and $add)
					url::redirect($this->base.'relationshiphabtm/'.$args[1].'/'.$args[2]);
				else
					url::redirect($this->base.'relationship/'.$args[1].'/'.$args[2]);

			}


		}
		}



		return $ret.(isset($form) ? $form->render() : '');


	}

	protected function _relationshipdelete($args,$habtm=false)
	{
		//Initial
		$this->base=explode('relationshipdelete',$this->base);
		$this->base=$this->base[0];

		$tabrel=$this->tablename.'_'.$args[2];

		//Get result
		$db=new Database();
		$db->delete($tabrel,
		array(
			$tabrel.'.'.substr($this->tablename,0,-1).'_id' => $args[1],
			$tabrel.'.'.substr($args[2],0,-1).'_id' => $args[3]
		));

		Message::add_flash('Relationship has been deleted.');
		if($habtm)
		{
			$this->base=explode('relationshiphabtmdelete',$this->base);
			$this->base=$this->base[0];
			url::redirect($this->base.'relationshiphabtm/'.$args[1].'/'.$args[2]);
		}
		else	
			url::redirect($this->base.'relationship/'.$args[1].'/'.$args[2]);
	}


}
