<?php
$clans = db_prefix('clans');
//if (e_rand(1, 100) <= get_module_setting('villagepercent')) {
    $args['dopyramid'] = 1;
//}
$lm=get_module_setting('lastwinner');
output_notl("`n");

if ($owned1==0){
    $mid1 = "`iOpen`i";
}
else if ($owned1>0){
    $sql = db_query("SELECT * FROM $clans WHERE clanid = '$owned1'");
    $row = db_fetch_assoc($sql);
    $mid1 = $row['clanname'];
}

if ($owned2==0){
    $mid2 = "`iOpen`i";
}
else if ($owned2>0){
    $sql = db_query("SELECT * FROM $clans WHERE clanid = '$owned2'");
    $row = db_fetch_assoc($sql);
    $mid2 = $row['clanname'];
}

if ($owned3==0){
    $mid3 = "`iOpen`i";
}
else if($owned3>0){
    $sql = db_query("SELECT * FROM $clans WHERE clanid = '$owned3'");
    $row = db_fetch_assoc($sql);
    $mid3 = $row['clanname'];
}

if ($lm){
    $sql = db_query("SELECT clanad FROM $clans WHERE clanname = '$lm'");
    $winner = db_fetch_assoc($sql);
}
    output(
        "<table align='center''>
            <tr>
                <td width='30%' align='center'>Rapacity</td>
                <td width='30%' align='center'>Cupidity</td>
                <td width='30%' align='center'>Avarice</td>
            </tr>
            <tr>
                <td align='center'>$mid1</td>
                <td align='center'>$mid2</td>
                <td align='center'>$mid3</td>
            </tr>
            <tr>
                <td colspan='3' align='center'>
                    <fieldset class='village clanAdContainer' style='border-radius: 5px; border: 1px solid rgba(255,255,255,.1);'>
                    <legend class='village clanAdWinners' style='padding: 5px;'>$lm</legend>
                    {$winner['clanad']}
                    </fieldset>
                </td>
            </tr>
        </table>",
        true
    );
?>
