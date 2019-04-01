<?php

namespace app\models\application;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\application\Propertydoccategories;

/**
 * PropertydoccategoriesSearch represents the model behind the search form about `app\models\application\Propertydoccategories`.
 */
class PropertydoccategoriesSearch extends Propertydoccategories {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['category_id', 'sort_order'], 'integer'],
            [['category_title', 'path'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Propertydoccategories::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
            //'attributes' => [$columns],             /* format: 'role_type_name', 'role_type_so' */
            //'defaultOrder' => [$columns=>SORT_ASC]
            ]
        ]);


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'category_id' => $this->category_id,
            'sort_order' => $this->sort_order,
        ]);

        $query->andFilterWhere(['like', 'category_title', $this->category_title])
                ->andFilterWhere(['like', 'path', $this->path]);

        return $dataProvider;
    }

    public function loadparams($params) {
        $this->load($params);
    }

    public static function getRecord($id=0) {
        return Propertydoccategories::find()->where(['category_id'=>$id])->one();
    }

}
