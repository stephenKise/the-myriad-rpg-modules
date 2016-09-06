<?php
$clan=$u['clanid'];
if ($clan==$owned1 && $clan==$owned2 && $clan==$owned3 && $clan<>0) output("`n`b`c`^Your Guild owns all three Vaults, you receive a experience bonus for forest fights.`c`n`b");
else if ($clan>0) output("`n`b`c`^As your Guild doesn't control all the Vaults, you don't receive a experience bonus.`c`n`b");
else output("`n`b`c`^You should join a Guild and take control of the Vaults to earn an experience bonus!`c`b`n");
?>