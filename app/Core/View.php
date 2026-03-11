<?php
declare(strict_types=1);
namespace App\Core;

class View
{
    private array $data = [];
    private array $sections = [];
    private ?string $layout = null;
    private string $currentSection = '';

    public function render(string $template, array $data = [], string $layout = 'main'): void
    {
        $this->data   = $data;
        $this->layout = $layout;

        $templateFile = VIEW_PATH . '/' . str_replace('.', '/', $template) . '.php';
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("View template not found: {$templateFile}");
        }

        // Buffer the template
        ob_start();
        extract($this->data);
        require $templateFile;
        $content = ob_get_clean();

        // If a layout is specified, wrap content in it
        if ($this->layout) {
            $layoutFile = VIEW_PATH . '/layouts/' . $this->layout . '.php';
            if (file_exists($layoutFile)) {
                extract($this->data);
                $this->data['content'] = $content;
                extract(['content' => $content]);
                require $layoutFile;
                return;
            }
        }

        echo $content;
    }

    public function renderPartial(string $partial, array $data = []): string
    {
        $file = VIEW_PATH . '/partials/' . $partial . '.php';
        if (!file_exists($file)) return '';
        ob_start();
        extract(array_merge($this->data, $data));
        require $file;
        return ob_get_clean();
    }

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
