=== WZM Json ===
Contributors: 响当当是我大姐头
Tags: wzm,json,seo
Requires at least: 5.7.2
Tested up to: 5.7.2
Requires PHP: 7.2.34
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WZM Json only provides a file, you need to do more if you want to improve the page loading speed.
WZM Json 仅仅是提供一个文件，想要提升网页加载速度还需要做到更多。


== Description ==
Made to improve the loading speed of the website, the specific function is to:
1. The basic information (name, date, abstract, link, address with pictures) of the latest n articles (reverse order) under each article category;
2. Basic information of all article categories (name, alias, link, description);
3. Basic information of all pages (name, alias, link, summary);
4. The basic information of the latest n products in the selected products (reverse order) (WooCommerce plug-in needs to be installed, name, date, summary, link, and picture address);
The above four cases are saved as JSON files in the form of objects, which are used for loading the homepage, thereby improving the loading speed of the homepage.

Note: The JSON file is stored in the root directory of the website.


为提升网站加载速度而制作，具体功能是将：
1. 每个文章分类下最新的 n 篇文章（倒序）的基本信息（名称、日期、摘要、链接、配图地址）；
2. 所有文章分类的基本信息（名称、别名、链接、描述）；
3. 所有页面的基本信息（名称、别名、链接、摘要）；
4. 精选产品中最新的 n 个产品（倒序）的基本信息（需安装WooCommerce插件，名称、日期、摘要、链接、配图地址）；
以上四种情况以对象的形式保存为JSON文件，用于首页加载使用，从而提高首页的加载速度。

注：JSON文件保存于网站的根目录之下。



== Installation ==
Installing it is like installing any other WordPress plugin.

Dashboard method:
1. Log in to your WordPress administrator, and then go to "Plugins -> Add New";
2. Type "wzm json" in the search bar, then select this plug-in, and click "Install Now";
3. After the installation is complete, click the "Enable" button.


安装它就像安装其他任何WordPress插件一样。

仪表板方法：
1.登录到您的WordPress管理员，然后转到“插件->添加新”；
2.在搜索栏中键入“wzm json”，然后选择此插件，点击“现在安装”；
3.等待安装完成后，点击“启用”按钮即可。



== Frequently Asked Questions ==



== Screenshots ==



== Changelog ==
= 1.0.1 =
* Download and install from SVN, it prompts an error: "Call to undefined function get_home_path()"; therefore, replace get_home_path() with ABSPATH (Reference: https://wordpress.org/support/topic/uncaught-error-call-to-undefined-function-get_home_path-after-update-to-3-5-1/).
* 从SVN下载安装，提示错误：“Call to undefined function get_home_path()”；因此将 get_home_path() 替换为 ABSPATH（参考：https://wordpress.org/support/topic/uncaught-error-call-to-undefined-function-get_home_path-after-update-to-3-5-1/）。

= 1.0 =
* Initial release
* 初始发行