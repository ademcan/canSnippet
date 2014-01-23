<?php
$config = array(
	"dbname" => "snippets.sqlite",
);

// modloader for prism
function mod_prism_loader($rel_js_path){
	$balise = "";
	//add slash at the end
	if(!preg_match("/.+\.(css|js)\/$/",$rel_js_path))
		$rel_js_path.="/";

	$rel_js_path.='prism_mod_loader/';
	$files = scandir($rel_js_path);
		for ($i=0;$i<count($files);$i++)
			if(preg_match("/.+\.(css|js)$/",$files[$i]))
				if(preg_match("/.+\.css$/",$files[$i]))
					$balise .= '<link href="'.$rel_js_path.$files[$i].'" rel="stylesheet" />';
				else
					$balise .= '<script src="'.$rel_js_path.$files[$i].'"></script>';
	return $balise;
}
