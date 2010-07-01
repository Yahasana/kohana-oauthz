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

    public $format = 'json';

    public function __construct(array $params = array())
    {
        foreach($params as $key => $val)
        {
            $this->$key = $val;
        }
    }

    public function json()
    {
        if(empty($this->error))
        {
            $json = get_object_vars($this);
            foreach($json as $key => $val)
            {
                if(empty($val))
                {
                    unset($json[$key]);
                }
            }
        }
        else
        {
            $json = array('error' => $this->error);

            if(property_exists($this, 'state') AND $this->state)
                $json['state']  = $this->state;
        }
        return json_encode($json);
    }

    public function xml()
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $oauth = $doc->createElement('OAuth');
        $doc->appendChild($oauth);

        if(empty($this->error))
        {
            foreach(get_object_vars($this) as $key => $val)
            {
                if(empty($val)) continue;

                $node = $doc->createElement($key);
                $node->appendChild($doc->createTextNode($val));
                $oauth->appendChild($node);
            }
        }
        else
        {
            $node = $doc->createElement('error');
            $node->appendChild($doc->createTextNode($this->error));
            $oauth->appendChild($node);

            if(property_exists($this, 'state') AND $this->state)
            {
                $node = $doc->createElement('state');
                $node->appendChild($doc->createTextNode($this->state));
                $oauth->appendChild($node);
            }
        }
        return $doc->saveXML();
    }

    public function query()
    {
        if(empty($this->error))
        {
            $form = get_object_vars($this);
            foreach($form as $key => $val)
            {
                if(empty($val))
                {
                    unset($form[$key]);
                }
            }
        }
        else
        {
            $form = array('error' => $this->error);

            if(property_exists($this, 'error_description') AND $this->error_description)
                $form['error_description']  = $this->error_description;

            if(property_exists($this, 'error_uri') AND $this->error_uri)
                $form['error_uri']  = $this->error_uri;

            if(property_exists($this, 'state') AND $this->state)
                $form['state']  = $this->state;
        }
        return Oauth::build_query($form);
    }

    public function html()
    {
        $text = '<dl>';
        if(empty($this->error))
        {
            $form = get_object_vars($this);
            foreach($form as $key => $val)
            {
                if(empty($val)) continue;

                $text .= '<dt>'.$key.'</dt><dd>'.$val.'</dd>';
            }
        }
        else
        {
            $text .= '<dt>error</dt><dd>'.$this->error.'</dd>';

            if(property_exists($this, 'state') AND $this->state)
                $text .= '<dt>state</dt><dd>'.$this->state.'</dd>';
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
        $format = $this->format;
        $this->format = NULL;
        switch($format)
        {
            case 'xml':
            case 'application/xhtml+xml':
                $res = $this->xml();
                $this->format = 'application/xml';
                break;
            case 'form':
                $res = $this->query();
                $this->format = 'application/x-www-form-urlencoded';
                break;
            case 'text/html':
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

    public function __set($key, $val)
    {
        if( ! empty($val)) $this->$key = $val;
    }

} // END Oauth_Token
