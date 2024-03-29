<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 27 Jul 2011 14:55:22 GMT
 */

if (!defined('NV_IS_MOD_FLIPBOOK')) {
    die('Stop!!!');
}

$page_title = $module_info['site_title'];
$key_words = $module_info['keywords'];

$per_page = $nv_flipbook_setting['numsub'];

$array_search = [];
$key = $nv_Request->get_title('q', 'get,post', '');
$key = str_replace('+', ' ', $key);
$key = trim(nv_substr($key, 0, NV_MAX_SEARCH_LENGTH));
$array_search['key'] = $key;

$sfrom = nv_substr($nv_Request->get_title('sfrom', 'get', ''), 0, 10);
$array_search['sfrom'] = $sfrom;
$sto = nv_substr($nv_Request->get_title('sto', 'get', ''), 0, 10);
$array_search['sto'] = $sto;

$area = $nv_Request->get_int('area', 'get', 0);
$array_search['area'] = $area;
$cat = $nv_Request->get_int('cat', 'get', 0);
$array_search['cat'] = $cat;
$subject = $nv_Request->get_int('subject', 'get', 0);
$array_search['subject'] = $subject;
$sstatus = $nv_Request->get_int('status', 'get', 0);
$array_search['sstatus'] = $sstatus;
$is_advance = $nv_Request->get_int('is_advance', 'get', 0);
$array_search['is_advance'] = $is_advance;
$approval = $nv_Request->get_int('approval', 'get', 0);
$array_search['approval'] = $area;
$examine = $nv_Request->get_int('examine', 'get', 0);
$array_search['examine'] = $examine;

unset($m);
if (preg_match("/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/", $sfrom, $m)) {
    $sfrom1 = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
} else {
    $sfrom1 = 0;
}

unset($m);
if (preg_match("/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/", $sto, $m)) {
    $sto1 = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
} else {
    $sto1 = 0;
}

$base_url_rewrite = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&q=' . htmlspecialchars(nv_unhtmlspecialchars($key));
$where = '';
$search = false;
if (!empty($key) or !empty($area) or !empty($cat) or !empty($subject) or !empty($sstatus) or !empty($ssigner) or !empty($sfrom1) or !empty($sto1) or !empty($approval) or !empty($examine)) {
    $search = true;

    if (!empty($key)) {
        $dbkey = $db->dblikeescape($key);
        $keyhtml = nv_htmlspecialchars($key);
        $where .= " AND ( title LIKE '%" . $keyhtml . "%' OR code LIKE '%" . $keyhtml . "%' OR bodytext LIKE '%" . $dbkey . "%' )";
    }

    if (!empty($area)) {
        $base_url_rewrite .= "&area=" . $area;

        $tmp = $nv_flipbook_listarea[$area];
        $in = "";
        if (empty($tmp['subcats'])) {
            $in = " AND t2.area_id=" . $area;
        } else {
            $in = $tmp['subcats'];
            $in[] = $area;
            $in = " AND t2.area_id IN(" . implode(",", $in) . ")";
        }

        $where .= $in;
    }

    if (!empty($cat)) {
        $base_url_rewrite .= "&cat=" . $cat;

        $tmp = $nv_flipbook_listcat[$cat];
        $in = "";
        if (empty($tmp['subcats'])) {
            $in = " AND cid=" . $cat;
        } else {
            $in = $tmp['subcats'];
            $in[] = $cat;
            $in = " AND cid IN(" . implode(",", $in) . ")";
        }

        $where .= $in;
    }

    if (!empty($subject)) {
        $where .= " AND sid=" . $subject;
        $base_url_rewrite .= "&subject=" . $subject;
    }

    if ($approval != 2 && $module_config[$module_name]['activecomm']==1) {
        $where .= " AND approval=" . $approval;
        $base_url_rewrite .= "&approval=" . $approval;
    }

    if (!empty($examine) && $module_config[$module_name]['activecomm']==1) {
        $where .= " AND eid=" . $examine;
        $base_url_rewrite .= "&examine=" . $examine;
    }

    if (!empty($sfrom1)) {
        $where .= " AND publtime>=" . $sfrom1;
        $base_url_rewrite .= "&sfrom=" . $sfrom;
    }

    if (!empty($sto1)) {
        $where .= " AND publtime<=" . $sto1;
        $base_url_rewrite .= "&sto=" . $sto;
    }

    if (!empty($sstatus)) {
        if ($sstatus == 1) {
            $where .= " AND ( exptime=0 OR exptime>=" . NV_CURRENTTIME . ")";
            $base_url_rewrite .= "&status=" . $sstatus;
        } else {
            $where .= " AND ( exptime!=0 AND exptime<" . NV_CURRENTTIME . ")";
            $base_url_rewrite .= "&status=" . $sstatus;
        }
    }

    if ($is_advance) {
        $base_url_rewrite .= "&is_advance=" . $is_advance;
    }
}

$page = $nv_Request->get_int('page', 'get', 1);
if ($page > 1) {
    $base_url_rewrite .= '&page=' . $page;
}

if (!$search) {
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme(nv_theme_flipbook_search([], "", 0));
    include NV_ROOTDIR . '/includes/footer.php';
}

$db->sqlreset()
    ->select('COUNT(*)')
    ->from(NV_PREFIXLANG . '_' . $module_data . '_row t1')
    ->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_row_area t2 ON (t1.id=t2.row_id)')
    ->where('status=1' . $where);

$all_page = $db->query($db->sql())->fetchColumn();

$_order = ($nv_flipbook_setting['typeview'] == 1 or $nv_flipbook_setting['typeview'] == 4) ? 'ASC' : 'DESC';
$_order_param = ($nv_flipbook_setting['typeview'] == 0 or $nv_flipbook_setting['typeview'] == 1) ? 'publtime' : 'addtime';

$db->select('*')
    ->order($_order_param . ' ' . $_order)
    ->limit($per_page)
    ->offset(($page - 1) * $per_page);

$result = $db->query($db->sql());

$array_data = raw_law_list_by_result($result, $page, $per_page);

$generate_page = '';
if ($all_page > $per_page) {
    $url_link = $_SERVER['REQUEST_URI'];
    if (strpos($url_link, '&page=') > 0) {
        $url_link = substr($url_link, 0, strpos($url_link, '&page='));
    } elseif (strpos($url_link, '?page=') > 0) {
        $url_link = substr($url_link, 0, strpos($url_link, '?page='));
    }
    $_array_url = array(
        'link' => $url_link,
        'amp' => '&page='
    );
    $generate_page = nv_generate_page($_array_url, $all_page, $per_page, $page);
}

$contents = nv_theme_flipbook_search($array_data, $generate_page, $all_page, $array_search);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
