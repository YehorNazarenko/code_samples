<?php

namespace App\Handler;

use App\Helpers\WebhookHelper;
use App\Models\Entity;
use App\Models\District;
use App\Models\Place;
use App\Models\Human;
use App\Models\HumanUser;
use App\Models\User;
use App\Models\UserToken;
use ReflectionProperty;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;


class WebhookJobHandlerForService extends ProcessWebhookJob
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    public function handle()
    {
        logger('WebhookJobHandlerForService');
        $data = json_decode($this->webhookCall, true);
        $payload = $data['payload'];

        switch ($payload['method']) {
            case WebhookHelper::METHOD_DELETE:
                $this->deleteModel($payload);
                break;
            case WebhookHelper::METHOD_UPDATE:
                $this->updateModel($payload);
                break;
            case WebhookHelper::METHOD_CREATE:
                $this->createModel($payload);
                break;
            case WebhookHelper::METHOD_ATTACH:
                $this->addHumanUser($payload);
                break;
            case WebhookHelper::METHOD_DETACH:
                $this->deleteHumanUser($payload);
                break;
            case WebhookHelper::METHOD_SIGN_OUT:
                $this->signOut($payload);
                break;
        }
    }

    protected function deleteModel(array $payload)
    {
        $model = $payload['model']::where($payload['unique_key'], $payload['unique_value'])->first();
        $model->delete();
    }

    protected function updateModel(array $payload)
    {
        $data = $payload['data'];
        $model = $payload['model']::where($payload['unique_key'], $payload['unique_value'])->first();
        if (!empty($payload['foreign_key'])) {
            foreach ($payload['foreign_key'] as $foreign_key) {
                if ($foreign_key['key'] === 'place_id' &&
                    $foreign_key['model_value'] !== $model->place->id) {
                    $model->transitionToNewPlace();
                }

                $data[$foreign_key['key']] = $this->getForeignKeyValue($foreign_key);
            }
        }

        if (!empty($payload['relation'])) {
            foreach ($payload['relation'] as $relation) {
                $this->syncRelation($model, $relation);
            }
        }
        $model->update($data);
    }

    protected function createModel(array $payload)
    {
        $data = $payload['data'];
        if (!empty($payload['foreign_key'])) {
            foreach ($payload['foreign_key'] as $foreign_key) {
                $data[$foreign_key['key']] = $this->getForeignKeyValue($foreign_key);
            }
        }

        $model = $payload['model']::query()->create($data);

        if (!empty($payload['relation'])) {
            foreach ($payload['relation'] as $relation) {
                $this->syncRelation($model, $relation);
            }
        }
    }

    protected function addHumanUser(array $payload)
    {
        /** @var Human $human */
        $human = Human::query()->where('id', $payload['human_id'])->first();
        /** @var User $user */
        $user = User::query()->where('email', $payload['user_email'])->first();
        HumanUser::create([
            'human_id' => $human->getKey(),
            'user_id' => $user->getKey(),
        ]);
    }

    protected function deleteHumanUser(array $payload)
    {
        $human = Human::query()->where('id', $payload['human_id'])->first();
        $user = User::query()->where('email', $payload['user_email'])->first();

        HumanUser::query()
            ->where('user_id', $user->getKey())
            ->where('human_id', $human->getKey())
            ->delete();
    }

    protected function syncRelation($model, $relation)
    {
        if ($relation['name'] === 'entity') {
            $entity = Entity::find($relation['id']);

            $model?->entities()?->sync($entity?->getKey());
        }

        if ($relation['name'] === 'place') {
            $place = Place::query()
                ->where('id', $relation['id'])
                ->first();
            $model->place()->sync($place->getKey());
        } elseif ($relation['name'] === 'district') {
            $district = District::query()
                ->where('id', $relation['id'])
                ->first();
            $model->district()->sync($district->getKey());
        }
    }

    protected function getForeignKeyValue($data)
    {
        $relationModel = $data['model']::where($data['model_key'], $data['model_value'])->first();

        return $relationModel->getKey();
    }

    protected function signOut(array $payload): void
    {
        $user = User::query()->where('email', data_get($payload, 'data.user_email'))->first();
        if ( $user && $user->get() ) {
            UserToken::query()
                ->where('user_id', $user->getKey())
                ->get()
                ->each(function ($userToken) {
                    $userToken->delete();
                });
        }
        
    }
}
