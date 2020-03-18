<?php

namespace wdmg\media\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use wdmg\media\models\Media;
use wdmg\media\models\Categories;

/**
 * MediaSearch represents the model behind the search form of `wdmg\media\models\Media`.
 */
class MediaSearch extends Media
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cat_id', 'size'], 'integer'],
            [['name', 'alias', 'path', 'size', 'title', 'caption', 'alt', 'description', 'mime_type', 'reference', 'status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Media::find()->alias('media');

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'path', $this->path])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'caption', $this->caption])
            ->andFilterWhere(['like', 'alt', $this->alt])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'reference', $this->reference]);

        if ($this->mime_type !== "*")
            $query->andFilterWhere(['like', 'mime_type', $this->mime_type]);

        if ($this->size !== "*")
            $query->andFilterWhere(['like', 'size', $this->size]);

        if ($this->status !== "*")
            $query->andFilterWhere(['like', 'status', $this->status]);

        if (intval($this->cat_id) !== 0) {
            $query->leftJoin(['cats' => Categories::tableName()], '`media`.`cat_id` = `cats`.`id`');
        }

        return $dataProvider;
    }

}
