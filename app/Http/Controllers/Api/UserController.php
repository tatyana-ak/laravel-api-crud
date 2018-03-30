<?php namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/api/v1/users",
     *     description="Get list of users",
     *     summary="Get list of users",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *         description="Conducting research with logic condition 'AND'",
     *         in="query",
     *         name="search",
     *         required=false,
     *         type="string",
     *         description="email:mail,description:Atque magnam nisi eligendi,status:1",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *              @SWG\Property(property="error", type="boolean", default=FALSE),
     *              @SWG\Property(property="data",type="object",
     *              @SWG\Property(property="users",type="array",
     *                  @SWG\Items(ref="#/definitions/user")
     *              ),
     *              ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad request",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        if ($searchQuery = $request->get('search', false)) {

            $searchEntity = explode(',', $searchQuery);

            if (!empty($searchEntity)) {

                $searchMatch = [];
                foreach ($searchEntity as $entity) {
                    $paramVal = explode(':', $entity);

                    if (2 == count($paramVal)) {
                        $searchMatch[] = ['match' => [$paramVal[0] => $paramVal[1]]];
                    }
                }
            }

            if (!empty($searchMatch)) {
                $users = User::complexSearch([
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    $searchMatch
                                ]
                            ]
                        ],
                        'aggs' => [
                            'dedup' => [
                                'terms' => ['field' => 'id'],
                                'aggs' => [
                                    'dedup_docs' => [
                                        'top_hits' => [
                                            'size' => 1
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

                return response()->success(compact('users'));
            }

        }

        $users = User::all();
        return response()->success(compact('users'));
    }

    /**
     * @SWG\Get(
     *     path="/api/v1/users/{id}",
     *     description="Get the user details",
     *     summary="Get the user details",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *         description="ID of user to show details",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer",
     *         format="int64",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *              @SWG\Property(property="error",type="boolean", default=FALSE),
     *              @SWG\Property(property="data",type="object",
     *              @SWG\Property(property="user",type="object",
     *                  @SWG\Property(property="id",type="integer"),
     *                  @SWG\Property(property="name",type="string"),
     *                  @SWG\Property(property="email",type="string"),
     *                  @SWG\Property(property="description",type="string"),
     *                  @SWG\Property(property="status",type="integer"),
     *                  @SWG\Property(property="created_at",type="string"),
     *                  @SWG\Property(property="updated_at",type="string"),
     *                  @SWG\Property(property="deleted_at",type="string")
     *              ),
     *              ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad request",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->success(compact('user'));
    }

    /**
     * @SWG\Post(
     *     path="/api/v1/users",
     *     description="Store a new user",
     *     summary="Store a new user",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         required=false,
     *     @SWG\Schema(
     *     required={
     *          "name",
     *          "email",
     *          "status",
     *          "description"
     *      },
     *     @SWG\Property(
     *         property="name",
     *         type="string",
     *     ),
     *     @SWG\Property(
     *         property="email",
     *         type="string"
     *     ),
     *     @SWG\Property(
     *         property="status",
     *         type="integer",
     *         default=0, enum={0,1}
     *     ),
     *     @SWG\Property(
     *         property="description",
     *         type="string"
     *     ))),
     *     @SWG\Response(
     *         response=201,
     *         description="Successful operation",
     *     @SWG\Schema(
     *              @SWG\Property(property="error",type="boolean", default=FALSE),
     *              @SWG\Property(property="data",type="object",
     *                  @SWG\Property(property="id",type="integer"),
     *              ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Bad request",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user = new User();
        $user->fill($request->all());

        if ($user->isInvalid()) {
            return response()->error($user->getErrors(), 422);
        }

        $user->save();
        return response()->success(['id' => $user->id], 201);
    }

    /**
     * @SWG\Put(
     *     path="/api/v1/users/{id}",
     *     description="User update",
     *     summary="User update",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *         description="ID of user to update",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer",
     *         format="int32"
     *     ),
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         required=false,
     *     @SWG\Schema(
     *     required={
     *      },
     *     @SWG\Property(
     *         property="name",
     *         type="string",
     *     ),
     *     @SWG\Property(
     *         property="email",
     *         type="string"
     *     ),
     *     @SWG\Property(
     *         property="status",
     *         type="integer",
     *         default=0, enum={0,1}
     *     ),
     *     @SWG\Property(
     *         property="description",
     *         type="string"
     *     ))),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/user")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad request",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        $user->fill($request->all());

        if ($user->isInvalid()) {
            return response()->error($user->getErrors(), 422);
        }

        $user->save();
        return response()->success($user, 200);
    }

    /**
     * @SWG\Delete(
     *     path="/api/v1/users/{id}",
     *     description="User delete",
     *     summary="User delete",
     *     operationId="deleteUser",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="User id to delete",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Successful operation",
     *         @SWG\Schema(
     *              required={"error", "data"},
     *              @SWG\Property(property="error", type="boolean", default=FALSE),
     *              @SWG\Property(property="data", type="string", default=NULL),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="User not found.",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     )
     * )
     *
     * @param integer $id
     * @return mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->success(null, 204);
    }
}
