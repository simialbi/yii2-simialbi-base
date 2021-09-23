<?php

namespace simialbi\yii2\controllers;

use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;

class ConditionBuilderController extends Controller
{
    /**
     * This action can be used to get the options for a filter via ajax. e.g. with the select2 plugin.
     *
     * Additional parameters are:
     * - primaryKey: Primary key of the database-table. Default is 'id'.
     * - filter: Conditions which are always applied. See the yii\db\Query where() method
     * - order: The ordering of the searched table. See the yii\db\Query orderBy method
     *
     * @param string $class The full classname of the relation
     * @param string $q The string to search for
     * @param array $filterColumns The columns to be searched
     * @param string $display The pattern to display. e.g [[col1]] - [[col2]]
     * @return array
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionGetOptions(string $class, string $q, array $filterColumns, string $display): array
    {
        // get additional parameters which are not mandatory
        $get = Yii::$app->request->get();
        $pk = ArrayHelper::getValue($get, 'primaryKey', 'id');
        $filter = ArrayHelper::getValue($get, 'filter');
        $order = ArrayHelper::getValue($get, 'order');


        // get columns
        $columns = [];
        $pattern = '/\{([a-zA-Z0-9]*)}/';
        $success = preg_match_all($pattern, $display, $columns);
        if (!$success) {
            throw new InvalidConfigException('Error in columns for ' . $class);
        }
        $columns = $columns[1];


        $class = str_replace('/', '\\', $class);
        /**
         * @var ActiveRecord $model
         */
        $model = Instance::ensure($class);
        $query = $model
            ->find()
            ->select($columns);

        // add pk, if not already in array
        if (!in_array($pk, $columns)) {
            $query->addSelect($pk);
        }

        $words = preg_split('/ +/', $q);

        // sorting if available
        if ($order) {
            $order = array_map('intval', $order);
            $query->orderBy($order);
        }

        // add orwhere for each filtercolumn
        foreach ($filterColumns as $filterColumn) {
            foreach ($words as $word) {
                $query->orWhere([
                    'like',
                    $filterColumn,
                    $word
                ]);
            }
        }

        // add filter if available
        if ($filter) {
            $query->andWhere($filter);
        }

        $results = $query->asArray()->all();

        // create array to return
        $results = array_map(function ($item) use ($pk, $display) {
            $str = $display;

            $data = array_combine(array_map(function ($value) { return '{' . $value . '}';}, array_keys($item)), $item);
            $str = strtr($str, $data);

            return [
                'id' => $item[$pk],
                'text' => $str
            ];
        }, $results);

        // return arr
        return ['results' => $results];
    }
}
