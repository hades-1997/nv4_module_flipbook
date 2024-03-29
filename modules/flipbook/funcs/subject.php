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

$alias = isset($array_op[1]) ? $array_op[1] : "";

if (!preg_match("/^([a-z0-9\-\_\.]+)$/i", $alias)) {
    nv_redirect_location(NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true);
}

$catid = 0;
foreach ($nv_flipbook_listsubject as $c) {
    if ($c['alias'] == $alias) {
        $catid = $c['id'];
        break;
    }
}

if (empty($catid)) {
    nv_redirect_location(NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true);
}

// Set page title, keywords, description
$page_title = $mod_title = $nv_flipbook_listsubject[$catid]['title'];
$key_words = empty($nv_flipbook_listsubject[$catid]['keywords']) ? $module_info['keywords'] : $nv_flipbook_listsubject[$catid]['keywords'];
$description = empty($nv_flipbook_listsubject[$catid]['introduction']) ? $page_title : $nv_flipbook_listsubject[$catid]['introduction'];

$page = 1;
if (isset($array_op[2])) {
    if (preg_match('/^page\-([0-9]{1,10})$/', $array_op[2], $m)) {
        $page = intval($m[1]);
    } else {
        nv_redirect_location(NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name);
    }
}
if (isset($array_op[3])) {
    nv_redirect_location(NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name);
}
$per_page = $nv_flipbook_setting['numsub'];
$base_url = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=subject/" . $nv_flipbook_listsubject[$catid]['alias'];

if (!defined('NV_IS_MODADMIN') and $page < 5) {
    $cache_file = NV_LANG_DATA . '_' . $module_info['template'] . '_' . $op . '_' . $catid . '_' . $page . '_' . NV_CACHE_PREFIX . '.cache';
    if (($cache = $nv_Cache->getItem($module_name, $cache_file)) != false) {
        $contents = $cache;
    }
}

if (empty($contents)) {
    $order = ($nv_flipbook_setting['typeview'] == 1) ? "ASC" : "DESC";

    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . NV_PREFIXLANG . "_" . $module_data . "_row WHERE status=1 AND sid=" . $catid . " ORDER BY addtime " . $order . " LIMIT " . $per_page . " OFFSET " . ($page - 1) * $per_page;
    $result = $db->query($sql);
    $query = $db->query("SELECT FOUND_ROWS()");
    $all_page = $query->fetchColumn();

    $generate_page = nv_alias_page($page_title, $base_url, $all_page, $per_page, $page);
    $array_data = raw_law_list_by_result($result, $page, $per_page);
    $contents = nv_theme_flipbook_subject($array_data, $generate_page, $nv_flipbook_listsubject[$catid]);

    if (!defined('NV_IS_MODADMIN') and $contents != '' and $cache_file != '') {
        $nv_Cache->setItem($module_name, $cache_file, $contents);
    }
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
