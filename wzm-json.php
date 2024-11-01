<?php
/**
 * Plugin Name:       Wzm Json
 * Description:       将首页可能用到的数据保存为 JSON 格式的文件，你可以引用它，来提升首页的加载速度；您可以在“设置”中找到它。
 * Version:           1.0.1
 * Requires at least: 5.7.2
 * Requires PHP:      7.2.34
 * Author:            响当当是我大姐头
 * Author URI:        https://www.wangzhaomin.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * 免费插件，遵循 GPLv2 及后续升级协议，追逐知识，共同进步。
 */



/**
 *  安全措施：如果直接调用此文件，则程序中止。
 */
//  如果直接调用此文件，则程序中止。
if ( ! defined( 'WPINC' ) ) {
    die;
}



/**
 *  5. 其他设置
 *      5.1. 注册全局变量
 *          声明插件最终生成的JSON文件所处位置
 *      5.2. 引入各种文件
 *
 *
 *  注意事项：
 *      1. JSON文件置于根目录的原因：方便用户进行调用。
 *          1.1. 插件安装提示 Call to undefined function get_home_path()；
 *               因此将 get_home_path() 替换为 ABSPATH（代码来源于 wp-config.php 文件）。
 *               参考文档：https://wordpress.org/support/topic/uncaught-error-call-to-undefined-function-get_home_path-after-update-to-3-5-1/
 *      2. 引入文件顺序不代表制作时顺序，这是重新调整的顺序。
 */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
global $wzm_file;
$wzm_file = ABSPATH.'wzm.json';

/**
 *  引入文件
 *      1. wzm-json-functions.php 函数主体，用于生成最终的JSON文件。
 *      2. wzm-json-regenerate.php 重新生成JSON文件，通过表单里的“立即生成”按钮触发。
 */
//  1. wzm-json-functions.php 函数主体，用于生成最终的JSON文件。
require_once plugin_dir_path( __FILE__ ).'wzm-json-functions.php';
//  2. wzm-json-regenerate.php 重新生成JSON文件，通过表单里的“立即生成”按钮触发。
require_once plugin_dir_path( __FILE__ ).'wzm-json-regenerate.php';



/**
 *  4. 插件的控制面板
 *      4.1. 检测用户是否具有管理员权限
 */
if ( ! function_exists( 'wzm_control_panel' ) ) {
    function wzm_control_panel() {
        if ( current_user_can( 'manage_options' ) ) {
            //  引入文件，构造插件的控制面板
            require_once plugin_dir_path( __FILE__ ).'wzm-json-options.php';
        }
    }
    add_action( 'after_setup_theme', 'wzm_control_panel' );
}



//  插件三部曲：激活、停止与卸载

/**
 *  1.  激活插件
 *      1.1. 注册选项
 *      1.2. 重复生成检测
 *
 *
 *  注意事项：
 *      1. 使用绝对路径的原因：
 *          相对路径不工作。
 *      2.  register_activation_hook() 中启用的功能函数只运行一次，因此多用于添加选项和注册数据库表；
 *          而插件的控制面板和交互菜单应在单独的功能中完成。
 *
 *  参考文档：
 *      1. register_activation_hook()：
 *          https://developer.wordpress.org/reference/functions/register_activation_hook/
 */
function wzm_register() {
    // 添加选项，方便用户自行设置。
    add_option('wzm_posts_per_page','4');
    add_option('wzm_products_per_page','9');
    add_option('wzm_json_update_time','21:30:00');

    global $wzm_file;
    // 检查JSON文件是否已生成，避免重复生成。
    if ( ! is_file( $wzm_file) ) {
        wzm_json_functions();
    }
}
register_activation_hook( __FILE__, 'wzm_register' );



/**
 *  2. 停止插件
 *      2.1. 停止运行插件的控制面板函数
 *      2.2. 删除定时器任务
 *
 *  参考文档：
 *      1. register_deactivation_hook()：
 *          https://developer.wordpress.org/reference/functions/register_deactivation_hook/
 */
function wzm_prohibit() {
    wzm_control_panel();
    wp_clear_scheduled_hook( 'wzm_cron_hook' );
}
register_deactivation_hook( __FILE__, 'wzm_prohibit' );



/**
 *  3. 卸载插件
 *      3.1. 删除已注册的选项
 *      3.2. 删除生成的JSON文件
 *      3.3. 删除定时器
 *
 *
 *  参考文档：
 *      1. register_deactivation_hook()：
 *          https://developer.wordpress.org/reference/functions/register_deactivation_hook/
 */
function wzm_uninstall(){
    delete_option('wzm_posts_per_page');
    delete_option('wzm_products_per_page');
    delete_option('wzm_json_update_time');

    global $wzm_file;
    if( is_file( $wzm_file) ){
        unlink($wzm_file);
    }

    if ( wp_next_scheduled( 'wzm_cron_hook' ) ) {
        wp_clear_scheduled_hook( 'wzm_cron_hook' );
    }
}
register_uninstall_hook( __FILE__, 'wzm_uninstall' );