<?php defined('SYSPATH') or die('No direct script access.');        
    if(Request::$current->param('id'))
        $uri = Request::$current->controller.'/'.Request::$current->action.'/';
    else
        $uri = Request::$current->uri.'/';
?><table><caption><h3>Service error codes description</h3></caption><thead>
    <tr><th>Request Stage</th><th>Error Code</th><th>Description</th><th>URI</th></tr>
    </thead><?php
    if(isset($code_errors))
    {
        echo '<tbody>';
        $count = count($code_errors);
        foreach($code_errors as $error_code => $error_info)
        {
            ?><tr><?php
                if($count) {
                    echo '<th rowspan="'.$count.'">'.__('Authorization Response').'</th>';
                    $count = FALSE;
                }
            ?><td><a href="/<?php echo $uri.$error_code;
            ?>"><?php echo $error_code; ?></a></td><td><?php echo $error_info['error_description']; ?></td><td>/<?php
                echo $uri.$error_code; ?></td></tr><?php
        }
        echo '</tbody>';
    }

    if(isset($token_errors))
    {
        echo '<tbody>';
        $count = count($token_errors);
        foreach($token_errors as $error_code => $error_info)
        {
            ?><tr><?php
                if($count) {
                    echo '<th rowspan="'.$count.'">'.__('Access Token Response').'</th>';
                    $count = FALSE;
                }
            ?><td><a href="/<?php echo $uri.$error_code;
            ?>"><?php echo $error_code; ?></a></td><td><?php echo $error_info['error_description']; ?></td><td>/<?php
                echo $uri.$error_code; ?></td></tr><?php
        }
        echo '</tbody>';
    }

    if(isset($access_errors))
    {
        echo '<tbody>';
        $count = count($access_errors);            
        foreach($access_errors as $error_code => $error_info)
        {
            ?><tr><?php
                if($count) {
                    echo '<th rowspan="'.$count.'">'.__('Access Protected Resource').'</th>';
                    $count = FALSE;
                }
            ?><td><a href="/<?php echo $uri.$error_code;
            ?>"><?php echo $error_code; ?></a></td><td><?php echo $error_info['error_description']; ?></td><td>/<?php
                echo $uri.$error_code; ?></td></tr><?php
        }
        echo '</tbody>';
    }
?></table>