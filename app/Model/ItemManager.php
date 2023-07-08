<?php


namespace App\Model;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Row;
use Nette\Database\Table\Selection;
use Nette\Database\Explorer;
use Nette\Utils\Image;

class ItemManager extends DatabaseManager
{
    private $picturePath;

    const
        TABLE_NAME = 'items',
        COLUMN_ID = 'id',
        COLUMN_ON_HOMEPAGE = 'on_homepage',
        COLUMN_URL = 'url';

    public function __construct(Explorer $database, string $picturePath)
    {
        parent::__construct($database);
        $this->picturePath = $picturePath;
    }

    public function getItem(string $url, string $columns = null): ActiveRow
    {
        return $this->database->table(self::TABLE_NAME)
            ->select($columns ? $columns : '*')
            ->where(self::COLUMN_URL, $url)
            ->fetch();
    }

    public function getItemFromId(int $id, string $columns = null): ActiveRow
    {
        return $this->database->table(self::TABLE_NAME)
            ->select($columns ? $columns : '*')
            ->where(self::COLUMN_ID, $id)
            ->fetch();
    }

    public function getItems(array $parameters): Selection
    {
        $items = $this->database->table(self::TABLE_NAME);

        // Filtrování podle kategorie.
        if (!empty($parameters['category_id']))
            $items->where(':' . self::TABLE_NAME . '_' . CategoryManager::TABLE_NAME .
                '.' . CategoryManager::TABLE_NAME .
                '.' . CategoryManager::COLUMN_ID,
                $parameters['category_id']
            );

        return $items;
    }

    public function getAllItems(): Selection {
        return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_ID . ' DESC');
    }

    public function saveItem(array $values): ActiveRow
    {
        $itemData = [
            'title' => $values['title'],
            'url' => $values['url'],
            'short_description' => $values['short_description'],
            'description' => $values['description'],
            'price' => $values['price']
        ];

        if (!empty($values['picture']) && $values['picture']->isOk()) {
            $itemData['has_picture'] = true;
        }

        if (!empty($values['id'])) {
            $itemCategoryData = [
                'item_id' => $values['id'],
                'category_id' => $values['categories'],
            ];
            $item = $this->database->table('items')
                ->wherePrimary($values['id']);

            $item->update($itemData);
            $item = $item->fetch();

            $this->database->table('item_category')
                ->where('item_id = ?', $item->id)
                ->delete();
        } else {

            $item = $this->database->table('items')
                ->insert($itemData);

            $item_id = $item->id;
            $itemCategoryData = [
                'item_id' => $item_id,
                'category_id' => $values['categories'],
            ];
        }

        if (!empty($values['picture']) && $values['picture']->isOk()) {
            /** @var Image $im */
            $im = $values['picture']->toImage();
            $im->resize(900, 400, Image::Cover);
            $im->save(sprintf('%s/%d.jpg', $this->picturePath, $item->id), 90, Image::JPEG);
        }
        if (!empty($values['categories'])) {
            $this->database->table('item_category')->insert($itemCategoryData);
        }

        return $item;
    }

    public function removeItem(string $url) {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->delete();
    }

    public function getItemsCount() {
        return $this->database->table(self::TABLE_NAME)->count('*');
    }
}