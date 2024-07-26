<?php

namespace App\Http\Controllers;

use App\Library\Application\CaseRoom\CaseRoomReader;
use App\Library\Fractal\Transformers\CaseRoom\CaseRoomListTransformer;
use Illuminate\Http\Request;

/**
 * Class CaseRoomController
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author John Doe <john@doe.test>
 */
class CaseRoomController extends APIController
{
    /** @var CaseRoomReader */
    protected $caseRoomReader;

    //-------------------------------------------------------------------------

    /**
     * CaseRoomController constructor.
     *
     * @param CaseRoomReader $caseRoomReader
     */
    public function __construct(CaseRoomReader $caseRoomReader)
    {
        $this->caseRoomReader = $caseRoomReader;
    }

    //-------------------------------------------------------------------------

    /**
     * @OA\Get(
     *     path="/api/case_room",
     *     summary="List of case room",
     *     description="This end point returns the list of case rooms",
     *     operationId="listCaseRoom",
     *     tags={"Case Rooms"},     *
     *     @OA\Response(
     *          response=200,
     *          description="Operation Successfull",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="id",
     *                          type="integer",
     *                          description="Unique id of the case room",
     *                          example=3
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          description="Name of the case room",
     *                          example="Maiores"
     *                      )
     *                  )
     *              )
     *          )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = $this->caseRoomReader->listAll($request);
        if (count($result)) {
            $transformedResult = $this->getCustomFractal()->setCollection($result)
                                    ->setTransformer(new CaseRoomListTransformer())
                                    ->toArray();

            return $this->responseWithSuccess($transformedResult['data']);
        }

        return $this->responseWithNotFound();
    }
}
//end of class CaseRoomController
// end of file CaseRoomController.php
