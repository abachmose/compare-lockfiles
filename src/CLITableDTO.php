<?php namespace LockFiles;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class CLITableDTO
{
    protected $headers = [];
    protected $rows = [];

    public function __construct(array $headers = [], array $rows = [])
    {
        $this->rows    = new Collection($rows);
        $this->headers = new Collection($headers);
    }

    public function render(OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders($this->headers->toArray())
            ->setRows($this->rows->toArray());
        $table->render();
    }

    /**
     * @return Collection
     */
    public function getHeaders(): Collection
    {
        return $this->headers;
    }

    public function addRow(string $key, array $row)
    {
        $this->rows->put($key, $row);
    }

    public function addHeader(string $header)
    {
        $this->headers->push($header);
    }

    /**
     * @return Collection
     */
    public function getRows(): Collection
    {
        return $this->rows;
    }
}