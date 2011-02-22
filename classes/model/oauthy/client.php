<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Handle OAuth data storage
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthy
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * @see         Oauthy_Model
 * *
 */
class Model_Oauthy_Client extends Oauthy_Model {

    public function get($user_id)
    {
        return ctype_digit($user_id)
            ? DB::select('user_id','client_id','redirect_uri','confirm_type','client_level','modified','created','scope','expired_date','remark','client_desc')
                ->from('t_oauth_clients')
                ->where('user_id', '=', $user_id)
                ->execute($this->_db)
                ->current()
            : NULL;
    }

    /**
     * Insert client
     *
     * @access	public
     * @param	array	$params
	 *      confirm_type: Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned
	 *      client_level: diferent client levels have different max request times
	 *      expired_date: date time
	 *
     * @return	mix     array(insert_id, affect_rows) or validate object
     */
    public function append(array $params)
    {
        if(isset($params['expired_date']) AND $timetamp = strtotime($params['expired_date']))
            $params['expired_date'] = $timetamp;
        else
            unset($params['expired_date']);

        $valid = Validate::factory($params)
			->rule('client_id', 'not_empty')
			->rule('redirect_uri', 'not_empty')
			->rule('modified', 'not_empty');

        $rules = array_intersect_key(array (
            'client_id'     => array ('max_length'  => array (128)),
            'redirect_uri'  => array ('max_length'  => array (512)),
            'confirm_type'  => array ('range'       => array (0,255)),
            'client_level'  => array ('range'       => array (0,255)),
            'scope'         => array ('max_length'  => array (512)),
            'expired_date'  => array ('range'       => array (0,4294967295)),
            'client_desc'   => array ('max_length'  => array (65535)),
        ), $params);

        foreach($rules as $field => $rule)
            foreach($rule as $r => $p)
                $valid->rule($field, $r, $p);

        if($valid->check())
        {
            $valid = $valid->as_array();

            foreach($valid as $key => $val)
            {
                if($val === '') $valid[$key] = NULL;
            }

            $valid['created'] = $_SERVER['REQUEST_TIME'];
            return DB::insert('t_oauth_clients', array_keys($valid))
                ->values(array_values($valid))
                ->execute($this->_db);
        }
        else
        {
            // Validation failed, collect the errors
            return $valid;
        }
    }

    /**
     * Update client
     *
     * @access	public
     * @param	int	    $user_id
     * @param	array	$params
	 *      confirm_type: Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned
	 *      client_level: diferent client levels have different max request times
	 *      expired_date: date time
	 *
     * @return	mix     update rows affect or validate object
     */
    public function update($user_id, array $params)
    {
        if(isset($params['expired_date']) AND $timetamp = strtotime($params['expired_date']))
            $params['expired_date'] = $timetamp;
        else
            unset($params['expired_date']);

        $valid = Validate::factory($params);

        $rules = array_intersect_key(array (
            'client_id'     => array ('not_empty'   => NULL, 'max_length' => array (128)),
            'redirect_uri'  => array ('not_empty'   => NULL, 'max_length' => array (512)),
            'confirm_type'  => array ('range'       => array (0,255)),
            'client_level'  => array ('range'       => array (0,255)),
            'scope'         => array ('max_length'  => array (512)),
            'expired_date'  => array ('range'       => array (0,4294967295)),
            'client_desc'   => array ('max_length'  => array (65535)),
        ), $params);

        foreach($rules as $field => $rule)
            foreach($rule as $r => $p)
                $valid->rule($field, $r, $p);

        if($valid->check())
        {
            $valid = $valid->as_array();

            foreach($valid as $key => $val)
            {
                if($val === '') $valid[$key] = NULL;
            }

            $valid['modifed'] = $_SERVER['REQUEST_TIME'];
            return DB::update('t_oauth_clients')
                ->set($valid)
                ->where('user_id', '=', $user_id)
                ->execute($this->_db);
        }
        else
        {
            // Validation failed, collect the errors
            return $valid;
        }
    }

    public function delete($client_id, $user_id)
    {
        return ctype_digit($user_id)
            ? DB::delete('t_oauth_clients')
                ->where('client_id', '=', $client_id)
                ->where('user_id', '=', $user_id)
                ->execute($this->_db)
            : NULL;
    }

    /**
     * List clients
     *
     * @access	public
     * @param	array	    $params
     * @param	Pagination	$pagination	default [ NULL ] passed by reference
     * @param	boolean	    $calc_total	default [ TRUE ] is needed to caculate the total records for pagination
     * @return	array       array('clients' => data, 'orderby' => $params['orderby'], 'pagination' => $pagination)
     */
    public function lists(array $params, $pagination = NULL, $calc_total = TRUE)
    {
        $pagination instanceOf Pagination OR $pagination = new Pagination;

        $sql = 'FROM `t_oauth_clients` ';

        // Customize where from params
        //$sql .= 'WHERE ... '

        // caculte the total rows
        if($calc_total === TRUE)
        {
            $pagination->total_items = $this->_db->query(
                Database::SELECT, 'SELECT COUNT(`user_id`) num_rows '.$sql, FALSE
            )->get('num_rows');

            $data['pagination'] = $pagination;

            if($pagination->total_items === 0)
            {
                $data['clients'] = array();
                isset($params['orderby']) AND $data['orderby'] = $params['orderby'];
                return $data;
            }
        }

        // Customize order by from params
        if(isset($params['orderby']))
        {
            switch($params['orderby'])
            {
                case 'level':
                    $sql .= ' ORDER BY client_level DESC';
                    break;
                case 'expired':
                    $sql .= ' ORDER BY expired_date DESC';
                    break;
                case 'update':
                    $sql .= ' ORDER BY modified DESC';
                    break;
                default:
                    $params['orderby'] = 'client';
                    $sql .= ' ORDER BY client_id DESC';
                    break;
            }
            $data['orderby'] = $params['orderby'];
        }

        $sql .= " LIMIT {$pagination->offset}, {$pagination->items_per_page}";

        $data['clients'] = $this->_db->query(Database::SELECT, 'SELECT * '.$sql, FALSE);

        return $data;
    }

} // END Model_Oauthy_Client
