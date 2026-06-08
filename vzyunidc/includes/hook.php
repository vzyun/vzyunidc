<?php
/**
 * vzyunIDC - Hook钩子系统
 * 类似魔方财务的 Hook 机制，支持插件挂载
 */

class Hook {
    private static $hooks = [];
    private static $loaded = false;

    /**
     * 注册钩子
     */
    public static function add($hookName, $callback, $priority = 10) {
        if (!isset(self::$hooks[$hookName])) {
            self::$hooks[$hookName] = [];
        }
        self::$hooks[$hookName][] = [
            'callback' => $callback,
            'priority' => $priority,
        ];
        // 按优先级排序
        usort(self::$hooks[$hookName], function($a, $b) {
            return $a['priority'] - $b['priority'];
        });
    }

    /**
     * 执行动作钩子
     */
    public static function action($hookName, &...$args) {
        if (!isset(self::$hooks[$hookName])) return;
        foreach (self::$hooks[$hookName] as $hook) {
            call_user_func_array($hook['callback'], $args);
        }
    }

    /**
     * 执行过滤器钩子（返回修改后的值）
     */
    public static function filter($hookName, $value, &...$args) {
        if (!isset(self::$hooks[$hookName])) return $value;
        foreach (self::$hooks[$hookName] as $hook) {
            $value = call_user_func_array($hook['callback'], array_merge([$value], $args));
        }
        return $value;
    }

    /**
     * 加载所有启用的插件
     */
    public static function loadPlugins() {
        if (self::$loaded) return;
        self::$loaded = true;

        try {
            $db = DB::instance();
            $plugins = $db->getRows('SELECT * FROM plugins WHERE status = 1');
        } catch (Exception $e) {
            return; // 数据库未初始化
        }

        foreach ($plugins as $plugin) {
            $pluginFile = __DIR__ . '/../plugins/' . $plugin['name'] . '/plugin.php';
            if (file_exists($pluginFile)) {
                require_once $pluginFile;
                $className = 'Plugin_' . $plugin['name'];
                if (class_exists($className)) {
                    $instance = new $className();
                    if (method_exists($instance, 'init')) {
                        $instance->init();
                    }
                }
            }
        }
    }
}
