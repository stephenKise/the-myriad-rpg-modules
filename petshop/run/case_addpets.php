<?php
	$fields = 'pettype,valuegold,valuegems,upkeepgold,upkeepgems,petdk,petcharm,petdesc,newdaymsg,villagemsg,gardenmsg,battlemsg,petattack,mindamage,maxdamage,petturns,petwild,petrace,';

	$pets_array = array(
		array('`7Gray Ferret', 4800, 3, 55, 0, 1, 0, 'A small gray ferret popular as pets among the younger crowd.', 'Emerging from your backpack with a yawn, your little ferret is ready for the days travels.', 'Your ferret hides in your pack as you wander about the village.', 'Your ferret goes chasing after a grasshopper as you stroll through the gardens.', 'Your ferret buries itself in your backpack as the battle begins.',0,0,0,0,0,'All'),
		array('`&White Rabbit', 4500, 2, 100, 0, 1, 0, 'A small rabbit with a soft white coat. Popular among children.', 'Your rabbit hops around your feet as you prepare for the new day at hand.', 'As you wander through the village, you hold your pet rabbit in your arms.', 'Your pet rabbit hops among the flower patches, searching for a meal.', 'With a squeal, your rabbit hides among the bushes as the enemy draws closer.',0,0,0,0,0,'All'),
		array('`)Black Rat', 3150, 2, 67, 0, 0, 0, 'A small, slightly diseased black rat. Perfect for any social occassion.', 'Not one for mornings, your little black rat remains inside your backpack.', 'Villagers keep a cautious distance from you and your black rat.', 'Your little black rat hunts for food scraps and insects among the flower beds.', 'Terrified, your little black rat buries itself in your backpack as the battle begins.',0,0,0,0,0,'All'),
		array('`qBrown Rat', 3200, 2, 85, 0, 0, 0, 'A small brown rat, common to many alleyways and shops.', 'New day dawning, your pet rat peeks out from your pack with a somewhat disinterested look upon its face.', 'Fearful of the villagers, your little brown rat hides in your backpack.', 'Your little brown rat hides in your pack as a fairy buzzes by.', 'Terrified, your little brown rat buries itself in your backpack as the battle begins.',0,0,0,0,0,'All'),
		array('`&White Rat', 3500, 3, 75, 0, 0, 0, 'A small, white rat; a common pet among children.', 'Your little pet rat looks out from your backpack as the new day dawns.', 'Fearful of the villagers, your little white rat hides in your backpack.', 'Hungry, you little white rat hunts for insects among the flower beds.', 'Terrified, your little white rat buries itself in your backpack as the battle begins.',0,0,0,0,0,'All'),
		array('`&White Ferret', 6500, 3, 110, 0, 2, 0, 'A small while ferret popular as pets among the younger crowd.', 'Emerging from your backpack with a yawn, your little ferret is ready for the days travels.', 'Your ferret hides in your pack as you wander about the village.', 'Your ferret goes chasing after a grasshopper as you stroll through the gardens.', 'Your ferret buries itself in your backpack as the battle begins.',0,0,0,0,0,'All'),
		array('`2Iquana', 4700, 4, 140, 0, 3, 0, 'A horned lizard that spends it\'s days basking in the sunlight.', 'Your iquana remains in your backpack as the new day begins.', 'Villagers cast quizical looks at the iquana sitting upon your shoulder.', 'Hungry, your iquana scurries off in search of insects.', 'Disinterested in battle, your iquana naps in your backpack as the enemy draws closer.',0,0,0,0,0,'All'),
		array('`2Gecko', 6900, 3, 110, 0, 4, 0, 'A small, curious lizard fond of trees and wet climates.', 'Searching your things, you find your gecko snacking upon your morning rations!', 'Your gecko remains hidden in your backpack as you stroll through the village.', 'Your gecko plays among the trees and shrubs.', 'Your little gecko is nowhere to be found as the battle begins.',0,0,0,0,0,'All'),
		array('`2Quaker Parrot', 10850, 5, 120, 0, 5, 0, 'A small green parrot that seems quite friendly.', 'Your little quaker parrot greets the new day with a cheerful squawk.', 'Your little green parrot sits happily on your shoulder as you stroll through the village.', 'Your little parrot squawks loudly as a fairy buzzes past.', 'Alarmed, your quaker parrot takes off to a branch as the battle begins.',0,0,0,0,0,'All'),
		array('`7Bor`&der Col`7lie', 12500, 5, 115, 0, 5, 1, 'A regal dog with a lustrous black and white coat.', 'With an excited bark, your collie is ready for the new days adventures.', 'Your border collie trots along side of you as you walk through the village.', 'Your collie barks excitedly at the fairies buzzing around the garden.', 'Assuming a defensive posture in front of you, your collie prepares for battle.',1,10,16,10,0,'All'),
		array('`2Parakeet', 6500, 4, 103, 0, 3, 0, 'A tiny, yet lively bird which is a popular and noisy pet.', 'With a happy chirp, your parakeet greets the new day.', 'Your little parakeet remains on your shoulder as you stroll through the village.', 'Your little parakeet flies about the garden as you take a moment to relax.', 'Frightened, your parakeet disappears into the forest as the enemy draws closer.',0,0,0,0,0,'All'),
		array('`)Mastiff', 5250, 3, 120, 0, 2, 1, 'A large, imposing dog favored by the nobility to guard their estates.', 'Baying loudly, your large mastiff rouses you from your slumber.', 'Villagers keep a cautious distance from you and your large dog.', 'Your mastiff illicits fear among others gathered in the garden.', 'Ears flat and teeth bared, your mastiff prepares for battle.',1,11,21,10,0,'All'),
		array('`7Small Mutt', 3500, 1, 65, 0, 0, 0, 'A small, scrawny dog common to most alley ways and taverns.', 'Your little mutt begs for food as the morning dawns.', 'Your little dog trots happily by your feet as you wander through the village.', 'Hungry, the little mutt goes off in search of food.', 'With a whine, your little dog takes off for the forest in absolute terror.',0,0,0,0,0,'All'),
		array('`%Hairless Cat', 15540, 6, 120, 0, 5, 0, 'A small, exotic cat which possesses no hair whatsoever.', 'With a deep yawn and a lazy stretch, your hairless cat is ready for the new day.', 'Passing through, you and your hairless cat draw bemused glances from the locals.', 'Your hairless cat decides to hunt for mice among the flowers.', 'With a loud hiss, your hairless cat takes off for the bushes.',0,0,0,0,0,'All'),
		array('`7Lemur', 11200, 4, 85, 0, 2, 0, 'A strange simian with a long bushy tail found in lands far from this one.', 'Hopping upon your shoulder, your lemur is ready for a new days adventures.', 'Your lemur looks out about the village nervously.', 'Your lemur plays among the fruit trees as you take a moment to relax.', 'Frightened, your lemur retreats to the forest trees as the battle begins.',0,0,0,0,0,'All'),
		array('`QPhoenix', 35000, 10, 135, 0, 30, 1, 'A majestic bird of legend with fiery plumes and a majestic demeanor', 'Your phoenix perches upon your shoulder, and preens its fiery orange feathers.', 'Villagers stare in awe at the phoenix that is perched upon your shoulder.', 'Your phoenix looks about the garden with disinterested eyes.', 'Letting loose a shrill call, your phoenix prepares itself for battle.',1,14,27,10,0,'All'),
		array('Chicken', 2500, 2, 35, 0, 0, 0, 'A small, white and grey hen; common to many farms and dinner plates.', 'Your pet chicken pecks at the ground as the new day begins.', 'Weary of hungry villagers, you keep a close watch on your pet chicken.', 'Your pet chicken pecks at the ground while you stroll about the gardens.', 'Terrified, your pet chicken retreats to the bushes as the enemy advances.',0,0,0,0,0,'All'),
		array('`7Bull Terrier', 8500, 4, 90, 0, 4, 1, 'A small, scrappy dog with a bad demeanor.', 'With an excited yap, your little mutt greets the new day.', 'Your little bull terrier trots along side of you as you walk through the village.', 'Your little dog decides to take a bathroom break among the flower patches.', 'With a growl, your bull terrier prepares for battle.',1,9,15,10,0,'All'),
		array('`QOrange Tabby', 9500, 5, 103, 0, 6, 0, 'A small yellow and orange feline; a common pet among many.', 'Your cat hacks up a hairball and brushes up against your leg as the new day starts.', 'Your orange tabby cat stays close to your side as you wander through the village.', 'Bored, your orange tabby hunts among the flower patches.', 'You little cat hisses and runs for cover as the enemy draws closer.',0,0,0,0,0,'All'),
		array('`7Gray Tabby', 9200, 4, 98, 0, 3, 0, 'A small gray tabby cat; a common pet among many.', 'Your cat hacks up a hairball and brushes up against your leg as the new day starts.', 'Disinterested in the village, your gray tabby instead swats at your ankles.', 'Hungry, your little gray tabby cat hunts for mice among the flowers.', 'You little cat hisses and runs for cover as the enemy draws closer.',0,0,0,0,0,'All'),
		array('`)Pe`&nqu`)in', 21600, 8, 130, 0, 10, 0, 'A peculiar black and white bird commonly found in the arctic lands to the north.', 'Your penquin awaits its breakfast of fish as the new day begins.', 'Your penquin waddles about, drawing curious glances from villagers.', 'Feeling out of place, your penquin complains loudly as you stroll about.', 'Danger approaching, your penquin heads for the forest.',0,0,0,0,0,'All'),
		array('Chicken Hawk', 9860, 5, 110, 0, 2, 1, 'A small, predatory hawk with a knack for hunting poultry.', 'Your small hawk stretches its wings as the new day dawns.', 'Your chicken hawk sits proudly upon your shoulders and you wander about the village.', 'Your hawk decides to hunt for mice and rabbits among the flower patches.', 'Perching upon your arm, your hawk prepares for battle.',1,9,15,12,0,'All'),
		array('`QBengal Tiger', 25500, 12, 150, 0, 25, 1, 'An exotic and highly dangerous tiger.', 'With a menacing yawn, your large tiger pads about sniffing for prey.', 'Terrified villagers scramble out of the way as you walk through with your tiger by your side.', 'Not content to hunt for small vermin, your large tiger cats its eyes upon other heros.', 'With a loud, menacing growl your tiger pads towards the enemy.',1,11,29,10,0,'All'),
		array('`6Fox Hound', 7500, 5, 95, 0, 9, 1, 'A fit and affectionate canine prized among the nobility for their hunting skills.', 'Baying loudly, your hound rouses you from your slumber.', 'Your fox hound trots along side of you as you walk through the village.', 'Your fox hound sniffs among the flower patches as you stroll about the garden.', 'With a loud bay, your fox hound prepares for battle.',1,9,18,10,0,'All'),
		array('`6Gerbil', 3200, 4, 54, 0, 1, 0, 'A small, fuzzy rodent beloved among children.', 'Your little gerbil rummages around in your backpack for food as the new day dawns.', 'Your little gerbil remains out of sight as you pass through the village.', 'Content to stay upon your shoulder, your little gerbil cleans its paws.', 'Your little gerbil naps in your backpack as the enemy draws closer.',0,0,0,0,0,'All'),
		array('`)Doberman', 11250, 6, 100, 0, 5, 1, 'A popular breed of dog for security, prized for their loyalty and viciousness.', 'Ears alert, your doberman greets the day with a cautious demeanor.', 'Villagers keep a cautious distance from you and your doberman.', 'Your doberman stays by your side as you wander about the garden.', 'Ears flat and teeth bared, your doberman awaits battle.',1,11,17,10,0,'All'),
		array('`&Cockatoo', 12500, 6, 97, 0, 8, 0, 'A large, talkative white parrot favored by bird collectors.', 'With a happy squawek, your cockatoo greets the new day.', 'Content and happy, your cockatoo perches upon your shoulder as you stroll through the village.', 'Your parrot cranes its head quizically as a fairy flits past.', 'With a surprised squawk, your cockatoo takes off to a branch as the battle begins.',0,0,0,0,0,'All'),
		array('`2Spo`3tted Ge`2cko', 6580, 6, 95, 0, 3, 0, 'A blue and green spotted lizard fond of trees and wet climates.', 'Searching your things, you find your gecko snacking upon your morning rations!', 'Your gecko remains hidden in your backpack as you stroll through the village.', 'Your gecko plays among the trees and shrubs.', 'Your little gecko is nowhere to be found as the battle begins.',0,0,0,0,0,'All'),
		array('`4Rooster', 3200, 2, 65, 0, 1, 0, 'A barnyard fowl, common to both farms and dinner plates.', 'Your rooster perches upon a tree stump and sounds off loudly as the new day dawns.', 'Weary of hungry villagers, you keep a close watch on your pet rooster.', 'Your pet rooster pecks at the ground while you stroll about the gardens.', 'Terrified, your pet rooster retreats to the bushes as the enemy advances.',0,0,0,0,0,'All'),
		array('`^Le`6opo`^rd', 13250, 6, 110, 0, 10, 1, 'A sleek, agile wild cat found deep in southern jungles.', 'With a yawn and a stretch, your leapord bounds off in search of breakfast.', 'Curious, yet cautious, villagers keep their distance as you and your leopord pass by.', 'Your pet leapord sneaks away silently to hunt smaller pets as prey.', 'With a menacing growl your leapord prepares for battle.',1,15,28,10,0,'All'),
		array('`QFox', 300, 0, 50, 1, 0, 0, 'A small, wily fox which makes its home in the deep, dark forest.', 'With a small yawn, the fox stretches and is ready for the days adventure.', 'Your fox looks about the village with much apprehension', 'Your fox decides to take a nap among the bushes.', 'Tail between its legs, your fox retreats to the underbrush.',0,0,0,0,1,'All'),
		array('`^Golden Fox', 550, 1, 75, 2, 1, 0, 'A small fox with a bright golden coat common to many forests.', 'The little golden fox yawns as the new day dawns.', 'Weary of the villagers, your fox sticks close to you.', 'Your pet fox plays happily among the bushes and flowers.', 'Frightened, your fox retreats to the forest as the battle begins.',0,0,0,0,1,'All'),
		array('`7Wild Ferret', 400, 0, 80, 1, 0, 0, 'A small gray ferret popular as pets among the younger crowd.', 'Emerging from your backpack with a yawn, your little ferret is ready for the days travels.', 'Your ferret hides in your pack as you wander about the village.', 'Your ferret goes chasing after a grasshopper as you stroll through the gardens.', 'Your ferret buries itself in your backpack as the battle begins.',0,0,0,0,1,'All'),
		array('`3Feral Cat', 300, 0, 50, 1, 0, 1, 'A wild cat common in many alleyways and forests.', 'Your cat hacks up a hairball and brushes up against your leg as the new day starts.', 'Your wild cat hisses as villagers walk past.', 'Your little cat hunts for mice among the flower patches.', 'Back arched and claws out, your wild cat prepares for battle.',1,5,9,10,1,'All'),
		array('`&Wild Dog', 450, 1, 85, 1, 2, 1, 'A wild mutt, usually found wandering in packs in the forest.', 'With an excited bark, your wild dog is ready for the new days adventures.', 'Your wild dog barks at the passing villagers.', 'Your wild dog curls up for a nap under a bush as you stroll through the gardens.', 'Your wild dog growls as the enemy draws closer.',1,8,11,12,1,'All'),
		array('`7Wombat', 900, 2, 112, 2, 4, 0, 'A strange marsupial found in lands far from this one.', 'Your wombat greets the new day with a yawn.', 'Villagers stare quizically at your wombat as you stroll through.', 'Your pet wombat decides to take a nap among the flower patches.', 'Your wombat heads for the bushes as the battle begins.',0,0,0,0,1,'All'),
	);

	$categories = explode('::', trim(get_module_setting('categories')));
	$categories[] = translate_inline('Storage');
	$count = count($pets_array);

	$op = httpget('op2');
	$cat = ( httpget('cat') ) ? httpget('cat') : 0;

	if( $op == 'install' )
	{
		$pets = httppost('pets');
		$allpets = httppost('allpets');
		$count2 = count($pets_array[0]);
		$passfail = '';

		$j = 0;
		$k = 0;
		for( $i=0; $i<$count; $i++ )
		{
			if( $pets["pet$i"] == 1 || $allpets == 1 )
			{
				$pet = '';
				foreach( $pets_array[$i] as $key => $value )
				{
					$pet .= "'".addslashes($value)."',";
				}

				$sql = "INSERT INTO " . db_prefix('pets') . " (" . $fields . "petcat) VALUES (" . $pet . "'" . $cat . "')";

				if( db_query($sql) !== FALSE )
				{
					$passfail .= "`^$i `@Pass`n";
					$j++;
				}
				else
				{
					$passfail .= "`^$i `\$Fail`n";
				}
				$k++;
			}
		
		}

		debug(appoencode($passfail));
		output("`n`#%s `3pet(s) were added to the database out of the `#%s `3 that you selected.`n`n", $j, $k);

		addnav('Return');
		addnav('Pet Editor','runmodule.php?module=petshop&op=editor&op2=view&cat='.$cat);

	}
	else
	{
		require_once('lib/showform.php');

		output("`3Which of the following pets do you want to install?.`n`n");
		output("These pets will be installed to the `#%s `3category.`0`n`n", $categories[$cat]);

		$pets = '';
		for( $i=0; $i<$count; $i++ )
		{
			$pets .= ",pet$i," . appoencode($pets_array[$i][0]);
		}

		$row = array(
			'allpets'=>'',
			'pets'=>array()
		);
		$form = array(
			'Install Which Pets?,title',
			'allpets'=>'Install ALL pets?,bool',
			'`^Aren\'t you glad I put this at the top? Heh.`0,note',
			'pets'=>'Pets:,checklist' . $pets
		);

		rawoutput('<form action="runmodule.php?module=petshop&op=addpets&op2=install&cat='.$cat.'" method="POST">');
		addnav('','runmodule.php?module=petshop&op=addpets&op2=install&cat='.$cat);
		showform($form,$row);
		rawoutput('</form>');

		addnav('Back');
		addnav('Go back','runmodule.php?module=petshop&op=editor&op2=view&cat='.$cat);
	}
?>