<?php

namespace Misico\Controller\Output;

use Misico\Common\Common;
use \RuntimeException;

class ViewOutput implements OutputInterface
{
    const DIR_VIEWS = APP_ROOT_PATH . 'views' . DS;

    /** @var array */
    private $variables;

    /** @var string */
    private $template;

    /** @var Common */
    private $common;

    public function __construct(Common $common)
    {
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->common = $common;
    }

    public function assign($var, $value)
    {
        $this->variables[$var] = $value;
    }

    public function setTemplate(string $file)
    {
        $this->template = $file;
    }

    public function processControllerReturn($controller, $action): void
    {

        if (empty($this->template)) {
            if ((!empty($controller)) && (!empty($action))) {
                $this->setTemplate(strtolower($controller) . '_' . strtolower($action));
            } else {
                throw new RuntimeException('Cannot determine template name');
            }
        }
        $this->render($this->template);
    }

    private function render(string $template): void
    {

        $templateName = Common::safeFileName($template);
        $template = $templateName . '.inc.php';

        if (is_file(self::DIR_VIEWS . $template)) {
            /** @noinspection PhpIncludeInspection */
            require self::DIR_VIEWS . $template;
        } else {
            throw new RuntimeException('Cannot find template ' . $template);
        }
    }

    private function renderTableStat($with, $compare, $whatIs) {
        $this->assign('firstData', $with);
        $this->assign('compareFilesData', $compare);
        $this->assign('whatIs', $whatIs);
        $this->render('_table');
    }

    private function renderStatementStat($with, $compare, $whatIs) {
        $this->assign('firstData', $with);
        $this->assign('compareFilesData', $compare);
        $this->assign('whatIs', $whatIs);
        $this->render('_table_statement');
    }

}
