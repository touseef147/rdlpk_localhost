<?php

namespace app\models\application;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\application\Propertyapplicationpchoices;

/**
 * PropertyapplicationpchoicesSearch represents the model behind the search form about `app\modules\finance\models\Propertyapplicationpchoices`.
 */
class PropertyapplicationpchoicesSearch extends Propertyapplicationpchoices
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['choice_id', 'application_id', 'category_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = Propertyapplicationpchoices::find();

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
            'choice_id' => $this->choice_id,
            'application_id' => $this->application_id,
            'category_id' => $this->category_id,
        ]);

        return $dataProvider;
    }
    
    public function loadparams($params)
    {
        $this->load($params);
    }
}
