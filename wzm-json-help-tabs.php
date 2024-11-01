<?php
/**
 *  1. 帮助菜单
 *      1.1 创建插件主界面帮助选项卡。
 *
 *
 *  注意事项：
 *      1. 启用帮助选项卡的代码位于 wzm-json-options.php 里的 1.2 中。
 *      2. 这里单独列出来的原因：为后期可能会增加的多个页面先做好准备。
 *
 *  参考文档：
 *      1. add_help_tab
 *          https://developer.wordpress.org/reference/classes/wp_screen/add_help_tab/
 *          path: /wp-admin/admin-general.php
 */
if ( ! function_exists( 'wzm_help_tabs_primary' ) ) {
    //  创建帮助选项卡
    function wzm_help_tabs_primary(){
        $wzm_help_overview =
            '<p>欢迎使用本插件，如发现问题请前往<a href="https://wordpress.org/support/plugin/wzm-json" target="_blank">插件支持论坛</a>；
            这边收到后尽快前来解决的。</p>'.
            '<p>其中文章和产品的显示数量最小为1，最大为20。'.
            '<br>另外，精品产品只有安装了WooCommerce产品插件后才能起作用，如果没有安装的话就不用管它。</p>';
        $wzm_help_attention =
            '<p>1. 该插件生成的JSON文件位于网站根目录下，文件名为“wzm.json”。</p>'.
            '<p>2. 突破数量限制的方式是修改插件中“wzm-json-options.php”文件里的第91行和第95行，将标签<code>input</code>中的参数<code>max</code>的值修改为你想要的大小即可。</p>'.
            '<p>3. 对加密的文章无处理，如果您有这方面的想法可以通过<a href="https://wordpress.org/support/plugin/wzm-json" target="_blank">插件支持论坛</a>告知我。</p>';
        get_current_screen()->add_help_tab(
            array(
                'id' => 'wzm_help_overview',
                'title' => '概述',
                'content' => $wzm_help_overview,
            )
        );
        get_current_screen()->add_help_tab(
            array(
                'id' => 'wzm_help_attention',
                'title' => '注意事项',
                'content' => $wzm_help_attention,
            )
        );
    }
}