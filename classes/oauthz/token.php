<?php
/**
 * Access tokens and request tokens
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 * *
 */
class Oauthz_Token {

    public $format = 'json';

    public function __construct(array $params = array())
    {
        foreach($params as $key => $val)
        {
            $this->$key = $val;
        }
    }

    public function as_json()
    {
        if(empty($this->error))
        {
            $json = get_object_vars($this);
            foreach($json as $key => $val)
            {
                if(empty($val)) unset($json[$key]);
            }
        }
        else
        {
            $json = array('error' => $this->error);

            isset($this->state) AND $json['state'] = $this->state;
        }

        // JSON_UNESCAPED_SLASHES
        return str_replace('\\/', '/', json_encode($json));
    }

    public function as_xml()
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

            if(isset($this->state))
            {
                $node = $doc->createElement('state');
                $node->appendChild($doc->createTextNode($this->state));
                $oauth->appendChild($node);
            }
        }
        return $doc->saveXML();
    }

    public function as_query()
    {
        if(empty($this->error))
        {
            $form = get_object_vars($this);
            foreach($form as $key => $val)
            {
                if(empty($val)) unset($form[$key]);
            }
        }
        else
        {
            $form = array('error' => $this->error);

            isset($this->state) AND $form['state'] = $this->state;

            isset($this->error_uri) AND $form['error_uri'] = $this->error_uri;

            isset($this->error_description) AND $form['error_description'] = $this->error_description;
        }
        return http_build_query($form, '', '&');
    }

    public function as_html()
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

            isset($this->state) AND $text .= '<dt>state</dt><dd>'.$this->state.'</dd>';
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
                $res = $this->as_xml();
                $this->format = 'application/xml';
                break;
            case 'form':
                $res = $this->as_query();
                $this->format = 'application/x-www-form-urlencoded';
                break;
            case 'text/html':
                $res = $this->as_html();
                $this->format = 'text/html';
                break;
            default:
                $res = $this->as_json();
                $this->format = 'application/json';
                break;
        }
        return $res;
    }

    public function __set($key, $val)
    {
        if( ! empty($val)) $this->$key = $val;
    }

} // END Oauthz_Token
