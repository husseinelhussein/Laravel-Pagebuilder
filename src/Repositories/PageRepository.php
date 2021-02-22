<?php
namespace HansSchouten\LaravelPageBuilder\Repositories;
use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\Repositories\PageRepository as BaseRepository;
use Exception;
class PageRepository extends BaseRepository
{
    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        foreach (['name', 'layout'] as $field) {
            if (! isset($data[$field]) || ! is_string($data[$field])) {
                return false;
            }
        }

        $page = $this->superCreate([
            'name' => $data['name'],
            'layout' => $data['layout'],
            'meta' => isset($data['meta'])? $data['meta']: null,
        ]);
        if (! ($page instanceof PageContract)) {
            throw new Exception("Page not of type PageContract");
        }
        return $this->replaceTranslations($page, $data);
    }

    protected function superCreate($data){
        $columns = array_keys($data);
        foreach ($columns as &$column) {
            $column = $this->removeNonAlphaNumeric($column);
        }
        $columns = implode(', ', $columns);
        $questionMarks = implode(', ', array_fill(0, sizeof($data), '?'));

        $this->db->query(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$questionMarks})",
            array_values($data)
        );

        $id = $this->db->lastInsertId();
        if ($id) {
            return $this->findWithId($id);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function update($page, array $data)
    {
        foreach (['name', 'layout'] as $field) {
            if (! isset($data[$field]) || ! is_string($data[$field])) {
                return false;
            }
        }

        $page->invalidateCache();
        $this->replaceTranslations($page, $data);

        return parent::update($page, [
            'name' => $data['name'],
            'layout' => $data['layout'],
            'meta' => isset($data['meta'])? $data['meta']: null,
        ]);
    }
}