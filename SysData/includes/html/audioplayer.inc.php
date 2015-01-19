<?php
class html_audioplayer
{
	function __construct($containerdiv = 'audio', $urls = array(), $playerautoload = TRUE)
	{
		$this->containerdiv = $containerdiv;
		$this->playerautoload = $playerautoload;
		$this->playerautostart = FALSE;
		$this->songnamediv = '';
		$this->songurls = array();
		if (is_array($urls))
		{
			$i = 0;
			foreach ($urls as $url)
			{
				if (is_array($url))
				{
					$this->songurls[] = array('jscriptid' => $i, 'url' => $url[0], 'name' => $url[1]);
				} else {
					$this->songurls[] = array('jscriptid' => $i, 'url' => $url, 'name' => 'song'.$i);
				}
				$i++;
			}
		} else {
			$this->songurls[] = array('jscriptid' => '0', 'url' => $urls);
		}
	}
	
	public function autostart($start = TRUE)
	{
		if ($start)
		{
			$this->playerautostart = TRUE;
			$this->playerautoload = FALSE;
		} else {
			$this->playerautostart = FALSE;
		}
	}
	
	public function autoload($start = TRUE)
	{
		if ($start)
		{
			$this->playerautoload = TRUE;
			$this->playerautostart = FALSE;
		} else {
			$this->playerautoload = FALSE;
		}
	}
	
	public function addsong($url, $name)
	{
		$this->songurls[] = array('jscriptid' => count($this->songurls), 'url' => $url, 'name' => $name);
	}
	
	public function generatecode()
	{
		if ($this->playerautoload)
		{
			$playloadcode = 'addLoadEvent(showPlayer);';
		} else {
			$playloadcode = '';
		}
		if ($this->playerautostart)
		{
			$playstartcode = 'addLoadEvent(loadPlayer);';
		} else {
			$playstartcode = '';
		}
		$html = '
		<script type="text/javascript">
		
		function addLoadEvent(func) {
  			var oldonload = window.onload;
  			if (typeof window.onload != \'function\') {
    			window.onload = func;
  			} else {
    		window.onload = function() {
      			if (oldonload) {
        			oldonload();
      			}
      			func();
    		}
  		}
	}
 	var allowplay = \'1\';
    function loadPlayer() {
		var audioPlayer = new Audio();
		audioPlayer.controls="controls";
		audioPlayer.addEventListener(\'ended\',nextSong,false);
		audioPlayer.addEventListener(\'error\',errorFallback,true);
		document.getElementById("'.addslashes($this->containerdiv).'").appendChild(audioPlayer);
		nextSong();
    }
    
    function showPlayer() {
		var audioPlayer = new Audio();
		audioPlayer.controls="controls";
		audioPlayer.addEventListener(\'ended\',nextSong,false);
		audioPlayer.addEventListener(\'error\',errorFallback,true);
		document.getElementById("'.addslashes($this->containerdiv).'").appendChild(audioPlayer);
		nextSong();
		audioPlayer.pause();
    }

    function nextSong() {
        if(urls[next]!=undefined) {
            var audioPlayer = document.getElementsByTagName(\'audio\')[0];
            if(audioPlayer!=undefined) {
                audioPlayer.src=urls[next][0];
                '.$this->songnamediv.'
                audioPlayer.load();
                audioPlayer.play();
                next++;
            } else {
                loadPlayer();
            }
        } else {
        }
    }
    function errorFallback() {
            nextSong();
    }
    function playPause() {
        var audioPlayer = document.getElementsByTagName(\'audio\')[0];
        if(audioPlayer!=undefined) {
            if (audioPlayer.paused) {
                audioPlayer.play();
            } else {
                audioPlayer.pause();
            }
        } else {
            loadPlayer();
        }
    }
    function pickSong(num) {
        next = num;
        nextSong();
    }
 
    var urls = new Array();
    ';
    	foreach($this->songurls as $song){
    		$html .= 'urls['.addslashes($song['jscriptid']).'] = new Array(\''.addslashes($song['url']).'\', \''.addslashes($song['name']).'\');'.PHP_EOL;
    	}

    $html .= 'var next = 0;
    '.$playloadcode.PHP_EOL.$playstartcode.'
    </script>';
    return $html;
	}
	
	public function displayname($namediv)
	{
		$this->songnamediv = 'document.getElementById(\''.addslashes($namediv).'\').innerHTML = urls[next][1];'.PHP_EOL;
	}
	
	public function songsarray()
	{
		foreach($this->songurls as $songdata)
		{
			if (isset($songdata['name']))
			{
				$returnsongarray[] = array(
				 'jscriptid' => $songdata['jscriptid']
				,'url' => $songdata['url']
				,'name' => $songdata['name']
				);
			} else {
				$returnsongarray[] = array(
				 'jscriptid' => $songdata['jscriptid']
				,'url' => $songdata['url']
				,'name' => 'song'
				);
			}
		}
		if (isset($returnsongarray)){
			return $returnsongarray;
		} else {
			return FALSE;
		}
	}
	
	public function __tostring()
	{
		return $this->generatecode();
	}
	
}
?>