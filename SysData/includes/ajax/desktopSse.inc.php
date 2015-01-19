<?php

class ajax_desktopSse
{
    function __construct()
    {
        //$this->push = new data_serverPush('travlrDesktopConnection');
        data_serverPush::serverStart();
    }
    
    public function launcherUpdate($htmlCode)
    {
    	error_log($htmlCode);
    	data_serverPush::serverPush($htmlCode, 'updateLauncher');
    }
    
    public function showMessage($message)
    {
    	data_serverPush::serverPush($message, 'showMessage');
    }

    
    public function editWindow($code, $title, $appID, $url)
    {
        $windowTask =
            array(
                 'task' => 'edit'
                ,'code' => $this->generateWindowCode($code)
                ,'title' => $title
                ,'appID' => $this->generateWindowCode($appID)
                ,'url' => $url
            );
        $this->windowTask($windowTask);
    }
    
    public function delWindow($code)
    {
        $windowTask =
            array(
                 'task' => 'del'
                ,'code' => $this->generateWindowCode($code)
            );
        $this->windowTask($windowTask);
    }
    
    public function moveWindow($code, $do)
    {
        $windowTask =
            array(
                 'task' => 'move'
                ,'code' => $this->generateWindowCode($code)
                ,'windowDo' => $do
            );
        $this->windowTask($windowTask);
    }
    
    public function logout($message)
    {
        global $siteRemoteRoot;
        data_serverPush::serverPush($siteRemoteRoot.'/login.php', 'redirect');
        $userTied  = new data_tied(false, 'user');
        $userTied->rawWrite(array('kicked' => true, 'kickedMessage' => $message, 'loginStage' => 'login', 'lastUsername' => $userTied->data['username']));
    }
    
    public function redirect($url)
    {
    	data_serverPush::serverPush($url, 'redirect');
    }
    
    private function windowTask($windowTask)
    {
        data_serverPush::serverPush(json_encode($windowTask), 'windowTask');
    }
    
    private function generateWindowCode($code)
    {
        return base64_encode('ewko2'.$code);
    }
}


?>