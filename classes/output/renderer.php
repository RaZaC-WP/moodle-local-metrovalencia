<?php
namespace local_metrovalencia\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for the Metrovalencia page.
 *
 * @package    local_metrovalencia
 * @copyright  RaZaC-WP - 2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base
{

    /**
     * Render the Metrovalencia page.
     *
     * @param metrovalencia_page $page Page data.
     * @return string HTML output.
     */
    public function render_metrovalencia_page(metrovalencia_page $page): string
    {
        return $this->render_from_template(
            'local_metrovalencia/metrovalencia_page',
            $page->export_for_template($this)
        );
    }
}