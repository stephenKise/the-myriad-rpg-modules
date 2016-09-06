<?php
	$what = httpget('what');
	if( $what == 'name' )
	{
		require_once('lib/showform.php');

		output("`3Looking over at %s`3, you decide %s could use a new name.`0`n", $allprefs['petname'], genders($allprefs['petgender'], 2));

		rawoutput('<form action="runmodule.php?module=petshop&op=petname&what=done" method="POST">');
		addnav('','runmodule.php?module=petshop&op=petname&what=done');
		$rename = translate_inline('Rename Your Pet');
		$petinfo = array("$rename,title",'petname'=>'Pet Name:,string,30');
		$row = array('petname'=>$allprefs['petname']);
		showform($petinfo,$row);
		rawoutput('</form>');
	}
	else
	{
		$newname = strip_tags(httppost('petname'));
		if( empty($newname) )
		{ 
			output("`3After having a moment of indecision, you decide not to rename your pet.`0`n`n");
		}
		else
		{
			$find = array('\'','"');
			$newname = str_replace($find, '', $newname);
			output("`3You decide to rename your pet %s`3. ", $newname);
			output("%s `3seems happy with your choice.`0`n`n", ucfirst(genders($allprefs['petgender'], 2)));
			$allprefs['petname'] = $newname;
			set_module_pref('allprefs',serialize($allprefs));
		}
	}

	addnav('Back');
	addnav('Go Back','runmodule.php?module=petshop');
?>