<?php

function marriageitems_getmoduleinfo(){
	$info = array(
		"name"=>"Marriage Flirt Items",
		"version"=>"5.0",
		"author"=>"CortalUX, overhaul: Oliver Brendel, Expanded by DaveS",
		"override_forced_nav"=>true,
		"category"=>"Character",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1068",
		"settings"=>array(
			"Action/Item 1,title",
			"name1"=>"What is the name of the item/action?,text|Chocolate",
			"type1"=>"Is this an Item or an Action?,enum,0,Item,1,Action|0",
			"cost-item1"=>"How much gold does the item cost?,int|160",
			"points-item1"=>"How many flirt points does the item/action change?,int|15",
			"0 sets to -no bonus- and you may set it to negative,note",
			"shortcut1"=>"Show Item in javascript shortcut at flirt selection?,bool|1",
			"Action/Item 2,title",
			"name2"=>"What is the name of the item/action?,text|Serenade",
			"type2"=>"Is this an Item or an Action?,enum,0,Item,1,Action|1",
			"cost-item2"=>"How much gold does the item cost?,int|350",
			"points-item2"=>"How many flirt points does the item/action change?,int|7",
			"0 sets to -no bonus- and you may set it to negative,note",
			"shortcut2"=>"Show Item in javascript shortcut at flirt selection?,bool|1",
			"Action/Item 3,title",
			"name3"=>"What is the name of the item/action?,text|Hire a Trained Monkey",
			"type3"=>"Is this an Item or an Action?,enum,0,Item,1,Action|1",
			"cost-item3"=>"How much gold does the item cost?,int|500",
			"points-item3"=>"How many flirt points does the item/action change?,int|6",
			"0 sets to -no bonus- and you may set it to negative,note",
			"shortcut3"=>"Show Item in javascript shortcut at flirt selection?,bool|1",
			"Action/Item 4,title",
			"name4"=>"What is the name of the item/action?,text|Blow a Kiss",
			"type4"=>"Is this an Item or an Action?,enum,0,Item,1,Action|1",
			"cost-item4"=>"How much gold does the item cost?,int|0",
			"points-item4"=>"How many flirt points does the item/action change?,int|1",
			"0 sets to -no bonus- and you may set it to negative,note",
			"shortcut4"=>"Show Item in javascript shortcut at flirt selection?,bool|1",
			"Action/Item 5,title",
			"name5"=>"What is the name of the item/action?,text|Shake Hands",
			"type5"=>"Is this an Item or an Action?,enum,0,Item,1,Action|1",
			"cost-item5"=>"How much gold does the item cost?,int|0",
			"points-item5"=>"How many flirt points does the item/action change?,int|0",
			"0 sets to -no bonus- and you may set it to negative,note",
			"shortcut5"=>"Show Item in javascript shortcut at flirt selection?,bool|1",
			"Action/Item 6,title",
			"name6"=>"What is the name of the item/action?,text|Pencil",
			"type6"=>"Is this an Item or an Action?,enum,0,Item,1,Action|0",
			"cost-item6"=>"How much gold does the item cost?,int|3",
			"points-item6"=>"How many flirt points does the item/action change?,int|0",
			"0 sets to -no bonus- and you may set it to negative,note",
			"shortcut6"=>"Show Item in javascript shortcut at flirt selection?,bool|1",
			"Action/Item 7,title",
			"name7"=>"What is the name of the item/action?,text|Smelly Sock",
			"type7"=>"Is this an Item or an Action?,enum,0,Item,1,Action|0",
			"cost-item7"=>"How much gold does the item cost?,int|1",
			"points-item7"=>"How many flirt points does the item/action change?,int|-1",
			"0 sets to -no bonus- and you may set it to negative,note",
			"shortcut7"=>"Show Item in javascript shortcut at flirt selection?,bool|1",
			"Action/Item 8,title",
			"name8"=>"What is the name of the item/action?,text|Flaming Pile of Excrement",
			"type8"=>"Is this an Item or an Action?,enum,0,Item,1,Action|0",
			"cost-item8"=>"How much gold does the item cost?,int|10",
			"points-item8"=>"How many flirt points does the item/action change?,int|-10",
			"0 sets to -no bonus- and you may set it to negative,note",
			"shortcut8"=>"Show Item in javascript shortcut at flirt selection?,bool|1",
			"Action/Item 9,title",
			"name9"=>"What is the name of the item/action?,text|Punch",
			"type9"=>"Is this an Item or an Action?,enum,0,Item,1,Action|1",
			"cost-item9"=>"How much gold does the item cost?,int|0",
			"points-item9"=>"How many flirt points does the item/action change?,int|-15",
			"0 sets to -no bonus- and you may set it to negative,note",
			"shortcut9"=>"Show Item in javascript shortcut at flirt selection?,bool|1",
			"Action/Item 10,title",
			"name10"=>"What is the name of the item/action?,text|Spew Vitrious Hatred",
			"type10"=>"Is this an Item or an Action?,enum,0,Item,1,Action|1",
			"cost-item10"=>"How much gold does the item cost?,int|0",
			"points-item10"=>"How many flirt points does the item/action change?,int|-30",
			"0 sets to -no bonus- and you may set it to negative,note",
			"shortcut10"=>"Show Item in javascript shortcut at flirt selection?,bool|1",

			),
		"requires"=>array(
			"marriage"=>"5.03|CortalUX, Oliver Brendel, expanded by DaveS",
			),
		);

	return $info;
}

function marriageitems_install(){
	module_addhook("marriage-items");
	module_addhook("marriage-pointvalue");
	return true;
}

function marriageitems_uninstall(){
	return true;
}

function marriageitems_dohook($hookname, $args){
	global $session;
	switch($hookname){
		case "marriage-pointvalue":
			for ($i = 0; $i < 10; $i++){
				$j=$i+1;
				if (get_module_setting("name".$j)>""){
					$action=get_module_setting("name".$j);
					$flpoint=get_module_setting('points-item'.$j);
					if ($flpoint==0) $flpoint=translate_inline("None");
					$flcost=get_module_setting("cost-item".$j);
					if ($flcost==0) $flcost=translate_inline("Free");
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'>");
					rawoutput("<td>$action</td><td><center>$flpoint</center></td><td><center>$flcost</center></td></tr>");
				}
			}		
		break;
		case "marriage-items":
			for ($i = 0; $i < 10; $i++){
				$j=$i+1;
				if (get_module_setting("name".$j)>"" && get_module_pref("user_option","marriage")==0 && get_module_pref("supernoflirt","marriage")==0){
					$name=get_module_setting("name".$j);
					$basic=strtolower($name);
					$lower=color_sanitize($basic);
					addnav("Flirt Actions");
					if (get_module_setting("cost-item".$j)<=0 || get_module_setting("type".$j)==1){
						addnav(array("%s", $name),"runmodule.php?module=marriage&op=loveshack&op2=flirt&flirtitem=$lower");
						if (get_module_setting('shortcut'.$j)) array_push($args['shortcut'],array($lower=>"".$name));
					}else{
						addnav(array("Buy %s", $name),"runmodule.php?module=marriage&op=loveshack&op2=flirt&flirtitem=$lower");
						if (get_module_setting('shortcut'.$j)) array_push($args['shortcut'],array($lower=>"Buy ".$name));
					}
					$mailheader="mailheader-".$lower;
					$args[$mailheader]=$name." from {mname}";
					$args['cost-'.$lower]=get_module_setting('cost-item'.$j);
					$points=get_module_setting('points-item'.$j);
					if ($points) $args['points-'.$lower]=$points;
					//The item/action gives flirt points
					if (get_module_setting("points-item".$j)>0){
						//This is an Item
						if (get_module_setting("type".$j)==0){
							$args['output-'.$lower]="`%{name}`@ emphatically thanks you for the `#".$lower."`@. Your flirt points with `%{name}`@ increase by `^{pts}`@.";
							//Free Item YoM
							if (get_module_setting("cost-item".$j)<=0) $args[$lower]="`%{mname}`^ gave you a `#".$lower."`^ at the loveshack!`n`%{mname}`%'s`^ flirt points increased by `@{pts}`^ with you!";
							//Cost Item YoM
							else $args[$lower]="`%{mname}`^ bought you a `#".$lower."`^ at the loveshack!`n`%{mname}`%'s`^ flirt points increased by `@{pts}`^ with you!";
						//This is an action
						}else{
							$args['output-'.$lower]="You ".$lower."`@. Your flirt points with `%{name}`@ increase by `^{pts}`@.";
							///Free Action YoM
							if (get_module_setting("cost-item".$j)<=0) $args[$lower]="`#'I will ".$lower." you.'`^ says `%{mname}`^. Soon enough it happens!`n`%{mname}`%'s`^ flirt points increased by `@{pts}`^ with you!";
							//Cost Action YoM
							else $args[$lower]="`%{mname}`^ paid to `#".$lower."`^ you at the loveshack!`n`%{mname}`%'s`^ flirt points increased by `@{pts}`^ with you!";
						}
					//This item/action subtracts flirt points
					}elseif (get_module_setting("points-item".$j)<0) {
						//This is an Item
						if (get_module_setting("type".$j)==0){
							$args['output-'.$lower]="`%{name}`@ looks at you with disgust for the `#".$lower."`@. Your flirt points with `%{name}`@ change by `^{pts}`@.";
							//Free Item YoM
							if (get_module_setting("cost-item".$j)<=0) $args[$lower]="`%{mname}`^ gave you a `#".$lower."`^ at the loveshack!`n`%{mname}`%'s`^ flirt points change by `@{pts}`^ with you.";
							//Cost Item YoM
							else $args[$lower]="`%{mname}`^ bought you a `#".$lower."`^ at the loveshack!`n`%{mname}`%'s`^ flirt points changed by `@{pts}`^ with you.";
						//This is an action
						}else{
							$args['output-'.$lower]="You ".$lower."`@. Your flirt points with `%{name}`@ change by `^{pts}`@.";
							///Free Action YoM
							if (get_module_setting("cost-item".$j)<=0) $args[$lower]="`#'I will ".$lower." you.'`^ says `%{mname}`^. Soon enough it happens!`n`%{mname}`%'s`^ flirt points change by `@{pts}`^ with you.";
							//Cost Action YoM
							else $args[$lower]="`%{mname}`^ paid to `#".$lower."`^ you at the loveshack!`n`%{mname}`%'s`^ flirt points change by `@{pts}`^ with you.";
						}
					//This item/action doesn't affect flirt points
					}else{
						//This is an Item
						if (get_module_setting("type".$j)==0){
							$args['output-'.$lower]="`%{name}`@ thanks you for the `#".$lower."`@. However, your flirt points with `%{name}`@ don't change.";
							//Free Item YoM
							if (get_module_setting("cost-item".$j)<=0) $args[$lower]="`%{mname}`^ gave you a `#".$lower."`^ at the loveshack.`nNot being very impressed, your flirt points don't change.";
							//Cost Item YoM
							else $args[$lower]="`%{mname}`^ bought you a `#".$lower."`^ at the loveshack.`nYou think it's a rather tacky gift and your flirt points with `%{mname}`^ don't change.";
						//This is an action
						}else{
							$args['output-'.$lower]="You ".$lower."`@. It doesn't feel very rewarding.  Your flirt points with `^{name}`@ don't change.";
							///Free Action YoM
							if (get_module_setting("cost-item".$j)<=0) $args[$lower]="`#'I will ".$lower." you.'`^ says `%{mname}`^. Not impressed, your flirt points with `%{mname}`^ don't change.";
							//Cost Action YoM
							else $args[$lower]="`%{mname}`^ paid to `#".$lower."`^ you at the loveshack.`nYou are not wooed by the purchase and your flirt points with `%{mname}`^ do not change.";
						}
					}
				}
			}
		break;
	}
	return $args;
}

function marriageitems_run(){

}
?>