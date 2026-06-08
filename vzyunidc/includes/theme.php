<?php
/**
 * vzyunIDC - 主题引擎
 * 支持模板继承、区块插入、主题切换
 */

class Theme {
    private static $instance = null;
    private $themeName;
    private $themePath;
    private $blocks = [];
    private $extends;

    private function __construct() {
        $this->themeName = getSetting('default_theme', DEFAULT_THEME);
        $this->themePath = __DIR__ . '/../themes/' . $this->themeName . '/';
    }

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 设置主题
     */
    public function setTheme($name) {
        $this->themeName = $name;
        $this->themePath = __DIR__ . '/../themes/' . $name . '/';
    }

    /**
     * 获取主题路径
     */
    public function getPath() {
        return $this->themePath;
    }

    /**
     * 获取主题URL
     */
    public function getUrl() {
        return SITE_URL . '/themes/' . $this->themeName;
    }

    /**
     * 渲染模板
     */
    public function render($template, $data = []) {
        extract($data);
        $this->extends = null;
        $this->blocks = [];
        
        $file = $this->themePath . $template . '.php';
        if (!file_exists($file)) {
            $file = __DIR__ . '/../themes/default/' . $template . '.php';
        }
        if (!file_exists($file)) {
            throw new Exception("模板不存在: {$template}");
        }

        ob_start();
        require $file;
        $content = ob_get_clean();

        // 如果有继承父模板
        if ($this->extends) {
            ob_start();
            require $this->extends;
            $content = ob_get_clean();
        }

        return $content;
    }

    /**
     * 布局继承
     */
    public function extend($template) {
        $this->extends = $this->themePath . $template . '.php';
        if (!file_exists($this->extends)) {
            $this->extends = __DIR__ . '/../themes/default/' . $template . '.php';
        }
    }

    /**
     * 定义区块
     */
    public function block($name, $content = null) {
        if ($content === null) {
            // 开始捕获区块
            ob_start();
            $this->blocks[$name] = '';
        } else {
            $this->blocks[$name] = $content;
        }
    }

    /**
     * 结束区块捕获
     */
    public function endBlock() {
        $content = ob_get_clean();
        $keys = array_keys($this->blocks);
        $lastKey = end($keys);
        if ($lastKey !== null) {
            $this->blocks[$lastKey] = $content;
        }
    }

    /**
     * 显示区块
     */
    public function showBlock($name) {
        echo $this->blocks[$name] ?? '';
    }

    /**
     * 包含子模板
     */
    public function include($template, $data = []) {
        extract($data);
        $file = $this->themePath . $template . '.php';
        if (!file_exists($file)) {
            $file = __DIR__ . '/../themes/default/' . $template . '.php';
        }
        if (file_exists($file)) {
            require $file;
        }
    }
}
