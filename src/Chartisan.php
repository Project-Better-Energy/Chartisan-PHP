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
     * @param integer $id
     * @param array $extra
     * @return Chartisan
     */
    public function advancedDataset(string $name, array $values, int $id, array $extra): Chartisan
    {
        // Get or create the given dataset.
        [$dataset, $isNew] = $this->getOrCreateDataset($name, $values, $id, $extra);
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
        [$dataset] = $this->getOrCreateDataset($name, $values, $this->getNewID(), []);
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
     * getNewID returns an ID that is not used by any of the datasets.
     * Keep in mind, this will panic when n > 2^32 if int is 32 bits.
     * If you need more than 2^32 datasets, you're crazy.
     *
     * @return integer
     */
    protected function getNewID(): int
    {
        for ($n = 0; ; $n++) {
            if (!$this->idUsed($n)) {
                return $n;
            }
        }
    }

    /**
     * Returns true if the given ID is already used.
     *
     * @param integer $id
     * @return boolean
     */
    protected function idUsed(int $id): bool
    {
        foreach ($this->serverData->datasets as $dataset) {
            if ($dataset->id == $id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns a dataset from the chart or creates a new one given the data.
     *
     * @param string $name
     * @param array $values
     * @param integer $id
     * @param array $extra
     * @return array
     */
    protected function getOrCreateDataset(string $name, array $values, int $id, array $extra): array
    {
        foreach ($this->serverData->datasets as $dataset) {
            if ($dataset->id== $id) {
                return [$dataset, false];
            }
        }
        return [new DatasetData($name, $values, $id, $extra), true];
    }
}
