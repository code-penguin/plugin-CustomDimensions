<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CustomDimensions\DataTable\Filter;

use Piwik\DataTable\BaseFilter;
use Piwik\DataTable\Row;
use Piwik\DataTable;
use Piwik\Plugins\CustomDimensions\Archiver;
use Piwik\Plugins\CustomDimensions\Tracker\CustomDimensionsRequestProcessor;
use Piwik\Tracker\PageUrl;

class AddSubtableSegmentMetadata extends BaseFilter
{
    private $idDimension;
    private $dimensionValue;

    /**
     * Constructor.
     *
     * @param DataTable $table The table to eventually filter.
     */
    public function __construct($table, $idDimension, $dimensionValue)
    {
        parent::__construct($table);
        $this->idDimension = $idDimension;
        $this->dimensionValue = $dimensionValue;
    }

    /**
     * @param DataTable $table
     */
    public function filter($table)
    {
        if (!$this->dimensionValue || $this->dimensionValue === Archiver::LABEL_CUSTOM_VALUE_NOT_DEFINED) {
            return;
        }

        $dimension = CustomDimensionsRequestProcessor::buildCustomDimensionTrackingApiName($this->idDimension);
        $conditionAnd = ';';

        $partDimension = $dimension . '==' . urlencode($this->dimensionValue) . $conditionAnd;

        foreach ($table->getRows() as $row) {
            $label = $row->getColumn('label');
            if ($label !== false) {
                $row->setMetadata('segment', $partDimension . 'actionUrl=$' . urlencode($label));
            }
        }
    }
}