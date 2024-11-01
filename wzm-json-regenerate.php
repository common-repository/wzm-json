<?php
/**
 *  1. 给表单添加“立即生成”按钮
 *      1.1. 确定当前页面的 $hook 名称，使得 JS文件只在当前页面下启用。
 *      1.2. 添加“立即生成”按钮的样式，参考 wzm-json-options.php 里的 2.1 中。
 *      1.3. 创建JS文件，使得点击按钮能发出AJAX请求。
 *      1.4. 接收发出的AJAX请求，并处理请求。
 *
 *
 *  注意事项：
 *      1. 产生 400 bad request 的可能原因有：
 *          1.1. 函数缺失，检查 定义函数、JS文件和回调函数是否都正确引入到主程序中；
 *              我的之前报错的原因就是没有将接受请求的函数文件引入到程序主体。
 *          1.2. 缺少 action，JS文件里的 date 部分未注册 action 属性和值。
 *          1.3. 名称不相符， 1.2. 中提到的 action 属性的值 要和 回调函数绑定的钩子要相同。
 *              如果 js文件是这样的： date {'action': 'ABC'},
 *              那么回调函数绑定的挂钩则应是这样的：“add_action( 'wp_ajax_ABC', 'callback_function' )”。
 *      2. 产生 500 (Internal Server Error) 的原因：
 *          2.1. ERROR 500 表示：服务器接收到了请求，但在处理请求时出错。
 *          2.2. 检查接收函数内部的处理函数，发现昨天修改时添加的额外代码删除只删除了一半，彻底删除后问题解决。
 *
 *
 *  参考文档：
 *      1. 其他：
 *          path://wp-content/plugins/better-search-replace/includes/class-bar-admin.php #57
 *          path://wp-content/plugins/better-search-replace/includes/class-bar-main.php #107
 *      2. admin_enqueue_scripts 介绍：
 *          https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
 *      3. 接受AJAX请求：
 *          https://stackoverflow.com/questions/17855846/using-ajax-in-a-wordpress-plugin
 *          https://developer.wordpress.org/reference/hooks/wp_ajax_action/
 *          https://developer.wordpress.org/plugins/javascript/ajax/#url
 *      4. AJAX请求状态码介绍
 *          https://blog.csdn.net/qq_33454884/article/details/90288101
 */
/**
 *  判断页面的$hook名称，数组中的第一个值就是；
 *  使用时需复制代码到 wzm-json-options.php 里的 wzm_options_primary_html() 中，
 *  在其他地方使用会报错：未定义 get_current_screen()函数。
 */
//  1.1. 确定当前页面的 $hook 名称，使得 JS文件只在当前页面下启用。
//  $abc = get_current_screen();
//  print_r($abc);


//  1.3. 创建JS文件，使得点击按钮能发出AJAX请求。
if ( ! function_exists( 'wzm_scripts' ) ) {
    function wzm_scripts( $hook ) {
        if ( 'settings_page_wzm-json' === $hook ) {
            wp_register_script( 'wzm-json-ajax', plugin_dir_url( __FILE__ ) . 'wzm-json-regenerate-ajax.js', array('jquery'), '1.0', true );
            wp_enqueue_script('wzm-json-ajax');
        }
    }
    add_action( 'admin_enqueue_scripts', 'wzm_scripts' );
}

//  1.4. 接收发出的AJAX请求，并处理请求。
if ( ! function_exists( 'wzm_button_regenerate_callback' ) ) {
    function wzm_button_regenerate_callback() {
        wzm_json_functions();
        wp_die();
    }
    add_action( 'wp_ajax_wzm_button_regenerate', 'wzm_button_regenerate_callback' );
}