<?php
/**
 * Access tokens and request tokens
 *
 * @author     sumh <oalite@gmail.com>
 * @package    Oauth
 * @copyright  (c) 2009 OALite team
 * @license    http://www.oalite.com/license.txt
 * @version    $id$
 * @link       http://www.oalite.com
 * @since      Available since Release 1.0
 * *
 */
class Oauth_Token {

    protected $format;

    public function __construct(array $params = array())
    {
        foreach($params as $key => $val)
        {
            $this->$key = $val;
        }
        if(empty($this->format))
        {
            if($format = key(Request::accept_type()))
            {
                $this->format = $format;
            }
            else
            {
                $this->format = 'application/json';
            }
        }
    }

    protected function json()
    {
        $json = get_object_vars($this);
        foreach($json as $key => $val)
        {
            if(empty($val))
            {
                unset($json[$key]);
            }
        }
        return json_encode($json);
    }

    protected function xml()
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $oauth = $doc->createElement('OAuth');
        $doc->appendChild($oauth);

        foreach(get_object_vars($this) as $key => $val)
        {
            if(empty($val)) continue;

            $node = $doc->createElement($key);
            $node->appendChild($doc->createTextNode($val));
            $oauth->appendChild($node);
        }

        return $doc->saveXML();
    }

    protected function form()
    {
        $form = get_object_vars($this);
        foreach($form as $key => $val)
        {
            if(empty($val))
            {
                unset($form[$key]);
            }
        }
        return Oauth::build_query($form);
    }

    protected function html()
    {
        $text = '<dl>';
        $form = get_object_vars($this);
        foreach($form as $key => $val)
        {
            if(empty($val)) continue;

            $text .= '<dt>'.$key.'</dt><dd>'.$val.'</dd>';
        }
        return $text.'</dl>';
    }

    /**
     * Serialize token to string that a server would respond to
     *
     * @access    public
     * @return    string
     */
    public function __toString()
    {
        if( ! empty($this->error))
            return 'error='.$this->error;
        $format = $this->format;
        $this->format = NULL;
        switch($format)
        {
            case 'xml':
            case 'xhtml':
                $res = $this->xml();
                $this->format = 'application/xml';
                break;
            case 'form':
                $res = $this->form();
                $this->format = 'application/x-www-form-urlencoded';
                break;
            case 'text':
            case 'html':
                $res = $this->html();
                $this->format = 'text/html';
                break;
            default:
                $res = $this->json();
                $this->format = 'application/json';
                break;
        }
        return $res;
    }
}
