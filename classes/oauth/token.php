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
        $json = get_class_vars(__CLASS__);
        foreach($json as $key => $val)
        {
            if(empty($val))
            {
                unset($json[$key]);
                continue;
            }
        }
        return json_encode($json);
    }

    public function xml()
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $oauth = $doc->createElement('OAuth');
        $doc->appendChild($oauth);

        foreach(get_class_vars(__CLASS__) as $key => $val)
        {
            if(empty($val)) continue;

            $node = $doc->createElement($key);
            $node->appendChild($doc->createTextNode($val));
            $oauth->appendChild($node);
        }

        return $doc->saveXML();
    }

    public function form()
    {
        $form = get_class_vars(__CLASS__);
        foreach($form as $key => $val)
        {
            if(empty($val))
            {
                unset($form[$key]);
                continue;
            }
        }
        return Oauth::build_query($form);
    }

    /**
     * Serialize token to string that a server would respond to
     *
     * @access    public
     * @return    string
     */
    public function __toString()
    {
        switch($this->format)
        {
            case 'json':
                $res = $this->json();
                break;
            case 'xml':
                $res = $this->xml();
                break;
            case 'form':
                $res = $this->form();
                break;
            default:
                $res = 'Unsupport format';
                break;
        }
        return $res;
    }
}
