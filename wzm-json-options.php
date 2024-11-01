<?php
/**
 *  1. 启用控制面板
 *      1.1. 在栏目"设置"下启用控制面板
 *      1.2. 启用帮助选项卡（插件设置页面，右上角的帮助按钮，点击可启用一个下拉菜单）
 *
 *
 *  注意事项：
 *      1. 添加设置页面的相关函数介绍：
 *          add_menu_page() 添加到后台主菜单，和栏目“设置”并列。
 *          add_submenu_page() 添加到后台主菜单中的某个栏目之下，包括栏目“设置”。
 *          add_options_page() 添加到后台主菜单中栏目“设置”之下。
 *      2. 帮助选项卡和页面是一一对应的关系。
 *
 *  参考文档：
 *      1. add_options_page
 *          https://developer.wordpress.org/reference/functions/add_options_page/
 *      2. add_help_tab
 *          https://developer.wordpress.org/reference/classes/wp_screen/add_help_tab/
 *          path://wp-admin/admin-general.php
 */
//  引入帮助选项卡
require_once plugin_dir_path( __FILE__ ).'wzm-json-help-tabs.php';
if ( ! function_exists( 'wzm_json_menu' ) ) {
    function wzm_json_menu() {
        //  创建设置页面，也可以说注册控制面板的位置
        $wzm_json_options_page = add_options_page(
            '生成 Json 数据',
            '生成 Json 数据',
            'manage_options',
            'wzm-json',
            'wzm_options_primary_html'
        );
        //  加载设置页面的同时加载帮助选项卡
        add_action( 'load-'.$wzm_json_options_page, 'wzm_help_tabs_primary' );
    }
}
add_action( 'admin_menu', 'wzm_json_menu' );



/**
 *  2. 创建控制面板的样式
 *      2.1 创建提交表单
 *      2.2 添加“立即生成”按钮
 *
 *
 *  注意事项：
 *      1. settings_fields()：
 *          用于输出设置页面的nonce（随机数，用于安全验证），action和option_page字段，如果没有这个，则不能正确提交表单。
 *          当没有时，点击提交按钮，页面会跳转到 options.php 页面，同时有报错提示，且插件选项未更改；
 *          当存在时，点击提交按钮，页面不会跳转，同时提示保存成功，插件选项已更改。
 *
 *  参考文档：
 *      1. 表单样式参考：
 *          path://wp-admin/options-general.php
 *          path://wp-admin/options-reading.php
 *          path://wp-content/plugins/better-search-replace
 *          https://www.solagirl.net/creating-an-admin-interface-settings-api.html
 *      2. settings_fields()：
 *          https://developer.wordpress.org/reference/functions/settings_fields/
 *      3. 其他：
 *          https://codex.wordpress.org/Settings_API
 */

function wzm_options_primary_html(){
    ?>

    <div class="wrap">
        <h1>生成 Json 数据</h1>
        <form method="post" action="options.php">
            <?php settings_fields('wzm_json'); ?>
            <table class="form-table" role="presentation">
                <tbody>
                <tr>
                    <th scope="row"><label for="wzm_posts_per_page">每个栏目至多显示文章</label></th>
                    <?php
                    /**
                     *  input 中 step 属性：用于指定<input>元素中合法编号之间的间隔。
                     *
                     *  示例：在“type=number”中如果“step=1”，则点击input输入框右侧的步进箭头（即上下箭头，又叫数字微调器），
                     *  每次增加或减少的值为1，如果手动填写则数字为整数；
                     *
                     *  如果“step=3”，则点击步进箭头时，每次增加或减少的值为3，如果手动填写则数字为整数的同时还要是3的倍数，如果不是则报错；
                     *  如果“step=0.1”，则点击步进箭头时，每次增加或减少的值为0.1，如果手动填写则数字为浮点数（xx.x）如果不是则报错。
                     *
                     *  https://www.w3schools.com/tags/att_input_step.asp
                     *  https://www.cnblogs.com/bluealine/p/7992305.html
                     */
                    ?>
                    <td><input name="wzm_posts_per_page" type="number" step="1" min="1" max="20" id="wzm_posts_per_page" value="<?php form_option( 'wzm_posts_per_page' ); ?>" class="small-text" /> 篇</td>
                </tr>
                <tr>
                    <th><label for="wzm_products_per_page">精选产品至多显示</label></th>
                    <td><input name="wzm_products_per_page" type="number" step="1" min="1" max="20" id="wzm_products_per_page" value="<?php form_option( 'wzm_products_per_page' ); ?>" class="small-text" /> 个</td>
                </tr>
                <tr>
                    <th><label for="wzm_json_update_time">每日更新时间</label></th>
                    <td><input name="wzm_json_update_time" type="time" step="1" min="00:00:00" max="23:59:59" id="wzm_json_update_time" value="<?php form_option( 'wzm_json_update_time' ); ?>" class="text"></td>
                </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary wzm-json-button-submit" value="保存更改">
                &nbsp;&nbsp;
                <input type="button" name="button" id="wzm-button-regenerate" class="button button-secondary" value="立即生成">
                &nbsp;
                <span id="wzm-ajax-regenerate-status" style="visibility: hidden;">JSON文件已重新生成。</span>
            </p>
        </form>
    </div>
    <?php
}



/**
 *  3. 关于控制面板的数据保存
 *      3.1 通过 register_setting() 注册选项的归属组（必选）、名称（必选）和预设数据（可选）。
 *
 *  注意事项：
 *      1. 通过 register_setting() 注册好的选项需要配合 settings_fields() 函数才能实现：点击提交按钮，数据自动保存。
 *      2. register_setting() 和 settings_fields() 的组名要保持一致。
 *
 *  参考文档：
 *      1. register_setting()：
 *          https://developer.wordpress.org/reference/functions/register_setting/
 *          https://www.solagirl.net/creating-an-admin-interface-settings-api.html
 *          https://pewae.com/2021/04/somethins-about-register_setting-function-of-wordpress.html
 *          https://pewae.com/2021/04/somethins-about-register_setting-function-of-wordpress.html
 *      2. settings_fields()：
 *          https://developer.wordpress.org/reference/functions/settings_fields/
 */
if ( ! function_exists( 'wzm_json_register' ) ) {
    function wzm_json_register() {
        register_setting('wzm_json','wzm_posts_per_page');
        register_setting('wzm_json','wzm_products_per_page');
        register_setting('wzm_json','wzm_json_update_time');
    }
    add_action('admin_init','wzm_json_register');
}