<?php
class ajax_desktopAjax
{
    private $transmitObject;
    function __construct()
    {
        $this->ready = false;
        $this->transmitObject = array(
                                'error' => 'none',
                                'launcher' => array('toUpdate' => '0'),
                                'windowTasks' => array(),
                                'desktopTasks' => array(),
                                'message' => '**nomessage**',
                                'clock' => ''
                            );
    }
    
    public function launcherUpdate($htmlCode)
    {
        $this->transmitObject['launcher'] = array('toUpdate' => '1', 'html' => $htmlCode);
    }
    
    public function showMessage($message)
    {
    	$this->transmitObject['message'] = $message;
    	$this->ready = true;
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
        $this->transmitObject['error'] = 'logout';
        $this->transmitObject['redirect'] = $siteRemoteRoot.'/login.php';
        $userTied  = new data_tied(false, 'user');
        $userTied->rawWrite(array('kicked' => true, 'kickedMessage' => $message, 'loginStage' => 'login', 'lastUsername' => $userTied->data['username']));
        $this->ready = true;
    }
    
    private function windowTask($windowTask)
    {
        $this->transmitObject['windowTasks'][] = $windowTask;
        $this->ready = true;
    }
    
    private function generateWindowCode($code)
    {
        return base64_encode('ewko2'.$code);
    }
       
    public function send()
    {
        global $currTime, $db, $userTied;
        $db->query("UPDATE Windows SET
                        forceActive='0'
                        ,forceHide='0'
                        ,forceFS='0' 
                        ,FSstate='0' 
                        ,forceReload='0'
                        ,hasChanged='0'
                        WHERE userID=':1'",
                $userTied->userID);
        $this->transmitObject['clock'] = $currTime;
        $this->sendRaw($this->transmitObject);
    }
    
    public static function timeout()
    {
        global $currTime;
        ajax_desktop::sendRaw(array('error' => 'timeout', 'clock' => $currTime));
    }
    
    public static function sendRaw($data)
    {
        header('Content-type: application/json');
        echo json_encode($data);
        die();
    }
}


?>