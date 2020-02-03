<?php

declare(strict_types = 1);

namespace Chartisan\PHP;

/**
 * Represents a chartisan chart instance.
 */
class Chartisan
{
    /**
     * Stores the server data of the chart.
     *
     * @var ServerData
     */
    protected ServerData $serverData;

    /**
     * Creates a new instance of a chartisan chart.
     *
     * @return Chartisan
     */
    public static function build(): Chartisan
    {
        $chartisan = new Chartisan;
        $chartisan->serverData = new ServerData;
        return $chartisan;
    }

    /**
     * Sets the chart labels.
     *
     * @param string[] $labels
     * @return Chartisan
     */
    public function labels(array $labels): Chartisan
    {
        $this->serverData->chart->labels = $labels;
        return $this;
    }

    /**
     * Adds extra information to the chart.
     *
     * @param array $value
     * @return Chartisan
     */
    public function extra(array $value): Chartisan
    {
        $this->serverData->chart->extra = $value;
        return $this;
    }

    /**
     * AdvancedDataset appends a new dataset to the chart or modifies an existing one.
     * If the ID has already been used, the dataset will be replaced with this one.
     *
     * @param string $name
     * @param array $values
     * @param array|null $extra
     * @return Chartisan
     */
    public function advancedDataset(string $name, array $values, ?array $extra): Chartisan
    {
        // Get or create the given dataset.
        [$dataset, $isNew] = $this->getOrCreateDataset($name, $values, $extra);
        if ($isNew) {
            // Append the new dataset.
            $this->serverData->datasets[] = $dataset;
            return $this;
        }
        // Modify the existing dataset.
        $dataset->name = $name;
        $dataset->values = $values;
        $dataset->extra = $extra;
        return $this;
    }

    /**
     * Dataset adds a new simple dataset to the chart. If more advanced control is
     * needed, consider using `AdvancedDataset` instead.
     *
     * @param string $name
     * @param array $values
     * @return Chartisan
     */
    public function dataset(string $name, array $values): Chartisan
    {
        [$dataset] = $this->getOrCreateDataset($name, $values, null);
        $this->serverData->datasets[] = $dataset;
        return $this;
    }

    /**
     * Returns the string representation JSON encoded.
     *
     * @return string
     */
    public function toJSON(): string
    {
        return json_encode($this->toObject());
    }

    /**
     * Transforms it to an array.
     *
     * @return array
     */
    public function toObject(): array
    {
        return (array) $this->serverData;
    }

    /**
     * Returns a dataset from the chart or creates a new one given the data.
     *
     * @param string $name
     * @param array $values
     * @param array|null $extra
     * @return array
     */
    protected function getOrCreateDataset(string $name, array $values, ?array $extra): array
    {
        foreach ($this->serverData->datasets as $dataset) {
            if ($dataset->name == $name) {
                return [$dataset, false];
            }
        }
        return [new DatasetData($name, $values, $extra), true];
    }
}
