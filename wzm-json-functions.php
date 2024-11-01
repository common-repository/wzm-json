<?php
/**
 *  生成JSON文件
 *
 *  1. 获取用户选项
 *      1.1 获取用户选项，并声明为函数的变量。
 *
 *  2. 创建一个对象，包含最近更新的 n 篇文章的信息。
 *      2.1. 创建一个对象，包含 1 篇文章的信息；
 *      2.2. 创建一个数组，包含 n 篇文章的信息；
 *      2.3. 创建一个对象，每个栏目都包含最新 n 篇文章信息的数组；
 *          2.3.1 创建一个数组，包含所有分类的栏目ID。
 *          2.3.2 创建一个空对象，用于创建 栏目名称和栏目最新 n 篇文章的信息 组成的对象。
 *          2.3.3 遍历栏目ID，分别调用各自栏目下最新的n篇文章，然后组合成对象。
 *      2.4. 创建一个对象，包含所有栏目的最新 n 篇文章信息。
 *      2.5. 创建一个对象，将所有最新文章的信息组合为一个整体；
 *          以 post 作为属性名，各个栏目生成的对象作为属性值。
 *
 *  2.n. 创建一个对象，包含所有文章分类的基础信息。
 *      2.n.1 遍历栏目ID，分别调用栏目格子的别名、连接和描述
 *      2.n.2 以 post_cat 作为属性名，各个栏目生成的对象作为属性值。
 *
 *  3. 创建一个对象，包含所有页面的基本信息。
 *      3.1. 创建一个对象，包含 1 个页面的信息。
 *      3.2. 创建一个对象，包含所有页面的信息。
 *          3.2.1 创建一个数组，包含所有页面的ID。
 *          3.2.2 遍历页面ID，分别调用各自页面的基本信息，然后组合成对象。
 *      3.3. 创建一个对象，将所有页面的信息组合为一个整体；
 *          以 page 作为属性名，所有页面的生成的对象作为属性值。
 *
 *  4. 创建一个对象，包含所有精选产品的基本信息。
 *      4.1. 创建一个对象，包含 1 个精选产品的基本信息。
 *      4.2. 创建一个数组，包含最新 n 个精选产品的信息。
 *          4.2.1. 检测是否启用 WooCommerce 插件，如果没启用则跳过整个代码块。
 *      4.3. 创建一个对象，将最新 n 个精选产品的信息组合为一个整体；
 *          以 product 作为属性名，最新 n 个精选产品构成的数组作为属性值。
 *
 *  5. 创建一个对象，整合步骤 2.5 + 3.3 + 4.3。
 *
 *  6. 将对象保存为JSON格式的字符串，然后写入到目标文件中。
 *
 *
 *
 *  注意事项：
 *      1. 获取用户选项是后添加的，函数在之前使用的变量是指定；
 *          在前一个函数在接收指定变量后运行正常，才会开始编写下一个函数；
 *          同时，将指定变量改为变量，然后交由下一个函数进行控制。
 *
 *      2. 函数的编写规则：
 *          由小到大，最后再将各部分拼接起来。
 *
 *      3. 在 3.2. 中创建对象的原因是：页面是互相独立的，需要指定调用信息。
 *
 *      4. 函数语句 function wzm_post_object(): wzm_post_object 后面跟随的的 “: $wzm_post_object”
 *          是为了声明函数返回的类型是数组还是对象，又或者其他类型。
 *
 *      5. 如果需要添加额外函数的话，只需要在第 5 步这里在创建一个对象，以 index 作为属性名，生成的3个对象作为属性值。
 */
function wzm_json_functions() {
    //  1.1 获取用户选项，并声明为函数的变量。
    $wzm_posts_per_page = get_option('wzm_posts_per_page');
    $wzm_products_per_page = get_option('wzm_products_per_page');

    //  4.2.1. 检测是否启用 WooCommerce 插件。
    $has_woocommerce = taxonomy_exists('product_visibility');


    //  2.1. 创建一个对象，包含 1 篇文章的信息。
    class wzm_post_object {
        public $title;
        public $date;
        public $excerpt;
        public $link;
        public $img;
    }
    function wzm_post_object(): wzm_post_object {
        $wzm_post_object = new wzm_post_object;
        $wzm_post_object ->title = get_post()->{"post_title"};
        $wzm_post_object ->date = get_post()->{"post_date"};
        $wzm_post_object ->excerpt = get_post()->{"post_excerpt"};
        $wzm_post_object ->link = get_the_permalink();
        $wzm_post_object ->img = get_the_post_thumbnail();
        return $wzm_post_object;
    }

    //  2.2. 创建一个数组，包含 n 篇文章的信息；
    function wzm_posts_array( $wzm_posts_cat, $wzm_posts_per_page ): array {
        $wzm_posts_array = array();
        $wzm_args = array(
            'cat' => $wzm_posts_cat,
            'posts_per_page' => $wzm_posts_per_page,
            'no_found_rows' => 1
        );
        $wzm_wp_query = new WP_Query($wzm_args);
        if($wzm_wp_query->have_posts()) : while ($wzm_wp_query->have_posts()) : $wzm_wp_query->the_post();
            $wzm_posts_array[] = wzm_post_object();
        endwhile;
        endif; wp_reset_postdata();
        return $wzm_posts_array;
    }

    //  2.3. 创建一个对象，每个栏目都包含最新 n 篇文章信息的数组；
    //      2.3.1 创建一个数组，包含所有分类的栏目ID。
    function wzm_categories_id_array(): array {
        $wzm_categories_id_array = array();
        $wzm_categories_array = get_categories();
        foreach ( $wzm_categories_array as $value ) {
            $wzm_category_id = $value->{"cat_ID"};
            $wzm_categories_id_array[] = $wzm_category_id;
        }
        return $wzm_categories_id_array;
    }

    //      2.3.2 创建一个空对象，用于创建 栏目名称和栏目最新 n 篇文章的信息 组成的对象。
    class wzm_empty_object {
    }

    //      2.3.3 遍历栏目ID，分别调用各自栏目下最新的n篇文章，然后组合成对象。
    function wzm_categories_object( $wzm_posts_per_page ): wzm_empty_object {
        $wzm_categories_object = new wzm_empty_object;
        $wzm_categories_id_array = wzm_categories_id_array();
        foreach ( $wzm_categories_id_array as $value ) {
            $wzm_category_id = $value;
            $wzm_category_slug = get_category($wzm_category_id)->slug;
            $wzm_category_value = wzm_posts_array($wzm_category_id,$wzm_posts_per_page);
            // 下注：动态添加属性，请参考书籍《深入php面向对象、模式与实践第三版pdf》第18页
            $wzm_categories_object->$wzm_category_slug = $wzm_category_value;
        }
        return $wzm_categories_object;
    }


    //  2.n. 创建一个对象，包含所有文章分类的基础信息。
    //      2.n.1 遍历栏目信息，调用各个栏目的名称、链接和描述
    class wzm_post_cat_object {
        public $slug;
        public $name;
        public $link;
        public $description;
    }
    function wzm_post_cat_object($wzm_post_cat_id): wzm_post_cat_object {
        $wzm_post_cat_object = new wzm_post_cat_object;
        $wzm_post_cat_object ->slug = get_category($wzm_post_cat_id)->{"category_nicename"};
        $wzm_post_cat_object ->name = get_category($wzm_post_cat_id)->{"cat_name"};
        $wzm_post_cat_object ->link = get_category_link($wzm_post_cat_id);
        $wzm_post_cat_object ->description = get_category($wzm_post_cat_id)->{"category_description"};
        return $wzm_post_cat_object;
    }
    function wzm_post_cat_aggregate_object(): wzm_empty_object {
        $wzm_post_cat_aggregate_object = new wzm_empty_object;
        $wzm_categories_id_array = wzm_categories_id_array();
        foreach ( $wzm_categories_id_array as $value ) {
            $wzm_post_cat_id = $value;
            $wzm_post_cat_slug = get_category($wzm_post_cat_id)->slug;
            $wzm_post_cat_value = wzm_post_cat_object($wzm_post_cat_id);
            $wzm_post_cat_aggregate_object->$wzm_post_cat_slug = $wzm_post_cat_value;
        }
        return $wzm_post_cat_aggregate_object;
    }


    //  3.1. 创建一个对象，包含 1 个页面的信息。
    function wzm_page_object( $wzm_page_id ): wzm_post_object {
        $wzm_page_object = new wzm_post_object;
        $wzm_page_object ->title = get_post($wzm_page_id)->{"post_title"};
        $wzm_page_object ->date = get_post($wzm_page_id)->{"post_date"};
        $wzm_page_object ->excerpt = get_post($wzm_page_id)->{"post_excerpt"};
        $wzm_page_object ->link = get_the_permalink($wzm_page_id);
        $wzm_page_object ->img = get_the_post_thumbnail($wzm_page_id);
        return $wzm_page_object;
    }

    //  3.2. 创建一个对象，包含所有页面的信息。
    //      3.2.1 创建一个数组，包含所有页面的ID。
    function wzm_pages_id_array(): array {
        $wzm_pages_id_array = array();
        $wzm_pages_array = get_pages();
        foreach ( $wzm_pages_array as $value ) {
            $wzm_page_id = $value->{"ID"};
            $wzm_pages_id_array[] = $wzm_page_id;
        }
        return $wzm_pages_id_array;
    }

    //      3.2.2 遍历页面ID，分别调用各自页面的基本信息，然后组合成对象。
    function wzm_pages_object(): wzm_empty_object {
        $wzm_pages_object = new wzm_empty_object;
        $wzm_pages_id_array = wzm_pages_id_array();
        foreach ( $wzm_pages_id_array as $value ) {
            $wzm_page_id = $value;
            $wzm_page_slug = get_post($wzm_page_id)->{"post_name"};
            $wzm_page_value = wzm_page_object($wzm_page_id);
            $wzm_pages_object->$wzm_page_slug = $wzm_page_value;
        }
        return $wzm_pages_object;
    }


    //  4.1. 创建一个对象，包含 1 个精选产品的基本信息。
    //      由于产品和文章调用基础信息的方式相同，因此这里直接使用文章的相关代码。

    //  4.2. 创建一个数组，包含最新 n 个精选产品的信息。
    //      4.2.1. 如果没安装 WooCommerce 插件呢？检测代码已挪至函数顶部，以方便用户查看。。
    if ( $has_woocommerce == true ) {
        function wzm_products_array( $wzm_products_per_page ): array {
            $wzm_products_array = array();
            $wzm_args = array(
                'tax_query' => array(
                    'relation' => 'OR',
                    array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                    ),
                ),
                'posts_per_page' => $wzm_products_per_page,
                'post_status'    => 'publish',
                'post_type'      => 'product',
                'no_found_rows'  => 1,
                'order'          => "DESC",
            );
            $wzm_wp_query = new WP_Query($wzm_args);
            if($wzm_wp_query->have_posts()) : while ($wzm_wp_query->have_posts()) : $wzm_wp_query->the_post();
                $wzm_products_array[] = wzm_post_object();
            endwhile;
            endif; wp_reset_postdata();
            return $wzm_products_array;
        }
    }


    //  5. 创建一个对象，整合步骤 2.5 + 3.3 + 4.3。
    function wzm_json_object( $wzm_posts_per_page, $wzm_products_per_page , $has_woocommerce ): wzm_empty_object {
        $wzm_json_object = new wzm_empty_object;
        //  文章对象
        $wzm_json_object_slug_post = 'post';
        $wzm_json_object ->$wzm_json_object_slug_post = wzm_categories_object( $wzm_posts_per_page );
        //  栏目对象
        $wzm_json_object_slug_post_cat = 'post_cat';
        $wzm_json_object ->$wzm_json_object_slug_post_cat = wzm_post_cat_aggregate_object();
        //  页面对象
        $wzm_json_object_slug_page = 'page';
        $wzm_json_object ->$wzm_json_object_slug_page = wzm_pages_object();
        //  产品对象
        if ( $has_woocommerce == true ) {
            $wzm_json_object_slug_product = 'product';
            $wzm_json_object ->$wzm_json_object_slug_product = wzm_products_array( $wzm_products_per_page );
        }

        return $wzm_json_object;
    }


    //  6. 将对象保存为JSON格式的字符串，然后写入到目标文件中。
    global $wzm_file;
    $wzm_file_content = json_encode(wzm_json_object( $wzm_posts_per_page, $wzm_products_per_page, $has_woocommerce ),320);
    $wzm_file_edit = fopen( $wzm_file,'w+');
    if( is_file($wzm_file) ) {
        fwrite($wzm_file_edit, $wzm_file_content);
    }
}



/**
 *  定时器任务
 *
 *  1. 创建任务
 *      1.1. 创建一个挂钩；第一个参数是您正在创建的挂钩的名称，第二个参数是要调用的函数的名称。
 *          1.1.1 正确定义任务时间
 *              1.1.1.1. 获取当前时区
 *              1.1.1.2. 获取选项时间
 *              1.1.1.3. 计算服务器时间 = 选项时间 - 当前时区
 *      1.2. 创建任务函数。
 *  2. 删除任务
 *      2.1. 代码以复制到 wzm-json.php（#121） 里的 2.2 中。
 *  3. 更新定时器时间
 *      3.1 更改选项后触发函数，从而更改定时器的时间。（不会用）
 *      3.1 提交表单后触发函数，从而更改定时器的时间。（能用）
 *
 *
 *  注意事项：
 *      1. add_action(updated_option) 完全不会用，折腾了一下午，哪怕照搬例子也不起任何作用，等明白了到底怎么做后再来；
 *          最终通过 issent($POST) 来进行了一个触发，但如果存在多个提交按钮的话，这里需要进行更改。
 *
 *  参考文档：
 *      1. WP_Cron 定时任务介绍：
 *          https://developer.wordpress.org/plugins/cron/
 *      2. wp_schedule_event()：
 *          https://developer.wordpress.org/reference/functions/wp_schedule_event/
 *      3. 如何在保存选项时触发自定义功能
 *          https://wordpress.stackexchange.com/questions/368653/trigger-a-custom-function-when-option-are-saved-in-admin-area
 *          https://stackoverflow.com/questions/43990966/wp-cron-not-updating-time-from-options-page
 *          https://wordpress.stackexchange.com/questions/315234/update-option-option-too-few-arguments?rq=1
 *      4. 步骤 3 不管用的原因可能是：如果更改是通过默认的WordPressGUI进行，这些操作将不会启动。
 *          https://wordpress.stackexchange.com/questions/174538/add-function-to-saving-change-on-options-pages
 *      5. wp_unschedule_event和 wp_clear_scheduled_hook的区别？
 *          http://wordpress-hackers.1065353.n5.nabble.com/Difference-between-wp-unschedule-event-and-wp-clear-scheduled-hook-td21273.html
 */

//  1. 创建定时任务
//  1.1. 创建一个挂钩；第一个参数是您正在创建的挂钩的名称，第二个参数是要调用的函数的名称。
function wzm_cron_event() {
    if ( ! wp_next_scheduled( 'wzm_cron_hook' ) ) {
        //  1.1.1. 正确的定义任务时间
        //  1.1.1.1. 获取当前时区
        $local_time = current_datetime();
        $timezone_offset = $local_time -> getOffset();

        //  1.1.1.2. 获取选项时间，并转换为时间戳。
        //  注：此时间为：服务器时间 + 当前时区
        $timestamp = get_option('wzm_json_update_time');
        $timestamp = strtotime($timestamp);

        //  1.1.1.3. 计算正确时间 = 选项时间 - 当前时区
        $timestamp = $timestamp - $timezone_offset;
        wp_schedule_event( $timestamp, 'daily', 'wzm_cron_hook' );
    }
    add_action( 'wzm_cron_hook', 'wzm_cron_exec' );
}
wzm_cron_event();

//  1.2. 创建任务函数。
if ( ! function_exists( 'wzm_cron_exec' ) ) {
    function wzm_cron_exec() {
        wzm_json_functions();
    }
}


//  2. 删除任务
//  2.1. 代码以复制到 wzm-json.php（#121） 里的 2.2 中。

//  3. 更新定时器时间
//  3.1 提交表单时触发更改函数。
if(isset($_POST)) {
    if ( wp_next_scheduled( 'wzm_cron_hook' ) ) {
        wp_clear_scheduled_hook( 'wzm_cron_hook' );
    }
    wzm_cron_event();
}