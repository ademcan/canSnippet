<?php
$config = array(
	"dbname" => "snippets.sqlite",
);

// modloader for prism
function mod_prism_loader($rel_js_path){
	$balise = "";
	//add slash at the end
	if(!preg_match("/.*\/$/",$rel_js_path))
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

// modloader for prism
function conf_mod_prism_loader($rel_js_path){
	$conf_mod = array();
	//add slash at the end
	if(!preg_match("/.*\/$/",$rel_js_path))
		$rel_js_path.="/";

	$rel_js_path.='prism_mod_loader/';
	$files = scandir($rel_js_path);

	for ($i=0;$i<count($files);$i++)
		if(preg_match("/.+\.conf$/",$files[$i]))
		{
			$file = fopen($rel_js_path.$files[$i], 'r');
			$text = "";
			if ($file)
			{
				while($line = fgets($file))
					$text .= $line;
				$data = json_decode($text);
				if ($data)
					$conf_mod[] = $data;
			}
		}
	return $conf_mod;
}
