<?php
	if( is_module_active('petshop') )
	{
		output("`c`b`QUpdating 'petshop' Module.`0`b`c`n");

		// Check to see if petshop has been updated before.
		$sql = "SELECT setting
				FROM " . db_prefix('module_userprefs') . "
				WHERE modulename = 'petshop'
					AND setting = 'haspet'";
		$result = db_query($sql);
		// If the setting value 'haspet' exists then update has not been done before.
		if( db_num_rows($result) > 0 )
		{
			// Grab all the player pet data.
			$sql = "SELECT setting, userid, value
					FROM " . db_prefix('module_userprefs') . "
					WHERE modulename = 'petshop'";
			$result = db_query($sql);
			$owners = array();
			while( $row = db_fetch_assoc($result) )
			{
				// Do a quick rename of two fields.
				if( $row['setting'] == 'petname' ) $row['setting'] = 'pettype';
				if( $row['setting'] == 'customname' ) $row['setting'] = 'petname';
				$owners[$row['userid']][$row['setting']] = $row['value'];
			}

			output("`3Old 'petshop' userpref setting exists, therefore old module version, storing player pet data...`n");
			output("Deleting unused userpref 'petshop' settings...`n");

			// No longer require all the old user prefs so delete them.
			db_query("DELETE FROM " . db_prefix('module_userprefs') . " WHERE modulename = 'petshop'");

			$i = 0;
			$j = 0;
			foreach( $owners as $user_id => $setting )
			{
				$i++;
				// Don't waste time converting data when the player has no pet.
				if( $setting['haspet'] == 1 )
				{
					$setting['haspet'] = $setting['petid'];
					// Attacktype value is now kept in petattack.
					if( $setting['petattack'] == 1 )
					{
						$setting['petattack'] = ( $setting['attacktype'] == 0 ) ? 1 : 2;
					}
					// No longer use these fields.
					unset($setting['petid'], $setting['giftedpet'], $setting['attacktype']);

					$j++;
					$allprefs = array();
					foreach( $setting as $key => $value )
					{
						$allprefs[$key] = $value;
					}
					// Save the allprefs array data for each player with a pet.
					set_module_pref('allprefs',serialize($allprefs),'petshop',$user_id);
				}
			}
			output("Saving new userpref 'petshop' setting...`n");
			output('Out of %s player(s), %s had pets so were updated and saved...`n`n', $i, $j);
			output("`#BEGIN - ALTERATIONS TO 'pets' TABLE...`n");

			// Make some table field name changes.
			output("`3Altering table, changing column name from 'petname' to 'pettype'...`n");
			db_query("ALTER TABLE " . db_prefix('pets') . " CHANGE COLUMN petname pettype VARCHAR(25) NULL");
			output("Altering table, changing column name from 'petbreed' to 'petcat'...`n");
			db_query("ALTER TABLE " . db_prefix('pets') . " CHANGE COLUMN petbreed petcat TINYINT(2) NOT NULL DEFAULT 0");

			$sql = "SELECT race
					FROM " . db_prefix('pets') . "
					WHERE petid != ''";
			$result = db_query($sql);
			if( $row = db_fetch_assoc($result) )
			{
				output("Altering table, changing column name from 'race' to 'petrace'...`n");
				db_query("ALTER TABLE " . db_prefix('pets') . " CHANGE COLUMN race petrace VARCHAR(25) NOT NULL DEFAULT 'All'");
			}
			else
			{
				output("Altering table, adding column name 'petrace'...`n");
				db_query("ALTER TABLE " . db_prefix('pets') . " ADD COLUMN petrace VARCHAR(25) NOT NULL DEFAULT 'All' AFTER petcat");
			}

			output("Altering field 'petrace', changing any 'all' or '' to 'All'...`n");
			db_query("UPDATE " . db_prefix('pets') . " SET petrace = 'All' WHERE petrace = 'all' OR petrace = ''");
			output("Altering table, adding column name 'petage'...`n");
			db_query("ALTER TABLE " . db_prefix('pets') . " ADD COLUMN petage MEDIUMINT(5) NOT NULL DEFAULT 1000 AFTER petrace");
			output("Altering table, adding column name 'petwild'...`n");
			db_query("ALTER TABLE " . db_prefix('pets') . " ADD COLUMN petwild SMALLINT(4) NOT NULL DEFAULT '0' AFTER petcat");
			output("Altering table, dropping column name 'attacktype'...`n");
			db_query("ALTER TABLE " . db_prefix('pets') . " DROP COLUMN attacktype");
			output("`#END - ALTERATIONS COMPLETE!`0`n");
		}
	}
	else
	{
		output("`c`b`QInstalling 'petshop' Module.`0`b`c`n");
		require_once('lib/tabledescriptor.php');
		$fields = array(
			'petid'		=>array('name'=>'petid',		'type'=>'smallint(4)	unsigned',	'null'=>'0',	'extra'=>'auto_increment'),
			'pettype'	=>array('name'=>'pettype',		'type'=>'varchar(30)',				'null'=>'1'),
			'petcat'	=>array('name'=>'petcat',		'type'=>'tinyint(2)		unsigned',	'null'=>'0',	'default'=>'0'),
			'petwild'	=>array('name'=>'petwild',		'type'=>'smallint(4)	unsigned',	'null'=>'0',	'default'=>'0'),
			'petrace'	=>array('name'=>'petrace',		'type'=>'varchar(50)',				'null'=>'0',	'default'=>'All'),
			'petdk'		=>array('name'=>'petdk',		'type'=>'smallint(4)	unsigned',	'null'=>'0',	'default'=>'0'),
			'petcharm'	=>array('name'=>'petcharm',		'type'=>'tinyint(3)		unsigned',	'null'=>'0',	'default'=>'0'),
			'petage'	=>array('name'=>'petage',		'type'=>'smallint(5)	unsigned',	'null'=>'0',	'default'=>'1000'),
			'petdesc'	=>array('name'=>'petdesc',		'type'=>'varchar(150)',				'null'=>'1'),
			'petturns'	=>array('name'=>'petturns',		'type'=>'tinyint(3)		unsigned',	'null'=>'0',	'default'=>'0'),
			'petattack'	=>array('name'=>'petattack',	'type'=>'tinyint(3)		unsigned',	'null'=>'0',	'default'=>'0'),
			'mindamage'	=>array('name'=>'mindamage',	'type'=>'tinyint(3)		unsigned',	'null'=>'0',	'default'=>'0'),
			'maxdamage'	=>array('name'=>'maxdamage',	'type'=>'tinyint(3)		unsigned',	'null'=>'0',	'default'=>'0'),
			'valuegold'	=>array('name'=>'valuegold',	'type'=>'mediumint(7)	unsigned',	'null'=>'0',	'default'=>'500'),
			'valuegems'	=>array('name'=>'valuegems',	'type'=>'tinyint(3)		unsigned',	'null'=>'0',	'default'=>'0'),
			'upkeepgold'=>array('name'=>'upkeepgold',	'type'=>'mediumint(5)	unsigned',	'null'=>'0',	'default'=>'30'),
			'upkeepgems'=>array('name'=>'upkeepgems',	'type'=>'tinyint(3)		unsigned',	'null'=>'0',	'default'=>'0'),
			'newdaymsg'	=>array('name'=>'newdaymsg',	'type'=>'varchar(150)',				'null'=>'1'),
			'villagemsg'=>array('name'=>'villagemsg',	'type'=>'varchar(150)',				'null'=>'1'),
			'gardenmsg'	=>array('name'=>'gardenmsg',	'type'=>'varchar(150)',				'null'=>'1'),
			'battlemsg'	=>array('name'=>'battlemsg',	'type'=>'varchar(150)',				'null'=>'1'),
			'key-PRIMARY'	=>array('name'=>'PRIMARY',	'type'=>'primary key',	'unique'=>'1',	'columns'=>'petid'),
			'key-petid'		=>array('name'=>'petid',	'type'=>'key',							'columns'=>'petid')
		);
		output('`3Installing \'pets\' table...`0`n');
		synctable(db_prefix('pets'), $fields, TRUE);
	}

	output('`3Installing modulehooks...`0`n');
	module_addhook('newday');
	module_addhook('forest');
	module_addhook('gardens');
	module_addhook('inn-desc');	
	module_addhook('village');
	module_addhook('village-desc');
	module_addhook('dragonkill');
	module_addhook('charstats');
	module_addhook('biostat');
	module_addhook('battle');
	module_addhook('battle-victory');
	module_addhook('battle-defeat');
	module_addhook('apply-specialties');
	module_addhook('fightnav-specialties');
	module_addhook('changesetting');
	module_addhook('allprefs');
	module_addhook('allprefnavs');
	module_addhook('superuser');
	module_addeventhook('forest', "return get_module_setting('forestodds','petshop');");
	module_addeventhook('travel', "return get_module_setting('travelodds','petshop');");
	output('`#Installion Complete.`0`n');
?>