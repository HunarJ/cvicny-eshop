<?php


namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Nette\Database\Table\ActiveRow;


class CategoryManager extends DatabaseManager
{

    const
        TABLE_NAME = 'category',
        COLUMN_ID = 'id',
        COLUMN_TITLE = 'name',
        COLUMN_URL = 'url';


    public function getItemCategories(int $id) {
        return $this->database->table(ItemManager::TABLE_NAME . '_' . self::TABLE_NAME)
            ->select(self::COLUMN_ID)
            ->where(ItemManager::COLUMN_ID, $id)
            ->fetchPairs(null, self::COLUMN_ID);
    }

    public function updateItemCategories(int $id, array $categories) {
        $this->database->table(ItemManager::TABLE_NAME . '_' . self::TABLE_NAME)
            ->where(ItemManager::COLUMN_ID, $id)->delete();
        $rows = array();
        foreach ($categories as $category) {
            $rows[] = array(
                ItemManager::COLUMN_ID => $id,
                self::COLUMN_ID => $category,
            );
        }
        $this->database->table(ItemManager::TABLE_NAME . '_' . self::TABLE_NAME)->insert($rows);
    }

    public function getItemCategory(int $id) {
        if ($row = $this->database
            ->table(ItemManager::TABLE_NAME . '_' . self::TABLE_NAME)
            ->where(ItemManager::COLUMN_ID, $id)
            ->fetch()
        )
            return $row->ref(self::TABLE_NAME)->url;
        else
            return '';
    }

    public function getCategories(): Selection {
        return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_ID . ' ASC');
    }

    public function getAllCategory() {
        return $this->database->table(self::TABLE_NAME)->fetchPairs(self::COLUMN_ID, self::COLUMN_TITLE);
    }

    public function getCategory(string $url): ActiveRow {
        return $this->database->table(self::TABLE_NAME)
            ->where(self::COLUMN_URL, $url)->fetch();
    }

    public function removeCategory(string $url) {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->delete();
    }

    public function saveCategory(array $category) {
        if (empty($category[self::COLUMN_ID])) {
            unset($category[self::COLUMN_ID]);
            $this->database->table(self::TABLE_NAME)->insert($category);
        } else {
            $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $category[self::COLUMN_ID])->update($category);
        }
    }
}