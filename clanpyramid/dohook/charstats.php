<?php
if (get_module_pref("square")>0){
	$which=httpget('p');
	if (get_module_pref("user_see") == 2){
		$vc=translate_inline("`7View Map");
		//copied and modified from ShadowRavens clinic.php
		if ($which==1){
			$map1="<a href='modules/clanpyramid/images/Map_pyramid1.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramid1.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
			addcharstat("Vault Maps");
			addcharstat("Vault 1", $map1);
			addnav("","modules/clanpyramid/images/Map_pyramid1.gif");
		}elseif ($which==3){
			$map1="<a href='modules/clanpyramid/images/Map_pyramid3.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramid3.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
			addcharstat("Vault Maps");
			addcharstat("Vault3", $map1);
			addnav("","modules/clanpyramid/images/Map_pyramid3.gif");
		}elseif ($which==2){
			$squarenew=get_module_pref("square");
			$s1=array(1001,1002,1014,1015); $s2=array(1003,1004,1005,1006,1007,1008,1009,1010,1011,1016,1017,1018,1019,1020,1021,1022,1023,1024,1029,1030,1031,1032,1035,1037); $s3=array(1027,1028,1040,1041,1053); $s4=array(1042,1043,1054,1055,1056,1068,1069,1081); $s5=array(1044,1045,1057,1058,1059,1070,1071,1084); $s6=array(1066,1067,1079,1080,1092,1093,1094,1105,1106,1107,1118,1119,1131,1132,1144); $s7=array(1157,1158,1170,1171); $s8=array(1012,1013,1025,1026); $s9=array(1033,1034,1046,1047); $s10=array(1036,1048,1049,1050,1051,1061,1062,1063); $s11=array(1060,1073); $s12=array(1074,1075,1086,1087,1088,1099,1100,1101); $s13=array(1082,1083,1095,1096,1097,1108,1109,1110,1121,1122); $s14=array(1120,1133,1134,1145,1146,1147,1148,1159,1160,1161,1162,1163,1164,1165,1166,1167,1173,1174,1175,1176,1177,1178,1179,1180,1151,1152,1153,1154,1155,1156,1140,1141,1142,1143,1127,1128,1129,1130,1172); $s15=array(1114,1115,1116,1117,1102,1103,1104,1089,1090,1091,1076,1077,1078,1064,1065,1052,1038,1039); $s16=array(1168,1169,1181,1182); $s17=array(1111,1112,1113,1123,1124,1125,1126,1135,1136,1137,1138,1149,1150); $st=array(1072,1085,1098); $defender=get_module_pref("defender");
			if (in_array($squarenew,$s1)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids1.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids1.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids1.gif");
			}elseif (in_array($squarenew,$s2)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids2.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids2.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids2.gif");
			}elseif (in_array($squarenew,$s3)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids3.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids3.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids3.gif");
			}elseif (in_array($squarenew,$s4)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids4.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids4.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids4.gif");
			}elseif (in_array($squarenew,$s5)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids5.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids5.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids5.gif");
			}elseif (in_array($squarenew,$s6)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids6.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids6.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids6.gif");
			}elseif (in_array($squarenew,$s7)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids7.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids7.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids7.gif");
			}elseif (in_array($squarenew,$s8)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids8.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids8.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids8.gif");
			}elseif (in_array($squarenew,$s9)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids9.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids9.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids9.gif");
			}elseif (in_array($squarenew,$s10)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids10.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids10.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids10.gif");
			}elseif (in_array($squarenew,$s11)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids11.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids11.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids11.gif");
			}elseif (in_array($squarenew,$s12)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids12.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids12.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids12.gif");
			}elseif (in_array($squarenew,$s13)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids13.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids13.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids13.gif");
			}elseif (in_array($squarenew,$s14)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids14.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids14.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids14.gif");
			}elseif (in_array($squarenew,$s15)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids15.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids15.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids15.gif");
			}elseif (in_array($squarenew,$s16)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids16.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids16.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids16.gif");
			}elseif (in_array($squarenew,$s17)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramids17.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramids17.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramids17.gif");
			}elseif (in_array($squarenew,$st)){
				$map1="<a href='modules/clanpyramid/images/Map_pyramidst.gif' onClick=\"".popup("modules/clanpyramid/images/Map_pyramidst.gif").";return false;\" target='_blank' align='center' class=\"charinfo\" style=\"font-size:12px\">".$vc."</a>";
				addcharstat("Vault Maps");
				addcharstat("Vault 2", $map1);
				addnav("","modules/clanpyramid/images/Map_pyramidst.gif");
			}
		}
	}
}
?>